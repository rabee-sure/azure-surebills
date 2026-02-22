@extends('layouts.app')

@section('title', __('Users'))

@push('css_styles')
  <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/select2/select2.css') }}">
@endpush

@section('content')

  <h4 class="mb-1">{{ __('Edit')}} : {{$user->name}}</h4>

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-custom-icon mb-6">
      <li class="breadcrumb-item">
        <a href="{{ url('/users') }}" title="{{ __('Users') }}">{{ __('Users')}}</a>
        <i class="breadcrumb-icon icon-base ti ti-chevron-right align-middle icon-xs"></i>
      </li>
      <li class="breadcrumb-item active">{{ __('Edit')}}</li>
    </ol>
  </nav>

  @if ($errors->any())
    <ul class="list-group mb-6">
      @foreach ($errors->all() as $error)
        <li class="list-group-item list-group-item-danger">{{ $error }}</li>
      @endforeach
    </ul>
  @endif


  <form method="post" action="{{ route('users.update', $user->id) }}" id="user_form" class="card mb-6">
    @method('PATCH')
    @csrf
    <div class="card-body">
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 g-6">
        <div class="col">
          <label for="Name" class="form-label">{{__('Name')}}<span class="requirement text-danger">*</span></label>
          <input name="name" type="text" class="form-control" id="Name" placeholder="{{__('Name')}}" value="{{old('name') ?? $user->name}}" autocomplete="off">
        </div><!-- col -->
        <div class="col">
          <label for="Mobile" class="form-label">{{ __('Mobile') }}<span class="requirement text-danger">*</span></label>
          <input name="mobile" type="tel" class="form-control" id="Mobile" placeholder="{{__('Mobile')}}" value="{{old('mobile') ?? $user->mobile}}" pattern="[0-9]*" maxlength="9" inputmod="numaric" autocomplete="off">
        </div><!-- col -->
        <div class="col">
          <label for="Email" class="form-label">{{__('Email')}}<span class="requirement text-danger">*</span></label>
          <input  name="email" type="email" class="form-control" id="Email" placeholder="{{__('Email')}}" value="{{old('email') ?? $user->email}}" inputmode="email" autocomplete="off">
        </div><!-- col -->
        <div class="col">
          <label for="Password" class="form-label">{{__('Password')}}</label>
          <div class="input-group input-group-merge custom-form-password-toggle">
            <input
              id="password"
              name="password"
              autocomplete="off"
              type="password"
              class="form-control"
              placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
              aria-describedby="password"
              />
            <span class="input-group-text cursor-pointer position-relative"><i class="icon-base ti ti-eye-off"></i></span>
          </div>
        </div><!-- col -->
        <div class="col">
          <label for="Confirm_Password" class="form-label">{{__('Confirm Password')}}</label>
          <div class="input-group input-group-merge custom-form-password-toggle">
            <input
              id="Confirm_Password"
              name="confirm_password"
              autocomplete="off"
              type="password"
              class="form-control"
              placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
              aria-describedby="Confirm_Password"
              />
            <span class="input-group-text cursor-pointer position-relative"><i class="icon-base ti ti-eye-off"></i></span>
          </div>
        </div><!-- col -->
        {{-- <div class="col">
          <label for="gender" class="form-label">{{ __('Gander')}}<span class="requirement text-danger">*</span></label>
          <select name="gender" id="gender" class="form-control select2-single">
            <option value="0" @if ($user->gender == 0 || old('gender') == 0)selected="selected"@endif>{{ __('Choose Gender')}}</option>
            <option value="1" @if ($user->gender == 1 || old('gender') == 1)selected="selected"@endif>{{ __('Male')}}</option>
            <option value="2" @if ($user->gender == 2 || old('gender') == 2)selected="selected"@endif>{{ __('female')}}</option>
          </select>
        </div><!-- col --> --}}
        @if($user->getRoleNames()->first() != 'super admin')
          <div class="col">
              <label for="role" class="form-label">{{__('Role')}}<span class="requirement text-danger">*</span></label>
              <select name="role" id="role" class="form-control select2-single">
                @foreach($roles as $role)
                      <option value="{{$role->id}}" {{ optional($user->roles->first())->id == $role->id || old('role') == $role->id ? 'selected' : ''}}>{{$role->name}}</option>
                @endforeach
              </select>
          </div><!-- col -->
        @endif
      </div><!-- row -->
    </div><!-- card-body -->
    <div class="card-footer d-flex align-items-center justify-content-end gap-3">
      <a href="{{ url('users') }}" title="{{__('Back')}}" class="btn btn-light">{{__('Back')}}</a>
      <button type="submit" class="btn btn-primary">{{__('Update')}}</button>
    </div>
  </form>

@endsection

@push('footer-scripts')
  <script src="{{ asset('assets/v2/vendor/libs/select2/select2.js') }}"></script>
  <script type="text/javascript">
    $(window).on('load',function() {
      setTimeout(function() {
        $("input[type=password]").val('');
      }, 100);
    });

    // Select2
    $(document).ready(function() {
      $('.select2-single').select2();
    });
    // Password Toggle
    document.addEventListener('DOMContentLoaded', function() {
      initPasswordToggle();
    });
    function initPasswordToggle() {
      const togglers = document.querySelectorAll('.custom-form-password-toggle i');
      togglers.forEach(icon => {
        icon.addEventListener('click', function(e) {
          e.preventDefault();

          const container = this.closest('.custom-form-password-toggle');
          const input = container.querySelector('input');
          const toggleIcon = container.querySelector('i');

          if (input.type === 'password') {
            input.type = 'text';
            toggleIcon.classList.replace('ti-eye-off', 'ti-eye');
          } else {
            input.type = 'password';
            toggleIcon.classList.replace('ti-eye', 'ti-eye-off');
          }
        });
      });
    }
  </script>
@endpush
