@extends('layouts.app')

@section('title', __('Credit Note') . ' CN' . $refundedbill->number . ' ' . __('Bills'))

@push('css_styles')
  <link rel="stylesheet" href="{{ asset('assets/v2/vendor/css/pages/app-invoice.css') }}?v={{ config('app.asset_version') }}" />
@endpush

@section('content')

  <h4 class="mb-1">{{ __('Credit Note') }}</h4>

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-custom-icon mb-6">
      <li class="breadcrumb-item">
        <a href="{{ url('bills') }}" title="{{ __('Bills') }}">{{ __('Bills')}}</a>
        <i class="breadcrumb-icon icon-base ti ti-chevron-right align-middle icon-xs"></i>
      </li>
      <li class="breadcrumb-item active">{{__('Credit Note No.')}} CN{{ $refundedbill->number }}</li>
    </ol>
  </nav>

  <div id="errors" class="d-print-none">
    @if ($errors->any())
      <div class="alert alert-danger mb-6">
        <ul class="list-group">
          @foreach ($errors->all() as $error)
            <li class="list-group-item list-group-item-danger">{{ $error }}</li>
          @endforeach
        </ul>
      </div><!-- alert -->
    @endif
  </div><!-- alert -->

  <div class="row g-6">
    <div class="col-12 col-md-6 invoice-preview">
      <div class="card invoice-preview-card p-5">

        <div class="card-body invoice-preview-header rounded p-5 mb-5">
          @if($refundedbill->bill->user->logo)
            <span class="app-brand-logo d-flex align-items-center justify-content-center mb-4">
              <img src="{{ $refundedbill->bill->user->logo_url }}" alt="{{ $refundedbill->bill->user->business_name }}" class="w-auto" height="32px">
            </span>
          @endif
          <div class="text-heading mb-xl-0 mb-5 d-flex flex-column gap-2 text-center">
            @if($refundedbill->bill->user->settings->add_tax_invoice)
              <p class="m-0">{{ __('Tax credit note') }}</p>
            @endif
            <p class="m-0">{{ $refundedbill->bill->user->business_name }}</p>
            @if(isset($refundedbill->bill->user->settings->header_bill))
              <p class="m-0">{{ $refundedbill->bill->user->settings->header_bill }}</p>
            @endif
            @if($refundedbill->bill->business_city)
              <p class="m-0">{{  $refundedbill->bill->business_city }}</p>
            @endif
            @if($refundedbill->bill->business_mobile)
              <p class="m-0">{{  $refundedbill->bill->business_mobile }}</p>
            @endif
          </div>
        </div><!-- card-body -->

        <div id="status">
          @if($refundedbill->method == 'online')
            <div class="alert alert-warning text-center text-capitalize mb-5"> {{ __('Refunded') }}</div>
          @elseif($refundedbill->method == 'cash')
            <div class="alert alert-warning text-center text-capitalize mb-5"> {{ __('Refunded Cash') }}</div>
          @elseif($refundedbill->method == 'bank_transfer')
            <div class="alert alert-warning text-center text-capitalize mb-5"> {{ __('Refunded Bank Transfer') }}</div>
          @endif
        </div><!-- status -->

        <div class="d-flex flex-column gap-3">
          <div class="d-flex align-items-center justify-content-between gap-2">
            <p class="mb-0">{{ __('Credit Note Date') }}</p>
            <p class="mb-0">{{ $refundedbill->created_at->format('d/m/Y')}}</p>
          </div><!-- d-flex -->
          <div class="d-flex align-items-center justify-content-between gap-2">
            <p class="mb-0">{{ __('Credit Note Number') }}</p>
            <p class="mb-0">CN{{ $refundedbill->number }}</p>
          </div><!-- d-flex -->
          <div class="d-flex align-items-center justify-content-between gap-2">
            <p class="mb-0">{{ __('Invoice Number') }}</p>
            <p class="mb-0"><a href="{{route('bills.show', $refundedbill->bill)}}" title="{{__('Bill')}} {{ $refundedbill->bill->number }} - {{ $refundedbill->bill->customer_name}}" target="_blank"><span>{{ $refundedbill->bill->number }}</span></a></p>
          </div><!-- d-flex -->
          <div class="d-flex align-items-center justify-content-between gap-2">
            <p class="mb-0">{{ __('Invoice Date') }}</p>
            <p class="mb-0">{{ $refundedbill->bill->created_at->format('d/m/Y')}}</p>
          </div><!-- d-flex -->
          @if($refundedbill->bill->user->settings->display_customer_details && $refundedbill->bill->customer_mobile != 555555555)
            <div class="d-flex align-items-center justify-content-between gap-2">
              <p class="mb-0">{{ __('Customer Name') }}</p>
              <p class="mb-0">{{ $refundedbill->bill->customer_name }}</p>
            </div><!-- d-flex -->
            <div class="d-flex align-items-center justify-content-between gap-2">
              <p class="mb-0">{{ __('Mobile Number') }}</p>
              <p class="mb-0">{{ $refundedbill->bill->customer_mobile }}</p>
            </div><!-- d-flex -->
          @endif
          <div class="d-flex align-items-center justify-content-between gap-2 border-top pt-3 fw-bold">
            <p class="mb-0">{{ __('Refund Amount') }}</p>
            <p class="mb-0 d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 text-heading">
              {{ $refundedbill->amount }} <i class="sar-icon"></i>
            </p>
          </div>
        </div><!-- card-body -->


        @if($refundedbill->bill->user->settings->add_tax_invoice)
          <hr class="my-5">
          <div class="card-body p-0 text-heading text-center text-capitalize">
            <a class="d-flex justify-content-center flex-column align-items-center" target="_blank" href="{{route('invoice', ['id' => $bill->pay_id])}}">
              {!! generateQRcode($refundedbill) !!}
              <!-- <p>تم إنشاء كود الاستجابة السريعة بواسطة حل الفوترة الإلكترونية لدافعي الضرائب وفقاً لمواصفات ZATCA.</p> -->
              <span class="d-block text-body">{{ __('Tax Invoice') }}</span>
            </a>
          </div>
        @endif

        @if(isset($refundedbill->bill->user->settings->footer_bill))
          <hr class="my-5">
          <div class="card-body p-0 text-heading text-center text-capitalize">{{ $refundedbill->bill->user->settings->footer_bill }}</div>
        @endif

      </div><!-- card -->
    </div><!-- col -->
    <div class="col-12 col-md-6 d-flex flex-column gap-6">
      <div class="card">
        <div class="card-body view-print-options d-flex flex-column gap-6">
          <div class="row">
            <div class="col-6">
              <div class="item bg-light d-flex align-items-center justify-content-between p-2 gap-2 rounded-2">
                <label for="billA4" class="w-50 m-0 position-relative">
                  <input type="radio" name="type" id="billA4" value="billA4" class="start-0 top-0 position-absolute w-100 h-100 opacity-0 z-1" checked>
                  <span class="btn waves-effect waves-light d-flex align-items-center justify-content-center">A4</span>
                </label>
                <label for="billTh" class="w-50 m-0 position-relative">
                  <input type="radio" name="type" id="billTh" value="billTh" class="start-0 top-0 position-absolute w-100 h-100 opacity-0 z-1">
                  <span class="btn waves-effect waves-light d-flex align-items-center justify-content-center">Thermal</span>
                </label>
              </div><!-- item -->
            </div><!-- col-12 -->
            <div class="col-6">
              <div class="item bg-light d-flex align-items-center justify-content-between p-2 gap-2 rounded-2">
                <label for="billAr" class="w-50 m-0 position-relative">
                  <input type="radio" name="lang" id="billAr" value="ar" class="start-0 top-0 position-absolute w-100 h-100 opacity-0 z-1" checked>
                  <span class="btn waves-effect waves-light d-flex align-items-center justify-content-center">عربي</span>
                </label>
                <label for="billEn" class="w-50 m-0 position-relative">
                  <input type="radio" name="lang" id="billEn" value="en" class="start-0 top-0 position-absolute w-100 h-100 opacity-0 z-1">
                  <span class="btn waves-effect waves-light d-flex align-items-center justify-content-center">English</span>
                </label>
              </div><!-- item -->
            </div><!-- col-12 -->
          </div><!-- row -->
          <div id="printBillBtn" class="d-flex align-items-center justify-content-center">
            <button type="button" class="btn btn-secondary waves-effect waves-light d-flex align-items-center justify-content-center gap-2" dir="ltr">
              <span class="ti ti-printer"></span> {{ __('Print Receipt') }}
            </button>
          </div><!-- printBillBtn -->
          <iframe id="ifrPaySlip"  name="ifrPaySlip" scrolling="yes" style="display:none"></iframe>
        </div><!-- card-body -->
      </div><!-- card -->

    </div><!-- col -->
  </div><!-- row -->

@endsection

@push('footer-scripts')

  <script src="{{ asset('js/bootstrap-notify.min.js') }}" defer></script>

  <script>
    $(document).ready(function(){

      var billType = $('input[type=radio][name=type]').val();
      var billLang = $('input[type=radio][name=lang]').val();
      var billId = '{{$refundedbill->id}}';
      var base_url = "{{url('/')}}";

      $('input[type=radio][name=type]').change(function() {
        billType = this.value;
      });
      $('input[type=radio][name=lang]').change(function() {
        billLang = this.value
      });
      $('#printBillBtn').click(function() {
        var newWin = window.frames[0];
            newWin.document.write('<body><iframe style="position:fixed; top:0px; left:0px; bottom:0px; right:0px; width:100%; height:100%; border:none; margin:0; padding:0; overflow:hidden; z-index:999999;" src="'+base_url+'/refundedbills/'+billId+'/print?type='+billType+'&lang='+billLang+'"></body>');
            newWin.document.close();
      });

    });
  </script>

@endpush
