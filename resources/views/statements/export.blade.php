<table class="table table-striped text-center">
  <thead>
    <tr>
      <th>{{ __('Payment Date') }}</th>
      <th>{{ __('Description') }}</th>
      <th>{{ __('Reference') }}</th>
      <th>{{ __('Receipt') }}</th>
    @if(count($channels))
      <th>{{ __('Application') }}</th>
    @endif
      <th>{{ __('Card') }}</th>
      <th>{{ __('Debit') }}</th>
      <th>{{ __('Credit') }}</th>
      <th>{{ __('Balance') }}</th>
    </tr>
  </thead>
  <tbody>
    @foreach($statement as $transaction)
      <tr>
        <td>{{ $transaction->created_at }}</td>
        <td>{{ $transaction->description }}</td>
        <td>{{ $transaction->reference }}</td>
        <td>{{ $transaction->receipt }}</td>
        @if(count($channels))
          <td>
            @if(isset($transaction->bill->application_id) && isset ($transaction->bill->application->channel_id))
                
              {{$transaction->bill->application_id}} - {{ $transaction->bill->user->business_name}}
            @else
            --
            @endif
          </td>
        @endif
        <td>
          @if ($transaction->card_brand == 'VISA')
            <img alt="mastercard" src="{{ asset('images/cards/visa.gif') }}" class="mr-1" width="18px"> 
          @elseif ($transaction->card_brand == 'MASTER')
            <img alt="mastercard" src="{{ asset('images/cards/mastercard.gif') }}" class="mr-1" width="18px"> 
          @elseif ($transaction->card_brand == 'MADA')
            <img alt="mastercard" src="{{ asset('images/cards/mada.gif') }}" class="mr-1" width="18px"> 
          @elseif ($transaction->card_brand == 'APPLEPAY')
            <img alt="mastercard" src="{{ asset('images/cards/applepay.gif') }}" class="mr-1" width="18px"> 
          @endif
          {{ $transaction->card }}
        </td>
        <td class="text-danger">{{ $transaction->type == 'debit' ? round($transaction->amount, 2) : '-' }}</td>
        <td class="text-success">{{ $transaction->type == 'credit' ? round($transaction->amount, 2) : '-' }}</td>
        <td>{{ round($transaction->balance, 2) }}</td>
      </tr>
    @endforeach
  </tbody>
</table>