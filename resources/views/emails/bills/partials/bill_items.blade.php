<div class="blockTable" @if(app()->getLocale() == 'ar') dir="rtl" @else dir="ltr" @endif>
  <table>
    <thead>
      <tr>
        <th @if(app()->getLocale() == 'ar') style="text-align: right;" @else style="text-align: left;" @endif>{{ __('Description') }}</th>
        <th>{{ __('Price') }}</th>
        <th>{{ __('Quantity') }}</th>
        @if($bill->add_tax)
          <th width="35%" @if(app()->getLocale() == 'ar') style="text-align: left;" @else style="text-align: right;" @endif>{{ __('Total include added tax') }}</th>
        @else
          <th width="35%" @if(app()->getLocale() == 'ar') style="text-align: left;" @else style="text-align: right;" @endif>{{ __('Total') }}</th>
        @endif
      </tr>
    </thead>
    <tbody>
      @foreach($bill->items as $item)
      <tr>
        <td @if(app()->getLocale() == 'ar') style="text-align: right;" @else style="text-align: left;" @endif>{!! $item->product_name !!}</td>
        <td>{{ $item->product_price  }}</td>
        <td>{{ $item->quantity  }}</td>
        @if( $bill->add_tax)
          <td @if(app()->getLocale() == 'ar') style="text-align: left;" @else style="text-align: right;" @endif>{{ ($item->product_price * $item->quantity) + (($item->product_price * $item->quantity) * $bill->tax_value / 100)  }}</td>
        @else
          <td @if(app()->getLocale() == 'ar') style="text-align: left;" @else style="text-align: right;" @endif>{{ $item->product_price * $item->quantity }}</td>
        @endif
      </tr>
      @endforeach
    </tbody>
  </table>
</div><!-- blockTable -->