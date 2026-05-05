 @extends('layouts.landing')

@section('title', __('Home'))

@section('content')
  <main class="grow flex flex-col gap-12 lg:gap-32 mb-10 lg:mb-18">


    <section id="hero" class="py-10 lg:py-16 overflow-hidden">
      <div class="container flex items-center justify-between flex-col lg:flex-row gap-12 lg:gap-6">
        <div class="w-full lg:w-1/2 ps-0 lg:ps-12">
          <h1 class="text-3xl lg:text-4xl text-center lg:text-start font-bold leading-relaxed lg:leading-normal text-black">أرسل فواتير منتجاتك <br> و استقبل مدفوعاتك <span class="text-[var(--PrimaryColor)]">بسهولة</span> !</h1>
          <p class="text-base lg:text-lg font-normal block text-[var(--MainColor)] mt-4 mb-8 text-center lg:text-start">بدون تعقيدات الربط مع بوابات الدفع إبدأ بإستخدام شور بيلز</p>
          <div class="flex items-center justify-center lg:justify-start gap-4">
            <a href="{{ url('/register') }}" title="سجل الآن" class="flex items-center justify-center rounded px-4 py-3 text-base bg-[var(--PrimaryColor)] min-w-40 text-white font-medium transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
              سجل الآن
            </a>
            <a href="{{ url('/#how') }}" title="كيف نعمل" class="flex items-center justify-center rounded px-4 py-3 text-base bg-white min-w-40 text-[var(--MainColor)] font-medium transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
              كيف نعمل
            </a>
          </div>
          <span class="flex items-center justify-center lg:justify-start gap-1 mt-3 text-sm text-[var(--MainColor)]">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-info-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 9h.01" /><path d="M11 12h1v4h1" /></svg>
            إعدادات سهلة وخلال دقائق
          </span>
        </div><!-- 1/2 -->
        <div class="w-full lg:w-1/2">
          <div class="flex items-center justify-center relative after:content-[''] after:absolute after:w-80 lg:after:w-96 after:h-80 lg:after:h-96 after:bg-[rgba(var(--PrimaryColorRGB),0.23)] after:rounded-full after:z-0 after:inset-auto h-auto lg:h-[550px] after:animate-leaves">
            <img src="{{ asset('assets/landing/images/intro_img.webp') }}" alt="Surebills Intro Image" class="relative z-10 max-w-full max-h-full">
          </div>
        </div><!-- 1/2 -->
      </div><!-- container -->
    </section><!-- hero -->

    <section id="about" class="py-10 lg:py-14 overflow-hidden scroll-mt-32">
      <div class="container">
        <div class="flex items-center justify-between flex-col lg:flex-row gap-20 lg:gap-0">
          <div class="w-full lg:w-3/5 self-stretch p-0 lg:p-12 flex flex-col justify-center">
            <span class="block text-3xl mb-2 font-bold text-black leading-relaxed">إبدأ عملك التجاري</span>
            <p class="text-xl mb-2 text-[var(--MainColor)] leading-relaxed font-medium">لا تخسر عميلك و سهل عملية الشراء والدفع</p>
            <p class="text-base mb-8 text-[var(--MainColor)] leading-relaxed">اذا كنت تملك عملك الحر وتتعامل مع عملائك بشكل يومي من خلال القنوات التقليدية مثل واتساب وانستقرام .. ، نحن هنا مساعدتك بتوفير رابط الكتروني يمكنك من عرض منتجاتك و خدماتك للعملاء لتوفير طريقة سداد آمنة وسريعة لهم، و لتحصل على أموالك بشكل أسرع وأسهل.</p>
            <div class="flex items-center justify-start">
              <a href="{{ url('/register') }}" title="سجل الآن" class="flex items-center justify-center rounded px-4 py-3 text-base bg-[var(--PrimaryColor)] min-w-40 text-white font-medium transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                سجل الآن
              </a>
            </div>
          </div><!-- 3/5 -->
          <div class="w-full lg:w-2/5 relative">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 46.05 50.27" class="z-0 absolute w-40 -top-12 -start-12"><defs><style>.cls-1{opacity:0.53;}.cls-2{fill:#add3ea;}</style></defs><title>Asset 1</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><g class="cls-1"><path class="cls-2" d="M20.21,2.36a1.15,1.15,0,1,0-1.15-1.15A1.14,1.14,0,0,0,20.21,2.36Z"/><path class="cls-2" d="M20.21,10.34A1.15,1.15,0,1,0,19.06,9.2,1.14,1.14,0,0,0,20.21,10.34Z"/><path class="cls-2" d="M20.21,18.33a1.15,1.15,0,1,0-1.15-1.15A1.15,1.15,0,0,0,20.21,18.33Z"/><path class="cls-2" d="M20.21,26.31a1.15,1.15,0,1,0-1.15-1.14A1.14,1.14,0,0,0,20.21,26.31Z"/><path class="cls-2" d="M20.21,34.3a1.15,1.15,0,1,0-1.15-1.15A1.14,1.14,0,0,0,20.21,34.3Z"/><path class="cls-2" d="M20.21,42.28a1.15,1.15,0,1,0-1.15-1.14A1.14,1.14,0,0,0,20.21,42.28Z"/><path class="cls-2" d="M20.21,50.27a1.15,1.15,0,1,0-1.15-1.15A1.15,1.15,0,0,0,20.21,50.27Z"/><path class="cls-2" d="M13.85,2.36a1.15,1.15,0,1,0-1.14-1.15A1.14,1.14,0,0,0,13.85,2.36Z"/><path class="cls-2" d="M13.85,10.34A1.15,1.15,0,1,0,12.71,9.2,1.14,1.14,0,0,0,13.85,10.34Z"/><path class="cls-2" d="M13.85,18.33a1.15,1.15,0,1,0-1.14-1.15A1.15,1.15,0,0,0,13.85,18.33Z"/><path class="cls-2" d="M13.85,26.31a1.15,1.15,0,1,0-1.14-1.14A1.14,1.14,0,0,0,13.85,26.31Z"/><path class="cls-2" d="M13.85,34.3a1.15,1.15,0,1,0-1.14-1.15A1.14,1.14,0,0,0,13.85,34.3Z"/><path class="cls-2" d="M13.85,42.28a1.15,1.15,0,1,0-1.14-1.14A1.14,1.14,0,0,0,13.85,42.28Z"/><path class="cls-2" d="M13.85,50.27a1.15,1.15,0,1,0-1.14-1.15A1.15,1.15,0,0,0,13.85,50.27Z"/><path class="cls-2" d="M7.5,2.36A1.15,1.15,0,1,0,6.35,1.21,1.14,1.14,0,0,0,7.5,2.36Z"/><path class="cls-2" d="M7.5,10.34a1.15,1.15,0,0,0,0-2.29,1.15,1.15,0,1,0,0,2.29Z"/><path class="cls-2" d="M7.5,18.33a1.15,1.15,0,1,0-1.15-1.15A1.15,1.15,0,0,0,7.5,18.33Z"/><path class="cls-2" d="M7.5,26.31A1.15,1.15,0,0,0,7.5,24a1.15,1.15,0,1,0,0,2.29Z"/><path class="cls-2" d="M7.5,34.3a1.15,1.15,0,1,0-1.15-1.15A1.14,1.14,0,0,0,7.5,34.3Z"/><path class="cls-2" d="M7.5,42.28a1.15,1.15,0,1,0-1.15-1.14A1.14,1.14,0,0,0,7.5,42.28Z"/><path class="cls-2" d="M7.5,50.27a1.15,1.15,0,1,0-1.15-1.15A1.15,1.15,0,0,0,7.5,50.27Z"/><path class="cls-2" d="M1.15,2.36A1.15,1.15,0,1,0,0,1.21,1.14,1.14,0,0,0,1.15,2.36Z"/><path class="cls-2" d="M1.15,10.34A1.15,1.15,0,1,0,0,9.2,1.14,1.14,0,0,0,1.15,10.34Z"/><path class="cls-2" d="M1.15,18.33A1.15,1.15,0,1,0,0,17.18,1.15,1.15,0,0,0,1.15,18.33Z"/><path class="cls-2" d="M1.15,26.31A1.15,1.15,0,1,0,0,25.17,1.14,1.14,0,0,0,1.15,26.31Z"/><path class="cls-2" d="M1.15,34.3A1.15,1.15,0,1,0,0,33.15,1.14,1.14,0,0,0,1.15,34.3Z"/><circle class="cls-2" cx="1.15" cy="41.14" r="1.15"/><path class="cls-2" d="M1.15,50.27A1.15,1.15,0,1,0,0,49.12,1.15,1.15,0,0,0,1.15,50.27Z"/></g><g class="cls-1"><path class="cls-2" d="M44.9,2.29a1.15,1.15,0,1,0-1.14-1.15A1.15,1.15,0,0,0,44.9,2.29Z"/><path class="cls-2" d="M44.9,10.27a1.15,1.15,0,1,0-1.14-1.14A1.15,1.15,0,0,0,44.9,10.27Z"/><path class="cls-2" d="M44.9,18.26a1.15,1.15,0,1,0-1.14-1.14A1.15,1.15,0,0,0,44.9,18.26Z"/><path class="cls-2" d="M44.9,26.25a1.15,1.15,0,1,0-1.14-1.15A1.16,1.16,0,0,0,44.9,26.25Z"/><path class="cls-2" d="M44.9,34.23a1.15,1.15,0,1,0-1.14-1.15A1.15,1.15,0,0,0,44.9,34.23Z"/><path class="cls-2" d="M44.9,42.21a1.15,1.15,0,1,0-1.14-1.14A1.15,1.15,0,0,0,44.9,42.21Z"/><path class="cls-2" d="M44.9,50.2a1.15,1.15,0,1,0-1.14-1.15A1.15,1.15,0,0,0,44.9,50.2Z"/><path class="cls-2" d="M38.55,2.29A1.15,1.15,0,1,0,37.4,1.14,1.14,1.14,0,0,0,38.55,2.29Z"/><path class="cls-2" d="M38.55,10.27a1.15,1.15,0,0,0,0-2.29,1.15,1.15,0,1,0,0,2.29Z"/><path class="cls-2" d="M38.55,18.26a1.15,1.15,0,1,0-1.15-1.14A1.14,1.14,0,0,0,38.55,18.26Z"/><path class="cls-2" d="M38.55,26.25A1.15,1.15,0,1,0,37.4,25.1,1.15,1.15,0,0,0,38.55,26.25Z"/><path class="cls-2" d="M38.55,34.23a1.15,1.15,0,1,0-1.15-1.15A1.14,1.14,0,0,0,38.55,34.23Z"/><path class="cls-2" d="M38.55,42.21a1.15,1.15,0,0,0,0-2.29,1.15,1.15,0,1,0,0,2.29Z"/><path class="cls-2" d="M38.55,50.2a1.15,1.15,0,1,0-1.15-1.15A1.14,1.14,0,0,0,38.55,50.2Z"/><path class="cls-2" d="M32.2,2.29a1.15,1.15,0,1,0-1.15-1.15A1.14,1.14,0,0,0,32.2,2.29Z"/><path class="cls-2" d="M32.2,10.27a1.15,1.15,0,1,0-1.15-1.14A1.14,1.14,0,0,0,32.2,10.27Z"/><path class="cls-2" d="M32.2,18.26a1.15,1.15,0,1,0-1.15-1.14A1.14,1.14,0,0,0,32.2,18.26Z"/><path class="cls-2" d="M32.2,26.25a1.15,1.15,0,1,0-1.15-1.15A1.15,1.15,0,0,0,32.2,26.25Z"/><path class="cls-2" d="M32.2,34.23a1.15,1.15,0,1,0-1.15-1.15A1.14,1.14,0,0,0,32.2,34.23Z"/><path class="cls-2" d="M32.2,42.21a1.15,1.15,0,1,0-1.15-1.14A1.14,1.14,0,0,0,32.2,42.21Z"/><path class="cls-2" d="M32.2,50.2a1.15,1.15,0,1,0-1.15-1.15A1.14,1.14,0,0,0,32.2,50.2Z"/><path class="cls-2" d="M25.84,2.29A1.15,1.15,0,1,0,24.7,1.14,1.15,1.15,0,0,0,25.84,2.29Z"/><path class="cls-2" d="M25.84,10.27A1.15,1.15,0,1,0,24.7,9.13,1.15,1.15,0,0,0,25.84,10.27Z"/><path class="cls-2" d="M25.84,18.26a1.15,1.15,0,1,0-1.14-1.14A1.15,1.15,0,0,0,25.84,18.26Z"/><path class="cls-2" d="M25.84,26.25A1.15,1.15,0,1,0,24.7,25.1,1.16,1.16,0,0,0,25.84,26.25Z"/><path class="cls-2" d="M25.84,34.23a1.15,1.15,0,1,0-1.14-1.15A1.15,1.15,0,0,0,25.84,34.23Z"/><circle class="cls-2" cx="25.84" cy="41.07" r="1.15"/><path class="cls-2" d="M25.84,50.2a1.15,1.15,0,1,0-1.14-1.15A1.15,1.15,0,0,0,25.84,50.2Z"/></g></g></g><script xmlns=""/></svg>
            <div class="flex items-center justify-center bg-[#DCE5FF] rounded-xl animate-jumping relative z-10">
              <img src="{{ asset('assets/landing/images/about_img.webp') }}" alt="Surebills Intro Image" class="max-w-full max-h-full">
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 46.05 50.27" class="z-0 absolute w-40 -bottom-12 -end-12"><defs><style>.cls-1{opacity:0.53;}.cls-2{fill:#add3ea;}</style></defs><title>Asset 1</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><g class="cls-1"><path class="cls-2" d="M20.21,2.36a1.15,1.15,0,1,0-1.15-1.15A1.14,1.14,0,0,0,20.21,2.36Z"/><path class="cls-2" d="M20.21,10.34A1.15,1.15,0,1,0,19.06,9.2,1.14,1.14,0,0,0,20.21,10.34Z"/><path class="cls-2" d="M20.21,18.33a1.15,1.15,0,1,0-1.15-1.15A1.15,1.15,0,0,0,20.21,18.33Z"/><path class="cls-2" d="M20.21,26.31a1.15,1.15,0,1,0-1.15-1.14A1.14,1.14,0,0,0,20.21,26.31Z"/><path class="cls-2" d="M20.21,34.3a1.15,1.15,0,1,0-1.15-1.15A1.14,1.14,0,0,0,20.21,34.3Z"/><path class="cls-2" d="M20.21,42.28a1.15,1.15,0,1,0-1.15-1.14A1.14,1.14,0,0,0,20.21,42.28Z"/><path class="cls-2" d="M20.21,50.27a1.15,1.15,0,1,0-1.15-1.15A1.15,1.15,0,0,0,20.21,50.27Z"/><path class="cls-2" d="M13.85,2.36a1.15,1.15,0,1,0-1.14-1.15A1.14,1.14,0,0,0,13.85,2.36Z"/><path class="cls-2" d="M13.85,10.34A1.15,1.15,0,1,0,12.71,9.2,1.14,1.14,0,0,0,13.85,10.34Z"/><path class="cls-2" d="M13.85,18.33a1.15,1.15,0,1,0-1.14-1.15A1.15,1.15,0,0,0,13.85,18.33Z"/><path class="cls-2" d="M13.85,26.31a1.15,1.15,0,1,0-1.14-1.14A1.14,1.14,0,0,0,13.85,26.31Z"/><path class="cls-2" d="M13.85,34.3a1.15,1.15,0,1,0-1.14-1.15A1.14,1.14,0,0,0,13.85,34.3Z"/><path class="cls-2" d="M13.85,42.28a1.15,1.15,0,1,0-1.14-1.14A1.14,1.14,0,0,0,13.85,42.28Z"/><path class="cls-2" d="M13.85,50.27a1.15,1.15,0,1,0-1.14-1.15A1.15,1.15,0,0,0,13.85,50.27Z"/><path class="cls-2" d="M7.5,2.36A1.15,1.15,0,1,0,6.35,1.21,1.14,1.14,0,0,0,7.5,2.36Z"/><path class="cls-2" d="M7.5,10.34a1.15,1.15,0,0,0,0-2.29,1.15,1.15,0,1,0,0,2.29Z"/><path class="cls-2" d="M7.5,18.33a1.15,1.15,0,1,0-1.15-1.15A1.15,1.15,0,0,0,7.5,18.33Z"/><path class="cls-2" d="M7.5,26.31A1.15,1.15,0,0,0,7.5,24a1.15,1.15,0,1,0,0,2.29Z"/><path class="cls-2" d="M7.5,34.3a1.15,1.15,0,1,0-1.15-1.15A1.14,1.14,0,0,0,7.5,34.3Z"/><path class="cls-2" d="M7.5,42.28a1.15,1.15,0,1,0-1.15-1.14A1.14,1.14,0,0,0,7.5,42.28Z"/><path class="cls-2" d="M7.5,50.27a1.15,1.15,0,1,0-1.15-1.15A1.15,1.15,0,0,0,7.5,50.27Z"/><path class="cls-2" d="M1.15,2.36A1.15,1.15,0,1,0,0,1.21,1.14,1.14,0,0,0,1.15,2.36Z"/><path class="cls-2" d="M1.15,10.34A1.15,1.15,0,1,0,0,9.2,1.14,1.14,0,0,0,1.15,10.34Z"/><path class="cls-2" d="M1.15,18.33A1.15,1.15,0,1,0,0,17.18,1.15,1.15,0,0,0,1.15,18.33Z"/><path class="cls-2" d="M1.15,26.31A1.15,1.15,0,1,0,0,25.17,1.14,1.14,0,0,0,1.15,26.31Z"/><path class="cls-2" d="M1.15,34.3A1.15,1.15,0,1,0,0,33.15,1.14,1.14,0,0,0,1.15,34.3Z"/><circle class="cls-2" cx="1.15" cy="41.14" r="1.15"/><path class="cls-2" d="M1.15,50.27A1.15,1.15,0,1,0,0,49.12,1.15,1.15,0,0,0,1.15,50.27Z"/></g><g class="cls-1"><path class="cls-2" d="M44.9,2.29a1.15,1.15,0,1,0-1.14-1.15A1.15,1.15,0,0,0,44.9,2.29Z"/><path class="cls-2" d="M44.9,10.27a1.15,1.15,0,1,0-1.14-1.14A1.15,1.15,0,0,0,44.9,10.27Z"/><path class="cls-2" d="M44.9,18.26a1.15,1.15,0,1,0-1.14-1.14A1.15,1.15,0,0,0,44.9,18.26Z"/><path class="cls-2" d="M44.9,26.25a1.15,1.15,0,1,0-1.14-1.15A1.16,1.16,0,0,0,44.9,26.25Z"/><path class="cls-2" d="M44.9,34.23a1.15,1.15,0,1,0-1.14-1.15A1.15,1.15,0,0,0,44.9,34.23Z"/><path class="cls-2" d="M44.9,42.21a1.15,1.15,0,1,0-1.14-1.14A1.15,1.15,0,0,0,44.9,42.21Z"/><path class="cls-2" d="M44.9,50.2a1.15,1.15,0,1,0-1.14-1.15A1.15,1.15,0,0,0,44.9,50.2Z"/><path class="cls-2" d="M38.55,2.29A1.15,1.15,0,1,0,37.4,1.14,1.14,1.14,0,0,0,38.55,2.29Z"/><path class="cls-2" d="M38.55,10.27a1.15,1.15,0,0,0,0-2.29,1.15,1.15,0,1,0,0,2.29Z"/><path class="cls-2" d="M38.55,18.26a1.15,1.15,0,1,0-1.15-1.14A1.14,1.14,0,0,0,38.55,18.26Z"/><path class="cls-2" d="M38.55,26.25A1.15,1.15,0,1,0,37.4,25.1,1.15,1.15,0,0,0,38.55,26.25Z"/><path class="cls-2" d="M38.55,34.23a1.15,1.15,0,1,0-1.15-1.15A1.14,1.14,0,0,0,38.55,34.23Z"/><path class="cls-2" d="M38.55,42.21a1.15,1.15,0,0,0,0-2.29,1.15,1.15,0,1,0,0,2.29Z"/><path class="cls-2" d="M38.55,50.2a1.15,1.15,0,1,0-1.15-1.15A1.14,1.14,0,0,0,38.55,50.2Z"/><path class="cls-2" d="M32.2,2.29a1.15,1.15,0,1,0-1.15-1.15A1.14,1.14,0,0,0,32.2,2.29Z"/><path class="cls-2" d="M32.2,10.27a1.15,1.15,0,1,0-1.15-1.14A1.14,1.14,0,0,0,32.2,10.27Z"/><path class="cls-2" d="M32.2,18.26a1.15,1.15,0,1,0-1.15-1.14A1.14,1.14,0,0,0,32.2,18.26Z"/><path class="cls-2" d="M32.2,26.25a1.15,1.15,0,1,0-1.15-1.15A1.15,1.15,0,0,0,32.2,26.25Z"/><path class="cls-2" d="M32.2,34.23a1.15,1.15,0,1,0-1.15-1.15A1.14,1.14,0,0,0,32.2,34.23Z"/><path class="cls-2" d="M32.2,42.21a1.15,1.15,0,1,0-1.15-1.14A1.14,1.14,0,0,0,32.2,42.21Z"/><path class="cls-2" d="M32.2,50.2a1.15,1.15,0,1,0-1.15-1.15A1.14,1.14,0,0,0,32.2,50.2Z"/><path class="cls-2" d="M25.84,2.29A1.15,1.15,0,1,0,24.7,1.14,1.15,1.15,0,0,0,25.84,2.29Z"/><path class="cls-2" d="M25.84,10.27A1.15,1.15,0,1,0,24.7,9.13,1.15,1.15,0,0,0,25.84,10.27Z"/><path class="cls-2" d="M25.84,18.26a1.15,1.15,0,1,0-1.14-1.14A1.15,1.15,0,0,0,25.84,18.26Z"/><path class="cls-2" d="M25.84,26.25A1.15,1.15,0,1,0,24.7,25.1,1.16,1.16,0,0,0,25.84,26.25Z"/><path class="cls-2" d="M25.84,34.23a1.15,1.15,0,1,0-1.14-1.15A1.15,1.15,0,0,0,25.84,34.23Z"/><circle class="cls-2" cx="25.84" cy="41.07" r="1.15"/><path class="cls-2" d="M25.84,50.2a1.15,1.15,0,1,0-1.14-1.15A1.15,1.15,0,0,0,25.84,50.2Z"/></g></g></g><script xmlns=""/></svg>
          </div><!-- 2/5 -->
        </div><!-- flex -->
      </div><!-- container -->
    </section><!-- about -->

    <section id="how" class="scroll-mt-32">
      <div class="container">
        <span class="block text-2xl lg:text-3xl mb-12 text-center font-bold text-black leading-relaxed">المدفوعات الإلكترونية ، <span class="text-[--PrimaryColor]">ببساطة</span> !</span>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-white rounded-lg py-10 px-6 flex flex-col items-center justify-center shadow transition-all duration-300 hover:shadow-lg hover:-translate-y-2">
            <span class="flex items-center justify-center bg-[rgba(var(--PrimaryColorRGB),0.1)] text-[--PrimaryColor] w-12 h-12 rounded-full font-bold text-xl mb-6">1</span>
            <img src="{{ asset('assets/landing/images/how_img_1.webp') }}" alt="How 1" class="max-w-full max-h-44 mb-8">
            <span class="block text-xl font-bold text-black mb-3">سجل معنا</span>
            <span class="block text-base text-center text-[--MainColor] leading-relaxed">سجل حسابك في خطوات بسيطة وسريعة بدون أي تعقيد، وابدأ خلال دقائق فقط.</span>
          </div>
          <div class="bg-white rounded-lg py-10 px-6 flex flex-col items-center justify-center shadow transition-all duration-300 hover:shadow-lg hover:-translate-y-2">
            <span class="flex items-center justify-center bg-[rgba(var(--PrimaryColorRGB),0.1)] text-[--PrimaryColor] w-12 h-12 rounded-full font-bold text-xl mb-6">2</span>
            <img src="{{ asset('assets/landing/images/how_img_2.webp') }}" alt="How 2" class="max-w-full max-h-44 mb-8">
            <span class="block text-xl font-bold text-black mb-3">أنشئ وشارك الفاتورة</span>
            <span class="block text-base text-center text-[--MainColor] leading-relaxed">ارسل فواتيرك من خلال رسالة نصية او مواقع التواصل الإجتماعي او في أي مكان يتواجد فيه عملاؤك</span>
          </div>
          <div class="bg-white rounded-lg py-10 px-6 flex flex-col items-center justify-center shadow transition-all duration-300 hover:shadow-lg hover:-translate-y-2">
            <span class="flex items-center justify-center bg-[rgba(var(--PrimaryColorRGB),0.1)] text-[--PrimaryColor] w-12 h-12 rounded-full font-bold text-xl mb-6">3</span>
            <img src="{{ asset('assets/landing/images/how_img_3.webp') }}" alt="How 3" class="max-w-full max-h-44 mb-8">
            <span class="block text-xl font-bold text-black mb-3">تابع مبيعاتك</span>
            <span class="block text-base text-center text-[--MainColor] leading-relaxed">تابع جميع معاملاتك المالية في مكان واحد مع تحديثات فورية تمنحك تحكم كامل في نشاطك التجاري.</span>
          </div>
        </div>
      </div><!-- container -->
    </section><!-- how -->

    <section id="faqs" class="scroll-mt-32">
      <div class="container">
        <span class="block text-2xl lg:text-3xl mb-12 text-center font-bold text-black leading-relaxed">الأسئلة <span class="text-[var(--PrimaryColor)]">الشائعة</span></span>
        <div class="flex items-center justify-between flex-col lg:flex-row gap-8">
          <div class="w-full lg:w-2/5 flex items-center justify-center">
            <img src="{{ asset('assets/landing/images/faqs.webp') }}" alt="Surebills faqs Image" class="max-w-full max-h-96">
          </div><!-- 2/5 -->
          <div class="w-full lg:w-3/5 self-stretch flex flex-col justify-center">

            <div class="faq-grid flex flex-col gap-4">
              <article class="faq-item bg-white rounded-xl border border-gray-100 shadow overflow-hidden active reveal">
                <button class="faq-button w-full bg-white backdrop-blur-md p-4 flex items-center justify-between gap-3" type="button" aria-expanded="true">
                  <span class="grow block font-medium text-black text-start text-base lg:text-lg">ماهو شور بيلز ؟</span>
                  <span class="faq-icon transition-all duration-300 ease-in-out text-lg font-bold shrink-0 w-9 h-9 rounded-lg bg-[rgba(var(--PrimaryColorRGB),0.19)] text-[--PrimaryColor] flex items-center justify-center">+</span>
                </button>
                <div class="faq-panel grid grid-rows-[0fr] transition-all duration-300 ease-in-out">
                  <div class="faq-panel-inner overflow-hidden">
                    <p class="block break-words pb-4 px-4 m-0 text-[--MainColor] text-base leading-relaxed">خدمة تسهل إنشاء الفواتير للمنتجات بشكل الكتروني و إرسال رابط للعميل ليقوم بالدفع من خلاله.</p>
                  </div>
                </div>
              </article>

              <article class="faq-item bg-white rounded-xl border border-gray-100 shadow overflow-hidden reveal">
                <button class="faq-button w-full bg-white backdrop-blur-md p-4 flex items-center justify-between gap-3" type="button" aria-expanded="false">
                  <span class="grow block font-medium text-black text-start text-base lg:text-lg">كيف يتم سحب رصيدي ؟</span>
                  <span class="faq-icon transition-all duration-300 ease-in-out text-lg font-bold shrink-0 w-9 h-9 rounded-lg bg-[rgba(var(--PrimaryColorRGB),0.19)] text-[--PrimaryColor] flex items-center justify-center">+</span>
                </button>
                <div class="faq-panel grid grid-rows-[0fr] transition-all duration-300 ease-in-out">
                  <div class="faq-panel-inner overflow-hidden">
                    <p class="block break-words pb-4 px-4 m-0 text-[--MainColor] text-base leading-relaxed">لابد من إضافة رقم حسابك البنكي ومن ثم سيتم تحويل المبالغ من خلال حوالة مصرفية كل يومين (من أيام العمل الرسمية) بعد خصم رسوم الخدمة.</p>
                  </div>
                </div>
              </article>

              <article class="faq-item bg-white rounded-xl border border-gray-100 shadow overflow-hidden reveal">
                <button class="faq-button w-full bg-white backdrop-blur-md p-4 flex items-center justify-between gap-3" type="button" aria-expanded="false">
                  <span class="grow block font-medium text-black text-start text-base lg:text-lg">ماهي وسائل الدفع المتوفرة ؟</span>
                  <span class="faq-icon transition-all duration-300 ease-in-out text-lg font-bold shrink-0 w-9 h-9 rounded-lg bg-[rgba(var(--PrimaryColorRGB),0.19)] text-[--PrimaryColor] flex items-center justify-center">+</span>
                </button>
                <div class="faq-panel grid grid-rows-[0fr] transition-all duration-300 ease-in-out">
                  <div class="faq-panel-inner overflow-hidden">
                    <p class="block break-words pb-4 px-4 m-0 text-[--MainColor] text-base leading-relaxed">يمكن لعملائك الدفع من خلال بطاقة مدى وفيزا وماستر كارد وابل باي.</p>
                  </div>
                </div>
              </article>

              <article class="faq-item bg-white rounded-xl border border-gray-100 shadow overflow-hidden reveal">
                <button class="faq-button w-full bg-white backdrop-blur-md p-4 flex items-center justify-between gap-3" type="button" aria-expanded="false">
                  <span class="grow block font-medium text-black text-start text-base lg:text-lg">رسوم الخدمة ؟</span>
                  <span class="faq-icon transition-all duration-300 ease-in-out text-lg font-bold shrink-0 w-9 h-9 rounded-lg bg-[rgba(var(--PrimaryColorRGB),0.19)] text-[--PrimaryColor] flex items-center justify-center">+</span>
                </button>
                <div class="faq-panel grid grid-rows-[0fr] transition-all duration-300 ease-in-out">
                  <div class="faq-panel-inner overflow-hidden">
                    <p class="block break-words pb-4 px-4 m-0 text-[--MainColor] text-base leading-relaxed">خدمة المتجر الالكتروني والفواتير تقدم مجانا بدون رسوم تاسيس او رسوم شهرية و الرسوم تؤخذ على كل عملية ناجحة.</p>
                  </div>
                </div>
              </article>
            </div>

          </div><!-- 3/5 -->
        </div>
      </div>
    </section><!-- about -->

    <section id="statistics">
      <div class="container">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

          <div class="statistics_item flex flex-col items-center justify-center border border-[rgba(69,80,91,0.14)] rounded-xl py-8 px-4">
            <div class="text-[var(--PrimaryColor)] rounded-lg shrink-0 w-20 h-20 flex items-center justify-center mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-20 h-20"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M19.5 12.572l-7.5 7.428l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572" /><path d="M12 6l-3.293 3.293a1 1 0 0 0 0 1.414l.543 .543c.69 .69 1.81 .69 2.5 0l1 -1a3.182 3.182 0 0 1 4.5 0l2.25 2.25" /><path d="M12.5 15.5l2 2" /><path d="M15 13l2 2" /></svg>
            </div>
            <span class="text-2xl flex items-center justify-center gap-2 leading-relaxed font-bold text-black mb-2"><span class="text-[--PrimaryColor] text-4xl">+508</span> عميل</span>
            <p class="text-base text-center text-[--MainColor]">تجّار وأصحاب أعمال يعتمدون على شور بيلز لإرسال الفواتير بشكل أسرع.</p>
          </div>

          <div class="statistics_item flex flex-col items-center justify-center border border-[rgba(69,80,91,0.14)] rounded-xl py-8 px-4">
            <div class="text-[var(--PrimaryColor)] rounded-lg shrink-0 w-20 h-20 flex items-center justify-center mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-20 h-20"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2m4 -14h6m-6 4h6m-2 4h2" /></svg>
            </div>
            <span class="text-2xl flex items-center justify-center gap-2 leading-relaxed font-bold text-black mb-2"><span class="text-[--PrimaryColor] text-4xl">+326</span> فاتورة</span>
            <p class="text-base text-center text-[--MainColor]">فواتير إلكترونية تم إنشاؤها ومشاركتها مع العملاء عبر روابط دفع مباشرة.</p>
          </div>

          <div class="statistics_item flex flex-col items-center justify-center border border-[rgba(69,80,91,0.14)] rounded-xl py-8 px-4">
            <div class="text-[var(--PrimaryColor)] rounded-lg shrink-0 w-20 h-20 flex items-center justify-center mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-20 h-20"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M2 4h9.914a3 3 0 0 1 1.92 .695l5.166 4.305" /><path d="M11.15 9h8.85a2 2 0 0 1 2 2v7a2 2 0 0 1 -2 2h-13a2 2 0 0 1 -2 -2v-8.7" /><path d="M3 8l7.2 4.7a1.803 1.803 0 0 0 2 -3l-4.2 -2.7" /><path d="M5 16h17" /></svg>
            </div>
            <span class="text-2xl flex items-center justify-center gap-2 leading-relaxed font-bold text-black mb-2"><span class="text-[--PrimaryColor] text-4xl">+3</span> بوابة دفع</span>
            <p class="text-base text-center text-[--MainColor]">تكامل مع وسائل دفع متعددة لتوفير تجربة سداد مرنة وآمنة للعملاء.</p>
          </div>

          <div class="statistics_item flex flex-col items-center justify-center border border-[rgba(69,80,91,0.14)] rounded-xl py-8 px-4">
            <div class="text-[var(--PrimaryColor)] rounded-lg shrink-0 w-20 h-20 flex items-center justify-center mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-20 h-20"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M4 14v-3a8 8 0 1 1 16 0v3" /><path d="M18 19c0 1.657 -2.686 3 -6 3" /><path d="M4 14a2 2 0 0 1 2 -2h1a2 2 0 0 1 2 2v3a2 2 0 0 1 -2 2h-1a2 2 0 0 1 -2 -2v-3" /><path d="M15 14a2 2 0 0 1 2 -2h1a2 2 0 0 1 2 2v3a2 2 0 0 1 -2 2h-1a2 2 0 0 1 -2 -2v-3" /></svg>
            </div>
            <span class="text-2xl flex items-center justify-center gap-2 leading-relaxed font-bold text-black mb-2"><span class="text-[--PrimaryColor] text-4xl">24/7</span> دعم فني</span>
            <p class="text-base text-center text-[--MainColor]">لوحة واضحة لمتابعة حالة الفواتير والمدفوعات في أي وقت وبشكل مباشر.</p>
          </div>

        </div>

      </div><!-- container -->
    </section><!-- statistics -->

    <section id="contact" class="scroll-mt-20">
      <div class="container">
        <div class="bg-[rgba(var(--PrimaryColorRGB),0.05)] rounded-2xl p-4 lg:p-12">
          <div class="flex items-center justify-between flex-col lg:flex-row gap-8">
            <div class="w-full lg:w-1/2">
              <span class="block text-3xl mb-2 font-bold text-black leading-relaxed">نرحب <span class="text-[var(--PrimaryColor)]">بتواصلك</span></span>
              <p class="block text-base text-black leading-relaxed mb-6">في شور للمدفوعات نقدم الدعم الفني عبر مجموعة متنوعة من القنوات</p>
              <div class="flex flex-col gap-4">
                <a href="tel:8001110102" title="الهاتف" class="flex items-center justify-start gap-4">
                  <div class="flex items-center justify-center text-[--MainColor]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-10 h-10"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" /></svg>
                  </div>
                  <div class="flex flex-col items-start justify-center">
                    <span class="block text-sm text-[--MainColor] leading-relaxed">الهاتف</span>
                    <span class="block text-base text-black leading-relaxed">8001110102</span>
                  </div>
                </a>
                <a href="https://api.whatsapp.com/send?phone=+966558946366" title="واتساب" class="flex items-center justify-start gap-4">
                  <div class="flex items-center justify-center text-[--MainColor]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-10 h-10"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9" /><path d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1" /></svg>
                  </div>
                  <div class="flex flex-col items-start justify-center">
                    <span class="block text-sm text-[--MainColor] leading-relaxed">واتساب</span>
                    <span class="block text-base text-black leading-relaxed"><span dir="ltr">+966558946366</span></span>
                  </div>
                </a>
                <a href="mailto:bills@surepay.sa" title="البريد الالكتروني" class="flex items-center justify-start gap-4">
                  <div class="flex items-center justify-center text-[--MainColor]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-10 h-10"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10" /><path d="M3 7l9 6l9 -6" /></svg>
                  </div>
                  <div class="flex flex-col items-start justify-center">
                    <span class="block text-sm text-[--MainColor] leading-relaxed">البريد الالكتروني</span>
                    <span class="block text-base text-black leading-relaxed">bills@surepay.sa</span>
                  </div>
                </a>
              </div>
            </div><!-- 2/5 -->
            <div class="w-full lg:w-1/2 self-stretch flex flex-col justify-center">
              <form action="#" id="contact_form" class="flex flex-col gap-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="flex flex-col gap-2">
                    <input type="text" value="{{ old('name') }}" name="name" id="name" placeholder="الاسم" class="w-full border border-gray-300 rounded-lg px-4 py-0 h-12 outline-none text-black text-base placeholder:text-start text-start focus:border-[var(--PrimaryColor)] transition-all duration-300">
                  </div>
                  <div class="flex flex-col gap-2">
                    <input type="email" value="{{ old('email') }}" name="email" id="email" inputmode="email" placeholder="البريد الالكتروني" class="w-full border border-gray-300 rounded-lg px-4 py-0 h-12 outline-none text-black text-base placeholder:text-start focus:border-[var(--PrimaryColor)] transition-all duration-300">
                  </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="flex flex-col gap-2">
                    <input type="tel" value="{{ old('mobile') }}" name="mobile" id="mobile" inputmode="numeric" autocomplete="off" pattern="[0-9]{9}" maxlength="9" placeholder="رقم الجوال" class="w-full border border-gray-300 rounded-lg px-4 py-0 h-12 outline-none text-black text-base placeholder:text-end focus:border-[var(--PrimaryColor)] transition-all duration-300">
                  </div>
                  <div class="flex flex-col gap-2">
                    <input type="text" value="{{ old('company') }}" name="company" id="company" placeholder="اسم المنشأة" class="w-full border border-gray-300 rounded-lg px-4 py-0 h-12 outline-none text-black text-base placeholder:text-start focus:border-[var(--PrimaryColor)] transition-all duration-300">
                  </div>
                </div>
                <textarea value="{{ old('message') }}" name="message" id="message" placeholder="الرسالة" class="w-full border border-gray-300 rounded-lg p-4 outline-none text-black text-base placeholder:text-start focus:border-[var(--PrimaryColor)] transition-all duration-300" rows="4"></textarea>
                <button type="submit" class="w-full bg-[var(--PrimaryColor)] text-white rounded-lg px-4 py-0 h-12 outline-none transition-all duration-300 hover:shadow-xl hover:bg-[var(--PrimaryColorHover)] text-base font-bold">إرسال</button>
              </form>
              <div id="successMessage" class="hidden flex-col text-green-500 items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="w-48 h-48"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M3 7h3" /><path d="M3 11h2" /><path d="M9.02 8.801l-.6 6a2 2 0 0 0 1.99 2.199h7.98a2 2 0 0 0 1.99 -1.801l.6 -6a2 2 0 0 0 -1.99 -2.199h-7.98a2 2 0 0 0 -1.99 1.801" /><path d="M9.8 7.5l2.982 3.28a3 3 0 0 0 4.238 .202l3.28 -2.982" /></svg>
                <span class="text-base leading-relaxed">تم إرسال رسالتك بنجاح! سنقوم بالرد عليك في أقرب وقت ممكن.</span>
              </div><!-- successMessage -->
            </div><!-- 3/5 -->
          </div>
        </div>
      </div>
    </section><!-- contact -->

  </main><!-- main -->
