<table class="table table-striped text-center">
  <thead>
    <tr>
      <th>MID</th>
      <th>Merchant_Name</th>
      <th>Payment_gateway_ID</th>
      <th>paid_at</th>
      <th>status</th>
      <th>Channel_Name</th>
      <th>total</th>
      <th>Card_type</th>
      <th>vat_percentage</th>
      <th>Total_Fees</th>
      <th>Total_Fees_VAT</th>
      <th>total_fees_fixed</th>
      <th>total_fees_percentage</th>
      <th>Channel_Fees</th>
      <th>Channel_Fees_VAT</th>
      <th>channel_fees_fixed</th>
      <th>channel_fees_percentage</th>
      <th>Surebills_Fees</th>
      <th>Surebills_Fees_VAT</th>
      <th>surebills_fees_fixed</th>
      <th>surebills_fees_percentage</th>
      <th>refund_amount</th>
      <th>Transfer_id</th>
    </tr>
  </thead>
  <tbody>
    @foreach($data as $raw)
      <tr>
        <td>{{ $raw->MID }}</td>
        <td>{{ $raw->Merchant_Name }}</td>
        <td>{{ $raw->Payment_gateway_ID }}</td>
        <td>{{ $raw->paid_at }}</td>
        <td>{{ $raw->status }}</td>
        <td>{{ $raw->Channel_Name }}</td>
        <td>{{ $raw->total }}</td>
        <td>{{ $raw->Card_type }}</td>
        <td>{{ $raw->vat_percentage }}</td>
        <td>{{ $raw->Total_Fees }}</td>
        <td>{{ $raw->Total_Fees_VAT }}</td>
        <td>{{ $raw->total_fees_fixed }}</td>
        <td>{{ $raw->total_fees_percentage }}</td>
        <td>{{ $raw->Channel_Fees }}</td>
        <td>{{ $raw->Channel_Fees_VAT }}</td>
        <td>{{ $raw->channel_fees_fixed }}</td>
        <td>{{ $raw->channel_fees_percentage }}</td>
        <td>{{ $raw->Surebills_Fees }}</td>
        <td>{{ $raw->Surebills_Fees_VAT }}</td>
        <td>{{ $raw->surebills_fees_fixed }}</td>
        <td>{{ $raw->surebills_fees_percentage }}</td>
        <td>{{ $raw->refund_amount }}</td>
        <td>{{ $raw->Transfer_id }}</td>
      </tr>
    @endforeach
  </tbody>
</table>
