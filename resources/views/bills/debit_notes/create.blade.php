@extends('layouts.app')

@section('title', __('Create Debit Note'))

@section('css_styles')
  <link rel="stylesheet" href="{{ asset('new/css/plugins/jquery-ui/jquery-ui.css') }}?v={{ config('app.asset_version') }}">
  <link rel="stylesheet" href="{{ asset('new/css/plugins/daterangepicker/daterangepicker.css') }}?v={{ config('app.asset_version') }}">
  <link rel="stylesheet" href="{{ asset('new/css/plugins/select2/select2.min.css') }}?v={{ config('app.asset_version') }}">
  <link rel="stylesheet" href="{{ asset('new/css/plugins/select2/select2-bootstrap.min.css') }}?v={{ config('app.asset_version') }}">
@endsection

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <a href="/bills" title="{{ __('Bills') }}">{{ __('Bills') }}</a>
    <i>/</i>
    <span>{{ __('Create Debit Note')}}</span>
  </div><!-- breadcrump -->

  <section id="billCreatePage">
    <div class="title mb-4">
      <h1 class="d-block fw-bold m-0 fs-5">{{ __('Create Debit Note') }}</h1>
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

    <div class="block bg-white shadow-sm rounded-3">
      <form method="POST" action="{{ route('debitNote.store') }}" class="repeater" id="bill_create">
        @csrf

        <input type="hidden" name="bill_id" value="{{ $bill->id }}">
        <div class="row">
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="customer_name" class="d-block mb-2">{{ __('Customer Name') }} <span class="requirement text-danger">*</span></label>
              <input value="{{ $bill->customer_name }}" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="customer_name" placeholder="{{ __('Customer Name') }} *" readonly>
            </div><!-- form-group -->
          </div><!-- col -->
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="customer_mobile" class="d-block mb-2">{{ __('Mobile Number') }} <span class="requirement text-danger">*</span></label>
              <div class="phoneInput overflow-hidden position-relative">
                <input value="{{ $bill->customer_mobile }}"  type="tel" class="form-control shadow-none bg-white border w-100 rounded-3 text-body @error('customer_mobile') is-invalid @enderror" id="customer_mobile" placeholder="5XXXXXXXX"  pattern="[0-9]*" maxlength="9" inputmod="numaric" readonly>
              </div><!-- phoneInput -->
              @error('customer_mobile')
                <p class="invalid-feedback" role="alert">{{ $message }}</p>
              @enderror
            </div><!-- mb-3 -->
          </div><!-- col -->
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="customer_email" class="d-block mb-2">{{ __('Email') }}</label>
              <input value="{{ $bill->customer_email }}" type="email" class="form-control shadow-none bg-white border w-100 rounded-3 text-body @error('customer_email') is-invalid @enderror" id="customer_email" inputmode="email" placeholder="{{ __('Email') }}" readonly>
              @error('customer_email')
                <p class="invalid-feedback" role="alert">{{ $message }}</p>
              @enderror
            </div><!-- form-group -->
          </div><!-- col -->
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="customer_notes" class="d-block mb-2">{{ __('Special Note') }}</label>
              <input value="@if(old('customer_notes')) {{ old('customer_notes') }} @else {{$bill->customer->notes}} @endif" name="customer_notes" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="customer_notes" placeholder="{{ __('Special Note') }}">
            </div><!-- form-group -->
          </div><!-- col -->
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="due_date" class="d-block mb-2">{{ __('Due Date') }}</label>
              <input value="{{ Carbon\Carbon::now()->format('d/m/y') }}" name="due_date" id="due_date" class="form-control shadow-none bg-white border w-100 rounded-3 text-body dueDate" placeholder="{{ __('Due Date') }}">
            </div><!-- form-group -->
          </div><!-- col -->
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="expiry_date" class="d-block mb-2">{{ __('Expiry Date') }}</label>
              <select value="{{ old('expiry_date') }}" name="expiry_date" id="expiry_date" class="form-control shadow-none bg-white border w-100 rounded-3 text-body select2-single">
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
        @if($settings->add_tax_invoice)
          <button type="button" class="additionalInformationBtn border-0 d-flex align-items-center justify-content-start bg-transparent p-0">{{__('Additional Information')}}</button>
          <div class="additionalInformationArea">
            <div class="pt-3">
              <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                  <div class="form-group mb-3">
                    <label for="bullding_no" class="d-block mb-2">{{__('Building Number')}}</label>
                    <input value="{{ $bill->customer->bullding_no }}" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="bullding_no" placeholder="{{__('Building Number')}}" readonly>
                  </div><!-- form-group -->
                </div><!-- col -->
                <div class="col-12 col-md-6 col-lg-4">
                  <div class="form-group mb-3">
                    <label for="street_name" class="d-block mb-2">{{__('Street Name')}}</label>
                    <input value="{{ $bill->customer->street_name }}" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="street_name" placeholder="{{__('Street Name')}}" readonly>
                  </div><!-- form-group -->
                </div><!-- col -->
                <div class="col-12 col-md-6 col-lg-4">
                  <div class="form-group mb-3">
                    <label for="district" class="d-block mb-2">{{__('District')}}</label>
                    <input value="{{ $bill->customer->district }}" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="district" placeholder="{{__('District')}}" readonly>
                  </div><!-- form-group -->
                </div><!-- col -->
                <div class="col-12 col-md-6 col-lg-4">
                  <div class="form-group mb-3">
                    <label for="city" class="d-block mb-2">{{__('City')}}</label>
                    <input value="{{ $bill->customer->city }}" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="city" placeholder="{{__('City')}}" readonly>
                  </div><!-- form-group -->
                </div><!-- col -->
                <div class="col-12 col-md-6 col-lg-4">
                  <div class="form-group mb-3">
                    <label for="postal_code" class="d-block mb-2">{{__('Postal Code')}}</label>
                    <input value="{{ $bill->customer->postal_code }}" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="postal_code" placeholder="{{__('Postal Code')}}" readonly>
                  </div><!-- form-group -->
                </div><!-- col -->
                <div class="col-12 col-md-6 col-lg-4">
                  <div class="form-group mb-3">
                    <label for="additional_no" class="d-block mb-2">{{__('Additional Number')}}</label>
                    <input value="{{ $bill->customer->additional_no }}"  type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="additional_no" placeholder="{{__('Additional Number')}}" readonly>
                  </div><!-- form-group -->
                </div><!-- col -->
                <div class="col-12 col-md-6 col-lg-4">
                  <div class="form-group mb-3">
                    <label for="other_buyer_id" class="d-block mb-2">{{__('Additional ID')}}</label>
                    <input value="{{ $bill->customer->other_buyer_id }}"  type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="other_buyer_id" placeholder="{{__('Additional ID')}}" readonly>
                  </div><!-- form-group -->
                </div><!-- col -->
                <div class="col-12 col-md-6 col-lg-4">
                  <div class="form-group mb-3">
                    <label for="vat_registration_number" class="d-block mb-2">{{__('VAT Registration Number (optional)')}}</label>
                    <input value="{{ $bill->customer->vat_registration_number }}"  type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="vat_registration_number" placeholder="{{__('VAT Registration Number (optional)')}}" readonly>
                  </div><!-- form-group -->
                </div><!-- col -->
              </div><!-- row -->
            </div><!-- pt-3 -->
          </div><!-- additionalInformationArea -->
        @endif
        <hr>
        <div class="title2 d-flex align-items-center justify-content-between mb-4">
          <span class="d-block fw-bold">{{ __('Bill items') }}</span>
          <input data-repeater-create type="button" class="addNewItem rounded-3 d-flex align-items-center justify-content-center border-0 text-white" value="{{ __('Add Item') }}">
        </div><!-- title2 -->
        <div class="d-flex justify-content-end">
        </div><!-- d-flex  -->
        <div class="inner-repeater">
          <div class="repeaterItems" data-repeater-list="items">
            @if(old('items'))
              @foreach( old('items') as $item)
                <div class="repeaterItem row align-items-end" data-repeater-item>
                  <div class="col-12 col-lg-6">
                    <div class="form-group mb-3">
                      <label for="inputEmail1" class="d-block mb-2">{{ __('Product/Service') }} <span class="requirement text-danger">*</span></label>
                      <input name="name" value="{{$item['name']}}" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body product_name" placeholder="{{ __('Name') }}">
                    </div><!-- form-group -->
                  </div><!-- col-12 -->
                  <div class="col-12 col-lg-3">
                    <div class="form-group mb-3">
                      <label for="Price" class="d-block mb-2">{{ __('Product/Service Price') }} <span class="requirement text-danger">*</span></label>
                      <input name="price"  value="{{$item['price']}}" min="1" type="tel" class="form-control shadow-none bg-white border w-100 rounded-3 text-body qty1 product_price" placeholder="{{ __('Price') }}">
                    </div><!-- form-group -->
                  </div><!-- col-12 -->
                  <div class="col-12 col-lg-3">
                    <div class="form-group mb-3">
                      <label for="Price" class="d-block mb-2">{{ __('Quantity') }} <span class="requirement text-danger">*</span></label>
                      <input type="tel" name="quantity" value="{{$item['quantity']}}" min="1" class="form-control shadow-none bg-white border w-100 rounded-3 text-body qty1 product_quantity" placeholder="{{ __('Quantity') }}">
                    </div><!-- form-group -->
                  </div><!-- col-12 -->
                  <div class="col-12 col-lg-3">
                    <div class="form-group mb-3">
                      <label for="Price" class="d-block mb-2">{{ __('Total') }}</label>
                      <input type="tel" name="total" value="{{ $item['price']* $item['quantity']}}" class="form-control shadow-none bg-white border w-100 rounded-3 text-body text-center fw-bold" disabled>
                    </div><!-- form-group -->
                  </div><!-- col-12 -->
                  <div class="col-12 col-lg-1">
                    <div class="form-group mb-3">
                      <!-- <label for="Delete" class="d-block">{{ __('Delete') }}</label> -->
                      <input data-repeater-delete type="button" class="deleteBtn w-100 border-0 rounded-3 text-white d-flex align-items-center justify-content-center" value="X"/>
                    </div><!-- form-group -->
                  </div><!-- col-12 -->
                </div><!-- repeaterItem -->
              @endforeach
            @else
              <div class="repeaterItem row align-items-end" data-repeater-item>
                <div class="col-12 col-lg-5">
                  <div class="form-group mb-3">
                    <label for="inputEmail1" class="d-block mb-2">{{ __('Product/Service') }} <span class="requirement text-danger">*</span></label>
                    <input name="name" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body product_name" placeholder="{{ __('Name') }}">
                  </div><!-- form-group -->
                </div><!-- col-12 -->
                <div class="col-6 col-lg-2">
                  <div class="form-group mb-3">
                    <label for="Price" class="d-block mb-2">{{ __('Product/Service Price') }} <span class="requirement text-danger">*</span></label>
                    <input type="tel" name="price" min="1" class="form-control shadow-none bg-white border w-100 rounded-3 text-body qty1 product_price" placeholder="{{ __('Price') }}">
                  </div><!-- form-group -->
                </div><!-- col-6 -->
                <div class="col-6 col-lg-2">
                  <div class="form-group mb-3">
                    <label for="Price" class="d-block mb-2">{{ __('Quantity') }} <span class="requirement text-danger">*</span></label>
                    <input type="tel" name="quantity" min="1" class="form-control shadow-none bg-white border w-100 rounded-3 text-body qty1 product_quantity" placeholder="{{ __('Quantity') }}">
                  </div><!-- form-group -->
                </div><!-- col-6 -->
                <div class="col-6 col-lg-2">
                  <div class="form-group mb-3">
                    <label for="Price" class="d-block mb-2">{{ __('Total') }}</label>
                    <input name="total" type="tel" class="form-control shadow-none bg-white border w-100 rounded-3 text-body text-center fw-bold" disabled>
                  </div><!-- form-group -->
                </div><!-- col-6 -->
                <div class="col-6 col-lg-1">
                  <div class="form-group mb-3 delete_block">
                    <!-- <label for="Delete" class="d-block">{{ __('Delete') }}</label> -->
                    <input data-repeater-delete type="button" class="deleteBtn w-100 border-0 rounded-3 text-white d-flex align-items-center justify-content-center" value="X"/>
                  </div><!-- form-group -->
                </div><!-- col-6 -->
              </div><!-- repeaterItem -->
            @endif
          </div><!-- repeaterItems -->
        </div><!-- inner-repeater -->
        <hr>
        <div class="title2 fw-bold mb-4">{{ __('Additonal Details') }}</div>
        <div class="row">
          <div class="col-12 col-lg-6">
            <label for="Discount_Values_Checkbox" class="checkboxItem position-relative mb-3 mb-md-0">
              <input name="add_discount" class="position-absolute top-0 strat-0 w-100 h-100" id="Discount_Values_Checkbox" type="checkbox" @if(old('add_discount')) checked @endif>
              <span class="d-flex align-items-center justify-content-start">
                <i class="d-block rounded-pill position-relative"></i>
                {{ __('Add Discount') }}
              </span>
            </label>
            <div class="Discount_Values" style="display: none;">
              <div class="row py-3">
                <div class="col-6">
                  <div class="form-group">
                    <label for="type" class="d-block mb-2">{{ __('Discount type') }}</label>
                    <select name="discount_type" id="discount_type" class="form-control shadow-none bg-white border w-100 rounded-3">
                      <option value="fixed" @if(old('discount_type') == 'fixed') selected @endif> {{ __('fixed') }}</option>
                      <option value="percentage" @if(old('discount_type') == 'percentage') selected @endif>{{ __('Percentage Discount (%)') }}</option>
                    </select>
                  </div><!-- form-group -->
                </div><!-- col-6 -->
                <div class="col-6">
                  <div class="form-group">
                    <label for="Price" class="d-block mb-2">{{ __('Discount Value') }}</label>
                    <div class="inputGroup position-relative d-flex align-items-center justify-content-start flex-wrap">
                      <div class="txt align-items-center justify-content-center position-absolute rounded-3" id="fixed">{{ __('SAR') }}</div>
                      <div class="txt align-items-center justify-content-center position-absolute rounded-3" id="percentage"><i class="far fa-percentage"></i></div>
                      <input type="tel" name="discount_value" class="form-control shadow-none bg-white border w-100 rounded-3" value="{{old('discount_value')}}" id="Discount_Value" aria-describedby="basic-addon2">
                    </div><!-- inputGroup -->
                  </div><!-- form-group -->
                </div><!-- col-6 -->
              </div><!-- row -->
            </div><!-- Discount_Values -->
          </div><!-- col-12 -->
          <div class="col-12 col-lg-6">
            <label for="Tax_Values_Checkbox" class="checkboxItem position-relative mb-3 mb-md-0">
              <input name="add_tax" class="position-absolute top-0 strat-0 w-100 h-100" id="Tax_Values_Checkbox" @if($errors->any()) @if(old('add_tax') == true) checked @endif @else @if($settings->add_tax) checked @endif @endif type="checkbox">
              <span class="d-flex align-items-center justify-content-start">
                <i class="d-block rounded-pill position-relative"></i>
                {{ __('Add Tax') }}
              </span>
            </label>
            <div class="Tax_Values" style="display: none;">
              <div class="row py-3">
                <div class="col-12 col-md-6">
                  <div class="form-group">
                    <label for="Tax" class="d-block mb-2">{{ __('Tax Value') }}</label>
                    <div class="inputGroup position-relative d-flex align-items-center justify-content-start flex-wrap">
                      <div class="txt align-items-center justify-content-center position-absolute rounded-3" id="percentage"><i class="far fa-percentage"></i></div>
                      <input type="tel" name="tax_value" class="form-control shadow-none bg-white border w-100 rounded-3" id="Value" value="@if($settings->add_tax){{$settings->tax_value}}@else{{old('tax_value')}}@endif" aria-describedby="basic-addon3" readonly>
                    </div><!-- inputGroup -->
                  </div><!-- form-group -->
                </div><!-- col-12 -->
              </div><!-- row -->
            </div><!-- Tax_Values -->
          </div><!-- col-12 -->
        </div><!-- row -->
        <hr>
        <div class="title2 fw-bold mb-4">{{ __('Send The Bill To Customer') }}</div>
        <div class="row">
          <div class="col-12 col-lg-6">
            <label for="send_sms" class="checkboxItem position-relative mb-3 mb-md-0">
              <input name="send_sms" class="position-absolute top-0 strat-0 w-100 h-100" id="send_sms" type="checkbox" @if($settings->create_send_sms || old('send_sms')) checked @endif>
              <span class="d-flex align-items-center justify-content-start">
                <i class="d-block rounded-pill position-relative"></i>
                {{ __('Send SMS') }}
              </span>
            </label>
          </div><!-- col-12 -->
          <div class="col-12 col-lg-6">
            <label for="send_email" class="checkboxItem position-relative m-0">
              <input name="send_email" class="position-absolute top-0 strat-0 w-100 h-100" id="send_email" type="checkbox" @if($settings->create_send_email || old('send_email')) checked @endif>
              <span class="d-flex align-items-center justify-content-start">
                <i class="d-block rounded-pill position-relative"></i>
                {{ __('Send Email') }}
              </span>
            </label>
          </div><!-- col-12 -->
        </div><!-- row -->
        <div class="sendBtn d-flex justify-content-start mt-5">
          <button id="create-bill" type="submit" class="formBtn btn-primary rounded-3 border-0 d-flex align-items-center justify-content-center fw-bold"> {{__('Send')}}</button>
        </div><!-- sendBtn  -->
      </form>
    </div><!-- block -->
  </section><!-- billCreatePage -->

