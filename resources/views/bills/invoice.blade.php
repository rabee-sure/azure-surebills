<!DOCTYPE html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><html class="no-js"> <![endif]-->
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>SurePay</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/invoice.css">
  </head>
  <body>
    <div class="app">
      <div class="header" style="margin: 15px auto 20px;text-align: center;">
        <span style="font-weight: bold;font-size: 20px;color: #000;">فاتورة ضريبية</span>
        <p style="font-size: 20px;font-weight: bold;margin: 10px auto 0;">Tax Invoice</p>
      </div><!-- header -->
      <table style="float: right;margin: 0 auto 20px;">
        <tbody>
          <tr>
            <td style="width: 150px;height: 150px;vertical-align: middle;text-align: center;border: 2px solid #000;">
              {!! QrCode::size(140)->generate(route('invoice', ['id' => $id])) !!}
            </td>
          </tr>
        </tbody>
      </table>
      <table style="width: 75%;float: left;">
        <tr>
          <td style="text-align: right;border: 2px solid #000;padding: 10px;font-size: 8pt;width: 25%;">رقم الفاتورة :</td>
          <td style="text-align: center;border: 2px solid #000;padding: 10px;font-size: 8pt;width: 25%;">{{ $bill->number ?? $bill->id }}</td>
          <td style="text-align: center;border: 2px solid #000;padding: 10px;font-size: 8pt;width: 25%;">{{ $bill->number ?? $bill->id }}</td>
          <td style="text-align: left;border: 2px solid #000;padding: 10px;font-size: 8pt;width: 25%; direction: ltr;">Invoice Number :</td>
        </tr>
        <tr><td><br></td></tr>
        <tr>
          <td style="text-align: right;border: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;">تاريخ اصدار الفاتورة :</td>
          <td style="text-align: center;border: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;">{{ $bill->created_at->format('d/m/Y')}}</td>
          <td style="text-align: center;border: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;">{{ $bill->created_at->format('d/m/Y')}}</td>
          <td style="text-align: left;border: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;direction: ltr;">Invoice Issue Date :</td>
        </tr>
        <tr>
          <td style="text-align: right;border: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;">تاريخ التوريد :</td>
          <td style="text-align: center;border: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;">{{ $bill->paid_at->format('d/m/Y')}}</td>
          <td style="text-align: center;border: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;">{{ $bill->paid_at->format('d/m/Y')}}</td>
          <td style="text-align: left;border: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;direction: ltr;">Date Of Supply :</td>
        </tr>
      </table>
      <div class="table_1" style="width: 100%;margin: 0 auto 20px;">
        <table class="table_title" style="width: 100%;margin-bottom: 8px;">
          <tbody>
            <tr>
              <td width="50%">
                <table width="100%">
                  <tbody>
                    <tr>
                      <td colspan="4" style="color: #fff;background-color: #777;border: 1px solid #555;padding: 5px;font-weight: bold;font-size: 8pt;">
                        <span style="float: right;">العميل</span>
                        <span style="float: left;direction: ltr;">Buyer</span>
                      </td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">الاسم :</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->customer->name}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->customer->name}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Name :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">رقم المبني :</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->customer->bullding_no}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->customer->bullding_no}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Bullding No. :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">اسم الشارع :</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->customer->street_name}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->customer->street_name}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Street Name :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">الحي :</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->customer->district}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->customer->district}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">District :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">المدينة :</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->customer->city}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->customer->city}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">City :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">البلد :</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">المملكة العربية السعودية</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">saudi arabia</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Country :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">الرمز البريدي :</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->customer->postal_code}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->customer->postal_code}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Postal Code :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">الرقم الاضافي للعنوان :</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->customer->additional_no}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->customer->additional_no}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Additional No. :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">رقم تسجيل ضريبة القيمة المضافة :</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;"></td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;"></td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">VAT Number :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">معرف آخر :</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->customer->other_buyer_id}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->customer->other_buyer_id}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Other Buyer ID :</td>
                    </tr>
                  </tbody>
                </table>
              </td>
              <td width="50%">
                <table width="100%">
                  <tbody>
                    <tr>
                      <td colspan="4" style="color: #fff;background-color: #777;border: 1px solid #555;padding: 5px;font-weight: bold;font-size: 8pt;">
                        <span style="float: right;">المورد</span>
                        <span style="float: left;direction: ltr;">Seller</span>
                      </td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">الاسم :</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->user->name}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->user->name}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Name :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">رقم المبني :</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->user->bullding_no}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->user->bullding_no}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Bullding No. :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">اسم الشارع :</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->user->street_name}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->user->street_name}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Street Name :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">الحي :</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->user->district}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->user->district}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">District :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">المدينة :</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->user->city}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->user->city}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">City :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">البلد :</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">المملكة العربية السعودية</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">saudi arabia</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Country :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">الرمز البريدي :</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->user->postal_code}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->user->postal_code}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Postal Code :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">الرقم الاضافي للعنوان :</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->user->additional_no}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->user->additional_no}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Additional No. :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">رقم تسجيل ضريبة القيمة المضافة :</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->user->vat_registration_number}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->user->vat_registration_number}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">VAT Number :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">معرف آخر :</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->user->other_buyer_id}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->user->other_buyer_id}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Other Buyer ID :</td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
          </tbody>
        </table>
      </div><!-- tabe_1 -->
      <div class="table_1" style="width: 100%;margin: 0 auto 20px;">
        <table class="table_title" style="width: 100%;">
          <tbody>
            <tr>
              <td colspan="8" style="color: #fff;background-color: #777;border: 1px solid #555;padding: 5px;font-weight: bold;font-size: 8pt;">
                <span style="float: right;">توصيف السلعة آو الخدمة :</span>
                <span style="float: left;direction: ltr;">Line Items :</span>
              </td>
            </tr>
            <tr>
              <td style="border: 1px solid #000;font-size: 8pt;padding: 5px;color: #fff;background-color: #666;font-weight: normal;text-align: center;width: 12.5%;">
                Item Subtotal (Including VAT)
                <br>
                المجموع (شامل ضريبة القيمة المضافة)
              </td>
              <td style="border: 1px solid #000;font-size: 8pt;padding: 5px;color: #fff;background-color: #666;font-weight: normal;text-align: center;width: 12.5%;">
                Tax amount
                <br>
                مبلغ الضريبة
              </td>
              <td style="border: 1px solid #000;font-size: 8pt;padding: 5px;color: #fff;background-color: #666;font-weight: normal;text-align: center;width: 12.5%;">
                Tax Rate
                <br>
                نسبة الضريبة
              </td>
              <td style="border: 1px solid #000;font-size: 8pt;padding: 5px;color: #fff;background-color: #666;font-weight: normal;text-align: center;width: 12.5%;">
                Discount
                <br>
                خصومات
              </td>
              <td style="border: 1px solid #000;font-size: 8pt;padding: 5px;color: #fff;background-color: #666;font-weight: normal;text-align: center;width: 12.5%;">
                Taxable Amount
                <br>
                المبلغ الخاضع للضريبة
              </td>
              <td style="border: 1px solid #000;font-size: 8pt;padding: 5px;color: #fff;background-color: #666;font-weight: normal;text-align: center;width: 12.5%;">
                Quantity
                <br>
                الكمية
              </td>
              <td style="border: 1px solid #000;font-size: 8pt;padding: 5px;color: #fff;background-color: #666;font-weight: normal;text-align: center;width: 12.5%;">
                Unit Price
                <br>
                سعر الوحدة
              </td>
              <td style="border: 1px solid #000;font-size: 8pt;padding: 5px;color: #fff;background-color: #666;font-weight: normal;text-align: center;width: 12.5%;">
                Nature of Goods Or Services
                <br>
                تفاصيل السلع آو الخدمات
              </td>
            </tr>
            @foreach($bill->items as $item)
            @php

              if($bill->discount_type == 'percentage'){
                $discount_total = ($bill->add_discount) ? $bill->discount_value * $item->product_price* $item->quantity / 100 : 0;
              }elseif($bill->discount_type == 'fixed'){
                $perc = $bill->sub_total / $bill->discount_value;
                $discount_total = ($bill->add_discount) ? $perc * $item->product_price* $item->quantity / 100 : 0;
              }else{
                $discount_total = 0;
              }
              $tax_total = ($bill->add_tax) ? $bill->tax_value * (($item->product_price* $item->quantity)-$discount_total) / 100 : 0;
              $total = ($item->product_price* $item->quantity) - $discount_total + $tax_total;
            @endphp
            <tr>
              <td style="border: 1px solid #000;padding: 5px;text-align: center;font-weight: normal;font-size: 8pt;width: 12.5%;direction: ltr;">{{$total}}</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: center;font-weight: normal;font-size: 8pt;width: 12.5%;direction: ltr;">@if($bill->add_tax) {{$tax_total}} SAR @else 0 @endif</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: center;font-weight: normal;font-size: 8pt;width: 12.5%;direction: ltr;">@if($bill->add_tax) {{$bill->tax_value}} % @else -@endif</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: center;font-weight: normal;font-size: 8pt;width: 12.5%;direction: ltr;">@if($bill->add_discount) {{$discount_total}} SAR @else 0 @endif</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: center;font-weight: normal;font-size: 8pt;width: 12.5%;direction: ltr;">{{$item->total}} SAR</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: center;font-weight: normal;font-size: 8pt;width: 12.5%;direction: ltr;">{{$item->quantity}}</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: center;font-weight: normal;font-size: 8pt;width: 12.5%;direction: ltr;">{{$item->product_price}} SAR</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: center;font-weight: normal;font-size: 8pt;width: 12.5%;direction: ltr;">{{$item->product_name}}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div><!-- tabe_1 -->
      <div class="table_1" style="width: 100%;">
        <table class="table_title" style="width: 100%;">
          <tbody>
            <tr>
              <td colspan="8" style="color: #fff;background-color: #777;border: 1px solid #555;padding: 5px;font-weight: bold;font-size: 8pt;">
                <span style="float: right;">اجمالي المبالغ :</span>
                <span style="float: left;direction: ltr;">Total Amounts :</span>
              </td>
            </tr>
            <tr>
              <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;direction: ltr;">{{ $bill->sub_total}} SAR</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">الاجمالي (غير شامل ضريبة القيمة المضافة)</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Total (Excluding VAT)</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->sub_total}} SAR</td>
            </tr>
            <tr>
              <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;direction: ltr;">{{ $bill->discount}} SAR</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">مجموع الخصومات</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Discount</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->discount}} SAR</td>
            </tr>
            <tr>
              <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;direction: ltr;">{{ $bill->sub_total - $bill->discount}} SAR</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">الاجمالي الخاضع للضريبة</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Total Taxable Amount (Excluding VAT)</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->sub_total - $bill->discount}} SAR</td>
            </tr>
            <tr>
              <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;direction: ltr;">{{ $bill->vat}}  SAR</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">مجموع ضريبة القيمة المضافة</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Total VAT</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->vat}} SAR</td>
            </tr>
            <tr>
              <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;direction: ltr;">{{ $bill->total}} SAR</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">اجمالي المبلغ المستحق</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Total Amount Due</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->total}} SAR</td>
            </tr>
          </tbody>
        </table>
      </div><!-- tabe_1 -->
    </div><!-- app -->
  </body>
</html>