<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserPermissionResource;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class StoreUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show users', ['only' => ['index', 'show']]);
        $this->middleware('permission:create user', ['only' => ['create', 'store']]);
        $this->middleware('permission:update user', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete user', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::whereIn('user_id', auth()->user()->storeUsers(true))->get();
        $users = User::whereIn('store_main_user_id', auth()->user()->storeUsers(true))->orderBy('created_at', 'DESC')->paginate(10);
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
        $user = User::create([
            'name' => $request->name,
            'password' => bcrypt($request->password),
            'email' => $request->email,
            'mobile' => $request->mobile,
            'gender' => $request->gender,
            'gender' => $request->gender,
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

        $user->assignRole($request->role);
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
        $roles = Role::whereIn('user_id', auth()->user()->storeUsers(true))->get();
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
        if($request->filled('password'))
        {
            $user->password = bcrypt($request->password);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->gender = $request->gender;
        $user->save();

        $user->roles()->detach();
        $user->assignRole($request->role);

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
        $user->delete();
        return redirect()->route('users.index');
    }

    public function getUserPermissions()
    {
        return auth()->user()->getAllPermissions()->pluck('name');
    }
}