@endsection

@push('footer-scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.min.js') }}?v={{ config('app.asset_version')}}"></script>
  <script>
    const revealItems = document.querySelectorAll(".reveal");
    const faqItems = document.querySelectorAll(".faq-item");

    const revealObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible");
          revealObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });

    revealItems.forEach((item) => revealObserver.observe(item));

    faqItems.forEach((item) => {
      const button = item.querySelector(".faq-button");

      button.addEventListener("click", () => {
        const isOpen = item.classList.contains("active");

        faqItems.forEach((otherItem) => {
          otherItem.classList.remove("active");
          otherItem.querySelector(".faq-button").setAttribute("aria-expanded", "false");
        });

        if (!isOpen) {
          item.classList.add("active");
          button.setAttribute("aria-expanded", "true");
        }
      });
    });

    document.addEventListener('DOMContentLoaded', function() {
    // Get the CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Handle form submission
    const contactForm = document.getElementById('contact_form');
    const successMessage = document.getElementById('successMessage');

    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Get form values
        const formData = {
            source: 'Home page',
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            company: document.getElementById('company').value,
            mobile: document.getElementById('mobile').value,
            message: document.getElementById('message').value
        };

        // Send AJAX request
        fetch('/contact/send_form', {  // Replace with your actual route URL
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            // Hide the form
            contactForm.style.display = 'none';

            // Show success message
            successMessage.classList.remove('hidden');

            // Remove 'hidden' and add 'flex' after delay
            setTimeout(() => {
                successMessage.classList.add('flex');
            }, 3000);

            // Reset form (optional, since it's hidden)
            document.getElementById('name').value = '';
            document.getElementById('email').value = '';
            document.getElementById('company').value = '';
            document.getElementById('mobile').value = '';
            document.getElementById('message').value = '';
        })
        .catch(error => console.error('Error:', error));
    });
});
  </script>
  {!! JsValidator::formRequest('App\Http\Requests\ContactRequest', '#contact_form') !!}
@endpush
