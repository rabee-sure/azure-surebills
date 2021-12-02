@extends('layouts.app')

@section('title', __('Bill') . ' ' . $bill->number . ' ' . __('Bills'))

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
        <li class="breadcrumb-item active" aria-current="page">{{__('Bill')}} {{ $bill->number }}</li>
      </ol>
    </nav>
    <div class="separator mb-5"></div>
  </div>
</div>
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
    <div class="card mb-5">
      <div class="card-body">
        <!-- Button trigger modal -->
        <button class="btn btn-primary mr-2 mb-2 d-inline-block notify-btn rounded-sm copyButton" title="{{ __('Copy Link') }}" data-from="top" data-align="right">
          <img src="{{ asset('images/copy.svg') }}" alt="{{ __('Copy Link') }}" style="height: 25px;">
        </button>
        <a class="btn btn-primary mr-2 mb-2 d-inline-block rounded-sm" href="{{ $bill->pay_url}}" target="_blank" title="{{ __('Open Link') }}">
          <img src="{{ asset('images/link.svg') }}" alt="{{ __('Open Link') }}" style="height: 25px;">
        </a>
        <a class="btn btn-primary mr-2 mb-2 d-inline-block rounded-sm" href="{{ $bill->invoice_url}}" target="_blank" title="{{ __('Open Link') }}">
          <img src="{{ asset('images/qr.svg') }}" alt="{{ __('Open Link') }}" style="height: 25px;">
        </a>
        <input class="linkToCopy" value="{{ $bill->pay_url}}" style="position: absolute; z-index: -999; opacity: 0;" />
        <a onclick="window.print(); return false;" class="btn btn-primary mr-2 mb-2 rounded-sm d-inline-block" href="#" title="{{ __('Print') }}">
          <img src="{{ asset('images/printer.svg') }}" alt="{{ __('Print') }}" style="height: 25px;">
        </a>
        <!-- <a class="btn btn-primary mr-2 mb-2 d-inline-block" href="#">{{ __('Send Reminder') }}</a> -->
        @if($bill->is_pending)
          <button id="cancel_btn" type="button" class="btn btn-danger mr-2 mb-2 d-inline-block rounded-sm" data-toggle="modal" data-target="#cancelModal" title="{{ __('Cancel Bill') }}" data-from="top" data-align="right">
            <img src="{{ asset('images/cancel.svg') }}" alt="{{ __('Cancel Bill') }}" style="height: 25px;">
          </button>
        @endif 
        @if($bill->is_able_refund)
          <button id="cancel_btn" type="button" class="btn btn-warning mr-2 mb-2 d-inline-block rounded-sm" data-toggle="modal" data-target="#refundModal" title="{{ __('Refund Bill') }}" data-from="top" data-align="right">
              <img src="{{ asset('images/refund.svg') }}" alt="{{ __('Refund Bill') }}" style="height: 25px;">
            </button>
        @endif

        @if($bill->is_able_change_status)
          <button type="button" class="btn btn-success mr-2 mb-2 d-inline-block rounded-sm" data-toggle="modal" data-target="#changeStatusModal" title="{{ __('Change Status') }}" data-from="top" data-align="right">
              <img src="{{ asset('images/change_status.svg') }}" alt="{{ __('Change Status') }}" style="height: 25px;">
            </button>
        @endif

      </div>
    </div>
  </div>
