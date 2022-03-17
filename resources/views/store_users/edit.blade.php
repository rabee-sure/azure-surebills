@extends('layouts.app')

@section('title', __('Users'))

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <a href="{{ url('account')}}" title="{{ __('Settings') }}">{{ __('Settings') }}</a>
    <i>/</i>
    <a href="{{ url('/users')}}" title="{{ __('Users') }}">{{ __('Users') }}</a>
    <i>/</i>
    <span>{{ __('Edit')}}</span>
  </div><!-- breadcrump -->

  <section id="usersEditPage">
    <div class="title mb-4">
      <h1 class="d-block fw-bold m-0 fs-5">{{__('Edit')}}</h1>
    </div><!-- title -->
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div><!-- alert -->
    @endif
    <div class="blockArea bg-white rounded-3 shadow-sm p-3">
      <form method="post" action="{{ route('users.update', $user->id) }}" id="user_form">
        @method('PATCH')
        @csrf
        <div class="row">
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="Name" class="d-block mb-2">{{__('Name')}} <span class="requirement text-danger">*</span></label>
              <input name="name" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="Name" placeholder="{{__('Name')}}" value="{{$user->name}}">
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="Mobile" class="d-block mb-2">{{ __('Mobile') }}</label>
              <div class="phoneInput overflow-hidden position-relative">
                <span class="d-flex align-items-center justify-content-center position-absolute rounded-3">+966</span>
                <input name="mobile" type="tel" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="Mobile" placeholder="{{__('Mobile')}}" value="{{$user->mobile}}"  pattern="[0-9]*" maxlength="9" inputmod="numaric">
              </div><!-- phoneInput -->
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="Email" class="d-block mb-2">{{__('Email')}}</label>
              <input  name="email" type="email" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="Email" placeholder="{{__('Email')}}" value="{{$user->email}}">
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="Password" class="d-block mb-2">{{__('Password')}}</label>
              <input name="password" type="password" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="Password" placeholder="{{__('Password')}}" value="" autocomplete="false">
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="Confirm Password" class="d-block mb-2">{{__('Confirm Password')}}</label>
              <input name="confirm_password" type="password" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="Confirm_Password" placeholder="{{__('Confirm Password')}}" value="" autocomplete="false">
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group">
              <label for="gender" class="d-block mb-2">{{ __('Gander')}}</label>
              <select name="gender" id="gender" class="form-control shadow-none bg-white border w-100 rounded-3 text-body">
                <option value="0" @if ($user->gender == 0)selected="selected"@endif>{{ __('Choose Gender')}}</option>
                <option value="1" @if ($user->gender == 1)selected="selected"@endif>{{ __('Male')}}</option>
                <option value="2" @if ($user->gender == 2)selected="selected"@endif>{{ __('female')}}</option>
              </select>
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group">
              <label for="role" class="d-block mb-2">{{__('Role')}}</label>
              <select name="role" id="role" class="form-control shadow-none bg-white border w-100 rounded-3 text-body">
                @foreach($roles as $role)
                  <option value="{{$role->name}}" {{$user->getRoleNames()->first() == $role->name ? 'selected' : ''}}>{{$role->name}}</option>
                @endforeach
              </select>
            </div><!-- form-group -->
          </div><!-- col-12 -->
        </div><!-- row -->
        <div class="buttonsArea mt-5 d-flex align-items-center justify-content-start">
          <button type="submit" class="rounded-3 border-0 shadow-none d-flex align-items-center justify-content-center btn-primary fw-bold formBtn">{{__('Update')}}</button>
          <a href="{{ url('users') }}" title="{{__('Back')}}" class="rounded-3 border-0 shadow-none d-flex align-items-center fw-bold justify-content-center btn-light m-0">{{__('Back')}}</a>
        </div><!-- buttonsArea -->
      </form>
    </div><!-- blockArea -->
  </section><!-- usersEditPage -->

@endsection
