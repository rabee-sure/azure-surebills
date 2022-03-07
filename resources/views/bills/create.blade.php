@extends('layouts.app')

@section('title', __('Create a bill'))

@section('css_styles')
  <link rel="stylesheet" href="{{ asset('new/css/plugins/jquery-ui/jquery-ui.css') }}?v={{ config('app.asset_version') }}">
@endsection

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm border-bottom">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <a href="/bills" title="{{ __('Bills') }}">{{ __('Bills') }}</a>
    <i>/</i>
    <span>{{ __('Create a bill')}}</span>
  </div><!-- breadcrump -->

  <section id="billCreatePage">
    <div class="title mb-4">
      <h1 class="d-block fw-bold m-0">{{ __('Create a bill') }}</h1>
    </div><!-- title -->

    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="m-0 p-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div><!-- alert -->
    @endif

    <div class="block bg-white border shadow-sm rounded-3">
      <form method="POST" action="{{ route('bills.store') }}" class="repeater" id="bill_create">
        @csrf
        <div class="row">
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="customer_name" class="d-block mb-1">{{ __('Customer Name') }} <span class="requirement text-danger">*</span></label>
              <input value="{{ old('customer_name') }}" name="customer_name" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="customer_name" placeholder="{{ __('Customer Name') }} *">
            </div><!-- form-group -->
          </div><!-- col -->
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="customer_mobile" class="d-block mb-1">{{ __('Mobile Number') }} <span class="requirement text-danger">*</span></label>
              <div class="phoneInput overflow-hidden position-relative">
                <span class="d-flex align-items-center justify-content-center position-absolute rounded-3">+966</span>
                <input value="{{ old('customer_mobile') }}" name="customer_mobile" type="tel" class="form-control shadow-none bg-white border w-100 rounded-3 text-body @error('customer_mobile') is-invalid @enderror" id="customer_mobile" inputmode="numeric" placeholder="5XXXXXXXX" maxlength="9">
              </div><!-- phoneInput -->
              @error('customer_mobile')
                <p class="invalid-feedback" role="alert">{{ $message }}</p>
              @enderror
            </div><!-- mb-3 -->
          </div><!-- col -->
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="customer_email" class="d-block mb-1">{{ __('Email') }}</label>
              <input value="{{ old('customer_email') }}" name="customer_email" type="email" class="form-control shadow-none bg-white border w-100 rounded-3 text-body @error('customer_email') is-invalid @enderror" id="customer_email" inputmode="email" placeholder="{{ __('Email') }}">
              @error('customer_email')
                <p class="invalid-feedback" role="alert">{{ $message }}</p>
              @enderror
            </div><!-- form-group -->
          </div><!-- col -->
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="customer_notes" class="d-block mb-1">{{ __('Special Note') }}</label>
              <input value="{{ old('customer_notes') }}" name="customer_notes" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="customer_notes" placeholder="{{ __('Special Note') }}">
            </div><!-- form-group -->
          </div><!-- col -->
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="due_date" class="d-block mb-1">{{ __('Due Date') }}</label>
              <input value="{{ Carbon\Carbon::now()->format('m/d/Y') }}" name="due_date" id="due_date" class="form-control shadow-none bg-white border w-100 rounded-3 text-body datepicker" placeholder="{{ __('Due Date') }}">
            </div><!-- form-group -->
          </div><!-- col -->
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="expiry_date" class="d-block mb-1">{{ __('Expiry Date') }}</label>
              <select value="{{ old('expiry_date') }}" name="expiry_date" id="expiry_date" class="form-control shadow-none bg-white border w-100 rounded-3 text-body">
                <option value="1">{{ __('1 Day') }}</option>
                <option value="2">{{ __('2 Day') }}</option>
                <option value="3">{{ __('3 Day') }}</option>
                <option value="7">{{ __('7 Day') }}</option>
                <option value="30" selected="selected">{{ __('30 Day') }}</option>
                <option value="60">{{ __('60 Day') }}</option>
                <option value="90">{{ __('90 Day') }}</option>
                <option value="0">{{ __('Never') }}</option>
              </select>
            </div><!-- form-group -->
          </div><!-- col -->
        </div><!-- row -->
        @if(Auth::user()->settings->add_tax_invoice)
          <button type="button" class="additionalInformationBtn border-0 d-flex align-items-center justify-content-start bg-transparent p-0">{{__('Additional Information')}}</button>
          <div class="additionalInformationArea">
            <div class="pt-3">
              <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                  <div class="form-group mb-3">
                    <label for="bullding_no" class="d-block mb-1">{{__('bullding_no')}}</label>
                    <input value="{{ old('bullding_no') }}" name="bullding_no" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="bullding_no" placeholder="{{__('bullding_no')}}">
                  </div><!-- form-group -->
                </div><!-- col -->
                <div class="col-12 col-md-6 col-lg-4">
                  <div class="form-group mb-3">
                    <label for="street_name" class="d-block mb-1">{{__('street_name')}}</label>
                    <input value="{{ old('street_name') }}" name="street_name" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="street_name" placeholder="{{__('street_name')}}">
                  </div><!-- form-group -->
                </div><!-- col -->
                <div class="col-12 col-md-6 col-lg-4">
                  <div class="form-group mb-3">
                    <label for="district" class="d-block mb-1">{{__('district')}}</label>
                    <input value="{{ old('district') }}" name="district" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="district" placeholder="{{__('district')}}">
                  </div><!-- form-group -->
                </div><!-- col -->
                <div class="col-12 col-md-6 col-lg-4">
                  <div class="form-group mb-3">
                    <label for="city" class="d-block mb-1">{{__('city')}}</label>
                    <input value="{{ old('city') }}" name="city" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="city" placeholder="{{__('city')}}">
                  </div><!-- form-group -->
                </div><!-- col -->
                <div class="col-12 col-md-6 col-lg-4">
                  <div class="form-group mb-3">
                    <label for="postal_code" class="d-block mb-1">{{__('postal_code')}}</label>
                    <input value="{{ old('postal_code') }}" name="postal_code" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="postal_code" placeholder="{{__('postal_code')}}">
                  </div><!-- form-group -->
                </div><!-- col -->
                <div class="col-12 col-md-6 col-lg-4">
                  <div class="form-group mb-3">
                    <label for="additional_no" class="d-block mb-1">{{__('additional_no')}}</label>
                    <input value="{{ old('additional_no') }}"  name="additional_no" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="additional_no" placeholder="{{__('additional_no')}}">
                  </div><!-- form-group -->
                </div><!-- col -->
                <div class="col-12 col-md-6 col-lg-4">
                  <div class="form-group mb-3">
                    <label for="other_buyer_id" class="d-block mb-1">{{__('other_buyer_id')}}</label>
                    <input value="{{ old('other_buyer_id') }}"  name="other_buyer_id" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="other_buyer_id" placeholder="{{__('other_buyer_id')}}">
                  </div><!-- form-group -->
                </div><!-- col -->
                <div class="col-12 col-md-6 col-lg-4">
                  <div class="form-group mb-3">
                    <label for="vat_registration_number" class="d-block mb-1">{{__('vat_registration_number')}}</label>
                    <input value="{{ old('vat_registration_number') }}"  name="vat_registration_number" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="vat_registration_number" placeholder="{{__('vat_registration_number')}}">
                  </div><!-- form-group -->
                </div><!-- col -->
              </div><!-- row -->
            </div><!-- pt-3 -->
          </div><!-- additionalInformationArea -->
        @endif
        <hr>
        <div class="title2 fw-bold mb-4">{{ __('Bill items') }}</div>
              
          
              <div class="inner-repeater">
                <div data-repeater-list="items">
                  @if(old('items'))
                    @foreach( old('items') as $item)
                      <div data-repeater-item>
                        <div class="form-row mb-2 item_row">
                          <div class="form-group col-12 col-md-4 col-lg-6 col-xl-6">
                            <label for="inputEmail1">{{ __('Product/Service') }} <span class="requirement">*</span></label>
                            <input name="name" value="{{$item['name']}}" type="text" class="form-control product_name" placeholder="{{ __('Name') }}">
                          </div><!-- form-group -->
                          <div class="form-group col-6 col-md-2 col-lg-2 col-xl-2">
                            <label for="Price">{{ __('Product/Service Price') }} <span class="requirement">*</span></label>
                            <input name="price"  value="{{$item['price']}}" min="1" type="number" class="form-control _parseArabicNumbers qty1 product_price" placeholder="{{ __('Price') }}">
                          </div><!-- form-group -->
                          <div class="form-group col-6 col-md-2 col-lg-2 col-xl-2">
                            <label for="Price">{{ __('Quantity') }} <span class="requirement">*</span></label>
                            <input type="number" name="quantity" value="{{$item['quantity']}}" min="1" class="form-control _parseArabicNumbers qty1 product_quantity" placeholder="{{ __('Quantity') }}">
                          </div><!-- form-group -->
                          <div class="form-group col-6 col-md-1 col-lg-1 col-xl-1">
                            <label for="Price">{{ __('Total') }}</label>
                            <input type="number" name="total" value="{{ $item['price']* $item['quantity']}}" class="form-control _parseArabicNumbers text-center font-weight-bold" disabled>
                          </div><!-- form-group -->
                          <div class="form-group col-6 col-md-1 col-lg-1 col-xl-1 delete_block">
                            <label for="Delete" class="d-block">{{ __('Delete') }}</label>
                            <input data-repeater-delete type="button" class="btn btn-danger default d-block w-100" value="X"/>
                          </div><!-- form-group -->
                        </div><!-- form-row -->
                      </div><!-- inner-list-->
                    @endforeach
                  @else

                  <div data-repeater-item>
                    <div class="form-row mb-2 item_row">
                      <div class="form-group col-12 col-md-4 col-lg-6 col-xl-6">
                        <label for="inputEmail1">{{ __('Product/Service') }} <span class="requirement">*</span></label>
                        <input name="name" type="text" class="form-control product_name" placeholder="{{ __('Name') }}">
                      </div><!-- form-group -->
                      <div class="form-group col-6 col-md-2 col-lg-2 col-xl-2">
                        <label for="Price">{{ __('Product/Service Price') }} <span class="requirement">*</span></label>
                        <input type="number" name="price" min="1" class="form-control _parseArabicNumbers qty1 product_price" placeholder="{{ __('Price') }}">
                      </div><!-- form-group -->
                      <div class="form-group col-6 col-md-2 col-lg-2 col-xl-2">
                        <label for="Price">{{ __('Quantity') }} <span class="requirement">*</span></label>
                        <input type="number" name="quantity" min="1" class="form-control _parseArabicNumbers qty1 product_quantity" placeholder="{{ __('Quantity') }}">
                      </div><!-- form-group -->
                      <div class="form-group col-6 col-md-1 col-lg-1 col-xl-1">
                        <label for="Price">{{ __('Total') }}</label>
                        <input name="total" type="number" class="form-control _parseArabicNumbers text-center font-weight-bold" disabled>
                      </div><!-- form-group -->
                      <div class="form-group col-6 col-md-1 col-lg-1 col-xl-1 delete_block">
                        <label for="Delete" class="d-block">{{ __('Delete') }}</label>
                      <input data-repeater-delete type="button" class="btn btn-danger default d-block w-100 text-center" value="X"/>
                      </div><!-- form-group -->
                    </div><!-- form-row -->
                  </div><!-- inner-list-->
                  @endif
                </div><!-- form-row -->
              </div><!-- inner-repeater -->
              <div class="d-flex justify-content-end my-3">
                <input data-repeater-create type="button" class="btn btn-primary btn-lg add_new_item" value="{{ __('Add Item') }}">
              </div><!-- d-flex  -->
              <hr>
              <h1 class="mb-3">{{ __('Additonal Details') }}</h1>
              <div class="form-row">
                <div class="form-group col-6">
                  <label for="inputEmail1">{{ __('Add Discount') }}</label>
                  <div class="custom-switch custom-switch-primary mb-2">
                    <input name="add_discount" class="custom-switch-input" id="Discount_Values_Checkbox" type="checkbox" @if(old('add_discount')) checked @endif>
                    <label class="custom-switch-btn" for="Discount_Values_Checkbox"></label>
                  </div>
                </div><!-- form-group -->
                <div class="form-group col-6">
                  <label for="inputEmail1">{{ __('Add Tax') }}</label>
                  <div class="custom-switch custom-switch-primary mb-2">
                  <input name="add_tax" class="custom-switch-input" id="Tax_Values_Checkbox" type="checkbox">
                    <label class="custom-switch-btn" for="Tax_Values_Checkbox"></label>
                  </div>
                </div><!-- form-group -->
              </div><!-- form-row -->
              <div class="row">
                <div class="col-12 col-md-6 col-lg-6 col-xl-6">
                  <div class="Discount_Values form-row mb-2" style="display: none;">
                    <div class="form-group col-6 col-md-6 col-lg-6 col-xl-6">
                      <label for="type">{{ __('Discount type') }}</label>
                      <select name="discount_type" id="discount_type" class="form-control">
                        <option value="fixed" @if(old('discount_type') == 'fixed') selected @endif> {{ __('fixed') }}</option>
                        <option value="percentage" @if(old('discount_type') == 'percentage') selected @endif>{{ __('Percentage Discount (%)') }}</option>
                      </select>
                    </div><!-- form-group -->
                    <div class="form-group col-6 col-md-6 col-lg-6 col-xl-6">
                      <label for="Price">{{ __('Discount Value') }}</label>
                      <div class="input-group">
                      <input type="tel" name="discount_value" class="form-control _parseArabicNumbers" value="{{old('discount_value')}}" id="Discount_Value" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                          <span class="input-group-text discount_type_item" id="fixed">{{ __('SAR') }}</span>
                          <span class="input-group-text discount_type_item" id="percentage" style="display:none">%</span>
                        </div>
                      </div>
                    </div><!-- form-group -->
                  </div><!-- form-row -->
                </div><!-- col-12 -->
                <div class="col-12 col-md-6 col-lg-6 col-xl-6">
                  <div class="Tax_Values form-row mb-2" style="display: none;">
                    <div class="form-group col-12 col-md-12 col-lg-12 col-xl-12">
                      <label for="Tax">{{ __('Tax Value') }}</label>
                      <div class="input-group">
                      <input type="tel" name="tax_value" class="form-control _parseArabicNumbers" id="Value" value="@if(auth()->user()->settings->add_tax){{auth()->user()->settings->tax_value}}@else{{old('tax_value')}}@endif" aria-describedby="basic-addon3">
                        <div class="input-group-append">
                          <span class="input-group-text discount_type_item2" id="percentage">%</span>
                        </div>
                      </div>
                    </div><!-- form-group -->
                  </div><!-- form-row -->
                </div><!-- col-12 -->
              </div><!-- row -->
              <hr>
              <h1 class="mb-3">{{ __('Send The Bill To Customer') }}</h1>
              <div class="form-row">
                <div class="form-group col-6">
                  <label for="send_sms">{{ __('Send SMS') }}</label>
                  <div class="custom-switch custom-switch-primary mb-2">
                    <input name="send_sms" class="custom-switch-input" id="send_sms" type="checkbox"
                    @if(auth()->user()->settings->create_send_sms || old('send_sms')) checked @endif>
                    <label class="custom-switch-btn" for="send_sms"></label>
                  </div>
                </div><!-- form-group -->
                <div class="form-group col-6">
                  <label for="send_email">{{ __('Send Email') }}</label>
                  <div class="custom-switch custom-switch-primary mb-2">
                    <input name="send_email" class="custom-switch-input" id="send_email" type="checkbox"
                    @if(auth()->user()->settings->create_send_email || old('send_email')) checked @endif>
                    <label class="custom-switch-btn" for="send_email"></label>
                  </div>
                </div><!-- form-group -->
              </div><!-- form-row -->
              <div class="d-flex justify-content-start mt-3">
                <button id="create-bill" type="submit" class="btn btn-primary btn-lg login_button"> {{__('Send')}}</button>
              </div><!-- d-flex  -->
            </form>
    </div><!-- block -->
  </section><!-- billCreatePage -->

