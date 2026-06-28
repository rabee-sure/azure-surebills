@extends('layouts.app')
@section('title', $title . ' ' . $bill->number . ' ' . __('Bills'))

@push('css_styles')
  <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/notyf/notyf.css') }}?v={{ config('app.asset_version') }}" />
  <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/animate-css/animate.css') }}?v={{ config('app.asset_version') }}" />
  <link rel="stylesheet" href="{{ asset('assets/v2/vendor/css/pages/app-invoice.css') }}?v={{ config('app.asset_version') }}" />
@endpush

@php
  $statues = session('status_filters', ['pending', 'paid'])?? [];
  $separated = (count($statues)) ? 'statuses[]='.implode("&statuses[]=", $statues):'';
  $hasBillActions = ($bill->access_to_pay_page->status ?? false)
    || ($bill->user->settings->add_tax_invoice ?? false)
    || (auth()->user()->can('create debit note') && (!$bill->debit_note_bill_id) && in_array($bill->status, ['paid', 'paid_cash', 'paid_bank_transfer', 'paid_machine']) && ((!auth()->user()->mainStoreUser && count(auth()->user()->channels) == 0) || (auth()->user()->mainStoreUser && count(auth()->user()->mainStoreUser->channels) == 0)))
    || (auth()->user()->can('cancel bill') && $bill->is_pending)
    || (auth()->user()->can('refund bill') && $bill->is_able_refund && !$bill->debit_note_bill_id)
    || (auth()->user()->can('change bill status') && $bill->is_able_change_status);
@endphp

