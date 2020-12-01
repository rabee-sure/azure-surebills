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
        <li class="breadcrumb-item"><a href="{{ url('/') }}" title="{{__('Home')}}">{{__('Home')}}</a></li>
        <li class="breadcrumb-item"><a href="/bills?{{$separated}}" title="{{__('Bills')}}">{{__('Bills')}}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{__('Bill')}} {{ $bill->number }}</li>
      </ol>
    </nav>
    <div class="separator mb-5"></div>
  </div>
</div>
 <div class="row">
  <div class="col-12">
    <div class="card mb-5">
      <div class="card-body">
        <!-- Button trigger modal -->
        <button class="btn btn-primary mr-2 mb-2 d-inline-block notify-btn rounded-sm copyButton" title="{{ __('Copy Link') }}" data-from="top" data-align="right">
          <img src="{{ asset('img/copy.svg') }}" alt="{{ __('Copy Link') }}" style="height: 25px;">
        </button>
        <a class="btn btn-primary mr-2 mb-2 d-inline-block rounded-sm" href="{{ $bill->pay_url}}" target="_blanck" title="{{ __('Open Link') }}">
          <img src="{{ asset('img/link.svg') }}" alt="{{ __('Open Link') }}" style="height: 25px;">
        </a>
        <input class="linkToCopy" value="{{ $bill->pay_url}}" style="position: absolute; z-index: -999; opacity: 0;" />
        <a onclick="window.print(); return false;" class="btn btn-primary mr-2 mb-2 rounded-sm d-inline-block" href="#" title="{{ __('Print') }}">
          <img src="{{ asset('img/printer.svg') }}" alt="{{ __('Print') }}" style="height: 25px;">
        </a>
        <!-- <a class="btn btn-primary mr-2 mb-2 d-inline-block" href="#">{{ __('Send Reminder') }}</a> -->
        @if($bill->is_pending)
          <button id="cancel_btn" type="button" class="btn btn-danger mr-2 mb-2 d-inline-block rounded-sm" data-toggle="modal" data-target="#exampleModal" title="{{ __('Cancel Bill') }}" data-from="top" data-align="right">
            <img src="{{ asset('img/cancel.svg') }}" alt="{{ __('Cancel Bill') }}" style="height: 25px;">
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
          <img src="{{ url($bill->user->logo) }}" alt="{{ $bill->user->business_name}}">
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
              @endif
              @if($bill->status == 'paid')
                <div class="alert alert-success" role="alert">
                  @if ($bill->depositTransaction)
                    {{ __('Paid') }} - {{ $bill->depositTransaction->card_brand }} {{ $bill->depositTransaction->card }} {{ $bill->depositTransaction->receipt }}
                  @else
                  {{ __('this bill has been successfully', ['number' => $bill->number ]) }}
                  @endif
                </div>
              @endif
              @if($bill->status == 'canceled')
                <div class="alert alert-danger" role="alert">
                  {{ __('this bill has been canceled', ['number' => $bill->number ]) }}
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
          @if( $bill->add_tax && $bill->add_discount)
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
    <a href="/" title="Sure Bills" class="logo_bills"></a>
  </div><!-- col-12 -->
  @if(count($bill->payment_logs) > 0)
  <div class="col-12 col-md-6 col-lg-6 col-xl-6">
    <div class="card">
      <div class="card-body">
        <h2 class="mb-3">عملية الدفع</h2>
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th scope="col" width="5%"></th>
                <th scope="col">{{__('ID') }}</th>
                <th scope="col">{{__('Values') }}</th>
                <th scope="col">{{__('Date created') }}</th>
                <th scope="col" width="10%">{{__('Status') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($bill->payment_logs as $log)
                <tr>
                  @if(isset($log->results['response']) && isset($log->results['response']['paymentBrand']) && $log->results['response']['paymentBrand'] == 'MADA')
                    <td><img src="{{ asset('/payments/mada.png') }}" alt="mada" height="25px"></td>
                  @elseif(isset($log->results['response']) && isset($log->results['response']['paymentBrand']) && $log->results['response']['paymentBrand'] == 'VISA')
                    <td><img src="{{ asset('/payments/visa.png') }}" alt="visa" height="25px"></td>
                  @elseif(isset($log->results['response']) && isset($log->results['response']['paymentBrand']) && $log->results['response']['paymentBrand'] == 'MASTERCARD')
                    <td><img src="{{ asset('/payments/card.png') }}" alt="mastercard" height="25px"></td>
                  @elseif(isset($log->results['response']) && isset($log->results['response']['paymentBrand']) && $log->results['response']['paymentBrand'] == 'APPLEPAY')
                    <td><img src="{{ asset('/payments/pay.png') }}" alt="apple pay" height="25px"></td>
                  @else
                    <td><img src="{{ asset('/payments/cardnon.png') }}" alt="apple pay" height="25px"></td>
                  @endif

                  <td><a href="/logs/{{$log->id}}" title="{{ isset($log->results['response']) && isset($log->results['response']['id']) ? $log->results['response']['id'] : null }}">{{ isset($log->results['response']) && isset($log->results['response']['id']) ? $log->results['response']['id'] : null }}</a></td>
                  <td>{{ $bill->total}} {{__('SAR') }}</td>
                  <td>{{$log->created_at}}</td>
                  @if($log->status == true)
                    <td><span class="badge badge-pill badge-success bill_status_badge">{{ __('Paid') }}</span></td>
                  @else
                    <td><span class="badge badge-pill badge-danger bill_status_badge">{{ __('Failed') }}</span></td>
                  @endif
                </tr>
              @endforeach
            </tbody>
          </table>
        </div><!-- table-responsive -->
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-12 -->
  @endif
</div><!-- row -->
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">{{ __('Are you Sure to Cancel Bill ?')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-footer">
<form method="POST" action="{{ route('bills.cancel', ['id'=> $bill->id]) }}" class="repeater" id="bill_create">
  @csrf
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>

        <button type="submit" class="btn btn-primary">{{__('Cancel Bill')}}</button>
                  </form>
      </div>
    </div>
  </div>
</div>

@endsection

@push('footer-scripts')
  <script src="{{ asset('js/bootstrap-notify.min.js') }}" defer></script>
  <script>

  /* 03.12. Notification */
  function showNotification(placementFrom, placementAlign, type) {
      $.notify(
        {
          title: false,
          message: "تم نسخ الرابط",
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

  console.log('bill.{{$bill->id}}');
    Echo.channel('bill.{{$bill->id}}')
      .listen('BillStatusUpdated', (e) => {
        console.log(e.bill.id);
          switch(e.bill.status) {
            case "pending":
              break;
            case "paid":
              $("#cancel_btn").remove();
              $("#status").empty();
              $("#status").append('<div class="alert alert-success" role="alert">this bill paid successfully</div>');
              break;
            case "canceled":
              $("#cancel_btn").remove();
              $("#status").empty();
              $("#status").append('<div class="alert alert-danger" role="alert">this bill has been canceled</div>');
              break;
            case "expired":
              $("#cancel_btn").remove();
              $("#status").append('<div class="alert alert-secondary" role="alert">this bill has been expired</div>');
              break;
            default:
              $("#cancel_btn").remove();
          }
      });
  </script>
@endpush
