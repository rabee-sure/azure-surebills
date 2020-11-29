@extends('layouts.app')
@section('title', __('Settings'))

@section('css_styles')
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

  <div class="row">
    <div class="col-12">
      <h1>{{ __('Settings') }}</h1>
      <div class="separator mb-5"></div>
    </div>
    <div class="col-12">
      <div class="create_bill_page card mb-4">
        @if($user->settings)
        <div class="card-body" >
          <form method="POST" action="{{ route('post.settings') }}" class="repeater" id="settings">
            @csrf
              <h1 class="mb-3">{{ __('Taxs') }}</h1>
              <div class=" form-row mb-2">
                <div class="form-group col-6">
                  <label for="inputEmail1">{{ __('Add Tax') }}</label>
                  <div class="custom-switch custom-switch-primary mb-2">
                    <input name="add_tax" class="custom-switch-input" id="Tax_Values_Checkbox" type="checkbox" @if($user->settings->add_tax ?? null) checked @endif>
                    <label class="custom-switch-btn" for="Tax_Values_Checkbox"></label>
                  </div>
                </div><!-- form-group -->

                    <div class="form-group col-6 col-md-6 col-lg-6 col-xl-6 Tax_Values">
                      <label for="Tax">{{ __('Tax Value') }} (%)</label>
                      <input  value="{{ $user->settings->tax_value }}" type="tel" name="tax_value" class="form-control _parseArabicNumbers" id="Value">
                    </div><!-- form-group -->
              </div><!-- form-row -->

              <hr>
              <h1 class="mb-3">{{ __('Default Language for Bills') }}</h1>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label>{{ __('Active Langs') }}</label>

                  <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                      <!-- Default checked -->
                      <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="active_lang_ar" class="custom-control-input" id="arabic" @if($user->settings->active_lang  == 'ar'|| $user->settings->active_lang  == 'all') checked @endif >
                        <label class="custom-control-label" for="arabic">{{ __('Arabic') }}</label>
                      </div>
                    </li>
                    <li class="list-group-item">
                      <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="active_lang_en" class="custom-control-input" id="english" @if($user->settings->active_lang  == 'en'|| $user->settings->active_lang  == 'all') checked @endif>
                        <label class="custom-control-label" for="english">{{ __('English') }}</label>
                      </div>
                    </li>
                  </ul>
                </div><!-- form-group -->
                <div class="form-group col-md-6" id="default_lang">
                  <label>{{ __('Default Lang') }}</label>
                  <select name="default_lang" class="form-control">
                    <option value="ar" @if($user->settings->default_lang  == 'ar')selected="selected" @endif>{{ __('Arabic') }}</option>
                    <option value="en" @if($user->settings->default_lang  == 'en')selected="selected" @endif>{{ __('English') }}</option>
                  </select>
                </div><!-- form-group -->
              </div><!-- form-row -->

              <hr>
              <h1 class="mb-3">{{ __('bills header and footer') }} ( {{ __('optional') }} )</h1>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label>{{ __('Header ar') }}</label>
                  <textarea class="form-control" name="header_bill_ar" id="exampleFormControlTextarea1" rows="1">
                    {{ $user->settings->getTranslation('header_bill', 'ar') }}
                  </textarea>
                </div><!-- form-group -->
                <div class="form-group col-md-6" >
                  <label>{{ __('Header en') }}</label>
                  <textarea class="form-control" name="header_bill_en" id="exampleFormControlTextarea1" rows="1">
                    {{ $user->settings->getTranslation('header_bill', 'en') }}
                  </textarea>
                </div><!-- form-group -->
              </div><!-- form-row -->
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label>{{ __('Footer ar') }}</label>
                  <textarea class="form-control" name="footer_bill_ar" id="exampleFormControlTextarea1" rows="1">
                    {{ $user->settings->getTranslation('footer_bill', 'ar') }}
                  </textarea>
                </div><!-- form-group -->
                <div class="form-group col-md-6" >
                  <label>{{ __('Footer en') }}</label>
                  <textarea class="form-control" name="footer_bill_en" id="exampleFormControlTextarea1" rows="1">
                    {{ $user->settings->getTranslation('footer_bill', 'en') }}
                  </textarea>
                </div><!-- form-group -->
              </div><!-- form-row -->

              <hr>
              <h1 class="mb-3">{{ __('When Bill Created') }} ( {{ __('Default settings') }} )</h1>
              <div class="form-row">
                <div class="form-group col-6">
                  <label for="create_send_sms">{{ __('Send a text message to the customer') }}</label>
                  <div class="custom-switch custom-switch-primary mb-2">
                    <input name="create_send_sms" class="custom-switch-input" id="create_send_sms" type="checkbox" @if($user->settings->create_send_sms) checked @endif>
                    <label class="custom-switch-btn" for="create_send_sms"></label>
                  </div>
                </div><!-- form-group -->
                <div class="form-group col-6">
                  <label for="create_send_email">{{ __('Send an email to the customer') }}</label>
                  <div class="custom-switch custom-switch-primary mb-2">
                    <input name="create_send_email" class="custom-switch-input" id="create_send_email" type="checkbox"
                    @if($user->settings->create_send_email) checked @endif>
                    <label class="custom-switch-btn" for="create_send_email"></label>
                  </div>
                </div><!-- form-group -->
              </div><!-- form-row -->

            <h1 class="mb-3">{{ __('When Bill Paid') }}</h1>
            <div class="form-row">
              <div class="form-group col-6">
                <label for="paid_send_sms">{{ __('Send me a text message') }}</label>
                <div class="custom-switch custom-switch-primary mb-2">
                  <input name="paid_send_sms" class="custom-switch-input" id="paid_send_sms" type="checkbox"
                  @if($user->settings->paid_send_sms) checked @endif>
                  <label class="custom-switch-btn" for="paid_send_sms"></label>
                </div>
              </div><!-- form-group -->
              <div class="form-group col-6">
                <label for="paid_send_sms">{{ __('Send an email to me') }}</label>
                <div class="custom-switch custom-switch-primary mb-2">
                  <input name="paid_send_email" class="custom-switch-input" id="paid_send_email" type="checkbox"
                  @if($user->settings->paid_send_email) checked @endif>
                  <label class="custom-switch-btn" for="paid_send_email"></label>
              </div><!-- form-group -->
            </div><!-- form-row -->
            <div class="d-flex justify-content-start mt-3">
              <button type="submit" class="btn btn-primary btn-lg login_button"> {{__('Save')}}</button>
            </div><!-- d-flex  -->
          </form>
        </div>
        @endif
      </div>
    </div>
  </div>
@endsection

@push('footer-scripts')
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
    $(document).ready(function () {
      if($("#Tax_Values_Checkbox").is(':checked')){
        $(".Tax_Values").show();
      }else{
        $(".Tax_Values").hide();  // To hide
      }
      $('#Tax_Values_Checkbox').change(function() {
        $('.Tax_Values').toggle();
      });

      $('#arabic, #english').click(function() {
        toggleLangSelector()
      });
      toggleLangSelector()
   });
    function toggleLangSelector() {
      if($("#arabic").is(':checked') && $("#english").is(':checked')){
        $("#default_lang").show();
      }else{
        $("#default_lang").hide();  // To hide
      }
    }
  </script>
    {!! JsValidator::formRequest('App\Http\Requests\SettingsRequest', '#settings') !!}
@endpush
