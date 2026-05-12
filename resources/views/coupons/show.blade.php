@extends('layouts.app')

@section('title', __('Coupon Details'))

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <a href="{{ route('coupons.index')}}" title="{{ __('Coupons') }}">{{ __('Coupons') }}</a>
    <i>/</i>
    <span>{{ __('Coupon Details')}}</span>
  </div><!-- breadcrump -->

  <section id="couponsShowPage">

    <div class="title mb-4 d-flex align-items-center justify-content-between flex-wrap">
      <h1 class="d-block fw-bold m-0 fs-5">{{ __('Coupon Details')}}</h1>
      <div class="d-flex align-items-center justify-content-end gap-2">
        @if($coupon->mechanism && $coupon->mechanism->value() === 'one_time_usage' && $coupon->is_valid)
          <a href="{{ route('coupons.bulk-generate', $coupon->id)}}" class="d-flex btn-info border-0 shadow-none align-items-center justify-content-center gap-2 rounded-pill px-3">
            <i class="fal fa-plus-circle"></i> {{ __('Generate Codes') }}
          </a>
          <a href="{{ route('coupons.show-export', $coupon->id)}}" class="d-flex btn-secondary border-0 shadow-none align-items-center justify-content-center gap-2 rounded-pill px-3">
            <i class="fal fa-download"></i> {{ __('Export') }}
          </a>
        @endif
      </div>
    </div><!-- title -->

    @if (session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    @if (session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <div class="row g-4">

      {{-- Coupon Details --}}
      <div class="col-lg-8">
        <div class="customCard">
          <div class="customCard-header fs-6 fw-bold text-capitalize">{{ __('Coupon Information') }}</div>
          <div class="customCard-body bg-white">
            <table class="table m-0 text-nowrap">
              <tr>
                <td class="fw-bold" width="30%">{{ __('Name') }}</td>
                <td>{{ $coupon->name }}</td>
              </tr>
              <tr>
                <td class="fw-bold">{{ __('Mechanism') }}</td>
                <td>
                  <div class="d-flex flex-column gap-1">
                  @include('coupons.partials.mechanism-badge', ['coupon' => $coupon])
                  <small class="d-block text-muted">{{ $coupon->mechanism->description() }}</small>
                  </div>
                </td>
              </tr>
              <tr>
                <td class="fw-bold">{{ __('Discount') }}</td>
                <td>
                  @if($coupon->discount_type === 'percentage')
                    <strong>{{ number_format($coupon->discount_value, 2) }}%</strong>
                  @else
                    <div class="d-flex align-items-center justify-content-start gap-1 fw-bold rtl flex-shrink-0">
                      {{ number_format($coupon->discount_value, 2) }}  <span class="riyal-symbol-font">$</span>
                    </div><!-- d-flex -->
                  @endif
                </td>
              </tr>
              <tr>
                <td class="fw-bold">{{ __('Valid Period') }}</td>
                <td>
                  @if($coupon->valid_from && $coupon->valid_to)
                    {{ $coupon->valid_from->format('Y-m-d H:i') }} - {{ $coupon->valid_to->format('Y-m-d H:i') }}
                  @elseif($coupon->valid_from)
                    {{ __('From') }}: {{ $coupon->valid_from->format('Y-m-d H:i') }}
                  @elseif($coupon->valid_to)
                    {{ __('Until') }}: {{ $coupon->valid_to->format('Y-m-d H:i') }}
                  @else
                    <span class="text-muted">{{ __('No expiration') }}</span>
                  @endif
                </td>
              </tr>
              @if($coupon->mechanism && $coupon->mechanism->value() === 'max_usage' && $coupon->max_usage)
              <tr>
                <td class="fw-bold">{{ __('Max Usage') }}</td>
                <td>{{ number_format($coupon->max_usage) }}</td>
              </tr>
              @endif
              @if($coupon->mechanism && $coupon->mechanism->value() === 'max_customer_usage' && $coupon->max_customer_usage)
              <tr>
                <td class="fw-bold">{{ __('Max Per Customer') }}</td>
                <td>{{ number_format($coupon->max_customer_usage) }}</td>
              </tr>
              @endif
              @if(($coupon->mechanism && ($coupon->mechanism->value() === 'max_usage' || $coupon->mechanism->value() === 'max_customer_usage')) && $coupon->code_pattern)
              <tr>
                <td class="fw-bold">{{ __('Reusable Code') }}</td>
                <td><code class="bg-light p-2 rounded">{{ $coupon->code_pattern }}</code></td>
              </tr>
              @endif
              @if($coupon->mechanism && $coupon->mechanism->value() === 'one_time_usage' && $coupon->code_pattern)
              <tr>
                <td class="fw-bold">{{ __('Code Pattern') }}</td>
                <td><code class="bg-light p-2 rounded">{{ $coupon->code_pattern }}</code></td>
              </tr>
              @endif
              <tr>
                <td class="fw-bold">{{ __('Status') }}</td>
                <td>
                  @if($coupon->is_expired)
                    <span class="badge badge-pill bg-danger">{{ __('Expired') }}</span>
                  @elseif(!$coupon->is_active)
                    <span class="badge badge-pill bg-secondary">{{ __('Inactive') }}</span>
                  @elseif(!$coupon->is_valid)
                    <span class="badge badge-pill bg-warning">{{ __('Exhausted') }}</span>
                  @else
                    <span class="badge badge-pill bg-success">{{ __('Active') }}</span>
                  @endif
                </td>
              </tr>
              <tr>
                <td class="fw-bold">{{ __('Created At') }}</td>
                <td>{{ $coupon->created_at->format('Y-m-d H:i:s') }}</td>
              </tr>
            </table>
          </div>
        </div>
      </div><!-- col -->

      {{-- Usage Statistics --}}
      <div class="col-lg-4">
        <div class="customCard">
          <div class="customCard-header fs-6 fw-bold text-capitalize">{{ __('Usage Statistics') }}</div>
          <div class="customCard-body p-3 border bg-white">
            @include('coupons.partials.usage-stats', ['coupon' => $coupon, 'stats' => $stats])
          </div>
        </div>
      </div><!-- col -->

      {{-- Generated Codes (for ONE_TIME_USAGE) --}}
      @if($coupon->mechanism && $coupon->mechanism->value() === 'one_time_usage' && $codes && $codes->count())
        <div class="col-md-12">
          <div class="customCard">
            <div class="customCard-header text-capitalize d-flex align-items-center justify-content-between">
              <span class="d-block text-capitalize fs-6 fw-bold">{{ __('Recent Generated Codes') }}</span>
              <small class="text-muted">{{ __('Showing latest 50 codes') }}</small>
            </div>
            <div class="customCard-body p-3 border bg-white">
              <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-3">
                @foreach($codes as $code)
                  <div class="col">
                    <div class="border shadow-sm rounded-2 p-3 d-flex flex-column gap-2 h-100">
                      <span class="d-block text-capitalize">{{ __('Code') }} : <code>{{ $code->code }}</code></span>
                      <span class="d-block text-capitalize">{{ __('Status') }} : <span class="badge badge-pill badge-{{ $code->is_used ? 'danger' : 'success' }}">{{ $code->is_used ? __('Used') : __('Available') }}</span></span>
                      <small class="d-block text-capitalize text-muted">
                        {{ __('Used At') }} :
                        @if($code->is_used && $code->usage->first())
                          {{ $code->usage->first()->used_at->format('Y-m-d H:i:s') }}
                        @else
                          <span class="text-muted">-</span>
                        @endif
                      </small>
                    </div>
                  </div>
                @endforeach
              </div>


            </div>
          </div><!-- customCard -->
        </div><!-- col -->
      @endif

    </div><!-- row -->

  </section><!-- couponsShowPage -->

@endsection
