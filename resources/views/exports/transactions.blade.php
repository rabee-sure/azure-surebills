<table class="table table-striped text-center">
  <thead>
    <tr>
      <th>Created At</th>
      <th>Description</th>
      <th>Type</th>
      <th>Amount</th>
      <th>Transaction Id</th>
      <th>Bill Id</th>
      <th>Bill Reference Id</th>
      <th>Bill Number</th>
      <th>Merchant Id</th>
      <th>Merchant Name</th>
      <th>Card Type</th>
      <th>Card Last 4 digits</th>
      <th>Source</th>
      <th>Channel Id</th>
      <th>Channel Name</th>
    </tr>
  </thead>
  <tbody>
    @foreach($transactions as $transaction)
      <tr>
        <td>{{ $transaction['created_at'] }}</td>
        <td>{{ $transaction['description'] }}</td>
        <td>{{ $transaction['type'] }}</td>
        <td>{{ $transaction['amount'] }}</td>
        <td>{{ $transaction['id'] }}</td>
        <td>{{ $transaction['bill_id'] }}</td>
        <td>{{ $transaction['bill_reference_id'] }}</td>
        <td>{{ $transaction['bill_number'] }}</td>
        <td>{{ $transaction['bill_user_id'] }}</td>
        <td>{{ $transaction['bill_business_name'] }}</td>
        <td>{{ $transaction['card_brand'] }}</td>
        <td>{{ $transaction['card'] }}</td>
        <td>{{ $transaction['source'] }}</td>
        <td>{{ $transaction['bill_application_channel_id'] }}</td>
        <td>{{ $transaction['bill_application_channel_name'] }}</td>
      </tr>
    @endforeach
  </tbody>
</table>

{{--




merchant_id
merchant_name
channel_id (null if the transaction doesn't belong to channel)
channel_name (null if the transaction doesn't belong to channel)
card_type (MADA|VISA|MASTER)
card_last4digits (XXX1111)

 --}}

 {{--
 transaction_id
 bill_id
 bill_reference_id
 bill_number
 source (source of the transaction)
 --}}

    {{--  "bill" => array:46 [
      "id" => "a642b0a7-132e-45be-966c-5cdd1611cb8e"
      "number" => "1000001"
      "name" => "1000001-Kato Middleton"
      "status" => "paid"
      "payment_method" => "credit"
      "payment_method_type" => "VISA"
      "user_id" => 1
      "customer_id" => 35
      "business_name" => "weewe"
      "customer_name" => "Kato Middleton"
      "customer_mobile" => "500000008"
      "customer_email" => "siwux@mailinator.com"
      "customer_notes" => "Qui id velit adipis"
      "reference_id" => null
      "due_date" => "1994-06-23T21:00:00.000000Z"
      "expiry_date" => 3
      "add_discount" => 0
      "discount_type" => "fixed"
      "discount_value" => 0
      "add_tax" => 0
      "tax_name" => null
      "tax_value" => 0
      "send_sms" => 0
      "send_email" => 0
      "sub_total" => 10000
      "vat" => 0
      "payment_fees_vat" => 40.65
      "net" => 9688.35
      "discount" => 0
      "total" => 10000
      "paid_at" => "13/09/2021 17:27"
      "canceled_at" => null
      "pay_url" => "https://bills.test/bills/rw0mKrDWRGsQ5zLVxmWMC96qPDN/pay/ar"
      "payment_fees" => 271
      "created_at" => "13/09/2021 17:27"
      "title" => "الفاتورة 1000001 - Kato Middleton"
      "pricing" => array:8 [
        "type" => "user"
        "fees_fixed" => 1
        "vat_percentage" => 15
        "fees_percentage" => 2.7
        "channel_fees_fixed" => null
        "surebills_fees_fixed" => 1
        "channel_fees_percentage" => null
        "surebills_fees_percentage" => 2.7
      ]
      "source" => "Manual"
      "related_channel" => false
      "channel_name" => null
      "channel_relation" => "Owner"
      "payment_channel_fees" => 0
      "payment_channel_fees_vat" => 0
      "total_due" => 9688.35
      "payment_surebills_fees" => 271
      "payment_surebills_fees_vat" => 40.65
    ] --}}
