 @extends('layouts.landing')

@section('title', __('privacy_policy'))

@section('content')
  <main class="grow">

    <section class="py-10 lg:py-18">
      <div class="container flex flex-col gap-8">

        <h1 class="text-3xl lg:text-3xl font-bold leading-relaxed lg:leading-normal text-black">سياسة الخصوصية</h1>

        <article class="bg-white rounded-xl p-5 border border-gray-200 shadow-md leading-relaxed text-base text-black">
          {!! nl2br(app()->getLocale() == 'ar' ? config('privacy_policy.contentAr') : config('privacy_policy.contentEn')) !!}
        </article>

      </div><!-- container -->
    </section>

  </main><!-- main -->
@endsection
