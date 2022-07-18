@extends('layouts.app')

@section('title', __('Bill No.') . ' ' . $bill->number . ' ' . __('Bills'))

@php
  $statues = session('status_filters', ['pending', 'paid'])?? [];
  $separated = (count($statues)) ? 'statuses[]='.implode("&statuses[]=", $statues):'';
  // dd(app()->getLocale());
@endphp

@section('content')

<div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm d-print-none">
  <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
  <i>/</i>
  <a href="/bills" title="{{ __('Bills') }}">{{ __('Bills') }}</a>
  <i>/</i>
  <span>{{__('Bill No.')}} {{ $bill->number }}</span>
</div><!-- breadcrump -->

<section id="billShowPage">
  <div class="title mb-4 d-print-none">
    <h1 class="d-block fw-bold m-0 fs-5">{{ __('Bill') }}</h1>
  </div><!-- title -->

  <div id="errors" class="d-print-none">
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div><!-- table_items -->
    @endif
  </div><!-- alert -->

  <div class="buttonsArea p-2 d-flex align-items-center justify-content-center bg-white rounded-3 mb-3 shadow-sm d-print-none">
    <button class="btn-primary p-0 m-1 rounded-3 d-flex align-items-center justify-content-center border-0 shadow-none copyButton" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Copy payment link') }}" data-from="top" data-align="right"><i class="fal fa-copy"></i></button>
    <input class="linkToCopy" value="{{ $bill->pay_url}}" style="position: absolute; z-index: -999; opacity: 0;" />

    <a class="btn-primary p-0 m-1 rounded-3 d-flex align-items-center justify-content-center border-0 shadow-none" href="{{ $bill->pay_url}}" data-bs-toggle="tooltip" data-bs-placement="top" target="_blank" title="{{ __('Visit Payment Link') }}"><i class="fal fa-link"></i></a>

    @if($bill->user->settings->add_tax_invoice)
      <a class="btn-primary p-0 m-1 rounded-3 d-flex align-items-center justify-content-center border-0 shadow-none" href="{{ $bill->invoice_url}}" data-bs-toggle="tooltip" data-bs-placement="top" target="_blank" title="{{ __('Tax Invoice') }}"><i class="fal fa-qrcode"></i></a>
    @endif

    <!-- <a onclick="window.print(); return false;" class="btn-primary p-0 m-1 rounded-3 d-flex align-items-center justify-content-center border-0 shadow-none" data-bs-toggle="tooltip" data-bs-placement="top" href="#" title="{{ __('Print') }}"><i class="fal fa-print"></i></a> -->

    <!-- <a class="btn-primary p-0 m-1 rounded-3 d-flex align-items-center justify-content-center border-0 shadow-none" href="#">{{ __('Send Reminder') }}</a> -->

    @can('cancel bill')
    @if($bill->is_pending)
      <button id="cancel_btn" type="button" class="btn-danger p-0 m-1 rounded-3 d-flex align-items-center justify-content-center border-0 shadow-none" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Cancel Bill') }}">
        <span class="d-flex align-items-center justify-content-center w-100 h-100" data-from="top" data-align="right" data-bs-toggle="modal" data-bs-target="#cancelModal"><i class="fal fa-times-circle"></i></span>
      </button>
    @endif
    @endcan

    @can('refund bill')
    @if($bill->is_able_refund)
      <button id="refund_btn" type="button" class="btn-warning p-0 text-white m-1 rounded-3 d-flex align-items-center justify-content-center border-0 shadow-none" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Refund') }}">
        <span class="d-flex align-items-center justify-content-center w-100 h-100" data-from="top" data-align="right"><i class="fal fa-box-usd"></i></span>
      </button>
    @endif
    @endcan

    @can('change bill status')
    @if($bill->is_able_change_status)
      <button type="button" class="btn-info p-0 text-white m-1 rounded-3 d-flex align-items-center justify-content-center border-0 shadow-none" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Change Status') }}" >
        <span class="d-flex align-items-center justify-content-center w-100 h-100" data-from="top" data-align="right" data-bs-toggle="modal" data-bs-target="#changeStatusModal"><i class="fal fa-repeat"></i></span>
      </button>
    @endif
    @endcan
  </div><!-- buttonsArea -->

  <div class="row justify-content-center">
    <div class="col-12 col-md-6">
      <div class="showBill mb-3 bg-white shadow-sm rounded-3 p-2">
        <div class="aboutUser d-flex align-items-center justify-content-center flex-column">
          @if($bill->user->logo)
            <figure class="my-2">
              <img src="{{ $bill->user->logo_url }}" alt="{{ $bill->user->business_name }}" class="mw-100">
            </figure><!-- figure -->
          @endif
          @if($bill->user->settings->add_tax_invoice)
            <div class="taxInvoiceText text-secondary">{{ __('Simplified Tax Invoice') }}</div>
          @endif
          <span class="d-block fw-bold mt-3">{{ $bill->user->business_name }}</span>
          @if(isset($bill->user->settings->header_bill))
            <p class="d-block mb-0">{{ $bill->user->settings->header_bill }}</p>
          @endif
          <p class="d-block mb-0">{{  $bill->user->business_address }}</p>
          <b class="d-block fw-normal mb-2">{{  $bill->user->business_mobile }}</b>
        </div><!-- aboutUser -->
        <div id="status">
          @if($bill->status == 'expired')
            <div class="alert alert-danger"> {{ __('this bill has been expired', ['number' => $bill->number ]) }}</div>
          @elseif($bill->status == 'paid')
            <div class="alert alert-success text-center">
              @if ($bill->depositTransaction)
                {{ __('Paid') }} - {{ $bill->depositTransaction->card_brand }} {{ $bill->depositTransaction->card }} {{ $bill->depositTransaction->receipt }}
              @else
                {{ __('this bill has been successfully', ['number' => $bill->number ]) }}
              @endif
            </div>
          @elseif($bill->status == 'paid_cash')
            <div class="alert alert-success text-center"> {{ __('this bill has been Paid Cash successfully', ['number' => $bill->number ]) }}</div>
          @elseif($bill->status == 'paid_bank_transfer')
            <div class="alert alert-success text-center"> {{ __('this bill has been Paid Bank Transfer successfully', ['number' => $bill->number ]) }}</div>
          @elseif($bill->status == 'canceled')
            <div class="alert alert-danger text-center"> {{ __('this bill has been canceled', ['number' => $bill->number ]) }}</div>
          @elseif($bill->status == 'failed')
            <div class="alert alert-danger text-center"> {{ __('this bill has been failed', ['number' => $bill->number ]) }}</div>
          @elseif(in_array($bill->status, ['refunded', 'refunded_cash', 'refunded_bank_transfer']))
            <div class="alert alert-warning text-center"> {{ __('this bill has been refunded', ['number' => $bill->number ]) }}</div>
          @endif
        </div><!-- status -->
        <div class="billInfo pt-2 mt-2 borderTop">
          @if($bill->user->settings->add_tax_invoice)
            <div class="d-flex align-items-center justify-content-between">
              <span class="d-block mb-2">{{ __('Bill No.') }}</span>
              <span class="d-block mb-2">{{ $bill->number }}</span>
            </div><!-- d-flex -->
            <div class="d-flex align-items-center justify-content-between">
              <span class="d-block mb-2">{{ __('Date') }}</span>
              <span class="d-block mb-2">{{ $bill->created_at->format('d/m/Y')}}</span>
            </div><!-- d-flex -->
            @if($bill->user->vat_registration_number)
              <div class="d-flex align-items-center justify-content-between">
                <span class="d-block mb-2">{{ __('Organization VAT Registration Number') }}</span>
                <span class="d-block mb-2">{{ $bill->user->vat_registration_number }}</span>
              </div><!-- d-flex -->
            @endif
          @else
            <div class="d-flex align-items-center justify-content-between">
              <span class="d-block mb-2">{{ __('No.') }}</span>
              <span class="d-block mb-2">{{ $bill->number }}</span>
            </div><!-- d-flex -->
            <div class="d-flex align-items-center justify-content-between">
              <span class="d-block mb-2">{{ __('Date') }}</span>
              <span class="d-block mb-2">{{ $bill->created_at->format('d/m/Y')}}</span>
            </div><!-- d-flex -->
          @endif
          @if($bill->user->settings->display_customer_details)
            <div class="d-flex align-items-center justify-content-between">
              <span class="d-block mb-2">{{ __('Customer Name') }}</span>
              <span class="d-block mb-2">{{ $bill->customer->name }}</span>
            </div><!-- d-flex -->
            <div class="d-flex align-items-center justify-content-between">
              <span class="d-block mb-2">{{ __('Mobile Number') }}</span>
              <span class="d-block mb-2">{{ $bill->customer->mobile }}</span>
            </div><!-- d-flex -->
          @endif
        </div><!-- billInfo -->
        <div class="tableItems pt-2 borderTop">
          <table class="w-100">
            <thead>
              <tr>
                <th class="p-1 text-start">{{ __('Description') }}</th>
                <th class="p-1 text-center">{{ __('Price') }}</th>
                <th class="p-1 text-center">{{ __('Quantity') }}</th>
                @if($bill->add_tax)
                  <th th width="35%" class="p-1 text-end">{{ __('Total include added tax') }}</th>
                @else
                  <th width="35%" class="p-1 text-end">{{ __('Total') }}</th>
                @endif
              </tr>
            </thead>
            <tbody>
              @foreach($bill->items as $item)
              <tr>
                <td class="p-1 text-start">{!! $item->product_name !!}</td>
                <td class="p-1 text-center">{{ $item->product_price  }}</td>
                <td class="p-1 text-center">{{ $item->quantity  }}</td>
                @if( $bill->add_tax)
                  <td class="p-1 text-end">{{ ($item->product_price * $item->quantity) + (($item->product_price * $item->quantity) * $bill->tax_value / 100)  }}</td>
                @else
                  <td class="p-1 text-end">{{ $item->product_price * $item->quantity }}</td>
                @endif
              </tr>
              @endforeach
            </tbody>
          </table>
        </div><!-- tableItems -->
        <div class="billInfo pt-2 mt-2 borderTop">
          @if( $bill->add_tax || $bill->add_discount)
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-start justify-content-between flex-column mb-2">
                <span class="d-block">{{ __('Total amount') }}({{ __('SAR') }})</span>
                @if( $bill->add_tax)
                  <small class="d-block text-muted mt-1">( {{ __('Exclude added tax') }} )</small>
                @endif
              </div>
              <span class="d-block mb-2">{{ $bill->sub_total }}</span>
            </div><!-- d-flex -->
          @endif
          @if( $bill->add_discount)
            <div class="d-flex align-items-center justify-content-between">
              <span class="d-block mb-2">{{ __('Discount amount') }}({{ __('SAR') }})</span>
              <span class="d-block mb-2">{{ $bill->discount }}</span>
            </div><!-- d-flex -->
          @endif
          @if( $bill->user->pay_fees == 'client')
            <div class="d-flex align-items-center justify-content-between">
              <span class="d-block mb-2">{{ __('payment fees') }}({{ __('SAR') }})</span>
              <span class="d-block mb-2">{{ $bill->payment_fees }}</span>
            </div><!-- d-flex -->
          @endif
          @if( $bill->add_tax)
            <div class="d-flex align-items-center justify-content-between">
              <span class="d-block mb-2">{{ __('Added tax value (:percentge %)', ['percentge'=>$bill->tax_value]) }} ({{ __('SAR') }})</span>
              <span class="d-block mb-2">{{ $bill->vat }}</span>
            </div><!-- d-flex -->
          @endif
          @if( $bill->channel_extra_amount)
            <div class="d-flex align-items-center justify-content-between">
              <span class="d-block mb-2">{{$bill->channel_extra_title}}({{ __('SAR') }})</span>
              <span class="d-block mb-2">{{ $bill->channel_extra_amount }}</span>
            </div><!-- d-flex -->
          @endif
          @if( $bill->channel_extra_vat)
            <div class="d-flex align-items-center justify-content-between">
              <span class="d-block mb-2">{{ __('Vat') }} ({{$bill->channel_extra_title}} ({{ $bill->tax_value }}%))</span>
              <span class="d-block mb-2">{{ $bill->channel_extra_vat }} {{ __('SAR') }}</span>
            </div><!-- d-flex -->
          @endif
          @if( $bill->refund_amount)
            <div class="d-flex align-items-center justify-content-between">
              <span class="d-block mb-2">{{ __('Refund Amount') }}({{ __('SAR') }})</span>
              <span class="d-block mb-2">{{ $bill->refund_amount }}</span>
            </div><!-- d-flex -->
          @endif
          <div class="d-flex align-items-center justify-content-between">
            <span class="d-block mb-2">{{ __('Total amount') }}({{ __('SAR') }})</span>
            <span class="d-block mb-2">{{ $bill->total}}</span>
          </div><!-- d-flex -->
        </div><!-- bill_info -->
        @if($bill->customer_notes)
          <div class="customer_notes pt-2 mt-2 borderTop">{{$bill->customer_notes}}</div>
        @endif
        @if($bill->user->settings->add_tax_invoice)
          <div class="qrCode mt-2 pt-2 borderTop">
            <a class="d-flex justify-content-center flex-column align-items-center" target="_blank" href="{{route('invoice', ['id' => $bill->pay_id])}}">
              {!! generateQRcode($bill) !!}
              <!-- <p>تم إنشاء كود الاستجابة السريعة بواسطة حل الفوترة الإلكترونية لدافعي الضرائب وفقاً لمواصفات ZATCA.</p> -->
              <span class="d-block text-body">{{ __('Tax Invoice') }}</span>
            </a>
          </div><!-- qrCode -->
        @endif
        @if(isset($bill->user->settings->footer_bill))
          <p class="d-block mb-0 mt-2 text-center">{{ $bill->user->settings->footer_bill }}</p>
        @endif
      </div><!-- showBill -->
    </div><!-- col-12 -->
    <div class="col-12 col-md-6 d-print-none">
      <div class="viewPrintOptions bg-white shadow-sm rounded-3 p-3 mb-3">
        <div class="row justify-content-center">
          <div class="col-12 col-md-9">
            <div class="row">
              <div class="col-6">
                <div class="item d-flex align-items-center justify-content-between rounded-3">
                  <label for="billA4" class="w-50 m-1 position-relative">
                    <input type="radio" name="type" id="billA4" value="billA4" class="start-0 top-0 position-absolute w-100 h-100" checked>
                    <span class="d-flex align-items-center justify-content-center rounded-3">A4</span>
                  </label>
                  <label for="billTh" class="w-50 m-1 position-relative">
                    <input type="radio" name="type" id="billTh" value="billTh" class="start-0 top-0 position-absolute w-100 h-100">
                    <span class="d-flex align-items-center justify-content-center rounded-3">Thermal</span>
                  </label>
                </div><!-- item -->
              </div><!-- col-12 -->
              <div class="col-6">
                <div class="item d-flex align-items-center justify-content-between rounded-3">
                  <label for="billAr" class="w-50 m-1 position-relative">
                    <input type="radio" name="lang" id="billAr" value="ar" class="start-0 top-0 position-absolute w-100 h-100" checked>
                    <span class="d-flex align-items-center justify-content-center rounded-3">عربي</span>
                  </label>
                  <label for="billEn" class="w-50 m-1 position-relative">
                    <input type="radio" name="lang" id="billEn" value="en" class="start-0 top-0 position-absolute w-100 h-100">
                    <span class="d-flex align-items-center justify-content-center rounded-3">English</span>
                  </label>
                </div><!-- item -->
              </div><!-- col-12 -->
            </div><!-- row -->
          </div><!-- col-12 -->
        </div><!-- row -->
        <div id="printBillBtn" class="d-flex align-items-center justify-content-center mt-3">
          <span class="d-flex align-items-center justify-content-center text-center border rounded-3 bg-light text-body">Print Receipt</span>
        </div><!-- printBillBtn -->
        <iframe id="ifrPaySlip"  name="ifrPaySlip" scrolling="yes" style="display:none"></iframe>
      </div><!-- viewPrintOptions -->
      @if(count($bill->payment_logs) > 0)
        @include('bills.partials.payment_logs')
      @endif
    </div><!-- col-12 -->
  </div><!-- row -->

