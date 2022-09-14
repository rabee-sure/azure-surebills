<div class="billInfo">
  @if($bill->user->settings->add_tax_invoice)
    <div class="item">
      <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>{{ __('Bill No.') }}</span>
      <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>DN{{ $bill->number }}</p>
      <div class="clearfix"></div>
    </div><!-- item -->
    <div class="item">
      <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>{{ __('Date') }}</span>
      <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>{{ $bill->created_at->format('d/m/Y')}}</p>
      <div class="clearfix"></div>
    </div><!-- item -->
    @if($bill->user->vat_registration_number)
      <div class="item">
        <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>{{ __('Organization VAT Registration Number') }}</span>
        <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>{{ $bill->user->vat_registration_number }}</p>
      <div class="clearfix"></div>
      </div><!-- item -->
    @endif
  @else
    <div class="item">
      <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>{{ __('No.') }}</span>
      <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>DN{{ $bill->number }}</p>
      <div class="clearfix"></div>
    </div><!-- item -->
    <div class="item">
      <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>{{ __('Date') }}</span>
      <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>{{ $bill->created_at->format('d/m/Y')}}</p>
      <div class="clearfix"></div>
    </div><!-- item -->
  @endif
  @if($bill->user->settings->display_customer_details)
    <div class="item">
      <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>{{ __('Customer Name') }}</span>
      <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>{{ $bill->customer->name }}</p>
      <div class="clearfix"></div>
    </div><!-- item -->
    <div class="item">
      <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>{{ __('Mobile Number') }}</span>
      <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>{{ $bill->customer->mobile }}</p>
      <div class="clearfix"></div>
    </div><!-- item -->
  @endif
</div><!-- billInfo -->