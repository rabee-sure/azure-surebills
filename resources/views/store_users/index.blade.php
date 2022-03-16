@extends('layouts.app')

@section('title', __('Users'))

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <a href="{{ url('account')}}" title="{{ __('Settings') }}">{{ __('Settings') }}</a>
    <i>/</i>
    <span>{{ __('Users')}}</span>
  </div><!-- breadcrump -->

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <section id="usersIndexPage">
    <div class="tabsArea d-flex align-items-center justify-content-start flex-wrap mb-4">
      <span class="d-flex shadow-none align-items-center justify-content-center border bg-white text-body rounded-3">{{__('Users')}}</span>
      <a href="{{route('roles.index')}}" title="{{__('Roles')}}" class="d-flex btn-primary border-0 shadow-none align-items-center justify-content-center text-white rounded-3">{{__('Roles')}}</a>
    </div><!-- tabsArea -->
    <div class="title d-flex align-items-center justify-content-between mb-4">
      <h1 class="d-block fw-bold m-0 fs-5">{{__('Users')}}</h1>
      @can('create user')
        @include('store_users.create')
      @endcan
    </div><!-- title -->
    @if(count($users) == 0)
      <div class="no_customers_yet d-flex align-items-center justify-content-center flex-column bg-white shadow-sm rounded-3 overflow-hidden mb-3">
        <i class="fal fa-users"></i>
        <span class="d-block text-center mt-3 text-capitalize">{{ __('No users') }}</span>
      </div><!-- no_customers_yet -->
    @else
      <div class="blockArea bg-white shadow-sm rounded-3 overflow-hidden mb-3">
        <div class="table-responsive">
          <table class="table table-striped table-hover text-nowrap">
            <thead>
              <tr>
                <th scope="col" class="text-center">#</th>
                <th scope="col" class="text-center">{{__('Name')}}</th>
                <th scope="col" class="text-center">{{__('Mobile')}}</th>
                <th scope="col" class="text-center">{{__('Email')}}</th>
                <th scope="col" class="text-center">{{__('Gender')}}</th>
                <th scope="col" class="text-center">{{__('Role')}}</th>
                @canany(['update user', 'delete user'])
                  <th scope="col" class="text-center" width="10%">{{__('Actions')}}</th>
                @endcanany
              </tr>
            </thead>
            <tbody>
              @foreach($users as $user)
                <tr>
                  <td class="text-center">{{$loop->index+1}}</td>
                  <td class="text-center">{{$user->name}}</td>
                  <td class="text-center">{{$user->mobile}}</td>
                  <td class="text-center">{{$user->email}}</td>
                  <td class="text-center">{{$user->gender == 1 ? __('Male') : __('female')}}</td>
                  <td class="text-center">{{$user->getRoleNames()->first()}}</td>
                  @canany(['update user', 'delete user'])
                    <td class="text-center">
                      <div class="d-flex align-items-center justify-content-center">
                        @can('update user')
                          <a href="{{ route('users.edit', $user->id)}}" class="rounded-3 border-0 shadow-none p-0 btn-primary d-flex align-items-center justify-content-center mx-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Edit') }}"><i class="fal fa-edit"></i></a>
                        @endcan
                        @can('delete user')
                          @include('store_users.delete', ['user' => $user])
                        @endcan
                      </div>
                    </td>
                  @endcanany
                </tr>
              @endforeach
            </tbody>
          </table>
        </div><!-- table-responsive -->
      </div><!-- blockArea -->
    @endif
  </section><!-- usersIndexPage -->
@endsection

@push('footer-scripts')
  {!! JsValidator::formRequest('App\Http\Requests\StoreUserRequest', '#user_form') !!}
@endpush
