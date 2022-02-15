@extends('layouts.bill')
@section('title', __('Bill No.') . ' ' . $bill->number)
@section('content')

  <div id="app" class="singlebBillSimple_page d-flex align-items-center justify-content-center flex-column">
    <div class="all_bill_page">
      <div class="change_lang d-flex align-items-center justify-content-end w-100 mb-1">
        @if($bill->user->settings->active_lang == 'all')
          @if(App::isLocale('en'))
            <a href="{{ route('paybillpagelang', ['id' => $bill->pay_id, 'lang' => 'ar'])}}" title="عربي" class="d-block">عربي</a>
          @else
            <a href="{{ route('paybillpagelang', ['id' => $bill->pay_id, 'lang' => 'en'])}}" title="English" class="d-block">English</a>
          @endif
        @endif
      </div><!-- change_lang -->
      <div class="single_bill_content">
        <div class="about d-flex align-items-center justify-content-center flex-column">
          @if($bill->user->logo)
            <img src="https://surepay.sa/dist/images/logo.png" alt="logo">
          @endif

            @if($bill->application_id == null || !$bill->user->settings->api_bill_style)
              <div class="date_time">
                <span>
                  {{__('Due on')}} {{ $bill->dateLocalization()}}
                  @if($bill->user->vat_registration_number)
                    <div class="vat_reg"> {{ __('VAT Registration Number') }} : {{ $bill->user->vat_registration_number }}</div>
                  @endif
                </span>
                <div>
                  <p>{{ __('Bill No.') }} #{{ $bill->number }}</p>
                  <b>{{ $bill->created_at->format('Y/m/d')}}</b>
                </div>
              </div><!-- date_time -->
              <div class="shopping_cart">
                @foreach($bill->items as $item)
                  <div class="details_pay">
                    <p>{!! $item->product_name !!}</p>
                    <b>X {{ $item->quantity  }}</b>
                    <b>{{ $item->product_price  }} {{ __('SAR') }}</b>
                  </div><!-- details_pay -->
                @endforeach
              </div><!-- shopping_cart -->
              <div class="total_bill">
                @if( $bill->add_tax || $bill->add_discount || $bill->refund_amount)
                  <p>{{ __('Subtotal') }} : {{ $bill->sub_total }} {{ __('SAR') }}</p>
                @endif
                @if( $bill->add_discount)
                  @if($bill->discount_type == 'percentage')
                    <p>{{ __('Discount') }} ({{ $bill->discount_value }}%) : {{ $bill->discount }} {{ __('SAR') }}</p>
                  @else
                    <p>{{ __('Discount') }}  ({{ $bill->discount_value }} {{ __('SAR') }}) : {{ $bill->discount }} {{ __('SAR') }}</p>
                  @endif
                  <p>{{ __('Subtotal - Discount') }} : {{ $bill->sub_total- $bill->discount }} {{ __('SAR') }}</p>
                @endif
                @if( $bill->add_tax)
                  <p>{{ __('Vat') }} ({{ $bill->tax_value }}%) : {{ $bill->vat }} {{ __('SAR') }}</p>
                @endif
                @if( $bill->refund_amount)
                  <p>{{ __('Refund Amount') }} : {{ $bill->refund_amount }}  {{ __('SAR') }}</p>
                @endif
                <b>{{ __('Total') }} : {{ $bill->total}} {{ __('SAR') }}</b>
              </div><!-- total_bill -->
              @if($bill->customer_notes)<div class="customer_notes">{{$bill->customer_notes}}</div> @endif
            <div class="customer_information">
              <!-- <div class="name">Customer Information</div> -->
              <p>{{ $bill->customer_name}}</p>
              <p class="ltr">+966{{ $bill->customer_mobile}}</p>
              <p>{{ $bill->customer_email}}</p>
              @if(isset($bill->user->settings->footer_bill))
                <p>{{ $bill->user->settings->footer_bill }}</p>
              @endif
            </div><!-- customer_information -->

            @else
              <div class="date_time">
                <b>{{ $bill->total}}  {{ __('SAR') }}</b>
              </div>
            @endif
            
            @if($bill->application && $bill->status != 'paid')
              <div id="back_btn" class="text-center">
                <a href="{{ $bill->back_url}}" class="btn btn-light">{{__('Back')}}
                  <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-left-short" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M7.854 4.646a.5.5 0 0 1 0 .708L5.207 8l2.647 2.646a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 0 1 .708 0z"/>
                    <path fill-rule="evenodd" d="M4.5 8a.5.5 0 0 1 .5-.5h6.5a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5z"/>
                  </svg>
                </a>
              </div>
            @endif
            @if($bill->status == 'paid' && $bill->user->settings->add_tax_invoice)
            <div class="d-flex justify-content-center">
            {!! generateQRcode($bill) !!}
             </div>
             <a class="d-flex justify-content-center" target="_blank" href="{{route('invoice', ['id' => $bill->pay_id])}}">{{__('Tax Invoice')}}</a> 
            @endif
          </div><!-- single_bill_content -->
          <a target="_blank" title="Sure Bills" class="logo_bills"></a>
        </div><!-- col-12 -->
      </div><!-- row -->
    </div><!-- container -->