</section><!-- billShowPage -->

@can('cancel bill')
@if($bill->is_pending)
@include('bills.partials.cancel',['bill' => $bill])
@endif
@endcan

@can('refund bill')
@if($bill->is_able_refund)
@include('bills.partials.refund',['bill' => $bill])
@endif
@endcan

@can('change bill status')
@if($bill->is_able_change_status)
@include('bills.partials.change_status',['bill' => $bill])
@endif
@endcan

@endsection

@push('footer-scripts')

  <script src="{{ asset('js/bootstrap-notify.min.js') }}" defer></script>
  <script>
  /* 03.12. Notification */
  function showNotification(placementFrom, placementAlign, type) {
      $.notify(
        {
          title: false,
          message: "{{__('link is copied')}}",
          target: "_blank"
        },
        {
          element: "body",
          position: null,
          type: type,
          allow_dismiss: true,
          newest_on_top: false,
          showProgressbar: false,
          placement: {
            from: placementFrom,
            align: placementAlign
          },
          offset: 20,
          spacing: 10,
          z_index: 1031,
          delay: 4000,
          timer: 1000,
          url_target: "_blank",
          mouse_over: null,
          animate: {
            enter: "animated fadeInDown",
            exit: "animated fadeOutUp"
          },
          onShow: null,
          onShown: null,
          onClose: null,
          onClosed: null,
          icon_type: "class",
          template:
            '<div data-notify="container" class="col-11 col-sm-3 alert  alert-{0} " role="alert">' +
            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
            '<span data-notify="icon"></span> ' +
            '<span data-notify="title">{1}</span> ' +
            '<span data-notify="message">{2}</span>' +
            '<div class="progress" data-notify="progressbar">' +
            '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
            "</div>" +
            '<a href="{3}" target="{4}" data-notify="url"></a>' +
            "</div>"
        }
      );
    }

    $("body").on("click", ".notify-btn", function (event) {
      event.preventDefault();
      showNotification($(this).data("from"), $(this).data("align"), "primary");
    });


    $(document).on("click", '.copyButton', function() {
       $(this).siblings('input.linkToCopy').select();
        document.execCommand("copy");
    });


    Echo.channel('bill.{{$bill->id}}')
      .listen('BillStatusUpdated', (e) => {
          switch(e.bill.status) {
            case "pending":
              break;
            case "paid":
              $("#cancel_btn").remove();
              $("#status").empty();
              $("#status").append('<div class="alert alert-success" role="alert">{{ __("this bill is paid successfully") }}</div>');
              break;
            case "canceled":
              $("#cancel_btn").remove();
              $("#status").empty();
              $("#status").append('<div class="alert alert-danger" role="alert">{{ __("this bill is canceled") }}</div>');
              break;
            case "expired":
              $("#cancel_btn").remove();
              $("#status").empty();
              $("#status").append('<div class="alert alert-danger" role="alert">{{  __('this bill has been expired', ['number' => $bill->number ]) }}</div>');
              break;
            default:
              $("#cancel_btn").remove();
          }
      });
  </script>

  <script>
    $(document).ready(function(){

      var billType = $('input[type=radio][name=type]').val();
      var billLang = $('input[type=radio][name=lang]').val();
      var billId = '{{$bill->id}}';
      var base_url = "{{url('/')}}";

      $("#refund_btn").click(function(){
        console.log('refund');
        var otherDate = '{{\Carbon\Carbon::now()->subDays(14)}}';
        var billPaidAt = '{{$bill->paid_at}}';

        if(otherDate > billPaidAt){
          $("#errors").append('<div id="limitdays" class="alert alert-danger" role="alert">{{  __('It must not pass more than 14 days on the date of payment of the Bill') }}</div>');

          setTimeout(function() {
                $("#limitdays").remove();
          }, 4000);
        }else{
          $('#refundModal').modal('show');
        }
      });

      $('input[type=radio][name=type]').change(function() {
        billType = this.value;
      });
      $('input[type=radio][name=lang]').change(function() {
        billLang = this.value
      });
      $('#printBillBtn').click(function() {
        var newWin = window.frames[0];
            newWin.document.write('<body><iframe style="position:fixed; top:0px; left:0px; bottom:0px; right:0px; width:100%; height:100%; border:none; margin:0; padding:0; overflow:hidden; z-index:999999;" src="'+base_url+'/bills/'+billId+'/print?type='+billType+'&lang='+billLang+'"></body>');
            newWin.document.close();
      });

    });
  </script>

@endpush