</div>
<div class="row justify-content-center invoice">
<div class="col-12 col-md-6 col-lg-6 col-xl-6">
    <div class="show_bill_general invoice-contents">
      @if($bill->user->logo)
        <div class="logo_bill">
          <img src="{{ $bill->user->logo_url }}" alt="{{ $bill->user->business_name}}">
        </div><!-- logo_bill -->
      @endif
      <div class="title">
        <span>{{ $bill->user->business_name}}</span>

        @if(isset($bill->user->settings->header_bill))
          <p>{{ $bill->user->settings->header_bill }}</p>
        @endif

        <p>{{  $bill->user->business_address}}</p>
        <b>{{  $bill->user->business_mobile }}</b>
      </div><!-- title -->
      
      <div id="status">
          @if($bill->status == 'expired')
            <div class="alert alert-danger" role="alert">
              {{ __('this bill has been expired', ['number' => $bill->number ]) }}
            </div>
        @elseif($bill->status == 'paid')
            <div class="alert alert-success" role="alert">
              @if ($bill->depositTransaction)
                {{ __('Paid') }} - {{ $bill->depositTransaction->card_brand }} {{ $bill->depositTransaction->card }} {{ $bill->depositTransaction->receipt }}
              @else
              {{ __('this bill has been successfully', ['number' => $bill->number ]) }}
              @endif
            </div>
        @elseif($bill->status == 'paid_cash')
            <div class="alert alert-success" role="alert">
              {{ __('this bill has been Paid Cash successfully', ['number' => $bill->number ]) }}
            </div>
        @elseif($bill->status == 'paid_bank_transfer')
            <div class="alert alert-success" role="alert">
              {{ __('this bill has been Paid Bank Transfer successfully', ['number' => $bill->number ]) }}
            </div>
        @elseif($bill->status == 'canceled')
            <div class="alert alert-danger" role="alert">
              {{ __('this bill has been canceled', ['number' => $bill->number ]) }}
            </div>
        @elseif(in_array($bill->status, ['refunded', 'refunded_cash', 'refunded_bank_transfer']))
            <div class="alert alert-warning" role="alert">
              {{ __('this bill has been refunded', ['number' => $bill->number ]) }}
            </div>
          @endif
      </div>
      <div class="date_time">
        <span>
          {{__('Due on')}} {{ $bill->dateLocalization()}}
          @if($bill->user->vat_registration_number)
            <div class="vat_reg"> {{ __('VAT Registration Number') }} : {{ $bill->user->vat_registration_number }}</div>
          @endif
        </span>
        <div>
          <p>{{ __('Bill') }} #{{ $bill->number }}</p>
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
              <p>{{ __('Discount') }} ({{ $bill->discount_value }} {{ __('SAR') }}) : {{ $bill->discount }} {{ __('SAR') }}</p>
            @endif
            <p>{{ __('Subtotal - Discount') }} : {{ $bill->sub_total- $bill->discount }} {{ __('SAR') }}</p>
          @endif
          @if( $bill->add_tax)
            <p>{{ __('Vat') }} ({{ $bill->tax_value }}%) : {{ $bill->vat }} {{ __('SAR') }}</p>
          @endif

          @if( $bill->channel_extra_amount)
            <p> {{$bill->channel_extra_title}}  : {{ $bill->channel_extra_amount }} {{ __('SAR') }}</p>
          @endif

          @if( $bill->channel_extra_vat)
            <p> {{ __('Vat') }} ({{$bill->channel_extra_title}} ({{ $bill->tax_value }}%))  : {{ $bill->channel_extra_vat }} {{ __('SAR') }}</p>
          @endif

          @if( $bill->refund_amount)
            <p>{{ __('Refund Amount') }} : {{ $bill->refund_amount }}  {{ __('SAR') }}</p>
          @endif
          <b>{{ __('Total') }} : {{ $bill->total}} {{ __('SAR') }}</b>
      </div><!-- total_bill -->
      @if($bill->customer_notes)<div class="customer_notes">{{$bill->customer_notes}}</div> @endif
      <div class="customer_information">
        <!-- <div class="name">Customer Information</div> -->
        <p>{{ __('Billed to,') }} {{ $bill->customer_name}}</p>
        <p class="ltr">+966{{ $bill->customer_mobile}}</p>
        <p>{{ $bill->customer_email}}</p>

        @if(isset($bill->user->settings->footer_bill))
          <p>{{ $bill->user->settings->footer_bill }}</p>
        @endif
      </div><!-- customer_information -->
    </div><!-- show_bill_general -->
    <a title="Sure Bills" class="logo_bills"></a>
  </div><!-- col-12 -->
  @if(count($bill->payment_logs) > 0)
    @include('bills.partials.payment_logs')
  @endif
</div><!-- row -->



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

@endpush
