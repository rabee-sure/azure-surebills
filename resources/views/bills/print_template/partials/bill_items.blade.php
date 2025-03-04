<table class="w-100">
    <thead>
      <tr>
        <th class="p-1 text-start">{{ __('Description', [], $lang) }}</th>
        <th class="p-1 text-center">{{ __('Price', [], $lang) }}</th>
        <th class="p-1 text-center">{{ __('Quantity', [], $lang) }}</th>
        @if($bill->add_tax)
          <th th width="35%" class="p-1 text-end">{{ __('Total include added tax', [], $lang) }}</th>
        @else
          <th width="35%" class="p-1 text-end">{{ __('Total', [], $lang) }}</th>
        @endif
      </tr>
    </thead>
    <tbody>
      @foreach($bill->items as $item)
      @if($item->product_parent) @continue @endif
      <tr>
        <td class="p-1 text-start">
            {{ $item->product_name }}
            @foreach($item->customizations as $customization)
            <br>
            <span class="text-muted">{{$customization->product_name}}</span>
            @endforeach
        </td>
        <td class="p-1 text-center">
            <div class="d-flex align-items-center justify-content-center gap-1 fw-bold rtl flex-shrink-0">
              {{ $item->product_price  }} <span class="riyal-symbol-font">$</span>
            </div><!-- d-flex -->
            @foreach($item->customizations as $customization)
            <br>
            <span class="text-muted">{{$customization->product_price}}</span>
            @endforeach
        </td>
        <td class="p-1 text-center">
            {{ $item->quantity  }}
            @foreach($item->customizations as $customization)
            <br>
            <span class="text-muted">{{$customization->quantity}}</span>
            @endforeach
        </td>
        <td class="p-1 text-end">
        @if( $bill->add_tax)
            <div class="d-flex align-items-center gap-1 fw-bold rtl flex-shrink-0 @if($lang == 'ar') justify-content-end @else justify-content-start @endif">
              {{ ($item->product_price * $item->quantity) + (($item->product_price * $item->quantity) * $bill->tax_value / 100)  }}  <span class="riyal-symbol-font">$</span>
            </div><!-- d-flex -->
          @else
            <div class="d-flex align-items-center gap-1 fw-bold rtl flex-shrink-0 @if($lang == 'ar') justify-content-end @else justify-content-start @endif">
              {{ $item->product_price * $item->quantity }}  <span class="riyal-symbol-font">$</span>
            </div><!-- d-flex -->
          @endif
            @foreach($item->customizations as $customization)
            <br>
            <span class="text-muted">{{$bill->add_tax ?
                ($customization->product_price * $item->quantity) + (($customization->product_price * $item->quantity) * $bill->tax_value / 100) : $customization->product_price * $item->quantity}}</span>
            @endforeach
            </td>
      </tr>
      @endforeach
    </tbody>
  </table>