@endsection


@push('footer-scripts')
  <script type="text/javascript">
    Echo.channel('bill.{{$bill->id}}')
      .listen('BillStatusUpdated', (e) => {
          console.log(e.bill.id);
          var className;

          switch(e.bill.status) {
          case "pending":
              className = "badge-info";
              break;
          case "paid":
              $("#payment_method").remove();
              $("#back_btn").remove();
              $("#status").empty();
              $("#status").append('<div class="alert alert-success" role="alert">{{ __("this bill is paid successfully") }}</div>');
              break;
          case "paid_cash":
              $("#payment_method").remove();
              $("#back_btn").remove();
              $("#status").empty();
              $("#status").append('<div class="alert alert-success" role="alert">{{ __("this bill is paid successfully") }}</div>');
              break;
          case "paid_bank_trnasfer":
              $("#payment_method").remove();
              $("#back_btn").remove();
              $("#status").empty();
              $("#status").append('<div class="alert alert-success" role="alert">{{ __("this bill is paid successfully") }}</div>');
              break;
          case "refunded":
              $("#payment_method").remove();
              $("#back_btn").remove();
              $("#status").empty();
              $("#status").append('<div class="alert alert-warning" role="alert">{{ __("this bill is refunded successfully") }}</div>');
              break;
          case "refunded_cash":
              $("#payment_method").remove();
              $("#back_btn").remove();
              $("#status").empty();
              $("#status").append('<div class="alert alert-warning" role="alert">{{ __("this bill is refunded successfully") }}</div>');
              break;
          case "refunded_bank_transfer":
              $("#payment_method").remove();
              $("#back_btn").remove();
              $("#status").empty();
              $("#status").append('<div class="alert alert-warning" role="alert">{{ __("this bill is refunded successfully") }}</div>');
              break;
          case "canceled":
              $("#payment_method").remove();
              $("#back_btn").remove();
              $("#status").empty();
              $("#status").append('<div class="alert alert-danger" role="alert">{{ __("this bill is canceled") }}</div>');
              break;
          case "expired":
              $("#payment_method").remove();
              $("#back_btn").remove();
              $("#status").empty();
              $("#status").append('<div class="alert alert-danger" role="alert">{{  __('this bill has been expired', ['number' => $bill->number ]) }}</div>');
              break;
          default:
              $("#payment_method").remove();
              $("#back_btn").remove();
              $("#status").empty();
              className = "badge-info";
          }
          // $('#status')
          //   .text(e.bill.trans_status)
          //   .removeClass('badge-light badge-danger badge-success badge-info')
          //   .addClass(className);
      });
  </script>
@endpush
