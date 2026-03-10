@extends('layouts.auth')

@section('title', __('Reset Password') )

@section('content')

  <h4 class="mb-1">{{ __('Reset Password') }}</h4>
  <p class="mb-6">{{ __('Please use your e-mail to reset your password.') }}</p>

  <form class="mb-4" method="POST" action="{{ route('password.email') }}" id="form">
    @if (session('status'))
      <div class="alert alert-success mb-6" role="alert">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
      <ul class="list-group mb-6">
        @foreach ($errors->all() as $error)
          <li class="list-group-item list-group-item-danger">{{ $error }}</li>
        @endforeach
      </ul>
    @endif
    @csrf
    <div class="mb-6 @error('email') is-invalid @enderror">
      <label for="email" class="form-label">{{ __('E-Mail Address') }}</label>
      <input
        id="email"
        type="email"
        class="form-control @error('email') is-invalid @enderror"
        name="email"
        inputmode="email"
        value="{{ old('email') }}"
        autocomplete="off"
        placeholder="{{ __('E-Mail Address') }}"
        autofocus
      />
      @error('email')
        <div class="invalid-feedback text-danger" role="alert">{{ $message }}</div>
      @enderror
    </div>
    <div class="mb-6">
      <button class="btn btn-primary d-grid w-100" type="submit">{{ __('Send Password Reset Link') }}</button>
    </div>
  </form>

  <p class="text-center">
    <span>{{ __('If you are a member, please') }}</span>
    <a href="{{ route('login') }}" title="{{ __('Login') }}">
      <span>{{ __('Login') }}</span>
    </a>
  </p>
@endsection

@push('footer-scripts')
  {!! JsValidator::formRequest('App\Http\Requests\ForgotPasswordRequest', '#form') !!}
@endpush
