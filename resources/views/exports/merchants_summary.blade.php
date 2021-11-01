<table class="table table-striped text-center">
  <thead>
    <tr>
      <th>Client ID</th>
      <th>Payment Type</th>
      <th>No. of Trx</th>
      <th>Total Amount</th>
      <th>Total fees</th>
      <th>Total fees vat</th>
      <th>Total Fees variable rate</th>
      <th>Total Fees fixed rate</th>
      <th>Sure Variable rate</th>
      <th>Sure fixed rate</th>
      <th>Channel variable rate</th>
      <th>Channel fixed rate</th>
      <th>Sure fees</th>
      <th>Sure vat</th>
      <th>Channel Fees</th>
      <th>Channels vat</th>
      <th>Channel Id</th>
    </tr>
  </thead>
  <tbody>
    @foreach($items as $item)
      <tr>
        <td>{{ $item['client_id'] }}</td>
        <td>{{ $item['payment_type'] }}</td>
        <td>{{ $item['no_of_trx'] }}</td>
        <td>{{ $item['total_amount'] }}</td>
        <td>{{ $item['total_fees'] }}</td>
        <td>{{ $item['total_fees_vat'] }}</td>
        <td>{{ $item['total_fees_variable_rate'] }}</td>
        <td>{{ $item['total_fees_fixed_rate'] }}</td>
        <td>{{ $item['sure_variable_rate'] }}</td>
        <td>{{ $item['sure_fixed_rate'] }}</td>
        <td>{{ $item['channel_variable_rate'] }}</td>
        <td>{{ $item['channel_fixed_rate'] }}</td>
        <td>{{ $item['sure_fees'] }}</td>
        <td>{{ $item['sure_vat'] }}</td>
        <td>{{ $item['channel_fees'] }}</td>
        <td>{{ $item['channels_vat'] }}</td>
        <td>{{ $item['channel_id'] }}</td>
      </tr>
    @endforeach
  </tbody>
</table> 

