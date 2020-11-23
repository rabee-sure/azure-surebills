@php
    $direction = 'ltr';
    if(app()->getLocale() == 'ar')
    {
        $direction = 'rtl';
    }
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{$direction}}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <style type="text/css">
      @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@200;400;700&display=swap');
      @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap');
      body {
        padding: 0;
        margin: 0;
      }
      #mail_wrapper
      {
          direction: {{$direction}} !important;
      }
      @media screen {
        @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@200;400;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap');
        #mail_wrapper {
          font-family: 'Tajawal', "Nunito";
        }
        #mail_wrapper .title {
          border: 1px solid #ddd;
          background: #f8fafc;
          text-align: center;
          padding: 20px;
          font-size: 20px;
          color: #000000;
          border-radius: 5px 5px 0 0;
        }
        #mail_wrapper .content {
          background: #fff;
          border-right: 1px solid #ddd;
          border-left: 1px solid #ddd;
          color: #000000;
          padding: 20px;
        }
        #mail_wrapper .content p {
          display: block;
          font-size: 16px;
          margin: 0 auto 10px;
        }
        #mail_wrapper .content p:last-child {
          margin: 0;
        }
        #mail_wrapper .content a {
          display: inline-block;
          background: #00D595;
          color: #fff;
          text-decoration: none;
          font-weight: bold;
          font-size: 14px;
          padding: 10px 20px;
          margin: 0 auto 10px;
          border-radius: 5px;
        }
        #mail_wrapper .content a:hover {
          background: #00ac78;
        }
        #mail_wrapper .copyrights {
          border: 1px solid #ddd;
          background: #f8fafc;
          text-align: center;
          padding: 15px;
          font-size: 13px;
          color: #555555;
          direction: ltr;
          border-radius: 0 0 5px 5px;
        }
      }
    </style>
    <!--[if mso]>
    <style>
      table { border-collapse: collapse; }
      .o_col { float: left; }
    </style>
    <xml>
      <o:OfficeDocumentSettings>
        <o:PixelsPerInch>96</o:PixelsPerInch>
      </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->
  </head>
  <body>
    <div id="mail_wrapper">
      <div class="title">{{__('Password Reset')}}</div>
      <div class="content">
        <p>{{__('You are receiving this email because we received a password reset request for your account.')}} :</p>
        <a href="{{ $url }}" title="{{__('Set a new password')}}">{{__('Set a new password')}}</a>
        <p>{{__('This password reset link will expire in :count minutes.', ['count' => $count])}}</p>
        <p>{{__('If you did not request a password reset, no further action is required.')}}</p>
      </div><!-- content -->
      <div class="copyrights">© 2020 SureBills. All rights reserved</div>
    </div><!-- mail_wrapper -->
  </body>
</html>
