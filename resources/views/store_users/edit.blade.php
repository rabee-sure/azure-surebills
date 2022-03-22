@extends('layouts.app')

@section('title', __('Users'))

@section('content')

  <div class="row">
    <div class="col-12">
      <div class="mb-3">
        <h1>{{ __('Users')}}</h1>
        <div class="top-right-button-container">
      </div>
      <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
        <ol class="breadcrumb pt-0">
          <li class="breadcrumb-item">
            <a href="{{ url('/') }}">{{ __('Home')}}</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/users') }}">{{ __('Users')}}</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">{{ __('Edit')}}</li>
        </ol>
      </nav>
      </div>
    </div>
    <div class="separator mb-5"></div>
  </div>
  <div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="post" action="{{ route('users.update', $user->id) }}" id="user_form">
                <div class="modal-body">
                        @method('PATCH')
                        @csrf
                        <div class="form-group">
                            <label for="Name">{{__('Name')}} <span class="requirement">*</span></label>
                            <input name="name" type="text" class="form-control" id="Name" placeholder="{{__('Name')}}" value="{{$user->name}}">
                        </div>
                        <div class="form-group">
                            <label for="Mobile">{{ __('Mobile') }}</label>
                            <div class="input-group phone_inputs">
                              <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon3">+966</span>
                              </div>
                              <input name="mobile" type="tel" class="form-control" id="Mobile" placeholder="{{__('Mobile')}}" value="{{$user->mobile}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="Email">{{__('Email')}}</label>
                            <input  name="email" type="email" class="form-control" id="Email" placeholder="{{__('Email')}}" value="{{$user->email}}" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="Password">{{__('Password')}}</label>
                            <input name="password" type="password" class="form-control" id="Password" placeholder="{{__('Password')}}" value="" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="Confirm Password">{{__('Confirm Password')}}</label>
                            <input name="confirm_password" type="password" class="form-control" id="Confirm_Password" placeholder="{{__('Confirm Password')}}" value="" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label>{{ __('Gander')}}</label>
                            <select name="gender" id="gender" class="form-control">
                                <option value="0" @if ($user->gender == 0)selected="selected"@endif>{{ __('Choose Gender')}}</option>
                                <option value="1" @if ($user->gender == 1)selected="selected"@endif>{{ __('Male')}}</option>
                                <option value="2" @if ($user->gender == 2)selected="selected"@endif>{{ __('female')}}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{__('Role')}}</label>
                            <select name="role" class="form-control">
                                @foreach($roles as $role)
                                    <option value="{{$role->name}}" {{$user->getRoleNames()->first() == $role->name ? 'selected' : ''}}>{{$role->name}}</option>
                                @endforeach
                            </select>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary login_button mr-3">{{__('Update')}}</button>
                    <a href="{{ url('users') }}" class="btn btn-secondary m-0" data-dismiss="modal">{{__('Back')}}</a>
                </div>
            </form>
      </div>
    </div>
  </div>
  </div>
@endsection


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
              <input name="name" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="Name" placeholder="{{__('Name')}}" value="{{old('name') ?? $user->name}}">
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="Mobile" class="d-block mb-2">{{ __('Mobile') }}</label>
              <div class="phoneInput overflow-hidden position-relative">
                <span class="d-flex align-items-center justify-content-center position-absolute rounded-3">+966</span>
                <input name="mobile" type="tel" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="Mobile" placeholder="{{__('Mobile')}}" value="{{old('mobile') ?? $user->mobile}}" pattern="[0-9]*" maxlength="9" inputmod="numaric">
              </div><!-- phoneInput -->
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="Email" class="d-block mb-2">{{__('Email')}}</label>
              <input  name="email" type="email" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="Email" placeholder="{{__('Email')}}" value="{{old('email') ?? $user->email}}">
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
                <option value="0" @if ($user->gender == 0 || old('gender') == 0)selected="selected"@endif>{{ __('Choose Gender')}}</option>
                <option value="1" @if ($user->gender == 1 || old('gender') == 1)selected="selected"@endif>{{ __('Male')}}</option>
                <option value="2" @if ($user->gender == 2 || old('gender') == 2)selected="selected"@endif>{{ __('female')}}</option>
              </select>
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group">
              <label for="role" class="d-block mb-2">{{__('Role')}}</label>
              <select name="role" id="role" class="form-control shadow-none bg-white border w-100 rounded-3 text-body">
                @foreach($roles as $role)
                  <option value="{{$role->name}}" {{$user->getRoleNames()->first() == $role->name || old('role') == $role->name ? 'selected' : ''}}>{{$role->name}}</option>
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
