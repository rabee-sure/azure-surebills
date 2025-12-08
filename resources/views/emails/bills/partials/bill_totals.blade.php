<div class="totalArea">
  @if( $bill->add_tax || $bill->add_discount)
    <div class="item">
      <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>
        {{ __('Total amount') }}
        <small>( {{ __('SAR') }} )</small>
        @if( $bill->add_tax)
          <div class="excludeTax">( {{ __('Exclude added tax') }} )</div>
        @endif
      </span>
      <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>{{ $bill->sub_total }}</p>
      <div class="clearfix"></div>
    </div><!-- item -->
  @endif
  @if( $bill->add_discount)
    <div class="item Discount">
      <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>{{ __('Discount amount') }} <small>( {{ __('SAR') }} )</small></span>
      <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>{{ $bill->discount }}</p>
      <div class="clearfix"></div>
    </div><!-- item -->
  @endif
  @if( $bill->user->pay_fees == 'client')
    <div class="item">
      <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>{{ __('payment fees') }} <small>( {{ __('SAR') }} )</small></span>
      <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>{{ $bill->payment_fees }}</p>
      <div class="clearfix"></div>
    </div><!-- item -->
  @endif
  @if( $bill->add_tax)
    <div class="item">
      <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>{{ __('Added tax value (:percentge%)', ['percentge'=>$bill->tax_value]) }}</span>
      <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>{{ $bill->vat }}</p>
      <div class="clearfix"></div>
    </div><!-- item -->
  @endif
  @if( $bill->channel_extra_amount)
    <div class="item">
      <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>{{$bill->channel_extra_title}} <small>( {{ __('SAR') }} )</small></span>
      <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>{{ $bill->channel_extra_amount }}</p>
      <div class="clearfix"></div>
    </div><!-- item -->
  @endif
  @if( $bill->channel_extra_vat)
    <div class="item">
      <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>{{ __('Vat') }} <small>( {{$bill->channel_extra_title}} ( {{ $bill->tax_value }} % ) )</small></span>
      <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>{{ $bill->channel_extra_vat }}</p>
      <div class="clearfix"></div>
    </div><!-- item -->
  @endif
  <div class="item Total">
    <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>{{ __('Total amount') }} <small>( {{ __('SAR') }} )</small></span>
    <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>{{ $bill->total}}</p>
    <div class="clearfix"></div>
  </div><!-- item -->
</div><!-- totalArea -->
