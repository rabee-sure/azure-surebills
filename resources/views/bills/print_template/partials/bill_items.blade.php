<table class="table m-0">
  <thead>
    <tr>
      <th class="text-nowrap">{{ __('Description', [], $lang) }}</th>
      <th class="text-nowrap">{{ __('Price', [], $lang) }}</th>
      <th class="text-nowrap">{{ __('Quantity', [], $lang) }}</th>
      @if($bill->add_tax)
        <th class="text-nowrap">{{ __('Total include added tax', [], $lang) }}</th>
      @else
        <th class="text-nowrap">{{ __('Total', [], $lang) }}</th>
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
          <span class="d-flex align-items-center {{$lang == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 m-0">
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
            <span class="d-flex align-items-center {{$lang == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 m-0">
              {{ ($item->product_price * $item->quantity) + (($item->product_price * $item->quantity) * $bill->tax_value / 100)  }} <i class="sar-icon"></i>
            </span>
          @else
            <span class="d-flex align-items-center {{$lang == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 m-0">
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
