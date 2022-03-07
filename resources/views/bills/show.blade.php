@extends('layouts.app')

@section('title', __('Bill No.') . ' ' . $bill->number . ' ' . __('Bills'))

@php
  $statues = session('status_filters', ['pending', 'paid'])?? [];
  $separated = (count($statues)) ? 'statuses[]='.implode("&statuses[]=", $statues):'';
  // dd(app()->getLocale());
@endphp

@section('content')

<div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm border-bottom">
  <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
  <i>/</i>
  <a href="/bills" title="{{ __('Bills') }}">{{ __('Bills') }}</a>
  <i>/</i>
  <span>{{__('Bill No.')}} {{ $bill->number }}</span>
</div><!-- breadcrump -->

<section id="billShowPage">
  <div class="title mb-4">
    <h1 class="d-block fw-bold m-0">{{ __('Bill') }}</h1>
  </div><!-- title -->

  <div id="errors">
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
  </div><!-- alert -->

  <div class="buttonsArea p-2 d-flex align-items-center justify-content-center bg-white rounded-3 border mb-3 shadow-sm">
    <button class="btn-primary p-0 m-1 rounded-3 d-flex align-items-center justify-content-center border-0 shadow-none copyButton" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Copy payment link') }}" data-from="top" data-align="right"><i class="fal fa-copy"></i></button>
    <input class="linkToCopy" value="{{ $bill->pay_url}}" style="position: absolute; z-index: -999; opacity: 0;" />
    
    <a class="btn-primary p-0 m-1 rounded-3 d-flex align-items-center justify-content-center border-0 shadow-none" href="{{ $bill->pay_url}}" data-bs-toggle="tooltip" data-bs-placement="top" target="_blank" title="{{ __('Visit Payment Link') }}"><i class="fal fa-link"></i></a>

    @if($bill->user->settings->add_tax_invoice)
      <a class="btn-primary p-0 m-1 rounded-3 d-flex align-items-center justify-content-center border-0 shadow-none" href="{{ $bill->invoice_url}}" data-bs-toggle="tooltip" data-bs-placement="top" target="_blank" title="{{ __('Tax Invoice') }}"><i class="fal fa-qrcode"></i></a>
    @endif
    
    <a onclick="window.print(); return false;" class="btn-primary p-0 m-1 rounded-3 d-flex align-items-center justify-content-center border-0 shadow-none" data-bs-toggle="tooltip" data-bs-placement="top" href="#" title="{{ __('Print') }}"><i class="fal fa-print"></i></a>

    <!-- <a class="btn-primary p-0 m-1 rounded-3 d-flex align-items-center justify-content-center border-0 shadow-none" href="#">{{ __('Send Reminder') }}</a> -->
    
    @if($bill->is_pending)
      <button id="cancel_btn" type="button" class="btn-danger p-0 m-1 rounded-3 d-flex align-items-center justify-content-center border-0 shadow-none" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Cancel Bill') }}">
        <span class="d-flex align-items-center justify-content-center w-100 h-100" data-from="top" data-align="right" data-bs-toggle="modal" data-bs-target="#cancelModal"><i class="fal fa-times-circle"></i></span>
      </button>
    @endif

    @if($bill->is_able_refund)
      <button id="refund_btn" type="button" class="btn-warning p-0 text-white m-1 rounded-3 d-flex align-items-center justify-content-center border-0 shadow-none" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Refund') }}">
        <span class="d-flex align-items-center justify-content-center w-100 h-100" data-from="top" data-align="right"><i class="fal fa-box-usd"></i></span>
      </button>
    @endif

    @if($bill->is_able_change_status)
      <button type="button" class="btn-info p-0 text-white m-1 rounded-3 d-flex align-items-center justify-content-center border-0 shadow-none" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Change Status') }}" >
        <span class="d-flex align-items-center justify-content-center w-100 h-100" data-from="top" data-align="right" data-bs-toggle="modal" data-bs-target="#changeStatusModal"><i class="fal fa-repeat"></i></span>
      </button>
    @endif
  </div><!-- buttonsArea -->

</section><!-- billShowPage -->
 

