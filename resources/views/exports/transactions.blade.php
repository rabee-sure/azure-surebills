<table class="table table-striped text-center">
  <thead>
    <tr>
      <th>Created At</th>
      <th>Description</th>
      <th>Type</th>
      <th>Amount</th>
    </tr>
  </thead>
  <tbody>
    @foreach($transactions as $transaction)
      <tr>
        <td>{{ $transaction['created_at'] }}</td>
        <td>{{ $transaction['description'] }}</td>
        <td>{{ $transaction['type'] }}</td>
        <td>{{ $transaction['amount'] }}</td>
      </tr>
    @endforeach
  </tbody>
</table> 