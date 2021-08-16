<table class="table table-striped text-center">
  <thead>
    <tr>
      <th>Name</th>
      <th>Source</th>
      <th>Card Type</th>
      <th>Total Paid</th>
      <th>VAT Percentage</th>
      <th>Total Fees</th>
      <th>Total Fees VAT</th>
      <th>Total Fees Percentage</th>
      <th>Total Fees Fixed</th>
      <th>SureBills Fees</th>
      <th>SureBills Fees VAT</th>
      <th>SureBills Fees Percentage</th>
      <th>SureBills Fees Fixed</th>
      <th>Channel Fees</th>
      <th>Channel Fees VAT</th>
      <th>Channel Fees Percentage</th>
      <th>Channel Fees Fixed</th>
      <th>Channel Relation</th>
      <th>Total Due</th>
      <th>Paid At</th>
    </tr>
  </thead>
  <tbody>
    @foreach($bills as $bill)
      <tr>
        <td>{{ $bill['name'] }}</td>
        <td>{{ $bill['source'] }}</td>
        <td>{{ $bill['payment_method_type'] }}</td>
        <td>{{ $bill['total'] }}</td>
        <td>{{ $bill['pricing']['vat_percentage'] ?? ''}}</td>
        <td>{{ $bill['payment_fees'] }}</td>
        <td>{{ $bill['payment_fees_vat'] }}</td>
        <td>{{ $bill['pricing']['fees_percentage'] ?? ''}}</td>
        <td>{{ $bill['pricing']['fees_fixed'] ?? ''}}</td>
        <td>{{ $bill['payment_surebills_fees'] ?? ''}}</td>
        <td>{{ $bill['payment_surebills_fees_vat'] }}</td>
        <td>{{ $bill['pricing']['surebills_fees_percentage'] ?? ''}}</td>
        <td>{{ $bill['pricing']['surebills_fees_fixed'] ?? ''}}</td>
        <td>{{ $bill['payment_channel_fees'] }}</td>
        <td>{{ $bill['payment_channel_fees_vat'] }}</td>
        <td>{{ $bill['pricing']['channel_fees_percentage'] ?? ''}}</td>
        <td>{{ $bill['pricing']['channel_fees_fixed'] ?? ''}}</td>
        <td>{{ $bill['channel_relation'] }}</td>
        <td>{{ $bill['total_due'] }}</td>
        <td>{{ $bill['paid_at'] }}</td>
      </tr>
    @endforeach
  </tbody>
</table>