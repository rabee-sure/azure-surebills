@extends('layouts.bill')

@section('title', 'Page Title')

@section('content')
  <div class="single_bill_page">
    <div class="container">
      <div class="row  justify-content-center">
        <div class="col-12 col-md-8 col-lg-6 col-xl-6">
        <div class="single_bill_content">
          @if($bill->user->logo)
            <div class="logo">
              <img src="{{ url($bill->user->logo) }}" alt="logo">
            </div><!-- logo -->
          @endif
            
              @if($bill->status == 'expired')
                <div class="alert alert-secondary" role="alert">
                  this bill #{{ $bill->number }} has been expired
                </div>
              @endif
              @if($bill->status == 'paid')
                <div class="alert alert-success" role="alert">
                  this bill #{{ $bill->number }} paid successfully
                </div>
              @endif
              @if($bill->status == 'canceled')
                <div class="alert alert-danger" role="alert">
                  this bill #{{ $bill->number }} has been canceled
                </div>
              @endif
            <div class="title">
              <span>{{ $bill->business_name }}</span>
              <div>
                <p>{{  $bill->user->business_address }}</p>
                <b>{{  $bill->user->business_mobile }}</b>
              </div>
            </div><!-- title -->
            <div class="date_time">
              <span>
                Due on {{ $bill->due_date->format('M d Y')}}
                @if($bill->user->vat_registration_number)
                  <div class="vat_reg"> VAT Registration Number : {{ $bill->user->vat_registration_number }}</div>
                @endif
              </span>
              <div>
                <p>Bill #{{ $bill->number }}</p>
                <b>{{ $bill->created_at->format('Y/m/d')}}</b>
              </div>
            </div><!-- date_time -->
            <div class="shopping_cart">
              <div class="name"> @if($bill->customer_notes) {{$bill->customer_notes}}  @else Shopping Cart @endif</div>
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
                @if($bill->discount_type == 'percentage')
                  <p>Discount ({{ $bill->discount_value }}%) : {{ $bill->discount }} SAR</p>
                @else
                  <p>Discount ({{ $bill->discount_value }} SAR) : {{ $bill->discount }} SAR</p>
                @endif
                <p>Subtotal - Discount : {{ $bill->sub_total- $bill->discount }} SAR</p>
              @endif
              @if( $bill->add_tax)
                <p>{{ $bill->tax_name }} ({{ $bill->tax_value }}%) : {{ $bill->vat }} SAR</p>
              @endif
              <b>Total : {{ $bill->total}} SAR</b>
            </div><!-- total_bill -->
            <div class="customer_information">
              <div class="name">Customer Information</div>
              <p>Billed to, {{ $bill->customer_name}}</p>
              <p>+966{{ $bill->customer_mobile}}</p>
              <p>{{ $bill->customer_email}}</p>
            </div><!-- customer_information -->
          </div><!-- single_bill_content -->
        </div><!-- col-12 -->
      </div><!-- row -->
    </div><!-- container -->
  </div><!-- single_bill_page -->
@endsection


@section('footer-scripts')
    {!! JsValidator::formRequest('App\Http\Requests\PayBillRequest', '#bill_bay') !!}
@endsection
