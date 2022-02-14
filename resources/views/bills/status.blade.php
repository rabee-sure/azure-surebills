@extends('layouts.bill')
@section('title', __('Bill No.') . ' ' . $bill->number)
@section('content')

  <div id="app" class="singlebBillSimple_page d-flex align-items-center justify-content-center flex-column">
    <div class="all_bill_page">
      <div class="change_lang d-flex align-items-center justify-content-end w-100 mb-1">
        @if($bill->user->settings->active_lang == 'all')
          @if(App::isLocale('en'))
            <a href="{{ route('paybillpagelang', ['id' => $bill->pay_id, 'lang' => 'ar'])}}" title="عربي" class="d-block">عربي</a>
          @else
            <a href="{{ route('paybillpagelang', ['id' => $bill->pay_id, 'lang' => 'en'])}}" title="English" class="d-block">English</a>
          @endif
        @endif
      </div><!-- change_lang -->
      <div class="single_bill_content">
        <div class="about d-flex align-items-center justify-content-center flex-column">
          @if($bill->user->logo)
            <img src="https://surepay.sa/dist/images/logo.png" alt="logo">
          @endif
          <span class="d-block font-weight-bold">شركة شور العالميه للتقنيه</span>
          <p class="d-block mb-0">امام سعود بن عبدالعزيز بن محمد ، الرياض</p>
          <b class="d-block font-weight-normal">920008206</b>
        </div><!-- about -->
        <div class="bill_info">
          <div class="d-flex align-items-center justify-content-between">
            <span>رقم الفاتورة</span>
            <span>45678923</span>
          </div><!-- d-flex -->
          <div class="d-flex align-items-center justify-content-between">
            <span>تاريخ إصدار الفاتورة</span>
            <span>12/12/2021</span>
          </div><!-- d-flex -->
          <div class="d-flex align-items-center justify-content-between">
            <span>الرقم الضريبي للمنشأة</span>
            <span>232323</span>
          </div><!-- d-flex -->
        </div><!-- bill_info -->
        <div class="table_items">
          <table>
            <thead>
              <tr>
                <th>الوصف</th>
                <th>السعر</th>
                <th>الكمية</th>
                <th width="35%">المجموع شامل ضريبة القيمة المضافة</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>سلعه تجربه 1</td>
                <td>200 ريال</td>
                <td>1</td>
                <td>230 ريال</td>
              </tr>
              <tr>
                <td>سلعه تجربه 2</td>
                <td>300 ريال</td>
                <td>4</td>
                <td>330 ريال</td>
              </tr>
              <tr>
                <td>سلعه تجربه 3</td>
                <td>450 ريال</td>
                <td>2</td>
                <td>460 ريال</td>
              </tr>
            </tbody>
          </table>
        </div><!-- table_items -->
        <div class="bill_info">
          <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-start justify-content-between flex-column">
              <span>إجمالي المبلغ</span>
              <small>( غير شامل لضريبة القيمة المضافة )</small>
            </div>
            <span>900 ريال</span>
          </div><!-- d-flex -->
          <div class="d-flex align-items-center justify-content-between">
            <span>مجموع ضريبة القيمة المضافة</span>
            <span>135 ريال</span>
          </div><!-- d-flex -->
          <div class="d-flex align-items-center justify-content-between">
            <span>إجمالي المبلغ</span>
            <span>12,2333 ريال</span>
          </div><!-- d-flex -->
        </div><!-- bill_info -->
        <div id="status">
          <div class="alert alert-success">مدفوعة - VISA XXX1111 100000000607</div>
        </div><!-- status -->
        <div class="qrCode_area">
          <a class="d-flex justify-content-center flex-column align-items-center" target="_blank" href="{{route('invoice', ['id' => $bill->pay_id])}}">
            {!! QrCode::size(50)->generate(route('invoice', ['id' => $bill->pay_id])) !!}
            <span>الفاتورة الضريبية</span>
          </a>
        </div><!-- qrCode_area -->
      </div><!-- single_bill_content -->
    </div><!-- all_bill_page -->
  </div><!-- singlebBillSimple_page -->

@endsection


@push('footer-scripts')
  <script type="text/javascript">
    Echo.channel('bill.{{$bill->id}}')
      .listen('BillStatusUpdated', (e) => {
          console.log(e.bill.id);
          var className;

          switch(e.bill.status) {
          case "pending":
              className = "badge-info";
              break;
          case "paid":
              $("#payment_method").remove();
              $("#back_btn").remove();
              $("#status").empty();
              $("#status").append('<div class="alert alert-success" role="alert">{{ __("this bill is paid successfully") }}</div>');
              break;
          case "paid_cash":
              $("#payment_method").remove();
              $("#back_btn").remove();
              $("#status").empty();
              $("#status").append('<div class="alert alert-success" role="alert">{{ __("this bill is paid successfully") }}</div>');
              break;
          case "paid_bank_trnasfer":
              $("#payment_method").remove();
              $("#back_btn").remove();
              $("#status").empty();
              $("#status").append('<div class="alert alert-success" role="alert">{{ __("this bill is paid successfully") }}</div>');
              break;
          case "refunded":
              $("#payment_method").remove();
              $("#back_btn").remove();
              $("#status").empty();
              $("#status").append('<div class="alert alert-warning" role="alert">{{ __("this bill is refunded successfully") }}</div>');
              break;
          case "refunded_cash":
              $("#payment_method").remove();
              $("#back_btn").remove();
              $("#status").empty();
              $("#status").append('<div class="alert alert-warning" role="alert">{{ __("this bill is refunded successfully") }}</div>');
              break;
          case "refunded_bank_transfer":
              $("#payment_method").remove();
              $("#back_btn").remove();
              $("#status").empty();
              $("#status").append('<div class="alert alert-warning" role="alert">{{ __("this bill is refunded successfully") }}</div>');
              break;
          case "canceled":
              $("#payment_method").remove();
              $("#back_btn").remove();
              $("#status").empty();
              $("#status").append('<div class="alert alert-danger" role="alert">{{ __("this bill is canceled") }}</div>');
              break;
          case "expired":
              $("#payment_method").remove();
              $("#back_btn").remove();
              $("#status").empty();
              $("#status").append('<div class="alert alert-danger" role="alert">{{  __('this bill has been expired', ['number' => $bill->number ]) }}</div>');
              break;
          default:
              $("#payment_method").remove();
              $("#back_btn").remove();
              $("#status").empty();
              className = "badge-info";
          }
          // $('#status')
          //   .text(e.bill.trans_status)
          //   .removeClass('badge-light badge-danger badge-success badge-info')
          //   .addClass(className);
      });
  </script>
@endpush
