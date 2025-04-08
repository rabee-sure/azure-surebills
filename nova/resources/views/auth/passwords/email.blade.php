@extends('nova::auth.layout')

@section('content')

@include('nova::auth.partials.header')

<form
    class="bg-white shadow rounded-lg p-8 max-w-login mx-auto nova-login-area"
    method="POST"
    action="{{ route('nova.password.email') }}"
>
    {{ csrf_field() }}

    @component('nova::auth.partials.heading')
        {{ __('Forgot your password?') }}
    @endcomponent

    @if (session('status'))
    <div class="text-info text-center font-semibold my-3">
        {{-- {{ session('status') }} --}}
        {{__("If this email is associated with an account, you will receive a password reset link.")}}
    </div>
    @endif

    {{-- @include('nova::auth.partials.errors') --}}

    @if ($errors->any())
        @if ($errors->has('email'))
            <div class="text-info text-center font-semibold my-3">
                {{__("If this email is associated with an account, you will receive a password reset link.")}}
            </div>
        @endif
    @endif

    <div class="form-group mb-6 {{ $errors->has('email') ? ' has-error' : '' }}">
        <label class="block font-bold mb-2" for="email">{{ __('Email Address') }}</label>
        <input class="form-control form-input form-input-bordered w-full" id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="off" required>
    </div>

    <button type="submit">
        {{ __('Send Password Reset Link') }}
    </button>
</form>
@endsection
