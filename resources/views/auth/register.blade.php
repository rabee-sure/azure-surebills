@extends('layouts.auth')

@section('title', __('Register') )

@section('content')
  <aside class="shadow align-self-stretch">
    <div class="changeLang d-flex align-items-center justify-content-start mb-3 mb-md-5">
      @if(App::isLocale('en'))
        <a href="{{ route('changeLang', ['lang' => 'ar']) }}" title="عربي" class="d-block">عربي</a>
      @else
        <a href="{{ route('changeLang', ['lang' => 'en']) }}" title="English">English</a>
      @endif
    </div><!-- changeLang -->
    <div class="title d-block text-body text-center mb-3 fw-bold">{{ __('Register a new account') }}</div>
    <div class="desc text-center text-body mb-3">
      {{ __('Please use this form to register.') }}
      <br>
      {{ __('If you are a member, please') }} <a href="{{ route('login') }}" title="{{ __('Login') }}">{{ __('Login') }}</a>.
    </div><!-- desc -->
    <div class="authSlider">
      <div class="item d-flex align-items-center justify-content-center">
        <img data-lazy="{{ asset('new/images/authSlideImg_1.webp') }}" alt="login_slide_1" class="mw-100">
      </div><!-- item -->
      <div class="item d-flex align-items-center justify-content-center">
        <img data-lazy="{{ asset('new/images/authSlideImg_2.webp') }}" alt="login_slide_2" class="mw-100">
      </div><!-- item -->
      <div class="item d-flex align-items-center justify-content-center">
        <img data-lazy="{{ asset('new/images/authSlideImg_3.webp') }}" alt="login_slide_2" class="mw-100">
      </div><!-- item -->
    </div><!-- authSlider -->
  </aside>
  <article class="flex-grow-1 d-flex align-items-center justify-content-center flex-column align-self-stretch">
    <div class="topArea w-100 py-4 flex-grow-1 d-flex align-items-center justify-content-center flex-column">
      <div class="logo d-flex align-items-center justify-content-center mb-3 mb-md-5">
        <a href="{{ url('/') }}" title="SureBills">
          <img src="{{ asset('new/images/logo.webp') }}" alt="SureBills" loading="lazy" width="586px" height="187px" class="mw-100 w-auto h-auto">
        </a>
      </div><!-- logo -->
      <h1 class="d-block mb-3 fw-normal text-body">{{ __('Register') }}</h1>
      <form method="POST" action="{{ route('register') }}" id="register-form" class="registerForm w-100 mx-auto">
        @csrf
        <div class="registerFields">
          <div class="row row-cols-1 row-cols-sm-1 row-cols-md-2">
            <div class="col">
              <div class="form_group mb-3">
                <div class="inputIcon d-flex align-items-center justify-content-center rounded overflow-hidden border @error('business_name_en') is-invalid @enderror">
                  <span class="d-flex align-items-center justify-content-center h-100 fal fa-user-tie"></span>
                  <input id="business_name_en" type="text" class="bg-white border-0 h-100 flex-grow-1 text-body" name="business_name_en" value="{{ old('business_name_en') }}" autocomplete="business_name_en" placeholder="{{ __('Business Name') }}" autofocus />
                </div><!-- inputIcon -->
                @error('business_name_en')
                  <div class="invalid-feedback text-danger" role="alert">{{ $message }}</div>
                @enderror
              </div><!-- form_group -->
            </div><!-- col -->
            <div class="col">
              <div class="form_group mb-3">
                <div class="inputIcon d-flex align-items-center justify-content-center rounded overflow-hidden border @error('name') is-invalid @enderror">
                  <span class="d-flex align-items-center justify-content-center h-100 fal fa-user"></span>
                  <input id="name" type="text" class="bg-white border-0 h-100 flex-grow-1 text-body" name="name" value="{{ old('name') }}" placeholder="{{ __('Full Name') }}" autocomplete="name" autofocus/>
                </div><!-- inputIcon -->
                @error('name')
                  <div class="invalid-feedback text-danger" role="alert">{{ $message }}</div>
                @enderror
              </div><!-- form_group -->
            </div><!-- col -->
            <div class="col">
              <div class="form_group mb-3">
                <div class="inputIcon d-flex align-items-center justify-content-center rounded overflow-hidden border @error('email') is-invalid @enderror">
                  <span class="d-flex align-items-center justify-content-center h-100 fal fa-envelope"></span>
                  <input id="email" type="email" inputmode="email" class="bg-white border-0 h-100 flex-grow-1 text-body" name="email" value="{{ old('email') }}" placeholder="{{ __('E-Mail Address') }}" autocomplete="email" />
                </div><!-- inputIcon -->
                @error('email')
                  <div class="invalid-feedback text-danger" role="alert">{{ $message }}</div>
                @enderror
              </div><!-- form_group -->
            </div><!-- col -->
            <div class="col">
              <div class="form_group mb-3">
                <div class="inputIcon inputPhone d-flex align-items-center justify-content-center rounded overflow-hidden border @error('mobile') is-invalid @enderror">
                  <span class="d-flex align-items-center justify-content-center h-100 fal fa-phone"></span>
                  <input name="mobile" class="bg-white border-0 h-100 flex-grow-1 text-body" name="mobile" id="mobile" type="tel" pattern="[0-9]*" maxlength="9" inputmod="numaric" placeholder="{{ __('Mobile Number') }}" value="{{ old('mobile') }}" />
                  <div class="codeNum d-flex align-items-center justify-content-center h-100 text-body bg-white">+966</div>
                </div><!-- inputIcon -->
                @error('mobile')
                  <div class="invalid-feedback text-danger" role="alert">{{ $message }}</div>
                @enderror
              </div><!-- form_group -->
            </div><!-- col -->
            <div class="col">
              <div class="form_group mb-3">
                <div class="inputIcon d-flex align-items-center justify-content-center rounded overflow-hidden border @error('password') is-invalid @enderror">
                  <span class="d-flex align-items-center justify-content-center h-100 fal fa-lock-alt"></span>
                  <input id="password" type="password" class="bg-white border-0 h-100 flex-grow-1 text-body" name="password" placeholder="{{ __('Password') }}" autocomplete="new-password"/>
                </div><!-- inputIcon -->
                @error('password')
                  <div class="invalid-feedback text-danger" role="alert">{{ $message }}</div>
                @enderror
              </div><!-- form_group -->
            </div><!-- col -->
            <div class="col">
              <div class="form_group mb-3">
                <div class="inputIcon d-flex align-items-center justify-content-center rounded overflow-hidden border @error('password') is-invalid @enderror">
                  <span class="d-flex align-items-center justify-content-center h-100 fal fa-lock-alt"></span>
                  <input id="password-confirm" type="password" class="bg-white border-0 h-100 flex-grow-1 text-body" name="password_confirmation" placeholder="{{ __('Confirm Password') }}" autocomplete="new-password"/>
                </div><!-- inputIcon -->
                @error('password')
                  <div class="invalid-feedback text-danger" role="alert">{{ $message }}</div>
                @enderror
              </div><!-- form_group -->
            </div><!-- col -->
          </div><!-- row -->
        </div><!-- registerFields -->
        <label for="remember" class="checkboxArea d-block mb-3 position-relative">
          <input type="checkbox" class="w-100 h-100 position-absolute top-0 start-0 @error('terms') is-invalid @enderror" name="terms" value="1" id="customCheckThis">
          <span class="d-flex align-items-center justify-content-start">
          {{ __('I agree to') }} <a id="read_terms" href="#" title="{{ __('Terms & Conditions') }}" data-bs-toggle="modal" data-bs-target="#conditionsModal">{{ __('Terms & Conditions') }}</a>
          </span>
          @error('terms')
            <div class="invalid-feedback text-danger" role="alert">{{ $message }}</div>
          @enderror
        </label><!-- checkboxArea -->
        <div class="d-flex justify-content-end align-items-center">
          <button class="login_button rounded border-0 fw-bold d-flex align-items-center justify-content-center text-white p-0n" type="submit">{{ __('Register') }}</button>
        </div><!-- d-flex -->
      </form>
    </div><!-- topArea -->
  </article>
  <!-- Conditions Modal -->
  <div class="modal fade" id="conditionsModal" tabindex="-1" aria-labelledby="conditionsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header d-flex align-items-center justify-content-between">
          <span class="d-block fw-bold text-body">{{ __('Terms & Conditions') }}</span>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div><!-- modal-header -->
        <div class="modal-body">
          <p class="d-block m-0">
            سوف يتم تحويل المبالغ لحساب بنكي باسم منشأتك فقط في حال كنت تستخدم سجل تجاري ، ولحسابك الشخصي المسجل في وثيقة العمل الحر في حال كنت تستخدم وثيقة عمل حر .
            <br>
            تأكيد من تحميلك للسجل التجاري او وثيقة العمل الحر لتوثيق حسابك والبدء بإستقبال المدفوعات.
            <br>
            يرجى الافصاح إذا كان نشاطك التجاري يتطلب ترخيص من جهة غير وزارة التجارة.
          </p>
        </div><!-- modal-body -->
      </div><!-- modal-content -->
    </div><!-- modal-dialog -->
  </div><!-- modal -->
  <!-- Conditions Modal -->
@endsection

@push('footer-scripts')
  {!! JsValidator::formRequest('App\Http\Requests\ForgotPasswordRequest', '#form') !!}
@endpush
