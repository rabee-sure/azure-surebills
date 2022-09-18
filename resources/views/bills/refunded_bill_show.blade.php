@extends('layouts.app')

@section('title', __('Credit Note') . ' CN' . $refundedbill->number . ' ' . __('Bills'))

@section('content')

<div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm d-print-none">
  <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
  <i>/</i>
  <a href="/bills" title="{{ __('Bills') }}">{{ __('Bills') }}</a>
  <i>/</i>
  <span>{{__('Credit Note No.')}} CN{{ $refundedbill->number }}</span>
</div><!-- breadcrump -->

<section id="billShowPage">
  <div class="title mb-4 d-print-none">
    <h1 class="d-block fw-bold m-0 fs-5">{{ __('Credit Note') }}</h1>
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

  
  <div class="row justify-content-center">
    <div class="col-12 col-md-6">
      <div class="showBill mb-3 bg-white shadow-sm rounded-3 p-2">
        <div class="aboutUser d-flex align-items-center justify-content-center flex-column">
          @if($refundedbill->bill->user->logo)
            <figure class="my-2">
              <img src="{{ $refundedbill->bill->user->logo_url }}" alt="{{ $refundedbill->bill->user->business_name }}" class="mw-100">
            </figure><!-- figure -->
          @endif
          @if($refundedbill->bill->user->settings->add_tax_invoice)
            <div class="taxInvoiceText text-secondary">{{ __('Tax credit note') }}</div>
          @endif
          <span class="d-block fw-bold mt-3">{{ $refundedbill->bill->user->business_name }}</span>
          <p class="d-block mb-0">{{  $refundedbill->bill->user->business_address }}</p>
          <b class="d-block fw-normal mb-2">{{  $refundedbill->bill->user->business_mobile }}</b>
        </div><!-- aboutUser -->
        
        <div class="billInfo pt-2 mt-2 borderTop">
          <div class="d-flex align-items-center justify-content-between">
            <span class="d-block mb-2">{{ __('Credit Note Date') }}</span>
            <span class="d-block mb-2">{{ $refundedbill->created_at->format('d/m/Y')}}</span>
          </div><!-- d-flex -->
          <div class="d-flex align-items-center justify-content-between">
            <span class="d-block mb-2">{{ __('Credit Note Number') }}</span>
            <span class="d-block mb-2">CN{{ $refundedbill->number }}</span>
          </div><!-- d-flex -->
          
          <div class="d-flex align-items-center justify-content-between">
            <span class="d-block mb-2">{{ __('Invoice Number') }}</span>
            <span class="d-block mb-2">{{ $refundedbill->bill->number }}</span>
          </div><!-- d-flex -->
          <div class="d-flex align-items-center justify-content-between">
            <span class="d-block mb-2">{{ __('Invoice Date') }}</span>
            <span class="d-block mb-2">{{ $refundedbill->bill->created_at->format('d/m/Y')}}</span>
          </div><!-- d-flex -->
        </div><!-- billInfo -->
        <div class="billInfo pt-2 mt-2 borderTop">
          <div class="d-flex align-items-center justify-content-between">
            <span class="d-block mb-2">{{ __('Refund Amount') }}({{ __('SAR') }})</span>
            <span class="d-block mb-2">{{ $refundedbill->amount }}</span>
          </div><!-- d-flex -->
        </div><!-- bill_info -->
        @if($refundedbill->bill->user->settings->add_tax_invoice)
          <div class="qrCode mt-2 pt-2 borderTop">
            <a class="d-flex justify-content-center flex-column align-items-center" target="_blank" href="{{route('refundedinvoice', ['id' => $refundedbill->hashed_id])}}">
              {!! generateQRcode($refundedbill) !!}
              <!-- <p>تم إنشاء كود الاستجابة السريعة بواسطة حل الفوترة الإلكترونية لدافعي الضرائب وفقاً لمواصفات ZATCA.</p> -->
              <span class="d-block text-body">{{ __('Tax Invoice') }}</span>
            </a>
          </div><!-- qrCode -->
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
    </div><!-- col-12 -->
  </div><!-- row -->

</section><!-- billShowPage -->
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
