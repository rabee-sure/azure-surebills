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
        <span style="font-weight: bold;font-size: 20px;color: #000;">إشعار دائن</span>
        <p style="font-size: 20px;font-weight: bold;margin: 10px auto 0;">Credit Note</p>
      </div><!-- header -->
      <table style="float: right;margin: 0 auto 20px;">
        <tbody>
          <tr>
            <td style="width: 150px;height: 150px;vertical-align: middle;text-align: center;border: 1px solid #000;">
              {!! generateQRcode($refundedBill) !!}
              <span style="display: block;line-height: 1.5;font-size: 11px;margin-bottom: 5px;">تم إنشاء كود الاستجابة السريعة بواسطة حل الفوترة الإلكترونية لدافعي الضرائب وفقاً لمواصفات ZATCA.</span>
            </td>
          </tr>
        </tbody>
      </table>
      <table style="width: 70%;float: left;">
        <tr>
          <td style="text-align: right;border: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;">رقم إشعار الدائن :</td>
          <td style="text-align: center;border-bottom: 1px solid #000;border-top: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;">{{ $refundedBill->number ?? $refundedBill->id }}</td>
          <td style="text-align: left;border: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%; direction: ltr;">Credit Note Number :</td>
        </tr>
        <tr>
          <td style="text-align: right;border: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;">تاريخ إشعار الدائن :</td>
          <td style="text-align: center;border-bottom: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;">{{ $refundedBill->created_at->format('d/m/Y')}}</td>
          <td style="text-align: left;border: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;direction: ltr;">Credit Note Date :</td>
        </tr>
        <tr>
          <td style="text-align: right;border: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;">رقم الفاتورة :</td>
          <td style="text-align: center;border-bottom: 1px solid #000;border-top: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;">{{ $refundedBill->bill->number ?? $refundedBill->bill->id }}</td>
          <td style="text-align: left;border: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%; direction: ltr;">Invoice Number :</td>
        </tr>
        <tr>
          <td style="text-align: right;border: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;">تاريخ الفاتورة :</td>
          <td style="text-align: center;border-bottom: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;">{{ $refundedBill->bill->created_at->format('d/m/Y')}}</td>
          <td style="text-align: left;border: 1px solid #000;padding: 10px;font-size: 8pt;width: 25%;direction: ltr;">Invoice Date :</td>
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
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $refundedBill->bill->customer->name}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Name :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">رقم المبني :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $refundedBill->bill->customer->bullding_no}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Bullding No. :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">اسم الشارع :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $refundedBill->bill->customer->street_name}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Street Name :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">الحي :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $refundedBill->bill->customer->district}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">District :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">المدينة :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $refundedBill->bill->customer->city}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">City :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">البلد :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">المملكة العربية السعودية</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Country :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">الرمز البريدي :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $refundedBill->bill->customer->postal_code}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Postal Code :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">الرقم الاضافي للعنوان :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $refundedBill->bill->customer->additional_no}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Additional No. :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">رقم تسجيل ضريبة القيمة المضافة :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{$refundedBill->bill->customer->vat_registration_number}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">VAT Number :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">معرف آخر :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $refundedBill->bill->customer->other_buyer_id}}</td>
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
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $refundedBill->bill->user->business_name_en}}</td>
                      @elseif ( Config::get('app.locale') == 'ar')
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $refundedBill->bill->user->business_name_ar}}</td>
                      @endif
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Name :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">رقم المبني :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $refundedBill->bill->user->bullding_no}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Bullding No. :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">اسم الشارع :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $refundedBill->bill->user->street_name}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Street Name :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">الحي :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $refundedBill->bill->user->district}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">District :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">المدينة :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $refundedBill->bill->user->city}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">City :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">البلد :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">المملكة العربية السعودية</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Country :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">الرمز البريدي :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $refundedBill->bill->user->postal_code}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Postal Code :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">الرقم الاضافي للعنوان :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $refundedBill->bill->user->additional_no}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Additional No. :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">رقم تسجيل ضريبة القيمة المضافة :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $refundedBill->bill->user->vat_registration_number}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">VAT Number :</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">معرف آخر :</td>
                      <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 50%;">{{ $refundedBill->bill->user->other_buyer_id}}</td>
                      <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Other Buyer ID :</td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
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
              <td style="border: 1px solid #000;padding: 5px;text-align: right;font-weight: normal;font-size: 8pt;width: 25%;">اجمالي المبلغ المرتجع</td>
              <td style="border-bottom: 1px solid #000;padding: 5px;text-align: center;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">{{ $refundedBill->amount}} SAR</td>
              <td style="border: 1px solid #000;padding: 5px;text-align: left;direction: ltr;font-weight: normal;font-size: 8pt;width: 25%;">Total Amount Refunded</td>
            </tr>
          </tbody>
        </table>
      </div><!-- tabe_1 -->
    </div><!-- app -->
  </body>
</html>