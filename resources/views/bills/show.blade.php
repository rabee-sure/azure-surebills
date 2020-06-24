 @extends('layouts.app')

@section('title', 'Page Title')

@section('content')
        <div class="row">
          <div class="col-12">
            <h1>Bill</h1>
            <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
              <ol class="breadcrumb pt-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{__('Home')}}</a></li>
                <li class="breadcrumb-item"><a href="{{ url('bills') }}">{{__('Bills')}}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{__('Bill')}} {{ $bill->id }}</li>
              </ol>
            </nav>
            <div class="separator mb-5"></div>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <div class="card mb-5">
              <div class="card-body">
                <a class="btn btn-info mr-2 mb-2 d-inline-block" href="{{ $bill->pay_url}}" target="_blanck">{{ __('Open Link') }}</a>
                <button class="btn btn-info mr-2 mb-2 d-inline-block copyButton">{{ __('Copy Link') }}</button>
                <input class="linkToCopy" value="{{ $bill->pay_url}}"
style="position: absolute; z-index: -999; opacity: 0;" />
                <a onclick="window.print(); return false;" class="btn btn-info mr-2 mb-2 d-inline-block" href="#">{{ __('Print') }}</a>
                {{-- <a class="btn btn-info mr-2 mb-2 d-inline-block" href="#">{{ __('Send Reminder') }}</a> --}}
              </div>
            </div>
          </div>
        </div>

        <div class="row invoice">
          <div class="col-12">
            <div class="invoice-contents" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-color:#ffffff; min-height: 900px; max-width:830px; font-family: Helvetica,Arial,sans-serif !important; position: relative;">
              <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" style="width:100%; background-color:#ffffff;border-collapse:separate !important; border-spacing:0;color:#242128; margin:0;padding:15px;" heigth="auto">
                <tbody>
                  <tr>
                    <td align="left" valign="center" style="padding-bottom:35px; padding-top:15px; border-top:0;width:100% !important;"><img src="img/logoCN.png" width="100px" /></td>
                    <td align="right" valign="center" style="padding-bottom:35px; padding-top:15px; border-top:0;width:100% !important;">
                      <p style="color: #8f8f8f; font-weight: normal; line-height: 1.2; font-size: 12px; white-space: nowrap; ">
                        {{ $bill->business_name}}
                        <br>
                        Riyadh, Saudi Arabia
                        <br>
                        0551234567
                      </p>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2" style="padding-top:30px; border-top:1px solid #f1f0f0">
                      <table style="width: 100%;">
                        <tbody>
                          <tr>
                            <td style="vertical-align:middle; border-radius: 3px; padding:30px; background-color: #f9f9f9; border-right: 5px solid white;">
                              <p style="color:#303030; font-size: 14px;  line-height: 1.6; margin:0; padding:0;">
                                Due on {{ $bill->due_date->format('M d Y')}}
                              </p>
                            </td>
                            <td style="text-align: right; padding-top:0px; padding-bottom:0; vertical-align:middle; padding:30px; background-color: #f9f9f9; border-radius: 3px; border-left: 5px solid white;">
                              <p style="color:#8f8f8f; font-size: 14px; padding: 0; line-height: 1.6; margin:0; ">
                                Bill # : {{ $bill->id}}
                                <br>
                                {{ $bill->due_date->format('Y-d-m')}}
                              </p>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                      <table style="width: 100%; margin-top:40px;">
                        <thead>
                          <tr>
                            <th style="text-align:left; font-size: 10px; color:#8f8f8f; padding-bottom: 15px;">
                              ITEM NAME
                            </th>
                            <th style="text-align:left; font-size: 10px; color:#8f8f8f; padding-bottom: 15px;">
                              COUNT
                            </th>
                            <th style="text-align:right; font-size: 10px; color:#8f8f8f; padding-bottom: 15px;">
                              PRICE
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($bill->items as $item)
                            <tr>
                              <td style="padding-top:0px; padding-bottom:5px;">
                                <h4 style="font-size: 16px; line-height: 1; margin-bottom:0; color:#303030; font-weight:500; margin-top: 10px;">{{ $item->product_name}}</h4>
                              </td>
                              <td>
                                <p href="#" style="font-size: 13px; text-decoration: none; line-height: 1; color:#909090; margin-top:0px; margin-bottom:0;">{{$item->quantity}} X</p>
                              </td>
                              <td style="padding-top:0px; padding-bottom:0; text-align: right;">
                                <p style="font-size: 13px; line-height: 1; color:#303030; margin-bottom:0; margin-top:0; vertical-align:top; white-space:nowrap;">{{ $item->total}} SAR</p>
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

                      <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0"
                          style="position:absolute; bottom:0; width:100%; background-color:#ffffff;border-collapse:separate !important; border-spacing:0;color:#242128; margin:0;padding:30px; padding-top: 20px;"
                          heigth="auto">
                          <tr>
                              <td colspan="3" style="border-top:1px solid #f1f0f0">&nbsp;</td>
                          </tr>
                          <tr>
                              <td colspan="2" style="width: 100%">
                                  <p href="#"
                                      style="font-size: 13px; text-decoration: none; line-height: 1.6; color:#909090; margin-top:0px; margin-bottom:0; text-align: right;">
                                      Subtotal : </p>
                              </td>
                              <td style="padding-top:0px; text-align: right;">
                                  <p
                                      style="font-size: 13px; line-height: 1.6; color:#303030; margin-bottom:0; margin-top:0; vertical-align:top; white-space:nowrap; margin-left:15px">61.82 SAR</p>
                              </td>
                          </tr>
                          <tr>
                              <td colspan="2" style="width: 100%">
                                  <p href="#"
                                      style="font-size: 13px; text-decoration: none; line-height: 1.6; color:#909090; margin-top:0px; margin-bottom:0; text-align: right;">
                                      Tax : </p>
                              </td>
                              <td style="padding-top:0px; text-align: right;">
                                  <p
                                      style="font-size: 13px; line-height: 1.6; color:#303030; margin-bottom:0; margin-top:0; vertical-align:top; white-space:nowrap; margin-left:15px">2.18 SAR</p>
                              </td>
                          </tr>
                          <tr>
                              <td colspan="2" style=" width: 100%; padding-bottom:15px;">
                                  <p href="#"
                                      style="font-size: 13px; text-decoration: none; line-height: 1.6; color:#909090; margin-top:0px; margin-bottom:0; text-align: right;">
                                      <strong>Total : </strong></p>
                              </td>
                              <td style="padding-top:0px; text-align: right; padding-bottom:15px;">
                                  <p
                                      style="font-size: 13px; line-height: 1.6; color:#303030; margin-bottom:0; margin-top:0; vertical-align:top; white-space:nowrap; margin-left:15px">
                                      <strong>
                                          68.14 SAR</strong></p>
                              </td>
                          </tr>
              <tr>
                <td colspan="3" style="border-top:1px solid #f1f0f0">&nbsp;</td>
              </tr>
              <tr>
                <td colspan="3" style="text-align:center;">
                  <p style="color: #909090; font-size:11px; text-align:center;">Billed to, {{ $bill->customer_name}}</p>
                  <p style="color: #909090; font-size:11px; text-align:center;">+966{{ $bill->customer_mobile}}</p>
                  <p style="color: #909090; font-size:11px; text-align:center;">{{ $bill->customer_email}}</p>
                </td>
              </tr>
            </table>
          </div>
        </div>
        <div id="txt">http://bills.test/bills/16#</div>
@endsection

@section('footer-scripts')
  <script>
    $(document).on("click", '.copyButton', function() {
      // console.log('ddd');
       $(this).siblings('input.linkToCopy').select();      
        document.execCommand("copy");
    });
  </script>
@endsection