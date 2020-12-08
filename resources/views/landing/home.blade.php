 @extends('layouts.landing')

@section('title', __('Home'))

@section('content')
    <section id="main_slider">
      <div class="container">
        <div class="row justify-content-between align-items-center">
          <div class="col-12 col-md-6 col-lg-6 col-xl-6">
            <div class="txt">
              <span>أرسل فواتير منتجاتك <br> و استقبل مدفوعاتك بسهولة</span>
              <p>بدون تعقيدات الربط مع بوابات الدفع إبدأ بإستخدام شور بيلز</p>
              <a href="{{ url('/') }}/register" title="سجل الآن">سجل الآن</a>
              <small>إعدادات سهلة وخلال دقائق</small>
            </div><!-- txt -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6 col-lg-6 col-xl-6">
            <div class="imgthumb">
              <img src="/landing/dist/images/slider_img1.svg" alt="#">
            </div>
          </div><!-- col-12 -->
        </div><!-- row -->
      </div><!-- container -->
    </section><!-- main_slider -->

    <section id="start_work">
      <div class="container">
        <div class="desc">
          <span>إبدأ عملك التجاري</span>
          <p>لا تخسر عميلك و سهل عملية الشراء والدفع</p>
          <b>اذا كنت تملك عملك الحر وتتعامل مع عملائك بشكل يومي من خلال القنوات التقليدية مثل واتساب وانستقرام .. ، نحن هنا مساعدتك بتوفير رابط الكتروني يمكنك من عرض منتجاتك و خدماتك للعملاء لتوفير طريقة سداد آمنة وسريعة لهم، و لتحصل على أموالك بشكل أسرع وأسهل.</b>
        </div><!-- desc -->
        <a href="{{ url('/') }}/login" title="سجل مجاناً">سجل مجاناً</a>
      </div><!-- container -->
    </section><!-- start_work -->

    <section id="how_work">
      <div class="container">
        <div class="title">ببساطة أنشئ وشارك الفاتورة</div>
        <div class="how_work_slider owl-carousel owl-theme">
          <div class="item">
            <div class="item_inside">
              <div class="imgthumb">
                <img src="/landing/dist/images/how_img1.svg" alt="سجل معنا">
              </div><!-- imgthumb -->
              <span>سجل معنا</span>
              <p>التسجيل سهل وسريع وخلال دقائق يمكنك البدء بإصدار فواتيرك</p>
            </div><!-- item_inside -->
          </div><!-- item -->
          <div class="item">
            <div class="item_inside">
              <div class="imgthumb">
                <img src="/landing/dist/images/how_img2.svg" alt="أنشئ وشارك الفاتورة">
              </div><!-- imgthumb -->
              <span>أنشئ وشارك الفاتورة</span>
              <p>ارسل فواتيرك من خلال رسالة نصية او مواقع التواصل الإجتماعي او في أي مكان يتواجد فيه عملاؤك</p>
            </div><!-- item_inside -->
          </div><!-- item -->
          <div class="item">
            <div class="item_inside">
              <div class="imgthumb">
                <img src="/landing/dist/images/how_img3.svg" alt="تابع مبيعاتك">
              </div><!-- imgthumb -->
              <span>تابع مبيعاتك</span>
              <p>متابعة مبيعاتك ومعاملاتك المالية بشكل مباشر</p>
            </div><!-- item_inside -->
          </div><!-- item -->
        </div><!-- how_work_slider -->
      </div><!-- container -->
    </section><!-- how_work -->

    <section id="prices">
      <div class="title">الأسعار</div>
      <div class="content">
        <div class="imgthumb">
          <img src="/landing/dist/images/price_img.svg" alt="#">
        </div><!-- imgthumb -->
        <ul>
          <li>1.7 % + 1 ريال على كل عملية دفع عن طريق مدى</li>
          <li>2.7 % + 1 ريال على كل عملية دفع عن طريق البطاقة الائتمانية</li>
          <li>بدون رسوم تأسيس</li>
          <li>بدون رسوم شهرية</li>
          <li>8 ريال رسوم تحويل الرصيد لحساب التاجر</li>
        </ul>
      </div><!-- content -->
      <span>دعم مدى وآبل باي والبطاقات الإئتمانية</span>
      <div class="pay_icons">
        <img src="/landing/dist/images/price_icons.svg" alt="#">
      </div><!-- pay_icons -->
    </section><!-- prices -->

    <section id="faq">
      <div class="container">
        <div class="title">الأسئلة الشائعة</div>
        <div class="acc">
          <div class="acc__card">

            <div class="acc__title active">ماهو شور بيلز ؟</div>
            <div class="acc__panel" style="display: block;">
              <p>خدمة تسهل إنشاء الفواتير للمنتجات بشكل الكتروني و إرسال رابط للعميل ليقوم بالدفع من خلاله.</p>
            </div><!-- acc__panel -->
          </div><!-- acc__card -->

          <div class="acc__card">
            <div class="acc__title">كيف يتم سحب رصيدي ؟</div>
            <div class="acc__panel">
              <p>لابد من إضافة رقم حسابك البنكي ومن ثم سيتم تحويل المبالغ من خلال حوالة مصرفية كل ٧ ايام بعد خصم رسوم الخدمة.</p>
            </div><!-- acc__panel -->
          </div><!-- acc__card -->

          <!-- <div class="acc__card">
            <div class="acc__title">هل يمكنني سحب رصيدي بشكل أسرع قبل ال 7 أيام؟</div>
            <div class="acc__panel">
              <p>نعم ، يمكنك طلب سحب الرصيد المتوفر فورا بمقابل رسوم إضافية للتحويل.</p>
            </div> 
          </div> --> 

          <div class="acc__card">
            <div class="acc__title">ماهي وسائل الدفع المتوفرة ؟</div>
            <div class="acc__panel">
              <p>يمكن لعملائك الدفع من خلال بطاقة مدى وفيزا وماستر كارد وابل باي.</p>
            </div><!-- acc__panel -->
          </div><!-- acc__card -->

          <div class="acc__card">
            <div class="acc__title">رسوم الخدمة ؟</div>
            <div class="acc__panel">
              <p>خدمة المتجر الالكتروني والفواتير تقدم مجانا بدون رسوم تاسيس او رسوم شهرية و الرسوم تؤخذ على كل عملية ناجحة.</p>
            </div><!-- acc__panel -->
          </div><!-- acc__card -->

        </div><!-- acc -->
        <div class="contactus_now">
          <span>لديك إستفسار لا تتردد بالتواصل معنا</span>
          <a href="{{ url('/') }}/contact" title="اتصل بنا">اتصل بنا</a>
        </div><!-- contactus_now -->
      </div><!-- container -->
    </section><!-- faq -->
@endsection


@push('footer-scripts')
@endpush
