<table class="table table-striped text-center">
  <thead>
    <tr>
      <th>{{ __('Name') }}</th>
      <th>{{ __('Total') }}</th>
      <th>{{ __('Relation') }}</th>
      <th>{{ __('Total Due') }}</th>
      <th>{{ __('FEES') }}</th>
      <th>{{ __('Payment Fees Vat') }}</th>
      <th>{{ __('Channel Fees') }}</th>
      <th>{{ __('Channel Fees Vat') }}</th>
      <th>{{ __('Net') }}</th>
      <th>{{ __('Paid At') }}</th>
    </tr>
  </thead>
  <tbody>
    @foreach($bills as $bill)
      <tr>
        <td>{{ $bill['name'] }}</td>
        <td>{{ $bill['total'] }}</td>
        <td>{{ $bill['channel_relation'] }}</td>
        <td>{{ $bill['total_due'] }}</td>
        <td>{{ $bill['payment_fees'] }}</td>
        <td>{{ $bill['payment_fees_vat'] }}</td>
        <td>{{ $bill['payment_channel_fees'] }}</td>
        <td>{{ $bill['payment_channel_fees_vat'] }}</td>
        <td>{{ $bill['net'] }}</td>
        <td>{{ $bill['paid_at'] }}</td>
      </tr>
    @endforeach
  </tbody>
</table>