@extends('layouts.auth')

@section('title', __('Reset Password') )

@section('content')
    <div class="row h-100">
        <div class="col-12 col-md-10 mx-auto my-auto">
            <div class="card auth-card">
                <div class="position-relative image-side ">
                    <p class=" text-white h2">{{ __('Reset Password') }}</p>
                    <p class="white mb-0">
                      {{ __('Please use this form to register.') }}
                      <br>
                      {{ __('If you are a member, please') }} <a href="{{ route('login') }}" title="{{ __('Login') }}" class="white">{{ __('Login') }}</a>.
                    </p>
                </div>
                <div class="form-side" >
        <div class="changeLang">
          @if(App::isLocale('en'))
            <a  href="{{ route('changeLang', ['lang' => 'ar']) }}" title="عربي">عربي</a>
          @else
            <a href="{{ route('changeLang', ['lang' => 'en']) }}" title="English">English</a>
          @endif
        </div>
                    <a href="{{ url('/')}}"><span class="logo-single"></span></a>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <h6 class="mb-4">{{ __('Reset Password') }}</h6>
                        <form method="POST" action="{{ route('password.update') }}" id="form">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">
                            <label for="email"  class="form-group has-float-label mb-4">
                                <input  id="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ $email ?? old('email') }}" autofocus disabled="" />
                                <span>{{ __('E-Mail Address') }}</span>
                            </label>

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <label for="password" class="form-group has-float-label mb-4">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password"/>
                                <span>{{ __('Password') }}</span>
                            </label>
                            @error('password')
                                <p class="invalid-feedback" role="alert">{{ $message }}</p>
                            @enderror
                

                            <label for="password-confirm" class="form-group has-float-label mb-4">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password" />
                                <span>{{ __('Confirm Password') }}</span>
                            </label>


                            <div class="d-flex justify-content-end align-items-center">
                                <button class="btn btn-primary btn-lg btn-shadow login_button" type="submit">{{ __('Reset Password') }}</button>
                            </div>
                        </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
  {!! JsValidator::formRequest('App\Http\Requests\ResetPasswordRequest', '#form') !!}
@endsection
