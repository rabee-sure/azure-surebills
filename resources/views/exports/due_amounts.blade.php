<table class="table table-striped text-center">
  <thead>
    <tr>
      <th>Merchant ID</th>
      <th>Merchant Name</th>
      <th>Merchan Iban </th>
      <th>Bank</th>
      <th>Total amount</th>
      <th>total fees</th>
      <th>Total fees vat</th>
      <th>Total Refund</th>
{{--       <th>Sure fees</th>
      <th>sure fees vat</th>
      <th>channel fees</th>
      <th>channel fees vat</th> --}}
      <th>bank charges</th>
      <th>net due</th>
      <th>channel id</th>
      <th>reference</th>
    </tr>
  </thead>
  <tbody>
    @foreach($items as $item)
      <tr>
        <td>{{ $item['merchant_id'] }}</td>
        <td>{{ $item['merchant_name'] }}</td>
        <td>{{ $item['merchan_iban'] }}</td>
        <td>{{ $item['bank'] }}</td>
        <td>{{ $item['total_amount'] }}</td>
        <td>{{ $item['total_fees'] }}</td>
        <td>{{ $item['total_fees_vat'] }}</td>
        <td>{{ $item['total_refund'] }}</td>
{{--         <td>{{ $item['sure_fees'] }}</td>
        <td>{{ $item['sure_fees_vat'] }}</td>
        <td>{{ $item['channel_fees'] }}</td>
        <td>{{ $item['channel_fees_vat'] }}</td> --}}
        <td>{{ $item['bank_charges'] }}</td>
        <td>{{ $item['net_due'] }}</td>
        <td>{{ $item['channel_id'] }}</td>
        <td>{{ $item['transfer_id'] }}</td>
      </tr>
    @endforeach
  </tbody>
</table> 