<div class="billAndStatus mb-3 invoice @if(count($bill->payment_logs) > 0) billAndStatus_twoCol @else billAndStatus_oneCol @endif">
  <div class="showBill  invoice-contents">
    <div class="about d-flex align-items-center justify-content-center flex-column">
      @if($bill->user->logo)
        <img src="{{ $bill->user->logo_url }}" alt="logo">
      @endif
      @if($bill->user->settings->add_tax_invoice)
        <div class="taxInvoiceText">{{ __('Simplified Tax Invoice') }}</div>
      @endif
      <span class="d-block font-weight-bold">{{ $bill->user->business_name }}</span>
      @if(isset($bill->user->settings->header_bill))
        <p class="d-block mb-0">{{ $bill->user->settings->header_bill }}</p>
      @endif
      <p class="d-block mb-0">{{  $bill->user->business_address }}</p>
      <b class="d-block font-weight-normal">{{  $bill->user->business_mobile }}</b>
    </div><!-- about -->
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
    <div class="table_items">
      <table>
        <thead>
          <tr>
            <th>{{ __('Description') }}</th>
            <th>{{ __('Price') }}</th>
            <th>{{ __('Quantity') }}</th>
            @if($bill->add_tax)
            <th width="35%">{{ __('Total include added tax') }}</th>
            @else
            <th width="35%">{{ __('Total') }}</th>
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
            <td>{{ ($item->product_price * $item->quantity) + (($item->product_price * $item->quantity) * $bill->tax_value / 100)  }} {{ __('SAR') }}</td>
            @else
            <td>{{ $item->product_price * $item->quantity }} {{ __('SAR') }}</td>
            @endif
          </tr>
          @endforeach
        </tbody>
      </table>
    </div><!-- table_items -->
    <div class="bill_info">
      @if( $bill->add_tax || $bill->add_discount)
        <div class="d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-start justify-content-between flex-column">
            <span>{{ __('Total amount') }}</span>
            <small>( {{ __('Exclude added tax') }} )</small>
          </div>
          <span>{{ $bill->sub_total }} {{ __('SAR') }}</span>
        </div><!-- d-flex -->
      @endif
      @if( $bill->add_discount)
      <div class="d-flex align-items-center justify-content-between">
        <span>{{ __('Discount amount') }}</span>
        <span>{{ $bill->discount }} {{ __('SAR') }}</span>
      </div><!-- d-flex -->
      @endif
      @if( $bill->user->pay_fees == 'client')
        <div class="d-flex align-items-center justify-content-between">
          <span>{{ __('payment fees') }}</span>
          <span>{{ $bill->payment_fees }}  {{ __('SAR') }}</span>
        </div><!-- d-flex -->
      @endif
      @if( $bill->add_tax)
        <div class="d-flex align-items-center justify-content-between">
          <span>{{ __('Added tax value') }}</span>
          <span>{{ $bill->vat }}  {{ __('SAR') }}</span>
        </div><!-- d-flex -->
      @endif
      @if( $bill->channel_extra_amount)
        <div class="d-flex align-items-center justify-content-between">
          <span>{{$bill->channel_extra_title}}</span>
          <span>{{ $bill->channel_extra_amount }} {{ __('SAR') }}</span>
        </div><!-- d-flex -->
      @endif
      @if( $bill->channel_extra_vat)
        <div class="d-flex align-items-center justify-content-between">
          <span>{{ __('Vat') }} ({{$bill->channel_extra_title}} ({{ $bill->tax_value }}%))</span>
          <span>{{ $bill->channel_extra_vat }} {{ __('SAR') }}</span>
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
        <span>{{ $bill->total}}  {{ __('SAR') }}</span>
      </div><!-- d-flex -->
    </div><!-- bill_info -->
    @if($bill->customer_notes)
      <div class="customer_notes">{{$bill->customer_notes}}</div> 
    @endif
    @if($bill->user->settings->add_tax_invoice)
      <div class="qrCode_area">
        <a class="d-flex justify-content-center flex-column align-items-center" target="_blank" href="{{route('invoice', ['id' => $bill->pay_id])}}">
          {!! generateQRcode($bill) !!}
          <!-- <p>تم إنشاء كود الاستجابة السريعة بواسطة حل الفوترة الإلكترونية لدافعي الضرائب وفقاً لمواصفات ZATCA.</p> -->
          <span>{{ __('Tax Invoice') }}</span>
        </a>
      </div><!-- qrCode_area -->
    @endif
  </div><!-- showBill -->
  @if(count($bill->payment_logs) > 0)
    @include('bills.partials.payment_logs')
  @endif
</div><!-- billAndStatus -->

@include('bills.partials.cancel',['bill' => $bill])
@include('bills.partials.refund',['bill' => $bill])
@include('bills.partials.change_status',['bill' => $bill])

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
    });
  </script>

@endpush
