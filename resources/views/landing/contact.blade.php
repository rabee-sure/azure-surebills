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
        <div class="contact_form">
          <script type="text/javascript" src="https://form.jotform.com/jsform/202793542472054"></script>
        </div><!-- contact_form -->
      </div><!-- container -->
    </section><!-- contact_page -->
@endsection


@push('footer-scripts')
@endpush