@section('content')

  <h4 class="mb-1">@if($bill->debit_note_bill_id == null) {{__('Bill')}} @else {{__('Debit Note')}} @endif</h4>

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-custom-icon mb-6">
      <li class="breadcrumb-item">
        <a href="{{ url('bills') }}" title="{{ __('Bills') }}">{{ __('Bills')}}</a>
        <i class="breadcrumb-icon icon-base ti ti-chevron-right align-middle icon-xs"></i>
      </li>
      <li class="breadcrumb-item active">{{$title}} : {{ $bill->number }}</li>
    </ol>
  </nav>

  <div id="errors" class="d-print-none">
    @if ($errors->any())
      <div class="alert alert-danger mb-6">
        <ul class="list-group">
          @foreach ($errors->all() as $error)
            <li class="{{ $errors->count() === 1 ? 'list-group-item list-group-item-danger border-0 p-0' : 'list-group-item list-group-item-danger' }}">{{ $error }}</li>
          @endforeach
        </ul>
      </div><!-- alert -->
    @endif
  </div><!-- alert -->

  @if($hasBillActions)
    <div class="card mb-6">
      <div class="card-body p-3 d-flex align-items-center justify-content-center gap-3 flex-wrap">
        @if ($bill->access_to_pay_page->status)
          <button
            type="button"
            data-bs-toggle="tooltip"
            data-bs-placement="top"
            title="{{ __('Copy payment link') }}"
            data-clipboard-text="{{ $bill->pay_url }}"
            class="btn btn-icon btn-primary waves-effect waves-light copy-link-btn"
          >
            <i class="icon-base ti ti-copy"></i>
          </button>
          <a
            href="{{ $bill->pay_url}}"
            target="_blank"
            data-bs-toggle="tooltip"
            data-bs-placement="top"
            title="{{ __('Visit Payment Link') }}"
            class="btn btn-icon btn-primary waves-effect waves-light"
          >
            <i class="icon-base ti ti-link"></i>
          </a>
        @endif

        @if($bill->user->settings->add_tax_invoice)
          <a
            href="{{ $bill->invoice_url}}"
            target="_blank"
            data-bs-toggle="tooltip"
            data-bs-placement="top"
            title="{{ __('Tax Invoice') }}"
            class="btn btn-icon btn-primary waves-effect waves-light"
          >
            <i class="icon-base ti ti-qrcode"></i>
          </a>
        @endif

        <!-- <a onclick="window.print(); return false;" class="btn-primary p-0 m-1 rounded-3 d-flex align-items-center justify-content-center border-0 shadow-none" data-bs-toggle="tooltip" data-bs-placement="top" href="#" title="{{ __('Print') }}"><i class="fal fa-print"></i></a> -->

        <!-- <a class="btn-primary p-0 m-1 rounded-3 d-flex align-items-center justify-content-center border-0 shadow-none" href="#">{{ __('Send Reminder') }}</a> -->

        @can('create debit note')
          @if((!auth()->user()->mainStoreUser && count(auth()->user()->channels) == 0) || (auth()->user()->mainStoreUser && count(auth()->user()->mainStoreUser->channels) == 0))
            @if($bill->debit_note_bill_id == null && in_array($bill->status, ['paid', 'paid_cash', 'paid_bank_transfer', 'paid_machine']))
              <a
                href="{{ route('debitNote.create', ['bill_id' => $bill->id])}}"
                target="_blank"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="{{ __('Create Debit Note') }}"
                class="btn btn-icon btn-primary waves-effect waves-light"
              >
                <i class="icon-base ti ti-receipt"></i>
              </a>
            @endif
          @endif
        @endcan

        @can('cancel bill')
          @if($bill->is_pending)
            <button
              type="button"
              id="cancel_btn"
              data-bs-toggle="tooltip"
              data-bs-placement="top"
              title="@if($bill->debit_note_bill_id == null) {{ __('Cancel Bill') }} @else {{ __('Cancel Debit Note') }} @endif"
              class="btn btn-icon btn-danger waves-effect waves-light"
            >
              <i class="icon-base ti ti-square-x"></i>
            </button>
            @include('bills.partials.cancel',['bill' => $bill])
          @endif
        @endcan

        @can('refund bill')
          @if($bill->is_able_refund && $bill->debit_note_bill_id == null)
            <button
              type="button"
              id="refund_btn"
              data-bs-toggle="tooltip"
              data-bs-placement="top"
              title="{{ __('Refund') }}"
              class="btn btn-icon btn-warning waves-effect waves-light"
            >
              <i class="icon-base ti ti-receipt-dollar"></i>
            </button>
            @include('bills.partials.refund',['bill' => $bill])
          @endif
        @endcan

        @can('change bill status')
          @if($bill->is_able_change_status)
            <span class="d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Change Status') }}">
              <button
                type="button"
                data-bs-toggle="modal"
                data-bs-target="#changeStatusModal"
                class="btn btn-icon btn-info waves-effect waves-light"
              >
                <i class="icon-base ti ti-repeat"></i>
              </button>
            </span>
          @endif
        @endcan
      </div><!-- card-body -->
    </div><!-- card -->
  @endif

  <div class="row g-6">
    <div class="col-12 col-md-6 invoice-preview">
      <div class="card invoice-preview-card p-5">

        <div class="card-body invoice-preview-header rounded p-5 mb-5">
          @if($bill->user->logo)
            <span class="app-brand-logo d-flex align-items-center justify-content-center mb-4">
              <img src="{{ $bill->user->logo_url }}" alt="{{ $bill->user->business_name }}" class="w-auto" height="32px">
            </span>
          @endif
          <div class="text-heading mb-xl-0 mb-5 d-flex flex-column gap-2 text-center">
            @if($bill->user->settings->add_tax_invoice)
              <p class="m-0">@if($bill->debit_note_bill_id == null) {{ __('Simplified Tax Invoice') }} @else {{ __('Tax debit note') }} @endif</p>
            @endif
            <p class="m-0">{{ $bill->user->business_name }}</p>
            @if(isset($bill->user->settings->header_bill))
              <p class="m-0">{{ $bill->user->settings->header_bill }}</p>
            @endif
            <p class="m-0">{{  $bill->user->business_address }}</p>
            <p class="m-0">{{  $bill->user->business_mobile }}</p>
          </div>
        </div><!-- card-body -->

        <div id="status">
          @if($bill->status == 'expired')
            <div class="alert alert-danger text-center text-capitalize mb-5">
              @if($bill->debit_note_bill_id == null)
              {{ __('this bill has been expired', ['number' => $bill->number ]) }}
              @else
              {{ __('this debit note has been expired', ['number' => $bill->number ]) }}
              @endif
            </div>
          @elseif(in_array($bill->status, ['paid', 'refunded']))
            <div class="alert alert-success text-center text-capitalize mb-5">
              @if ($bill->depositTransaction)
                {{ __('Paid') }} - {{ $bill->depositTransaction->card_brand }} {{ $bill->depositTransaction->card }} {{ $bill->depositTransaction->receipt }}
              @else
                @if($bill->debit_note_bill_id == null)
                {{ __('this bill has been successfully', ['number' => $bill->number ]) }}
                @else
                {{ __('this debit note has been successfully', ['number' => $bill->number ]) }}
                @endif
              @endif
            </div>
          @elseif(in_array($bill->status, ['paid_cash', 'refunded_cash']))
            <div class="alert alert-success text-center text-capitalize mb-5">
              @if($bill->debit_note_bill_id == null)
                {{ __('this bill has been Paid Cash successfully', ['number' => $bill->number ]) }}
              @else
                {{ __('this debit note has been Paid Cash successfully', ['number' => $bill->number ]) }}
              @endif
            </div>
          @elseif(in_array($bill->status, ['paid_bank_transfer', 'refunded_bank_transfer']))
            <div class="alert alert-success text-center text-capitalize mb-5">
              @if($bill->debit_note_bill_id == null)
                {{ __('this bill has been Paid Bank Transfer successfully', ['number' => $bill->number ]) }}
              @else
                {{ __('this debit note has been Paid Bank Transfer successfully', ['number' => $bill->number ]) }}
              @endif
            </div>
          @elseif(in_array($bill->status, ['paid_machine', 'refunded_machine']))
            <div class="alert alert-success text-center text-capitalize mb-5">
              @if($bill->debit_note_bill_id == null)
                {{ __('this bill has been Paid Machine successfully', ['number' => $bill->number ]) }}
              @else
                {{ __('this debit note has been Paid Machine successfully', ['number' => $bill->number ]) }}
              @endif
            </div>
          @elseif($bill->status == 'canceled')
            <div class="alert alert-danger text-center text-capitalize mb-5">
              @if($bill->debit_note_bill_id == null)
                {{ __('this bill has been canceled', ['number' => $bill->number ]) }}
              @else
                {{ __('this debit note has been canceled', ['number' => $bill->number ]) }}
              @endif
            </div>
          @elseif($bill->status == 'failed')
            <div class="alert alert-danger text-center text-capitalize mb-5">
              @if($bill->debit_note_bill_id == null)
                {{ __('this bill has been failed', ['number' => $bill->number ]) }}
              @else
                {{ __('this debit note has been failed', ['number' => $bill->number ]) }}
              @endif
            </div>
            {{-- @elseif(in_array($bill->status, ['refunded', 'refunded_cash', 'refunded_bank_transfer']))
            <div class="alert alert-warning text-center text-capitalize mb-5"> {{ __('this bill has been refunded', ['number' => $bill->number ]) }}</div> --}}
          @elseif($bill->status == 'rejected')
            <div class="alert alert-danger text-center text-capitalize mb-5">
              @if($bill->debit_note_bill_id == null)
                {{ __('this bill has been rejected', ['number' => $bill->number ]) }}
              @else
                {{ __('this debit note has been rejected', ['number' => $bill->number ]) }}
              @endif
            </div>
          @endif
        </div><!-- status -->

        <div class="d-flex flex-column gap-2 mb-5">
          @if($bill->debit_note_bill_id == null)
            @include('bills.partials.bill_info',['bill' => $bill])
          @else
            @include('bills.partials.debit_note_info',['bill' => $bill])
          @endif
        </div><!-- d-flex -->

        <div class="table-responsive border border-bottom-0 border-top-0 rounded mb-5">
          <table class="table m-0">
            <thead>
              <tr>
                <th class="text-nowrap">{{ __('Description') }}</th>
                <th class="text-nowrap">{{ __('Price') }}</th>
                <th class="text-nowrap">{{ __('Quantity') }}</th>
                @if($bill->add_tax)
                  <th class="text-nowrap">{{ __('Total include added tax') }}</th>
                @else
                  <th class="text-nowrap">{{ __('Total') }}</th>
                @endif
              </tr>
            </thead>
            <tbody>
              @foreach($bill->items as $item)
                @if($item->product_parent) @continue @endif
                <tr>
                  <td class="text-nowrap text-heading">
                    {{ $item->product_name }}
                    @foreach($item->customizations as $customization)
                      <br>
                      <span class="text-muted">{{$customization->product_name}}</span>
                    @endforeach
                  </td>
                  <td class="text-nowrap">
                    <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 m-0">
                      {{ $item->product_price  }} <i class="sar-icon"></i>
                    </span>
                    @foreach($item->customizations as $customization)
                      <br>
                      <span class="text-muted">{{$customization->product_price}}</span>
                    @endforeach
                  </td>
                  <td class="text-nowrap">
                    {{ $item->quantity  }}
                    @foreach($item->customizations as $customization)
                    <br>
                    <span>{{$customization->quantity}}</span>
                    @endforeach
                  </td>
                  <td class="text-nowrap">
                    @if( $bill->add_tax)
                      <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 m-0">
                        {{ ($item->product_price * $item->quantity) + (($item->product_price * $item->quantity) * $bill->tax_value / 100)  }} <i class="sar-icon"></i>
                      </span>
                    @else
                      <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 m-0">
                        {{ $item->product_price * $item->quantity }} <i class="sar-icon"></i>
                      </span>
                    @endif
                    @foreach($item->customizations as $customization)
                      <br>
                      <span>{{$bill->add_tax ? $customization->product_price + ($customization->product_price * $bill->tax_value) / 100 : $customization->product_price}}</span>
                    @endforeach
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div><!-- table-responsive -->

        <div class="d-flex flex-column gap-3">
          @if( $bill->add_tax || $bill->add_discount)
            <div class="d-flex align-items-center justify-content-between gap-2">
              <p class="mb-0">
                {{ __('Total amount') }}
                @if( $bill->add_tax)
                  <small class="d-block text-muted mt-1">( {{ __('Exclude added tax') }} )</small>
                @endif
              </p>
              <p class="mb-0 d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 text-heading fw-medium">
                {{ $bill->sub_total }} <i class="sar-icon"></i>
              </p>
            </div>
          @endif
          @if( $bill->add_discount)
            <div class="d-flex align-items-center justify-content-between gap-2">
              <p class="mb-0">{{ __('Discount amount') }}</p>
              <p class="mb-0 d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 text-heading fw-medium">
                {{ $bill->discount }} <i class="sar-icon"></i>
              </p>
            </div>
          @endif
          @if( $bill->user->pay_fees == 'client')
            <div class="d-flex align-items-center justify-content-between gap-2">
              <p class="mb-0">{{ __('payment fees') }}</p>
              <p class="mb-0 d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 text-heading fw-medium">
                {{ $bill->payment_fees }} <i class="sar-icon"></i>
              </p>
            </div>
          @endif
          @if( $bill->add_tax)
            <div class="d-flex align-items-center justify-content-between gap-2">
              <p class="mb-0">{{ __('Added tax value (:percentge %)', ['percentge'=>$bill->tax_value]) }}</p>
              <p class="mb-0 d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 text-heading fw-medium">
                {{ $bill->vat }} <i class="sar-icon"></i>
              </p>
            </div>
          @endif
          @if( $bill->channel_extra_amount)
            <div class="d-flex align-items-center justify-content-between gap-2">
              <p class="mb-0">{{$bill->channel_extra_title}}</p>
              <p class="mb-0 d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 text-heading fw-medium">
                {{ $bill->channel_extra_amount }} <i class="sar-icon"></i>
              </p>
            </div>
          @endif
          @if( $bill->channel_extra_vat)
            <div class="d-flex align-items-center justify-content-between gap-2">
              <p class="mb-0">{{ __('Vat') }} ({{$bill->channel_extra_title}} ({{ $bill->tax_value }}%))</p>
              <p class="mb-0 d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 text-heading fw-medium">
                {{ $bill->channel_extra_vat }} <i class="sar-icon"></i>
              </p>
            </div>
          @endif
          {{-- @if( $bill->refund_amount)
            <div class="d-flex align-items-center justify-content-between">
              <span class="d-block mb-2">{{ __('Refund Amount') }}</span>
              <div class="d-flex align-items-center justify-content-center gap-1 fw-bold rtl flex-shrink-0 text-heading">
                {{ $bill->refund_amount }}  <span class="riyal-symbol-font">$</span>
              </div><!-- d-flex -->
            </div><!-- d-flex -->
          @endif --}}
          <div class="d-flex align-items-center justify-content-between gap-2 border-top pt-3 fw-bold">
            <p class="mb-0">{{ __('Total amount') }}</p>
            <p class="mb-0 d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 text-heading">
              {{ $bill->sub_total + $bill->vat - $bill->discount}} <i class="sar-icon"></i>
            </p>
          </div>
        </div><!-- d-flex -->

        @if($bill->customer_notes)
          <hr class="my-5">
          <div class="card-body p-0 text-heading text-center text-capitalize">{{$bill->customer_notes}}</div>
        @endif

        @if($bill->user->settings->add_tax_invoice)
          <hr class="my-5">
          <div class="card-body p-0 text-heading text-center text-capitalize">
            <a class="d-flex justify-content-center flex-column align-items-center" target="_blank" href="{{route('invoice', ['id' => $bill->pay_id])}}">
              {!! generateQRcode($bill) !!}
              <!-- <p>تم إنشاء كود الاستجابة السريعة بواسطة حل الفوترة الإلكترونية لدافعي الضرائب وفقاً لمواصفات ZATCA.</p> -->
              <span class="d-block text-body">{{ __('Tax Invoice') }}</span>
            </a>
          </div>
        @endif

        @if(isset($bill->user->settings->footer_bill))
          <hr class="my-5">
          <div class="card-body p-0 text-heading text-center text-capitalize">{{ $bill->user->settings->footer_bill }}</div>
        @endif

      </div>
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

      @if(count($bill->payment_logs) > 0)
        @include('bills.partials.payment_logs')
      @endif

      @if(count($billNotes) > 0)
        @include('bills.partials.bill_notes', ['billNotes' => $billNotes])
      @endif

    </div><!-- col -->
  </div><!-- row -->

  @can('change bill status')
    @if($bill->is_able_change_status)
      @include('bills.partials.change_status',['bill' => $bill])
    @endif
  @endcan

