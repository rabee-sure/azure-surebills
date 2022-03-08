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
                            <input  name="email" type="email" class="form-control" id="Email" placeholder="{{__('Email')}}" value="{{$user->email}}">
                        </div>
                        <div class="form-group">
                            <label for="Password">{{__('Password')}}</label>
                            <input name="password" type="password" class="form-control" id="Password" placeholder="{{__('Password')}}">
                        </div>
                        <div class="form-group">
                            <label for="Confirm Password">{{__('Confirm Password')}}</label>
                            <input name="confirm_password" type="password" class="form-control" id="Confirm_Password" placeholder="{{__('Confirm Password')}}">
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


@push('footer-scripts')

@endpush
