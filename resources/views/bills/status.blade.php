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
            <img src="{{ $bill->user->logo_url }}" alt="logo">
          @endif
          @if($bill->status == 'paid' && $bill->user->settings->add_tax_invoice)
            <div class="taxInvoiceText">{{ __('Simplified Tax Invoice') }}</div>
          @endif
          @if($bill->application_id == null || !$bill->user->settings->api_bill_style)
            <span class="d-block font-weight-bold">{{ $bill->user->business_name }}</span>
            <p class="d-block mb-0">{{  $bill->user->business_address }}</p>
            <b class="d-block font-weight-normal">{{  $bill->user->business_mobile }}</b>
          @endif
        </div><!-- about -->
        @if($bill->application_id == null || !$bill->user->settings->api_bill_style)
          <div class="bill_info">
            @if($bill->user->settings->add_tax_invoice)
              <div class="d-flex align-items-center justify-content-between">
                <span>{{ __('Bill No.') }}</span>
                <span>{{ $bill->number }}</span>
              </div><!-- d-flex -->
              <div class="d-flex align-items-center justify-content-between">
                <span>{{ __('Bill created date') }}</span>
                <span>{{ $bill->created_at->format('d/m/Y')}}</span>
              </div><!-- d-flex -->
              @if($bill->user->vat_registration_number)
                <div class="d-flex align-items-center justify-content-between">
                  <span>{{ __('Organization VAT Registration Number') }}</span>
                  <span>{{ $bill->user->vat_registration_number }}</span>
                </div><!-- d-flex -->
              @endif
            @else
            <div class="d-flex align-items-center justify-content-between">
              <span>{{ __('No.') }}</span>
              <span>{{ $bill->number }}</span>
            </div><!-- d-flex -->
            <div class="d-flex align-items-center justify-content-between">
              <span>{{ __('Date') }}</span>
              <span>{{ $bill->created_at->format('d/m/Y')}}</span>
            </div><!-- d-flex -->
            @endif
          </div><!-- bill_info -->
        @endif
        <div class="table_items">
          <table>
            <thead>
              <tr>
                <th>{{ __('Description') }}</th>
                <th>{{ __('Price') }}</th>
                <th>{{ __('Quantity') }}</th>
                @if($bill->user->settings->add_tax_invoice)
                <th width="35%">{{ __('Total include added tax') }}</th>
                @endif
              </tr>
            </thead>
            <tbody>
              @foreach($bill->items as $item)
              <tr>
                <td>{!! $item->product_name !!}</td>
                <td>{{ $item->product_price  }} {{ __('SAR') }}</td>
                <td>{{ $item->quantity  }}</td>
                @if( $bill->add_tax)
                <td>{{ ($item->product_price * $item->quantity) + $bill->vat  }} {{ __('SAR') }}</td>
                @else
                <td>{{ $item->product_price * $item->quantity }} {{ __('SAR') }}</td>
                @endif
              </tr>
              @endforeach
            </tbody>
          </table>
        </div><!-- table_items -->
        <div class="bill_info">
          <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-start justify-content-between flex-column">
              <span>{{ __('Total amount') }}</span>
              <small>( {{ __('Exclude added tax') }} )</small>
            </div>
            @if( $bill->add_tax || $bill->add_discount || $bill->refund_amount)
            <span>{{ $bill->sub_total }} {{ __('SAR') }}</span>
            @endif
          </div><!-- d-flex -->
          @if( $bill->add_discount)
          <div class="d-flex align-items-center justify-content-between">
            <span>{{ __('Discount') }} @if($bill->discount_type == 'percentage') ({{ $bill->discount_value }}%) @else ({{ $bill->discount_value }} {{ __('SAR') }}) @endif</span>
            <span>{{ $bill->discount }} {{ __('SAR') }}</span>
          </div><!-- d-flex -->
          @endif
          @if( $bill->add_tax)
          <div class="d-flex align-items-center justify-content-between">
            <span>{{ __('Added tax value') }}</span>
            <span>{{ $bill->vat }} {{ __('SAR') }}</span>
          </div><!-- d-flex -->
          @endif
          @if( $bill->refund_amount)
            <div class="d-flex align-items-center justify-content-between">
              <span>{{ __('Refund Amount') }}</span>
              <span>{{ $bill->refund_amount }}  {{ __('SAR') }}</span>
            </div><!-- d-flex -->
          @endif
          <div class="d-flex align-items-center justify-content-between">
            <span>{{ __('Total amount') }}</span>
            <span>{{ $bill->total}}</span>
          </div><!-- d-flex -->
        </div><!-- bill_info -->
        @if($bill->status == 'expired')
          <div id="status">
            <div class="alert alert-danger"> {{ __('this bill has been expired', ['number' => $bill->number ]) }}</div>
          </div><!-- status -->
        @elseif($bill->status == 'paid')
          <div id="status">
            <div class="alert alert-success"> 
              @if ($bill->depositTransaction)
                {{ __('Paid') }} - {{ $bill->depositTransaction->card_brand }} {{ $bill->depositTransaction->card }} {{ $bill->depositTransaction->receipt }}
              @else
                {{ __('this bill has been successfully', ['number' => $bill->number ]) }}
              @endif
            </div>
          </div><!-- status -->
        @elseif($bill->status == 'paid_cash')
          <div id="status">
            <div class="alert alert-success"> {{ __('this bill has been Paid Cash successfully', ['number' => $bill->number ]) }}</div>
          </div><!-- status -->
        @elseif($bill->status == 'paid_bank_transfer')
          <div id="status">
            <div class="alert alert-success"> {{ __('this bill has been Paid Bank Transfer successfully', ['number' => $bill->number ]) }}</div>
          </div><!-- status -->
        @elseif($bill->status == 'canceled')
          <div id="status">
            <div class="alert alert-danger"> {{ __('this bill has been canceled', ['number' => $bill->number ]) }}</div>
          </div><!-- status -->         
        @elseif($bill->status == 'failed')
          <div id="status">
            <div class="alert alert-danger"> {{ __('this bill has been failed', ['number' => $bill->number ]) }}</div>
          </div><!-- status -->     
        @elseif(in_array($bill->status, ['refunded', 'refunded_cash', 'refunded_bank_transfer']))
          <div id="status">
            <div class="alert alert-warning"> {{ __('this bill has been refunded', ['number' => $bill->number ]) }}</div>
          </div><!-- status -->
        @endif
        @if($bill->status == 'paid' && $bill->user->settings->add_tax_invoice)
          <div class="qrCode_area">
            <a class="d-flex justify-content-center flex-column align-items-center" target="_blank" href="{{route('invoice', ['id' => $bill->pay_id])}}">
              {!! generateQRcode($bill) !!}
              <span>{{ __('Tax Invoice') }}</span>
            </a>
          </div><!-- qrCode_area -->
        @endif
      </div><!-- single_bill_content -->
    </div><!-- all_bill_page -->
  </div><!-- singlebBillSimple_page -->

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
