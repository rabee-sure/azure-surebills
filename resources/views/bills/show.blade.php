@extends('layouts.app')
@section('title', 'Page Title')
@section('content')
<div class="row">
  <div class="col-12">
    <h1>Bill</h1>
    <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
      <ol class="breadcrumb pt-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" title="{{__('Home')}}">{{__('Home')}}</a></li>
        <li class="breadcrumb-item"><a href="{{ url('bills') }}" title="{{__('Bills')}}">{{__('Bills')}}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{__('Bill')}} {{ $bill->id }}</li>
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
      <div class="logo_bill">
        <img src="img/logoCN.png" alt="{{ $bill->business_name}}">
      </div><!-- logo_bill -->
      <div class="title">
        <span>{{ $bill->business_name}}</span>
        <div>
          <p>Riyadh, Saudi Arabia</p>
          <b>0551234567</b>
        </div>
      </div><!-- title -->
      <div class="date_time">
        <span>Due on {{ $bill->due_date->format('M d Y')}}</span>
        <div>
          <p>Bill # : {{ $bill->id}}</p>
          <b>2020/04/05</b>
        </div>
      </div><!-- date_time -->
      <div class="shopping_cart">
        <div class="name">Shopping Cart</div>
        @foreach($bill->items as $item)
          <div class="details_pay">
            <div class="info">
              <p>{{ $item->product_name }}</p>
              <p>price : <time>{{ $item->product_price  }}</time></p>
              <p>quantity : <time>{{ $item->quantity  }}</time></p>
            </div><!-- info -->
            <span>{{ $item->total }}</span>
          </div><!-- details_pay -->
        @endforeach
      </div><!-- shopping_cart -->
      <div class="total_bill">
        @if( $bill->add_tax && $bill->add_discount)
          <p>Subtotal : {{ $bill->sub_total }} SAR</p>
        @endif
        @if( $bill->add_discount)
          <p>Discount : {{ $bill->discount }} SAR</p>
          <p>Subtotal - Discount : {{ $bill->sub_total- $bill->discount }} SAR</p>
        @endif
        @if( $bill->add_tax)
          <p>Tax : {{ $bill->vat }} SAR</p>
        @endif
        <b>Total : {{ $bill->total}} SAR</b>
      </div><!-- total_bill -->
      <div class="customer_information">
        <div class="name">Customer Information</div>
        <p>Billed to, {{ $bill->customer_name}}</p>
        <p>+966{{ $bill->customer_mobile}}</p>
        <p>{{ $bill->customer_email}}</p>
      </div><!-- customer_information -->
      <div class="bottom_link">http://bills.test/bills/16#</div>
    </div><!-- show_bill_general -->  
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