@endsection

@push('footer-scripts')
  <!-- Laravel Javascript Validation -->
  <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.min.js')}}?v={{ config('app.asset_version') }}"></script>
  <script src="{{ asset('assets/v2/vendor/libs/clipboard/clipboard.js') }}?v={{ config('app.asset_version') }}"></script>
  <script src="{{ asset('assets/v2/vendor/libs/notyf/notyf.js') }}?v={{ config('app.asset_version') }}"></script>
  <script>
    (function() {
      var notyf = new Notyf({ duration: 3000, position: { x: 'right', y: 'top' } });
      var copyBtn = document.querySelector('.copy-link-btn');
      if (copyBtn && typeof ClipboardJS !== 'undefined') {
        var clipboard = new ClipboardJS('.copy-link-btn');
        clipboard.on('success', function() {
          notyf.success("{{ __('link is copied') }}");
        });
        clipboard.on('error', function() {
          notyf.error("{{ __('Failed to copy link') }}");
        });
      } else if (copyBtn) {
        copyBtn.addEventListener('click', function() {
          var url = this.getAttribute('data-clipboard-text');
          if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(function() {
              notyf.success("{{ __('link is copied') }}");
            }).catch(function() {
              notyf.error("{{ __('Failed to copy link') }}");
            });
          } else {
            notyf.error("{{ __('Failed to copy link') }}");
          }
        });
      }
    })();

    if (typeof window.Echo !== 'undefined') {
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
    }
  </script>

  <script>
    $(document).ready(function(){

      var billType = $('input[type=radio][name=type]').val();
      var billLang = $('input[type=radio][name=lang]').val();
      var billId = '{{$bill->id}}';
      var base_url = "{{url('/')}}";
      var userVerified = "{{Auth::user()->verified}}";
      console.log(userVerified);

      $("#refund_btn").click(function(){
        console.log('refund');
        var otherDate = '{{\Carbon\Carbon::now()->subDays(14)}}';
        var billPaidAt = '{{$bill->paid_at}}';

        if(otherDate > billPaidAt){
          $("#errors").append('<div id="limitdays" class="alert alert-danger" role="alert">{{  __('It must not pass more than 14 days on the date of payment of the Bill') }}</div>');

          setTimeout(function() {
                $("#limitdays").remove();
          }, 4000);
        }else if(userVerified == 0){
          $("#errors").append('<div id="limitdays" class="alert alert-danger" role="alert">{{  __('your account not verified please contact your administrator.') }}</div>');

          setTimeout(function() {
                $("#limitdays").remove();
          }, 4000);
        }else{
          $('#refundModal').modal('show');
        }
      });

      $("#cancel_btn").click(function(){
        if(userVerified == 0){
          $("#errors").append('<div id="limitdays" class="alert alert-danger" role="alert">{{  __('your account not verified please contact your administrator.') }}</div>');

          setTimeout(function() {
                $("#limitdays").remove();
          }, 4000);
        }else{
          $('#cancelModal').modal('show');
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
