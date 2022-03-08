<table class="table table-striped text-center">
  <thead>
    <tr>
      <th>MID</th>
      <th>Merchant_Name</th>
      <th>Total_amount_in</th>
      <th>Total_fee_with_vat</th>
      <th>Total_refund</th>
      <th>Total_transfer_fees</th>
      <th>Total_net_transfer</th>
      <th>Outstanding_balance</th>
      <th>Range_balance</th>
    </tr>
  </thead>
  <tbody>
    @foreach($data as $raw)
      <tr>
        <td>{{ $raw->MID }}</td>
        <td>{{ $raw->Merchant_Name }}</td>
        <td>{{ $raw->Total_amount_in }}</td>
        <td>{{ $raw->Total_fee_with_vat }}</td>
        <td>{{ $raw->Total_refund }}</td>
        <td>{{ $raw->Total_transfer_fees }}</td>
        <td>{{ $raw->Total_net_transfer }}</td>
        <td>{{ $raw->Outstanding_balance }}</td>
        <td>{{ $raw->Range_balance }}</td>
      </tr>
    @endforeach
  </tbody>
</table> 