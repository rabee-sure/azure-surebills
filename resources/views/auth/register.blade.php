@extends('layouts.auth')
@section('title', __('Register') )
@section('content')
<div class="row h-100">
  <div class="col-12 col-md-10 mx-auto my-auto">
    <div id="register_page" class="card auth-card">
      <div class="position-relative image-side">
        <div class="changeLang">
          @if(App::isLocale('en'))
            <a  href="{{ route('changeLang', ['lang' => 'ar']) }}" title="عربي">عربي</a>
          @else
            <a href="{{ route('changeLang', ['lang' => 'en']) }}" title="English">English</a>
          @endif
        </div>
        <p class="text-black h2">{{ __('Register a new account') }}</p>
        <p class="black mb-0">
        {{ __('Please use this form to register.') }}
        <br>
        <p>{{ __('If you are a member, please') }} <a href="{{ route('login') }}" title="{{ __('Login') }}" class="black">{{ __('Login') }}</a>.</p>
        <div class="slide_auth">
          <div class="glide single">
            <div class="glide__track" data-glide-el="track">
              <div class="glide__slides">
                <div class="glide__slide"><img src="{{ asset('images/login_slide_1.png') }}" alt="login_slide_1"></div>
                <div class="glide__slide"><img src="{{ asset('images/login_slide_2.png') }}" alt="login_slide_2"></div>
                <div class="glide__slide"><img src="{{ asset('images/login_slide_3.png') }}" alt="login_slide_3"></div>
              </div><!-- glide__slides -->
            </div><!-- glide__track -->
          </div><!-- glide -->
        </div><!-- slide_auth -->
      </div>
      <div class="form-side">
        <a href="{{ url('/') }}"><span class="logo-single"></span></a>
        <h6 class="mb-4">{{ __('Register') }}</h6>
        <form method="POST" action="{{ route('register') }}" id="register-form">
          @csrf
          <div class="row">
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
              <label class="form-group has-float-label mb-4">
                <input id="business_name_en" type="text" class="form-control @error('business_name_en') is-invalid @enderror" name="business_name_en" value="{{ old('business_name_en') }}" autocomplete="business_name_en" autofocus />
                <span>{{ __('Business Name') }}</span>
                @error('business_name_en')
                  <p class="invalid-feedback" role="alert">{{ $message }}</p>
                @enderror
              </label>
            </div><!-- col-12 -->
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
              <label for="name" class="form-group has-float-label mb-4">
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" autocomplete="name" autofocus/>
                <span>{{ __('Full Name') }}</span>
                @error('name')
                  <p class="invalid-feedback" role="alert">{{ $message }}</p>
                @enderror
              </label>
            </div><!-- col-12 -->
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
              <label for="email" class="form-group has-float-label mb-4">
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="email" />
                <span>{{ __('E-Mail Address') }}</span>
                @error('email')
                  <p class="invalid-feedback" role="alert">{{ $message }}</p>
                @enderror
              </label>
            </div><!-- col-12 -->
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
              <div class="phone_reg">
                <div class="phone_key">+966</div>
                <label class="form-group has-float-label mb-4">
                  <input name="mobile" class="form-control _parseArabicNumbers @error('mobile') is-invalid @enderror" name="mobile" id="mobile" type="tel" value="{{ old('mobile') }}" />
                  <span>{{ __('Mobile Number') }}</span>
                  @error('mobile')
                      <p class="invalid-feedback" role="alert">{{ $message }}</p>
                  @enderror
                </label>
              </div><!-- phone_reg -->
            </div><!-- col-12 -->
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
              <label for="password" class="form-group has-float-label mb-4">
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password"/>
                <span>{{ __('Password') }}</span>
                @error('password')
                  <p class="invalid-feedback" role="alert">{{ $message }}</p>
                @enderror
              </label>
            </div><!-- col-12 --> 
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
              <label for="password-confirm" class="form-group has-float-label mb-4">
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password" />
                <span>{{ __('Confirm Password') }}</span>
              </label>
            </div><!-- col-12 -->
          </div><!-- row -->
          <div class="custom-control custom-checkbox mb-4">
            <input type="checkbox" class="custom-control-input  @error('terms') is-invalid @enderror" name="terms" value="1" id="customCheckThis">
            <label class="custom-control-label" for="customCheckThis">
              {{ __('I agree to') }} 
            </label>
              <a id="read_terms" style="line-height: 1.5rem; padding-top: 1px;" href="#" title="{{ __('Terms & Conditions') }}"  data-toggle="modal" data-target="#conditionsModal">{{ __('Terms & Conditions') }}</a>
            @error('terms')
              <p class="invalid-feedback" role="alert">{{ $message }}</p>
            @enderror
          </div>
          <div class="d-flex justify-content-end align-items-center">
            <button class="btn btn-primary btn-lg btn-shadow login_button" type="submit">{{ __('Register') }}</button>
          </div>
        </form>
        <hr>
        <h5 class="mt-4 mb-0 text-center">لديك حساب في شور بيلز ! <br> <a style="color: #00D595;" class="d-inline-block mt-2" href="{{ route('login') }}" title="سجل دخول"> سجل دخول</a></h5>

        <!-- modal -->
        <div class="modal fade bd-example-modal-lg" id="conditionsModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">{{ __('Terms & Conditions') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <p>سوف يتم تحويل المبالغ لحساب بنكي باسم منشأتك فقط في حال كنت تستخدم سجل تجاري ، ولحسابك الشخصي المسجل في وثيقة العمل الحر في حال كنت تستخدم وثيقة عمل حر .
                تأكيد من تحميلك للسجل التجاري او وثيقة العمل الحر لتوثيق حسابك والبدء بإستقبال المدفوعات.
                يرجى الافصاح إذا كان نشاطك التجاري يتطلب ترخيص من جهة غير وزارة التجارة.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- modal -->

    </div>
    <div class="copyrights_auth">
      صُنع بـ <i class="heart"></i> في <i class="ksa"></i>
    </div><!-- copyrights_auth -->
  </div>
</div>
@endsection

@push('footer-scripts')
  {!! JsValidator::formRequest('App\Http\Requests\RegisterRequest', '#register-form') !!}
@endpush
