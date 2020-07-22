@extends('layouts.auth')
@section('title', __('Reset Password') )
@section('content')
<div class="row h-100">
  <div class="col-12 col-md-10 mx-auto my-auto">
    <div id="resset_email_page" class="card auth-card">
      <div class="position-relative image-side ">
        <p class=" text-white h2">{{ __('Reset Password') }}</p>
        <p class="white mb-0">
          {{ __('Please use your e-mail to reset your password.') }}
          <br>
          {{ __('If you are a member, please') }} <a href="{{ route('login') }}" title="{{ __('Login') }}" class="white">{{ __('Login') }}</a>.
        </p>
      </div>


      <div class="form-side">
        <div class="changeLang">
          @if(App::isLocale('en'))
            <a  href="{{ route('changeLang', ['lang' => 'ar']) }}" title="عربي">عربي</a>
          @else
            <a href="{{ route('changeLang', ['lang' => 'en']) }}" title="English">English</a>
          @endif
        </div>
        <a href="{{ url('/') }}"><span class="logo-single"></span></a>
        <h6 class="mb-4">{{ __('Reset Password') }}</h6>
        @if (session('status'))
          <div class="alert alert-success" role="alert">{{ session('status') }}</div>
        @endif
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('password.email') }}" id="form">
          @csrf
          <label  for="email" class="form-group has-float-label mb-4">
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus/>
            <span>{{ __('E-Mail Address') }}</span>
          </label>
          @error('email')
            <p class="invalid-feedback" role="alert">{{ $message }}</p>
          @enderror
          <div class="d-flex justify-content-end align-items-center">
            <button class="btn btn-primary btn-lg btn-shadow login_button" type="submit">{{ __('Send Password Reset Link') }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('footer-scripts')
  {!! JsValidator::formRequest('App\Http\Requests\ForgotPasswordRequest', '#form') !!}
@endsection