@endsection

@push('footer-scripts')
  <script src="{{ asset('new/js/daterangepicker/moment.min.js') }}?v={{ config('app.asset_version') }}" defer></script>
  <script src="{{ asset('new/js/daterangepicker/daterangepicker.min.js') }}?v={{ config('app.asset_version') }}" defer></script>
  <script src="{{ asset('new/js/jquery-ui/jquery-ui.js') }}?v={{ config('app.asset_version') }}" defer></script>
  <script src="{{ asset('new/js/repeater/jquery.repeater.min.js') }}?v={{ config('app.asset_version') }}"></script>
  <script src="{{ asset('new/js/select2/select2.full.js') }}?v={{ config('app.asset_version') }}" defer></script>
  <script>
    // Additional Information
    $(".additionalInformationArea").hide();
    $("button.additionalInformationBtn").click(function(){
      $(this).toggleClass("show");
      $(".additionalInformationArea").slideToggle();
    });

    // Single Daterangepicker
    $(function() {
      $('.dueDate').daterangepicker({
        "singleDatePicker": true,
        "autoApply": true,
        "maxSpan": {
          "days": 7
        },
        locale: {
          format: 'DD/MM/YYYY',
          daysOfWeek: [
            '{{__('Sun')}}',
            '{{__('Mon')}}',
            '{{__('Tue')}}',
            '{{__('Wed')}}',
            '{{__('Thur')}}',
            '{{__('Fri')}}',
            '{{__('Sat')}}'
          ],
          monthNames: [
            '{{__('January')}}',
            '{{__('February')}}',
            '{{__('March')}}',
            '{{__('April')}}',
            '{{__('May')}}',
            '{{__('June')}}',
            '{{__('July')}}',
            '{{__('August')}}',
            '{{__('September')}}',
            '{{__('October')}}',
            '{{__('November')}}',
            '{{__('December')}}'
          ],
          fromLabel: '{{__('from')}}',
          toLabel: '{{__('to')}}',
          applyLabel: '{{__('apply')}}',
          cancelLabel:'{{__('cancel')}}',
          customRangeLabel: '{{__('custom Range')}}',
          weekLabel: '{{__('week')}}',
        },
      });
    });

    // Repeater
    $('.repeater').repeater({
      show: function () {
        $(this).slideDown();
      },
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
      console.log($('#Discount_Values_Checkbox').prop('checked'));
      console.log($('#Tax_Values_Checkbox').prop('checked'));


      if($('#Discount_Values_Checkbox').prop('checked')){
        $('.Discount_Values').show();
        if($('#discount_type').val() === 'percentage') {
          $('#percentage').show();
          $('#fixed').hide();
        } else {
          $('#percentage').hide();
          $('#fixed').show();
        }
      }else{
        $('.Discount_Values').hide();
      }

      if($('#Tax_Values_Checkbox').prop('checked')){
        $('.Tax_Values').show();
      }else{
        $('.Discount_Values').hide();
      }

      // Tax & Discount
      $('#Discount_Values_Checkbox').change(function() {
        $('.Discount_Values').slideToggle();
        if($('#discount_type').val() === 'percentage') {
          $('#percentage').show();
          $('#fixed').hide();
        } else {
          $('#percentage').hide();
          $('#fixed').show();
        }
      });
      $('#discount_type').change(function() {
        if($('#discount_type').val() === 'percentage') {
          $('#percentage').show();
          $('#fixed').hide();
        } else {
          $('#percentage').hide();
          $('#fixed').show();
        }
      });

      $('#Tax_Values_Checkbox').change(function() {
        $('.Tax_Values').slideToggle();
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
