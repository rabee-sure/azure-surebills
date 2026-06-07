<!doctype html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  </head>
  <body>
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
              <ul style="color:#000;list-style: none;margin: 0;padding: 0;">
                <li style="font-size: 16px;margin-bottom: 10px;color: #777777;">
                  المصدر: <b style="color: #000;">{{ $data['source'] }}</b>
                </li>
                <li style="font-size: 16px;margin-bottom: 10px;color: #777777;">
                  الاسم: <b style="color: #000;">{{ $data['name'] }}</b>
                </li>
                <li style="font-size: 16px;margin-bottom: 10px;color: #777777;">
                  البريد الالكتروني: <b dir="ltr" style="color: #000;">{{ $data['email'] }}</b>
                </li>
                <li style="font-size: 16px;margin-bottom: 10px;color: #777777;">
                  اسم المنشأة: <b style="color: #000;">{{ $data['company'] }}</b>
                </li>
                <li style="font-size: 16px;margin-bottom: 10px;color: #777777;">
                  رقم الجوال: <b dir="ltr" style="color: #000;">{{ $data['mobile'] }}</b>
                </li>
                <li style="font-size: 16px;margin-bottom: 10px;color: #777777;">
                  الرسالة:
                  <p style="color: #000;white-space: pre-wrap;">{{ $data['message'] }}</p>
                </li>
              </ul>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </body>
</html>
