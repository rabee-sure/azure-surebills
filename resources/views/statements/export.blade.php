<table class="table w-100 table-striped table-hover text-nowrap">
  <thead>
    <tr>
      <th class="text-center">{{ __('Payment Date') }}</th>
      <th class="text-center">{{ __('Description') }}</th>
      <th class="text-center">{{ __('Reference') }}</th>
      <th class="text-center">{{ __('Receipt') }}</th>
      @if(count($channels))
        <th class="text-center">{{ __('Application') }}</th>
      @endif
      <th class="text-center">{{ __('Card') }}</th>
      <th class="text-center">{{ __('Debit') }}</th>
      <th class="text-center">{{ __('Credit') }}</th>
      <th class="text-center">{{ __('Balance') }}</th>
    </tr>
  </thead>
  <tbody>
    @foreach($statement as $transaction)
      <tr>
        <td class="text-center">{{ $transaction->created_at }}</td>
        <td class="text-center">{{ $transaction->description }}</td>
        <td class="text-center">{{ $transaction->reference }}</td>
        <td class="text-center">{{ $transaction->receipt }}</td>
        @if(count($channels))
          <td class="text-center">
            <!-- @if(isset($transaction->bill->application_id) && isset ($transaction->bill->application->channel_id))
                
              {{$transaction->bill->application_id}} - {{ $transaction->bill->user->business_name}}
            @else
            --
            @endif -->
            --
          </td>
        @endif
        <td class="text-center">{{ $transaction->card }}</td>
        <td class="text-danger text-center">{{ $transaction->type == 'debit' ? round2($transaction->amount) : '-' }}</td>
        <td class="text-success text-center">{{ $transaction->type == 'credit' ? round2($transaction->amount) : '-' }}</td>
        <td class="text-center">{{ fact_number(round($transaction->balance, 2)) }}</td>
      </tr>
    @endforeach
  </tbody>
</table>