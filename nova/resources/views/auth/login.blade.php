@extends('nova::auth.layout')

@section('content')

@include('nova::auth.partials.header')

<form
    class="bg-white shadow rounded-lg p-8 max-w-login mx-auto"
    method="POST"
    action="{{ route('nova.login') }}"
>
    {{ csrf_field() }}

    @component('nova::auth.partials.heading')
        {{ __('Welcome Back!') }}
    @endcomponent

    @if (session('status'))
    <div class="text-danger text-center font-semibold my-3">
        {{ __(session('status')) }}
    </div>
    @endif

    @if ($errors->any())
    <p class="text-center font-semibold text-danger my-3">
        @if ($errors->has('email'))
            {{ $errors->first('email') }}
        @else
            {{ $errors->first('password') }}
        @endif
        </p>
    @endif

    <div class="mb-6 {{ $errors->has('email') ? ' has-error' : '' }}">
        <label class="block font-bold mb-2" for="email">{{ __('Email Address') }}</label>
        <input class="form-control form-input form-input-bordered w-full" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="off">
    </div>

    <div class="mb-6 {{ $errors->has('password') ? ' has-error' : '' }}">
        <label class="block font-bold mb-2" for="password">{{ __('Password') }}</label>
        <input class="form-control form-input form-input-bordered w-full" id="password" type="text" name="password" required autocomplete="off">
    </div>

    <div class="flex mb-6">
        <label class="flex items-center block text-xl font-bold">
            <input class="" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
            <span class="text-base ml-2">{{ __('Remember Me') }}</span>
        </label>


        @if (\Laravel\Nova\Nova::resetsPasswords())
        <div class="ml-auto">
            <a class="text-primary dim font-bold no-underline" href="{{ route('nova.password.request') }}">
                {{ __('Forgot Your Password?') }}
            </a>
        </div>
        @endif
    </div>

    <button class="w-full btn btn-default btn-primary hover:bg-primary-dark" type="submit">
        {{ __('Login') }}
    </button>
</form>

<style>
    @font-face{
  font-family: text-security-disc;
  src: url("https://raw.githubusercontent.com/noppa/text-security/master/dist/text-security-disc.woff");
}
#password {
    -webkit-text-security: disc;
    font-family: text-security-disc;
    letter-spacing: .2rem;
}
#password::-webkit-input-placeholder { /* Chrome/Opera/Safari */
  font-family: "Almarai", "Roboto";
    letter-spacing: 0;
}
#password::-moz-placeholder { /* Firefox 19+ */
  font-family: "Almarai", "Roboto";
    letter-spacing: 0;
}
#password:-ms-input-placeholder { /* IE 10+ */
  font-family: "Almarai", "Roboto";
    letter-spacing: 0;
}
#password-moz-placeholder { /* Firefox 18- */
  font-family: "Almarai", "Roboto";
    letter-spacing: 0;
}
  </style>
@endsection
