@extends('layouts.app')
@section('title', 'Page Title')

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
      <h1>Create Bill</h1>
      <div class="separator mb-5"></div>
    </div>
    <div class="col-12">
      <div class="create_bill_page card mb-4">
        <div class="card-body">
          <form method="POST" action="{{ route('bills.store') }}" class="repeater" id="bill_create">
            @csrf
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="customer_name">{{ __('Customer Name') }}</label>
                <input  value="{{ old('customer_name') }}" name="customer_name" type="text" class="form-control" id="customer_name" placeholder="{{ __('Customer Name') }}">
              </div><!-- form-group -->
              <div class="form-group col-md-6">
                <label for="customer_mobile">{{ __('Mobile Number') }}</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon3">+966</span>
                  </div>
                  <input  value="{{ old('customer_mobile') }}" name="customer_mobile" type="tel" class="form-control _parseArabicNumbers @error('customer_mobile') is-invalid @enderror" id="customer_mobile" placeholder="5XXXXXXXX" maxlength="10">
                </div>
                @error('customer_mobile')
                  <p class="invalid-feedback" role="alert">{{ $message }}</p>
                @enderror
              </div><!-- form-group -->
            </div><!-- form-row -->

            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="customer_email">{{ __('Email') }}</label>
                <input value="{{ old('customer_email') }}" name="customer_email" type="email" class="form-control @error('customer_email') is-invalid @enderror" id="customer_email" placeholder="{{ __('Email') }}">
                @error('customer_email')
                  <p class="invalid-feedback" role="alert">{{ $message }}</p>
                @enderror
              </div><!-- form-group -->

              <div class="form-group col-md-6">
                <label for="customer_notes">{{ __('Special Note') }}</label>
                <input value="{{ old('customer_notes') }}" name="customer_notes" type="text" class="form-control" id="customer_notes" placeholder="{{ __('Special Note') }}">
              </div><!-- form-group -->
            </div><!-- form-row -->

            <div class="form-row">
              <div class="form-group col-md-6">
                <label>{{ __('Due Date') }}</label>
                <input value="{{ Carbon\Carbon::now()->format('m/d/Y') }}" name="due_date" class="form-control datepicker" placeholder="{{ __('Due Date') }}">
              </div><!-- form-group -->

              <div class="form-group col-md-6">
                <label>{{ __('Expiry Date') }}</label>
                <select value="{{ old('expiry_date') }}" name="expiry_date" class="form-control">
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
            </div><!-- form-row -->

            <hr>
            <h1 class="mb-3">{{ __('Bill items') }}</h1>
            <div class="inner-repeater">
              <div data-repeater-list="items">
                @if(old('items'))
                  @foreach( old('items') as $item)
                    <div data-repeater-item>
                      <div class="form-row mb-2 item_row">
                        <div class="form-group col-12 col-md-4 col-lg-6 col-xl-6">
                          <label for="inputEmail1">{{ __('Product/Service') }}</label>
                          <input name="name" value="{{$item['name']}}" type="text" class="form-control" placeholder="{{ __('Name') }}">
                        </div><!-- form-group -->
                        <div class="form-group col-12 col-md-2 col-lg-2 col-xl-2">
                          <label for="Price">{{ __('Product/Service Price') }}</label>
                          <input name="price"  value="{{$item['price']}}" type="tel" class="form-control _parseArabicNumbers qty1" placeholder="{{ __('Price') }}">
                        </div><!-- form-group -->
                        <div class="form-group col-12 col-md-2 col-lg-2 col-xl-2">
                          <label for="Price">{{ __('Quantity') }}</label>
                          <input type="tel" name="quantity" value="{{$item['quantity']}}" class="form-control _parseArabicNumbers qty1" placeholder="{{ __('Quantity') }}">
                        </div><!-- form-group -->
                        <div class="form-group col-12 col-md-1 col-lg-1 col-xl-1">
                          <label for="Price">{{ __('Total') }}</label>
                          <input type="tel" name="total" value="{{ $item['price']* $item['quantity']}}" class="form-control _parseArabicNumbers text-center font-weight-bold" disabled>
                        </div><!-- form-group -->
                        <div class="form-group col-12 col-md-1 col-lg-1 col-xl-1 delete_block">
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
                      <label for="inputEmail1">{{ __('Product/Service') }}</label>
                      <input name="name" type="text" class="form-control" placeholder="{{ __('Name') }}">
                    </div><!-- form-group -->
                    <div class="form-group col-12 col-md-2 col-lg-2 col-xl-2">
                      <label for="Price">{{ __('Product/Service Price') }}</label>
                      <input type="tel" name="price" class="form-control _parseArabicNumbers qty1" placeholder="{{ __('Price') }}">
                    </div><!-- form-group -->
                    <div class="form-group col-12 col-md-2 col-lg-2 col-xl-2">
                      <label for="Price">{{ __('Quantity') }}</label>
                      <input type="tel" name="quantity" class="form-control _parseArabicNumbers qty1" placeholder="{{ __('Quantity') }}">
                    </div><!-- form-group -->
                    <div class="form-group col-12 col-md-1 col-lg-1 col-xl-1">
                      <label for="Price">{{ __('Total') }}</label>
                      <input name="total" type="tel" class="form-control _parseArabicNumbers text-center font-weight-bold" disabled>
                    </div><!-- form-group -->
                    <div class="form-group col-12 col-md-1 col-lg-1 col-xl-1 delete_block">
                      <label for="Delete" class="d-block">{{ __('Delete') }}</label>
                    <input data-repeater-delete type="button" class="btn btn-danger default d-block w-100" value="X"/>
                    </div><!-- form-group -->
                  </div><!-- form-row -->
                </div><!-- inner-list-->
                @endif             
              </div><!-- form-row -->
            </div><!-- inner-repeater -->
            <div class="d-flex justify-content-end my-3">
              <input data-repeater-create type="button" class="btn btn-primary btn-lg" value="Add Item">
            </div><!-- d-flex  -->
            <hr>
            <h1 class="mb-3">{{ __('Additonal Details') }}</h1>
            <div class="form-row">
              <div class="form-group col-6">
                <label for="inputEmail1">{{ __('Add Discount') }}</label>
                <div class="custom-switch custom-switch-primary mb-2">
                  <input name="add_discount" class="custom-switch-input" id="Discount_Values_Checkbox" type="checkbox">
                  <label class="custom-switch-btn" for="Discount_Values_Checkbox"></label>
                </div>
              </div><!-- form-group -->
              <div class="form-group col-6">
                <label for="inputEmail1">{{ __('Add Tax') }}</label>
                <div class="custom-switch custom-switch-primary mb-2">
                  <input  name="add_tax" class="custom-switch-input" id="Tax_Values_Checkbox" type="checkbox">
                  <label class="custom-switch-btn" for="Tax_Values_Checkbox"></label>
                </div>
              </div><!-- form-group -->
            </div><!-- form-row -->

            <div class="row">
              <div class="col-12 col-md-6 col-lg-6 col-xl-6">
                <div class="Discount_Values form-row mb-2" style="display: none;">
                  <div class="form-group col-12 col-md-6 col-lg-6 col-xl-6">
                    <label for="type">{{ __('Discount type') }}</label>
                    <select name="discount_type" id="discount_type" class="form-control">
                      <option value="fixed">{{ __('fixed') }}</option>
                      <option value="percentage">{{ __('Percentage Discount (%)') }}</option>
                    </select>
                  </div><!-- form-group -->
                  <div class="form-group col-12 col-md-6 col-lg-6 col-xl-6">
                    <label for="Price">{{ __('Discount Value') }}</label>
                    <div class="input-group">
                      <input type="tel" name="discount_value" class="form-control _parseArabicNumbers" id="Discount_Value" aria-describedby="basic-addon2">
                      <div class="input-group-append">
                        <span class="input-group-text discount_type_item" id="fixed">SAR</span>
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
                    <input type="tel" name="tax_value" class="form-control _parseArabicNumbers" id="Value">
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
                  <input name="send_sms" class="custom-switch-input" id="send_sms" type="checkbox">
                  <label class="custom-switch-btn" for="send_sms"></label>
                </div>
              </div><!-- form-group -->
              <div class="form-group col-6">
                <label for="send_email">{{ __('Send Email') }}</label>
                <div class="custom-switch custom-switch-primary mb-2">
                  <input name="send_email" class="custom-switch-input" id="send_email" type="checkbox">
                  <label class="custom-switch-btn" for="send_email"></label>
                </div>
              </div><!-- form-group -->
            </div><!-- form-row -->
            <div class="d-flex justify-content-start mt-3">
              <button type="submit" class="btn btn-primary btn-lg"> {{__('Send')}}</button>
            </div><!-- d-flex  -->
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('footer-scripts')
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="{{ asset('js/jquery.repeater.min.js') }}" defer></script>
  <script>

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
      $( "#customer_name" ).autocomplete({
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
                    return {'value': obj.id, 'label': obj.name};
               }); 
               response(resp);
              }
          });
      },
      select: function (event, ui) {
        var item = customers.find(x => x.id === ui.item.value);
        $('#customer_name').val(item.name);
        $('#customer_mobile').val(item.mobile);
        $('#customer_email').val(item.email);
        $('#customer_notes').val(item.notes);
       return false;
      },
      minLength: 1
   });
  });
  </script>

    {!! JsValidator::formRequest('App\Http\Requests\BillRequest', '#bill_create') !!}
@endsection
