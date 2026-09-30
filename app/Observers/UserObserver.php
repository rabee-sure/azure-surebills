<?php

namespace App\Observers;

use App\Events\UserVerifiedChanged;
use App\Events\AddActionLogEvent;
use App\Events\UserUpdated;
use App\Jobs\SetNewMerchantSettings;
use App\Models\User;
use App\Models\Role;
use App\Models\SystemAction;
use App\Services\Cache\CacheKeys;
use App\Services\MerchantProfileService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;


class UserObserver
{
    public function __construct(
        private readonly MerchantProfileService $merchantProfile,
    ) {}

    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        $this->letUserSuperAdmin($user);

        SetNewMerchantSettings::dispatch($user);

        $user->myBalance()->create();

        $this->putProfile($user, true);
    }

    public function saved(User $user)
    {
        $this->letUserSuperAdmin($user);
    }

    public function updating(User $user){
        if($user->isDirty('verified')){
            if($user->verified){
                $user->can_create_bill = 1;
            }
        }
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        event(new UserUpdated($user));

        if(Auth::guard('admins')->check() && !Auth::guard('web')->check()){
            $fieldsArr = config('userfields');

            foreach($fieldsArr as $groupKey => $fieldsGroup){
                $fieldsChanges = [];
                if($user->isDirty($fieldsGroup)){
                    foreach($fieldsGroup as $field){
                        if($user->isDirty($field)){
                            $fieldsChanges[$field] = [
                                'old_value' => $user->getOriginal($field),
                                'new_value' => $user->$field
                            ];
                        }
                    }
                    event(new AddActionLogEvent(
                        'user_update',
                        Auth::id(),
                        [
                            'message' => [
                                'username' => $user->name,
                                'adminname' => Auth::user()->name,
                                'fields_group' => $groupKey,
                                'time' => $user->updated_at,
                            ],
                            'changes' => $fieldsChanges,
                        ],
                        $user->id,
                        User::class
                    ));
                }
            }
        }
        if($user->isDirty('verified')){
            UserVerifiedChanged::dispatch($user);

            if(Auth::guard('admins')->check()){
                event(new AddActionLogEvent(
                    $user->verified ? 'user_approved' : 'user_unapproved',
                    Auth::id(),
                    ['message' => [
                        'username' => $user->name,
                        'adminname' => Auth::user()->name,
                        'time' => $user->updated_at
                    ]],
                    $user->id,
                    User::class
                ));
            }
        }

        $this->putProfile($user, $user->wasChanged('name'));
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        $this->forgetProfile($user);
    }

    /**
     * Handle the User "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        $this->putProfile($user, true);
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        $this->forgetProfile($user);
    }

    /**
     * Refresh the sanitized profile. Bump the shared dropdown version only when
     * the merchant enters, leaves, or is renamed in the id/name dropdown.
     */
    private function putProfile(User $user, bool $bumpDropdown): void
    {
        try {
            $this->merchantProfile->put($user);
            if ($bumpDropdown) {
                $this->merchantProfile->bumpDropdown();
            }
        } catch (Throwable $e) {
            Log::warning('merchant_profile cache rewrite failed after user mutation', [
                'domain' => 'merchant_profile',
                'key' => CacheKeys::merchantProfile($user->id),
                'operation' => 'put',
                'exception' => $e::class,
            ]);
        }
    }

    private function forgetProfile(User $user): void
    {
        try {
            $this->merchantProfile->forget((int) $user->id);
            $this->merchantProfile->bumpDropdown();
        } catch (Throwable $e) {
            Log::warning('merchant_profile cache forget failed after user delete', [
                'domain' => 'merchant_profile',
                'key' => CacheKeys::merchantProfile($user->id),
                'operation' => 'forget',
                'exception' => $e::class,
            ]);
        }
    }

    private function letUserSuperAdmin($user)
    {
        if(!$user->store_main_user_id && $user->source != 'pos')
        {
            $role = Role::where([['name', 'super admin'], ['guard_name', 'web']])->first();
            $user->assignRole($role->id);
        }
    }
}
