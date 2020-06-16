@extends('layouts.auth')

@section('title', __('Reset Password') )

@section('content')
    <div class="row h-100">
        <div class="col-12 col-md-10 mx-auto my-auto">
            <div class="card auth-card">
                <div class="position-relative image-side ">
                    <p class=" text-white h2">{{ __('Start Sending Bills') }}</p>
                    <p class="white mb-0">
                      {{ __('Please use this form to register.') }}
                      <br>
                      {{ __('If you are a member, please') }} 
                      <a href="{{ route('register')}}" class="white">{{ __('Register') }}</a>.
                    </p>
                </div>
                <div class="form-side">
                    <a href="index.html"><span class="logo-single"></span></a>
                    <h6 class="mb-4">{{ __('Reset Password') }}</h6>
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">
                            <label for="email"  class="form-group has-float-label mb-4">
                                <input  id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus />
                                <span>{{ __('E-Mail Address') }}</span>
                            </label>

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                <label for="password" class="form-group has-float-label mb-4">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password"/>
                                    <span>{{ __('Password') }}</span>
                                </label>
                                @error('password')
                                    <p class="invalid-feedback" role="alert">{{ $message }}</p>
                                @enderror
                            </div><!-- col-12 -->       
                    
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                <label for="password-confirm" class="form-group has-float-label mb-4">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" />
                                    <span>{{ __('Confirm Password') }}</span>
                                </label>
                            </div><!-- col-12 -->

                            <div class="d-flex justify-content-end align-items-center">
                                <button class="btn btn-primary btn-lg btn-shadow" type="submit">{{ __('Reset Password') }}</button>
                            </div>
                        </form>
                </div>
            </div>
        </div>
    </div>
@endsection
