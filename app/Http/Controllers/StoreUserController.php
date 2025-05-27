<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserPermissionResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class StoreUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show users', ['only' => ['index', 'show']]);
        $this->middleware('permission:create user', ['only' => ['create', 'store']]);
        $this->middleware('permission:update user', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete user', ['only' => ['destroy']]);
        $this->middleware('permission:restore user', ['only' => ['restore']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::userId(auth()->user()->store_main_user_id ?? auth()->user()->id)->get();
        $users = User::withTrashed()->where('id', auth()->user()->store_main_user_id ?? auth()->user()->id)->orWhere('store_main_user_id', auth()->user()->store_main_user_id ?? auth()->user()->id)->orderBy('created_at', 'DESC')->paginate(10);

        return view('store_users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'last_name' => $request->last_name,
                'password' => bcrypt($request->password),
                'email' => $request->email,
                'mobile' => $request->mobile,
                'mobile_verified' => 1,
                'store_main_user_id' => auth()->user()->mainStoreUser ? auth()->user()->mainStoreUser->id : auth()->user()->id,
            ]);

            $user->verified = $user->mainStoreUser ? $user->mainStoreUser->verified : 0;
            $user->able_refund = $user->mainStoreUser ? $user->mainStoreUser->able_refund : 0;
            $user->vat_inclusive = $user->mainStoreUser ? $user->mainStoreUser->vat_inclusive : 0;
            $user->able_refund_with_fees = $user->mainStoreUser ? $user->mainStoreUser->able_refund_with_fees : 0;
            $user->auto_trnasfer = $user->mainStoreUser ? $user->mainStoreUser->auto_trnasfer : 0;
            $user->disable_business_documents = $user->mainStoreUser ? $user->mainStoreUser->disable_business_documents : 0;
            $user->disable_bank_documents = $user->mainStoreUser ? $user->mainStoreUser->disable_bank_documents : 0;
            $user->save();

            $role = Role::find($request->role);
            $user->assignRole($role);
        });
        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $this->authorize('updateMerchantUser', $user);
        $roles = Role::userId(auth()->user()->store_main_user_id ?? auth()->user()->id)->get();
        return view('store_users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUserRequest $request, User $user)
    {
        $this->authorize('updateMerchantUser', $user);

        DB::transaction(function () use ($request, $user) {
            if($request->filled('password'))
            {
                $user->password = bcrypt($request->password);
            }

            $user->name = $request->name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->gender = $request->gender;
            $user->save();

            if($request->has('role'))
            {
                $user->roles()->detach();
                $role = Role::find($request->role);
                $user->assignRole($role);
            }
        });

        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $this->authorize('deleteMerchantUser', $user);
        $user->delete();
        return redirect()->route('users.index');
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->find($id);
        $this->authorize('restoreMerchantUser', $user);
        $user->restore();
        return redirect()->route('users.index');
    }
}
