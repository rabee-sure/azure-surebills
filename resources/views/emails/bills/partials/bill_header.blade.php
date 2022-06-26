@if($bill->user->logo)
  <div class="clientLogo">
    <img src="{{ $bill->user->logo_url }}" alt="logo">
  </div><!-- clientLogo -->
@endif

@if($bill->user->settings->add_tax_invoice)
  <div class="taxInvoiceText">{{ __('Simplified Tax Invoice') }}</div>
@endif

<div class="clientMail">{{ $bill->user->business_name}}</div>

@if(isset($bill->user->settings->header_bill))
  <div class="headerBillText">{{ $bill->user->settings->header_bill }}</div>
@endif

<div class="businessAddress">{{ $bill->user->business_address }}</div>

<div class="businessMobile" dir="ltr">{{  $bill->user->business_mobile }}</div>

@if($bill->application_id && !$bill->is_expired && $bill->remaining_time_hours['hours'] == '00' && $bill->remaining_time_hours['days'] == 0)
  <div class="billWillExpire">{{ __('the bill will expire in')}}</div>
@endif