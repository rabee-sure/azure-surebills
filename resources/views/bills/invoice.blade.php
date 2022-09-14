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
    <div class="app" style="padding: 10px;">
      <div class="header" style="margin: 15px auto 20px;text-align: center;">
        <span style="font-weight: bold;font-size: 20px;color: #000;">فاتورة ضريبية</span>
        <p style="font-size: 20px;font-weight: bold;margin: 10px auto 0;">Tax Invoice</p>
      </div><!-- header -->
      <table style="float: right;margin: 0 auto 20px;">
        <tbody>
          <tr>
            <td style="width: 150px;height: 150px;vertical-align: middle;text-align: center;border: 1px solid #000;">
              {!! generateQRcode($bill) !!}
              <span style="display: block;line-height: 1.5;font-size: 11px;margin-bottom: 5px;">تم إنشاء كود الاستجابة السريعة بواسطة حل الفوترة الإلكترونية لدافعي الضرائب وفقاً لمواصفات ZATCA.</span>
            </td>
          </tr>
        </tbody>
      </table>
      <table style="width: 70%;float: left;">
        <tr>
          <td style="text-align: right;border: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;">رقم الفاتورة :</td>
          <td style="text-align: center;border-bottom: 1px solid #000;border-top: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;">DN{{ $bill->number ?? $bill->id }}</td>
          <!-- <td style="text-align: center;border: 2px solid #000;padding: 10px;font-size: 8pt;width: 25%;">{{ $bill->number ?? $bill->id }}</td> -->
          <td style="text-align: left;border: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%; direction: ltr;">Invoice Number :</td>
        </tr>
        <tr>
          <td style="text-align: right;border: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;">تاريخ اصدار الفاتورة :</td>
          <td style="text-align: center;border-bottom: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;">{{ $bill->created_at->format('d/m/Y')}}</td>
          <!-- <td style="text-align: center;border: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;">{{ $bill->created_at->format('d/m/Y')}}</td> -->
          <td style="text-align: left;border: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;direction: ltr;">Invoice Issue Date :</td>
        </tr>
        <tr>
          <td style="text-align: right;border: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;">تاريخ التوريد :</td>
          @if($bill->status == 'paid')
          <td style="text-align: center;border-bottom: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;">{{ $bill->paid_at->format('d/m/Y')}}</td>
          @else
          <td style="text-align: center;border-bottom: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;">{{ $bill->created_at->format('d/m/Y')}}</td>
          @endif
          <td style="text-align: left;border: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;direction: ltr;">Date Of Supply :</td>
        </tr>
        <tr>
          <td style="text-align: right;border: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;">تاريخ الاستحقاق :</td>
          @if($bill->status == 'paid')
          <td style="text-align: center;border-bottom: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;">{{ $bill->paid_at->format('d/m/Y')}}</td>
          @else
          <td style="text-align: center;border-bottom: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;">{{ $bill->created_at->format('d/m/Y')}}</td>
          @endif
          <td style="text-align: left;border: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;direction: ltr;">Due Date :</td>
        </tr>
      </table>
      <div class="table_1" style="width: 100%;margin: 0 auto 20px;">
        <table class="table_title" style="width: 100%;margin-bottom: 8px;">
          <tbody>
            <tr>
              <td width="50%">
                <table width="98%">
                  <tbody>
                    <tr>
                      <td colspan="4" style="color: #fff;background-color: #777;border: 1px solid #555;padding: 5px;font-weight: bold;font-size: 8pt;">
                        <span style="float: right;">العميل</span>
                        <span style="float: left;direction: ltr;">Buyer</span>
                      </td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">الاسم :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $bill->customer->name}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Name :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">رقم المبني :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $bill->customer->bullding_no}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Bullding No. :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">اسم الشارع :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $bill->customer->street_name}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Street Name :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">الحي :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $bill->customer->district}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">District :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">المدينة :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $bill->customer->city}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">City :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">البلد :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">المملكة العربية السعودية</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Country :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">الرمز البريدي :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $bill->customer->postal_code}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Postal Code :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">الرقم الاضافي للعنوان :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $bill->customer->additional_no}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Additional No. :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">رقم تسجيل ضريبة القيمة المضافة :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{$bill->customer->vat_registration_number}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">VAT Number :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">معرف آخر :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $bill->customer->other_buyer_id}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Other Buyer ID :</td>
                    </tr>
                  </tbody>
                </table>
              </td>
              <td width="50%">
                <table width="98%" style="float: left;">
                  <tbody>
                    <tr>
                      <td colspan="4" style="color: #fff;background-color: #777;border: 1px solid #555;padding: 5px;font-weight: bold;font-size: 8pt;">
                        <span style="float: right;">المورد</span>
                        <span style="float: left;direction: ltr;">Seller</span>
                      </td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">الاسم :</td>
                      @if ( Config::get('app.locale') == 'en')
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $bill->user->business_name_en}}</td>
                      @elseif ( Config::get('app.locale') == 'ar')
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $bill->user->business_name_ar}}</td>
                      @endif
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Name :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">رقم المبني :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $bill->user->bullding_no}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Bullding No. :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">اسم الشارع :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $bill->user->street_name}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Street Name :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">الحي :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $bill->user->district}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">District :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">المدينة :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $bill->user->city}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">City :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">البلد :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">المملكة العربية السعودية</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Country :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">الرمز البريدي :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $bill->user->postal_code}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Postal Code :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">الرقم الاضافي للعنوان :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $bill->user->additional_no}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Additional No. :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">رقم تسجيل ضريبة القيمة المضافة :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $bill->user->vat_registration_number}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">VAT Number :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">معرف آخر :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $bill->user->other_buyer_id}}</td>
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
              <td colspan="9" style="color: #fff;background-color: #777;border: 1px solid #555;padding: 5px;font-weight: bold;font-size: 8pt;">
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
              <!-- <td style="border: 1px solid #000;font-size: 8pt;padding: 5px;color: #fff;background-color: #666;font-weight: normal;text-align: center;width: 12.5%;">
                Discount
                <br>
                خصومات
              </td> -->
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
              <td style="border: 1px solid #000;font-size: 8pt;padding: 5px;color: #fff;background-color: #666;font-weight: normal;text-align: center;width: 5%;"># رقم</td>
            </tr>

            @php
            $index = 0;
            @endphp
            @foreach($bill->items as $item)
            @php
              $tax_total = ($bill->add_tax) ? $bill->tax_value * (($item->product_price* $item->quantity)) / 100 : 0;
              $total = ($item->product_price* $item->quantity) + $tax_total;
              $index++;
            @endphp
            <tr>
              <td style="border: 1px solid #000;padding: 5px;text-align: center;font-weight: normal;font-size: 8pt;width: 12.5%;direction: ltr;">{{$total}}</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: center;font-weight: normal;font-size: 8pt;width: 12.5%;direction: ltr;">@if($bill->add_tax) {{$tax_total}} SAR @else 0 @endif</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: center;font-weight: normal;font-size: 8pt;width: 12.5%;direction: ltr;">@if($bill->add_tax) {{$bill->tax_value}} % @else -@endif</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: center;font-weight: normal;font-size: 8pt;width: 12.5%;direction: ltr;">{{$item->total}} SAR</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: center;font-weight: normal;font-size: 8pt;width: 12.5%;direction: ltr;">{{$item->quantity}}</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: center;font-weight: normal;font-size: 8pt;width: 12.5%;direction: ltr;">{{$item->product_price}} SAR</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: center;font-weight: normal;font-size: 8pt;width: 12.5%;direction: ltr;">{{$item->product_name}}</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: center;font-weight: normal;font-size: 8pt;width:5%;">{{$index}}</td>
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
              <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">الاجمالي (غير شامل ضريبة القيمة المضافة)</td>
              <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;font-weight: normal;font-size: 8pt;width: 25%;direction: ltr;">{{ $bill->sub_total}} SAR</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Total (Excluding VAT)</td>
            </tr>
            <tr>
              <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">مجموع الخصومات</td>
              <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->discount}} SAR</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Discount</td>
            </tr>
            <tr>
              <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">الاجمالي الخاضع للضريبة</td>
              <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;font-weight: normal;font-size: 8pt;width: 25%;direction: ltr;">{{ $bill->sub_total - $bill->discount}} SAR</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Total Taxable Amount (Excluding VAT)</td>
            </tr>
            <tr>
              <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">مجموع ضريبة القيمة المضافة</td>
              <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->vat}} SAR</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Total VAT</td>
            </tr>
            <tr>
              <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">اجمالي المبلغ المستحق</td>
              <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $bill->sub_total + $bill->vat - $bill->discount}} SAR</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Total Amount Due</td>
            </tr>
          </tbody>
        </table>
      </div><!-- tabe_1 -->
    </div><!-- app -->
  </body>
</html>