@endsection

@push('footer-scripts')
  <script src="{{ asset('new/js/jquery-ui/jquery-ui.js') }}?v={{ config('app.asset_version') }}" defer></script>
  <script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
  <script>
    // Additional Information
    $(".additionalInformationArea").hide();
    $("button.additionalInformationBtn").click(function(){
      $(this).toggleClass("show");
      $(".additionalInformationArea").slideToggle();
    });

    var fewSeconds = 5;
    $('#create-bill').click(function() {
      var btn = $(this);
      btn.prop('disabled', true);
      $('#bill_create').submit();
      setTimeout(function(){
          btn.prop('disabled', false);
      }, fewSeconds*1000);
      return true;
    });

    $(document).on("change", ".qty1", function() {
      var name = $(this).attr('name');
      var res = name.replace("[price]", "");
      res = res.replace("[quantity]", "");
      var quantity_st  = 'input[name="'+res+ '[quantity]"]';
      var total_st  = 'input[name="'+res+ '[total]"]';
      var price_st  = 'input[name="'+res+ '[price]"]';
      var quantity = 1;
      if($(quantity_st).val() == ''){
        $(quantity_st).val(1);
      }else{
        quantity = $(quantity_st).val();
      }
      var price = $(price_st).val() == '' ? 0 :$(price_st).val();
      $(total_st).val(price * quantity);
    });

    $(document).ready(function () {
      @if(old('add_tax'))
        $('.Tax_Values').show();
        $('#Value').val({{old('tax_value')}});
        $('#Tax_Values_Checkbox').prop('checked', true);
      @elseif(old('add_tax') === 0)
        $('.Tax_Values').hide();
        $('#Tax_Values_Checkbox').prop('checked', false);
      @elseif(auth()->user()->settings->add_tax)
        $('.Tax_Values').show();
        $('#Value').val({{auth()->user()->settings->tax_value}});
        $('#Tax_Values_Checkbox').prop('checked', true);
      @else
        $('.Tax_Values').hide();
        $('#Tax_Values_Checkbox').prop('checked', false);
      @endif

      $('.repeater').repeater({
        show: function () {
          $(this).slideDown();
        },
      });

      $('#Tax_Values_Checkbox').change(function() {
        $('.Tax_Values').toggle();
      });

      $('#Discount_Values_Checkbox').change(function() {
        $('.Discount_Values').toggle();
      });

      

      var customers = [];
      
      $( "#customer_name").autocomplete({
        source: function(request, response) {
          $.ajax({
            url: "{{route('customers.search_name')}}",
            data: {
              // _token: CSRF_TOKEN,
              search : request.term
              },
            dataType: "json",
            success: function(data){
              customers = data;
              var resp = $.map(data,function(obj){
                return {'value': obj.name, 'label': obj.name, 'id': obj.id};
              });
              response(resp);
            }
          });
        },
        select: function (event, ui) {
          var item = customers.find(x => x.id === ui.item.id);
          $('#customer_name').val(item.name);
          $('#customer_mobile').val(item.mobile);
          $('#customer_email').val(item.email);
          $('#customer_notes').val(item.notes);
          $('#bullding_no').val(item.bullding_no);
          $('#street_name').val(item.street_name);
          $('#district').val(item.district);
          $('#city').val(item.city);
          $('#postal_code').val(item.postal_code);
          $('#additional_no').val(item.additional_no);
          $('#other_buyer_id').val(item.other_buyer_id);
          $('#vat_registration_number').val(item.vat_registration_number);        
          return false;
        },
        minLength: 1
      });
    });

    $('.inner-repeater').on('keypress', '.product_price',function (e) {
      var key = e.which;
      if(key == 13) {
        $(this).parent().parent().find(".product_quantity").focus();
        return false;
      }
    });
    $('.inner-repeater').on('keypress', '.product_quantity',function (e) {
      var key = e.which;
      if(key == 13) {
        $('.add_new_item').click();
        $('.product_name').last().focus();
        return false;
      }
    });
  </script>
  {!! JsValidator::formRequest('App\Http\Requests\BillRequest', '#bill_create') !!}
@endpush
