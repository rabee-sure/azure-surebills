@extends('layouts.app')
@section('title', 'Page Title')
@section('content')
<div class="row">
  <div class="col-12">
    <h1>{{ __('Bill') }}</h1>
    <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
      <ol class="breadcrumb pt-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" title="{{__('Home')}}">{{__('Home')}}</a></li>
        <li class="breadcrumb-item"><a href="{{ url('bills') }}" title="{{__('Bills')}}">{{__('Bills')}}</a></li>
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
        <a class="btn btn-info mr-2 mb-2 d-inline-block" href="{{ $bill->pay_url}}" target="_blanck" title="{{ __('Open Link') }}">{{ __('Open Link') }}</a>
        <button class="btn btn-info mr-2 mb-2 d-inline-block copyButton">{{ __('Copy Link') }}</button>
        <input class="linkToCopy" value="{{ $bill->pay_url}}" style="position: absolute; z-index: -999; opacity: 0;" />
        <a onclick="window.print(); return false;" class="btn btn-info mr-2 mb-2 d-inline-block" href="#" title="{{ __('Print') }}">{{ __('Print') }}</a>
        <!-- <a class="btn btn-info mr-2 mb-2 d-inline-block" href="#">{{ __('Send Reminder') }}</a> -->
      </div>
    </div>
  </div>
</div>
<div class="row justify-content-center invoice">
  <div class="col-12 col-md-8 col-lg-6 col-xl-6">
    <div class="show_bill_general invoice-contents">
      @if($bill->user->logo)
        <div class="logo_bill">
          <img src="{{ url($bill->user->logo) }}" alt="{{ $bill->business_name}}">
        </div><!-- logo_bill -->
      @endif
      <div class="title">
        <span>{{ $bill->business_name}}</span>
        <p>{{  $bill->user->business_address}}</p>
        <b>{{  $bill->user->business_mobile }}</b>
      </div><!-- title -->
      <div class="date_time">
        <span>
          {{__('Due on')}} {{ $bill->due_date->format('M d Y')}}
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
            <p>{{ $item->product_name }}</p>
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
        <p>+966{{ $bill->customer_mobile}}</p>
        <p>{{ $bill->customer_email}}</p>
      </div><!-- customer_information -->
    </div><!-- show_bill_general -->  
    <a href="https://bills.surepay.sa" target="_blank" title="Sure Bills" class="logo_bills"></a>
  </div><!-- col-12 -->
</div><!-- row -->
@endsection

@section('footer-scripts')
  <script>
    $(document).on("click", '.copyButton', function() {
       $(this).siblings('input.linkToCopy').select();      
        document.execCommand("copy");
    });
  </script>
@endsection
