 @extends('layouts.landing')

@section('title', __('Contact'))

@section('content')
    <div class="page_name">
      <div class="container">
        <div class="breadcrumbs">
          <ul>
            <li><a href="{{ url('/') }}" title="الرئيسية">الرئيسية</a></li>
            <li>|</li>
            <li>اتصل بنا</li>
          </ul>
        </div><!-- breadcrumbs -->
        <div class="title">اتصل بنا</div>
      </div><!-- container -->
    </div><!-- page_name -->

    <section id="contact_page">
      <div class="container">
        <h1>اتصل بنا</h1>
        <h2>في شور للمدفوعات نقدم الدعم الفني عبر مجموعة متنوعة من القنوات</h2>
        <div class="row align-items-center justify-content-center">
          <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-3">
            <div class="item">
              <a href="tel:920008206" class="phone" title="الهاتف">
                <span>الهاتف :</span>
                <p>920008206</p>
              </a>
            </div><!-- item -->
          </div><!-- col-12 --> 
          <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-3">
            <div class="item">
              <a href="tel:+966 53 223 2999" class="whatsapp" title="واتساب">
                <span>واتساب :</span>
                <p>+966 53 223 2999</p>
              </a>
            </div><!-- item -->
          </div><!-- col-12 --> 
          <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-3">
            <div class="item">
              <a href="mailto:bills@surepay.sa" class="mail" title="البريد الالكتروني">
                <span>البريد الالكتروني :</span>
                <p>bills@surepay.sa</p>
              </a>
            </div><!-- item -->
          </div><!-- col-12 --> 
        </div><!-- row -->
        <br><br><br><br>
        <h3>تواصل معنا</h3>
        <h4>فريق الدعم الفني بإنتظار أي استفسار</h4>
        <div class="row align-items-center justify-content-center">
          <div class="col-12 co-md-8 col-lg-8 col-xl-8">
            <form action="#">
              <div class="row">
                <div class="col-12 co-md-6 col-lg-6 col-xl-6">
                  <input type="text" placeholder="اسمك *">
                </div><!-- col-12 -->
                <div class="col-12 co-md-6 col-lg-6 col-xl-6">
                  <input type="email" placeholder="بريدك الإلكتروني *">
                </div><!-- col-12 -->
                <div class="col-12 co-md-6 col-lg-6 col-xl-6">
                  <input type="tel" placeholder="رقم الجوال *">
                </div><!-- col-12 -->
                <div class="col-12 co-md-6 col-lg-6 col-xl-6">
                  <input type="text" placeholder="اسم المنشأة *">
                </div><!-- col-12 -->
                <div class="col-12">
                  <textarea name="#" id="#" cols="30" rows="5" placeholder="رسالتك *"></textarea>
                </div><!-- col-12 -->
                <div class="col-12">
                  <button type="submit">إرسال</button>
                </div><!-- col-12 -->
              </div><!-- row -->
            </form>
          </div><!-- col-12 -->
        </div><!-- row -->
      </div><!-- container -->
    </section><!-- contact_page -->
@endsection


@push('footer-scripts')
@endpush
