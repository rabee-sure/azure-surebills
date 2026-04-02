@extends('layouts.app')

@section('title', __('Coupon Details'))

@section('content')

  <div class="d-flex align-items-center justify-content-between flex-wrap gap-4 mb-6">
    <div class="d-flex flex-column gap-1">
      <h4 class="mb-0">{{ __('Coupon Details')}}</h4>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-custom-icon m-0">
          <li class="breadcrumb-item">
            <a href="/account" title="{{ __('Settings') }}">{{ __('Settings')}}</a>
            <i class="breadcrumb-icon icon-base ti ti-chevron-right align-middle icon-xs"></i>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/coupons') }}" title="{{ __('Coupons') }}">{{ __('Coupons') }}</a>
            <i class="breadcrumb-icon icon-base ti ti-chevron-right align-middle icon-xs"></i>
          </li>
          <li class="breadcrumb-item active">{{ __('Coupon Details') }}</li>
        </ol>
      </nav>
    </div>
    @php
          $withinValidPeriod = (!$coupon->valid_from || now()->gte($coupon->valid_from))
            && (!$coupon->valid_to || now()->lte($coupon->valid_to));
          $canDelete = ($stats['total_usage'] ?? 0) === 0;
        @endphp

        @if($withinValidPeriod)
          <form method="POST" action="{{ route('coupons.toggle-status', $coupon->id) }}" class="d-inline">
            @csrf
            <button type="submit" class="d-flex {{ $coupon->is_active ? 'btn-warning' : 'btn-success' }} border-0 shadow-none align-items-center justify-content-center gap-2 rounded-pill px-3">
              <i class="fal {{ $coupon->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
              {{ $coupon->is_active ? __('Deactivate') : __('Activate') }}
            </button>
          </form>
        @endif

        @if($canDelete)
          <form method="POST" action="{{ route('coupons.delete', $coupon->id) }}" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this coupon?') }}');">
            @csrf
            <button type="submit" class="d-flex btn-danger border-0 shadow-none align-items-center justify-content-center gap-2 rounded-pill px-3">
              <i class="fal fa-trash-alt"></i> {{ __('Delete') }}
            </button>
          </form>
        @endif
    @if($coupon->mechanism && $coupon->mechanism->value() === 'one_time_usage' && $coupon->is_valid)
      <div class="d-flex align-items-center justify-content-end gap-3">
        <a href="{{ route('coupons.bulk-generate', $coupon->id)}}" class="btn btn-info waves-effect waves-light">
          <span class="icon-xs icon-base ti ti-circle-plus me-2"></span>
          {{ __('Generate Codes') }}
        </a>
        <a href="{{ route('coupons.show-export', $coupon->id)}}" class="btn btn-secondary waves-effect waves-light">
          <span class="icon-xs icon-base ti ti-download me-2"></span>
          {{ __('Export') }}
        </a>
      </div>
    @endif
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if (session('error'))
    <div class="alert alert-danger alert-dismissible" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="row g-6">
    <div class="col-lg-7">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title mb-5">{{ __('Coupon Information') }}</h5>
          <div class="table-responsive text-nowrap">
            <table class="table table-bordered">
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
                    <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1">
                      {{ number_format($coupon->discount_value, 2) }} <i class="sar-icon"></i>
                    </span>
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
                <td><code>{{ $coupon->code_pattern }}</code></td>
              </tr>
              @endif
              @if($coupon->mechanism && $coupon->mechanism->value() === 'one_time_usage' && $coupon->code_pattern)
              <tr>
                <td class="fw-bold">{{ __('Code Pattern') }}</td>
                <td><code>{{ $coupon->code_pattern }}</code></td>
              </tr>
              @endif
              <tr>
                <td class="fw-bold">{{ __('Status') }}</td>
                <td>
                  @if($coupon->is_expired)
                    <span class="badge bg-label-danger">{{ __('Expired') }}</span>
                  @elseif(!$coupon->is_active)
                    <span class="badge bg-label-secondary">{{ __('Inactive') }}</span>
                  @elseif(!$coupon->is_valid)
                    <span class="badge bg-label-warning">{{ __('Exhausted') }}</span>
                  @else
                    <span class="badge bg-label-success">{{ __('Active') }}</span>
                  @endif
                </td>
              </tr>
              <tr>
                <td class="fw-bold">{{ __('Created At') }}</td>
                <td>{{ $coupon->created_at->format('Y-m-d H:i:s') }}</td>
              </tr>
            </table>
          </div><!-- table-responsive -->
        </div><!-- card-body -->
      </div><!-- card -->
    </div><!-- col -->
    <div class="col-lg-5">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title mb-5">{{ __('Usage Statistics') }}</h5>
          @include('coupons.partials.usage-stats', ['coupon' => $coupon, 'stats' => $stats])
        </div><!-- card-body -->
      </div><!-- card -->
    </div><!-- col -->
    @if($coupon->mechanism && $coupon->mechanism->value() === 'one_time_usage' && $codes && $codes->count())
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between gap-3 mb-5">
              <h5 class="card-title mb-0">{{ __('Recent Generated Codes') }}</h5>
              <p class="text-muted m-0">{{ __('Showing latest 50 codes') }}</p>
            </div><!-- d-flex -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-6">
              @foreach($codes as $code)
                <div class="col">
                  <div class="card h-100 shadow-none {{ $code->is_used ? 'bg-label-secondary' : 'bg-transparent' }} border border-{{ $code->is_used ? 'none' : 'success' }}">
                    <div class="card-body p-3 d-flex flex-column gap-3">
                      <span class="d-block text-capitalize text-heading">{{ __('Code') }} : <code>{{ $code->code }}</code></span>
                      <span class="d-block text-capitalize text-heading">{{ __('Status') }} : <span class="badge bg-label-{{ $code->is_used ? 'danger' : 'success' }}">{{ $code->is_used ? __('Used') : __('Available') }}</span></span>
                      @if($code->is_used && $code->usage->first())
                        <small class="d-block text-capitalize text-danger">
                          {{ __('Used At') }} :
                          @if($code->is_used && $code->usage->first())
                            {{ $code->usage->first()->used_at->format('Y-m-d H:i:s') }}
                          @else
                            -
                          @endif
                        </small>
                      @endif
                    </div><!-- card-body -->
                  </div><!-- card -->
                </div><!-- col -->
              @endforeach
            </div><!-- row -->
          </div><!-- card-body -->
        </div><!-- card -->
      </div><!-- col -->
    @endif
  </div><!-- row -->

@endsection
