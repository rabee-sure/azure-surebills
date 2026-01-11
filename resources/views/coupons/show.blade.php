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
      <div>
        @if($coupon->mechanism && $coupon->mechanism->value() === 'one_time_usage' && $coupon->is_valid)
          <a href="{{ route('coupons.bulk-generate', $coupon->id)}}" class="btn btn-info me-2 rounded-3">
            <i class="fal fa-plus-circle me-1"></i>{{ __('Generate Codes') }}
          </a>
          <a href="{{ route('coupons.show-export', $coupon->id)}}" class="btn btn-secondary rounded-3">
            <i class="fal fa-download me-1"></i>{{ __('Export') }}
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
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">{{ __('Coupon Information') }}</h5>
          </div>
          <div class="card-body">
            <table class="table table-borderless">
              <tr>
                <td class="fw-bold" width="30%">{{ __('Name') }}</td>
                <td>{{ $coupon->name }}</td>
              </tr>
              <tr>
                <td class="fw-bold">{{ __('Mechanism') }}</td>
                <td>
                  @include('coupons.partials.mechanism-badge', ['coupon' => $coupon])
                  <small class="d-block text-muted mt-2">{{ $coupon->mechanism->description() }}</small>
                </td>
              </tr>
              <tr>
                <td class="fw-bold">{{ __('Discount') }}</td>
                <td>
                  @if($coupon->discount_type === 'percentage')
                    <strong>{{ number_format($coupon->discount_value, 2) }}%</strong>
                  @else
                    <strong>{{ number_format($coupon->discount_value, 2) }} {{ __('SAR') }}</strong>
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
                    <span class="badge bg-danger">{{ __('Expired') }}</span>
                  @elseif(!$coupon->is_active)
                    <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                  @elseif(!$coupon->is_valid)
                    <span class="badge bg-warning">{{ __('Exhausted') }}</span>
                  @else
                    <span class="badge bg-success">{{ __('Active') }}</span>
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
      </div>

      {{-- Usage Statistics --}}
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">{{ __('Usage Statistics') }}</h5>
          </div>
          <div class="card-body">
            @include('coupons.partials.usage-stats', ['coupon' => $coupon, 'stats' => $stats])
          </div>
        </div>
      </div>
    </div>

    {{-- Generated Codes (for ONE_TIME_USAGE) --}}
    @if($coupon->mechanism && $coupon->mechanism->value() === 'one_time_usage' && $codes && $codes->count())
    <div class="card border-0 shadow-sm mt-4">
      <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
        <h5 class="mb-0">{{ __('Recent Generated Codes') }}</h5>
        <small class="text-muted">{{ __('Showing latest 50 codes') }}</small>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm table-hover">
            <thead>
              <tr>
                <th>{{ __('Code') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Used At') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($codes as $code)
                <tr>
                  <td><code>{{ $code->code }}</code></td>
                  <td>
                    @if($code->is_used)
                      <span class="badge bg-danger">{{ __('Used') }}</span>
                    @else
                      <span class="badge bg-success">{{ __('Available') }}</span>
                    @endif
                  </td>
                  <td>
                    @if($code->is_used && $code->usage->first())
                      {{ $code->usage->first()->used_at->format('Y-m-d H:i:s') }}
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    @endif

  </section><!-- couponsShowPage -->

@endsection
