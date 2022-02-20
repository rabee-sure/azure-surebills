@extends('layouts.app')

@section('title', __('Bill No.') . ' ' . $bill->number . ' ' . __('Bills'))

@php
    $statues = session('status_filters', ['pending', 'paid'])?? [];
    $separated = (count($statues)) ? 'statuses[]='.implode("&statuses[]=", $statues):'';
    // dd(app()->getLocale());
@endphp

@section('content') 
<div class="row">
  <div class="col-12">
    <h1>{{ __('Bill') }}</h1>
    <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
      <ol class="breadcrumb pt-0">
        <li class="breadcrumb-item">
          <a href="{{ url('/') }}" title="{{__('Home')}}">{{__('Home')}}</a>
        </li>
        <li class="breadcrumb-item">
          <a href="/bills?{{$separated}}" title="{{__('Bills')}}">{{__('Bills')}}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{__('Bill No.')}} {{ $bill->number }}</li>
      </ol>
    </nav>
    <div class="separator mb-5"></div>
  </div>
</div>

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
</div>
 <div class="row">
  <div class="col-12">
    <div class="card mb-5">
      <div class="card-body">
        <!-- Button trigger modal -->
        <button class="btn btn-primary mr-2 mb-2 d-inline-block notify-btn rounded-sm copyButton" data-toggle="tooltip" data-placement="top" title="{{ __('Copy payment link') }}" data-from="top" data-align="right">
          <img src="{{ asset('images/copy.svg') }}" alt="{{ __('Copy Link') }}" style="height: 25px;">
        </button>
        <a class="btn btn-primary mr-2 mb-2 d-inline-block rounded-sm" href="{{ $bill->pay_url}}" data-toggle="tooltip" data-placement="top" target="_blank" title="{{ __('Visit Payment Link') }}">
          <img src="{{ asset('images/link.svg') }}" alt="{{ __('Open Link') }}" style="height: 25px;">
        </a>
        @if($bill->status == 'paid' && $bill->user->settings->add_tax_invoice)
        <a class="btn btn-primary mr-2 mb-2 d-inline-block rounded-sm" href="{{ $bill->invoice_url}}" data-toggle="tooltip" data-placement="top" target="_blank" title="{{ __('Tax Invoice') }}">
          <img src="{{ asset('images/qr.svg') }}" alt="{{ __('Tax Invoice') }}" style="height: 25px;">
        </a>
        @endif
        <input class="linkToCopy" value="{{ $bill->pay_url}}" style="position: absolute; z-index: -999; opacity: 0;" />
        <a onclick="window.print(); return false;" class="btn btn-primary mr-2 mb-2 rounded-sm d-inline-block" data-toggle="tooltip" data-placement="top" href="#" title="{{ __('Print') }}">
          <img src="{{ asset('images/printer.svg') }}" alt="{{ __('Print') }}" style="height: 25px;">
        </a>
        <!-- <a class="btn btn-primary mr-2 mb-2 d-inline-block" href="#">{{ __('Send Reminder') }}</a> -->
        @if($bill->is_pending)
          <button id="cancel_btn" type="button" class="btn btn-danger mr-2 mb-2 d-inline-block rounded-sm" data-toggle="tooltip" data-placement="top" title="{{ __('Cancel Bill') }}">
            <span class="d-block" data-from="top" data-align="right" data-toggle="modal" data-target="#cancelModal">
              <img src="{{ asset('images/cancel.svg') }}" alt="{{ __('Cancel Bill') }}" style="height: 25px;">
            </span>
          </button>
        @endif 
        
        @if($bill->is_able_refund)
          <button id="refund_btn" type="button" class="btn btn-warning mr-2 mb-2 d-inline-block rounded-sm" data-toggle="tooltip" data-placement="top" title="{{ __('Refund') }}">
            <span class="d-block" data-from="top" data-align="right">
              <img src="{{ asset('images/refund.svg') }}" alt="{{ __('Refund Bill') }}" style="height: 25px;">
            </span>
          </button>
        @endif

        @if($bill->is_able_change_status)
          <button type="button" class="btn btn-success mr-2 mb-2 d-inline-block rounded-sm" data-toggle="tooltip" data-placement="top" title="{{ __('Change Status') }}" >
            <span class="d-block" data-from="top" data-align="right" data-toggle="modal" data-target="#changeStatusModal">
              <img src="{{ asset('images/change_status.svg') }}" alt="{{ __('Change Status') }}" style="height: 25px;">
            </span>
            </button>
        @endif

      </div>
    </div>
  </div>
</div>
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
          <p>تم إنشاء كود الاستجابة السريعة بواسطة حل الفوترة الإلكترونية لدافعي الضرائب وفقاً لمواصفات ZATCA.</p>
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
