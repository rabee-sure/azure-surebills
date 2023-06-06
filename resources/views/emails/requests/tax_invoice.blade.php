<!doctype html>
<html lang="ar"  dir="rtl">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
  </head>
  <body>

  <!-- Start Code Here -->
  <table style="font-family: arial;border-spacing: 0;border-collapse: collapse;width: 100%;direction: rtl;">
    <thead>
      <tr>
        <th style="background-color: #edf2f7;padding: 25px 0 0;font-weight: bold;font-size: 30px;color: #3d4852;">SureBills</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td style="background-color: #edf2f7;padding: 25px;">
          <div style="background-color: #ffffff;padding: 30px;border-radius: 20px;box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.08);">
            @if($user->logo)
              <div class="logo" style="display: block;text-align: center;margin-bottom: 1rem;">
                <img src="{{ $user->logo_url }}" alt="logo">
              </div><!-- logo -->
            @endif
            <b style="text-align:center;color:#000;display: block;margin-bottom: 1rem;">{{ $user->business_address }}</b>
            <ul style="color:#000;list-style: none;margin: 0;padding: 0;">
              <li style="font-size: 16px;margin-bottom: 10px;color: #777777;">
                
              </li>
              <li style="font-size: 16px;margin-bottom: 10px;color: #777777;">
                تم إرسال طلب فاتورة ضريبية من التاجر 
                <b dir="ltr" style="color: #000">{{$user->business_name_en}}</b> 
              </li>
              <li style="font-size: 16px;margin-bottom: 10px;color: #777777;">
                {{__('Merchant ID', [], 'ar')}} : <b dir="ltr" style="color: #000000;">{{$user->id}}</b>
              </li>
              <li style="font-size: 16px;margin-bottom: 10px;color: #777777;">
                {{__('Email', [], 'ar')}} : <b dir="ltr" style="color: #000000;">{{$user->email}}</b>
              </li>
            </ul>

          </div>
        </td>
      </tr>
    </tbody>
    <tfoot>
      <tr>
        <th style="background-color: #edf2f7;font-weight: 100;font-size: 14px;direction: ltr;text-align: center;color: #b0adc5;padding:  0 0 25px">© 2022 SureBills. All rights reserved.</th>
      </tr>
    </tfoot>
  </table>
  <!-- End Code Here -->
  </body>
</html>