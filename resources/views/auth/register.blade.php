@extends('layouts.auth')
@section('title', __('ٌegister') )
@section('content')
<div class="row h-100">
  <div class="col-12 col-md-10 mx-auto my-auto">
    <div id="register_page" class="card auth-card">
      <div class="position-relative image-side ">
        <p class=" text-white h2">{{ __('Start Sending Bills') }}</p>
        <p class="white mb-0">
        {{ __('Please use this form to register.') }}
        <br>
        {{ __('If you are a member, please') }} <a href="{{ route('login') }}" class="white">{{ __('login') }}</a>.</p>
      </div>
      <div class="form-side">
        <a href="{{ url('/') }}"><span class="logo-single"></span></a>
        <h6 class="mb-4">{{ __('Register') }}</h6>
        <form method="POST" action="{{ route('register') }}" id="register-form">
          @csrf
          <div class="row">
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
              <label class="form-group has-float-label mb-4">
                <input id="business_name" type="text" class="form-control @error('business_name') is-invalid @enderror" name="business_name" value="{{ old('business_name') }}" autocomplete="business_name" autofocus />
                <span>{{ __('Business Name') }}</span>
                @error('business_name')
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
              <label class="form-group has-float-label mb-4">
                <input name="mobile" class="form-control _parseArabicNumbers @error('mobile') is-invalid @enderror" name="mobile" id="mobile" type="tel" value="{{ old('mobile') }}" />
                <span>{{ __('Mobile Number') }}</span>
                @error('mobile')
                    <p class="invalid-feedback" role="alert">{{ $message }}</p>
                @enderror
              </label>
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
              {{ __('I agree to') }} <a href="#" title="Terms & Conditions"  data-toggle="modal" data-target=".bd-example-modal-lg">{{ __('Terms & Conditions') }}</a>
            </label>
            @error('terms')
              <p class="invalid-feedback" role="alert">{{ $message }}</p>
            @enderror
          </div>
          <div class="d-flex justify-content-end align-items-center">
            <button class="btn btn-primary btn-lg btn-shadow" type="submit">{{ __('Register') }}</button>
          </div>
        </form>
        <hr>
        <a class="btn btn-lg btn-shadow login_now" href="{{ route('login') }}" title="{{ __('login') }}">{{ __('login') }}</a>


        <!-- modal -->
        <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Terms & Conditions</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- modal -->

    </div>
  </div>
</div>
@endsection

@section('footer-scripts')
    {!! JsValidator::formRequest('App\Http\Requests\RegisterRequest', '#register-form') !!}

    <script>
    $('input[type="email"]').keypress(function (e) {
      var regex = new RegExp("^[A-Za-z0-9\s!@#$%^&*()_+=-`~\\\]\[{}|';:/.,?><]*$");
      var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
      if (regex.test(str)) {
        return true;
      }
      e.preventDefault();
      return false;
    });
    String.prototype.replaceArray = function(find, replace) {
      var replaceString = this;
      var regex;
      for (var i = 0; i < find.length; i++) {
        regex = new RegExp(find[i], "g");
        replaceString = replaceString.replace(regex, replace[i]);
      }
      return replaceString;
    };
    String.prototype.toArabicDigits = function() {
      var find = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩',' ','-','/','|','~','٫','@'];
      var replace = ['0','1','2','3','4','5','6','7','8','9','','','','','','.','@'];
      return this.replaceArray(find, replace);
    }
    String.prototype.toArabic = function() {
      var find = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩','@'];
      var replace = ['0','1','2','3','4','5','6','7','8','9','@'];
      return this.replaceArray(find, replace);
    }
    function parseArabicNumbers(id, dot) {
      document.getElementById(id).value = document.getElementById(id).value.toArabicDigits();
      var new_val = document.getElementById(id).value.toArabicDigits();
      if (new_val >= 0) {
        if (new_val != 0) {
          if (dot == 1) {
            document.getElementById(id).value = Number(new_val).toString();
            document.getElementById(id).value = document.getElementById(id).value.replace(/^0+/, '');
          } else {
            document.getElementById(id).value = new_val;
            document.getElementById(id).value = document.getElementById(id).value;
            //.replace(/^0+/, '');
          }
        } else {
          document.getElementById(id).value = new_val;
        }
      } else {
        document.getElementById(id).value = "";
      }
      return true;
    }
    function parseArabic(id) {
      document.getElementById(id).value = document.getElementById(id).value.toArabic();
      var new_val = document.getElementById(id).value.toArabic();
      if (new_val >= 0) {
        document.getElementById(id).value = new_val;
      }
      return true;
    }
    $(document).on('keyup', 'input._parseArabicNumbers', function() {
      parseArabicNumbers($(this).attr('id'))
    });
    $(document).on('keyup', 'input._parseQuantityArabicNumbers', function() {
      parseArabicNumbers($(this).attr('id'), 1)
    });
    $(document).on('keyup', 'input._parseArabic', function() {
      parseArabic($(this).attr('id'))
    });
    function replaceToArabicNumber(str_input) {
      var replace = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩','@'];
      var find = ['0','1','2','3','4','5','6','7','8','9','@'];
      for (var i = 0; i < find.length; i++) {
        str_input = str_input.replace(find[i], replace[i]);
      }
      return str_input;
    }
  </script>
@endsection