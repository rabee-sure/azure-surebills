@extends('layouts.app')

@section('title', __('Coupons'))

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <span>{{ __('Coupons')}}</span>
  </div><!-- breadcrump -->

  <section id="couponsIndexPage">

    <div class="title mb-4 d-flex align-items-center justify-content-between flex-wrap">
      <h1 class="d-block fw-bold m-0 fs-5">{{ __('Coupons')}}</h1>
      <a href="{{ route('coupons.create')}}" class="addCouponBtn d-flex btn-primary border-0 shadow-none align-items-center justify-content-center gap-2 px-3 text-white rounded-pill" title="{{ __('Add Coupon') }}"><i class="fal fa-plus"></i> {{ __('Add Coupon') }}</a>
    </div><!-- title -->

    @if (session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="couponsArea bg-white shadow-sm rounded-3 overflow-hidden mb-3">
      @if($coupons->count())
        <div class="table-responsive">
          <table class="table table-striped table-hover text-nowrap">
            <thead>
              <tr>
                <th scope="col" class="text-center">#</th>
                <th scope="col" class="text-center">{{__('Name')}}</th>
                <th scope="col" class="text-center">{{__('Mechanism')}}</th>
                <th scope="col" class="text-center">{{__('Discount')}}</th>
                <th scope="col" class="text-center">{{__('Valid Period')}}</th>
                <th scope="col" class="text-center">{{__('Usage Progress')}}</th>
                <th scope="col" class="text-center">{{__('Status')}}</th>
                <th scope="col" class="text-center" width="15%">{{__('Actions')}}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($coupons as $coupon)
                @php
                  $stats = $coupon->stats ?? [];
                  $totalUsage = $stats['total_usage'] ?? $coupon->total_usage ?? 0;
                  $remaining = $stats['remaining'] ?? $coupon->remaining_usage ?? null;
                  $limit = $stats['limit'] ?? $coupon->max_usage ?? null;
                  $isExpired = $coupon->is_expired;
                  $isValid = $coupon->is_valid;
                  $now = now();
                  $withinValidPeriod = (!$coupon->valid_from || $now->gte($coupon->valid_from))
                    && (!$coupon->valid_to || $now->lte($coupon->valid_to));
                  $canDelete = $totalUsage === 0;
                @endphp
                <tr>
                  <td class="text-center">{{ $coupon->id }}</td>
                  <td class="text-center">{{ $coupon->name }}</td>
                  <td class="text-center">@include('coupons.partials.mechanism-badge', ['coupon' => $coupon])</td>
                  <td class="text-center">
                    @if($coupon->discount_type === 'percentage')
                      {{ number_format($coupon->discount_value, 2) }}%
                    @else
                      <div class="d-flex align-items-center justify-content-center gap-1 fw-bold rtl flex-shrink-0">
                        {{ number_format($coupon->discount_value, 2) }}  <span class="riyal-symbol-font">$</span>
                      </div><!-- d-flex -->
                    @endif
                  </td>
                  <td class="text-center">
                    @if($coupon->valid_from && $coupon->valid_to)
                      <div class="d-flex align-items-center justify-content-center gap-1 flex-column">
                        <span class="d-block text-capitalize">{{ __('from') }} : {{ $coupon->valid_from->format('Y-m-d') }}</span>
                        <span class="d-block text-capitalize">{{ __('to') }} : {{ $coupon->valid_to->format('Y-m-d') }}</span>
                      </div>
                    @elseif($coupon->valid_from)
                      <span class="d-block text-capitalize">{{ __('From') }} : {{ $coupon->valid_from->format('Y-m-d') }}</span>
                    @elseif($coupon->valid_to)
                      <span class="d-block text-capitalize">{{ __('Until') }} : {{ $coupon->valid_to->format('Y-m-d') }}</span>
                    @else
                      <span class="d-block text-capitalize">{{ __('no expiration') }}</span>
                    @endif
                  </td>
                  <td class="text-center">
                    @if($limit)
                      <div class="d-flex align-items-center flex-column gap-1">
                        <span class="d-block text-capitalize">{{ $totalUsage }}/{{ $limit }}</span>
                        @if($totalUsage > 0)
                          <div class="progress flex-grow-1" style="height: 8px; min-width: 100%;">
                            <div class="progress-bar {{ $remaining === 0 ? 'bg-danger' : 'bg-primary' }}" style="width: {{ min(100, ($totalUsage / $limit) * 100) }}%"></div>
                          </div>
                        @endif
                      </div>
                    @elseif($coupon->mechanism && $coupon->mechanism->value() === 'one_time_usage')
                      <span>{{ $totalUsage }} {{ __('used') }}</span>
                    @else
                      <span>{{ $totalUsage }} {{ __('uses') }}</span>
                    @endif
                  </td>
                  <td class="text-center">
                    @if($isExpired)
                      <span class="badge badge-pill bg-danger">{{ __('Expired') }}</span>
                    @elseif(!$coupon->is_active)
                      <span class="badge badge-pill bg-secondary">{{ __('Inactive') }}</span>
                    @elseif(!$isValid)
                      <span class="badge badge-pill bg-warning">{{ __('Exhausted') }}</span>
                    @else
                      <span class="badge badge-pill bg-success">{{ __('Active') }}</span>
                    @endif
                  </td>
                  <td class="text-center">
                    <div class="d-flex align-items-center justify-content-center">
                      <a href="{{ route('coupons.show', $coupon->id)}}"
                         class="rounded-3 border-0 shadow-none p-0 btn-primary d-flex align-items-center justify-content-center mx-1"
                         data-bs-toggle="tooltip"
                         data-bs-placement="top"
                         title="{{ __('View') }}">
                        <i class="fal fa-eye"></i>
                      </a>

                      @if($coupon->mechanism && $coupon->mechanism->value() === 'one_time_usage' && $isValid)
                        <a href="{{ route('coupons.bulk-generate', $coupon->id)}}"
                           class="rounded-3 border-0 shadow-none p-0 btn-info d-flex align-items-center justify-content-center mx-1"
                           data-bs-toggle="tooltip"
                           data-bs-placement="top"
                           title="{{ __('Generate Codes') }}">
                          <i class="fal fa-plus-circle"></i>
                        </a>

                        <a href="{{ route('coupons.show-export', $coupon->id)}}"
                           class="rounded-3 border-0 shadow-none p-0 btn-secondary d-flex align-items-center justify-content-center mx-1"
                           data-bs-toggle="tooltip"
                           data-bs-placement="top"
                           title="{{ __('Export') }}">
                          <i class="fal fa-download"></i>
                        </a>
                      @endif

                      @if($withinValidPeriod)
                        <form method="POST" action="{{ route('coupons.toggle-status', $coupon->id) }}" class="d-inline">
                          @csrf
                          <button type="submit"
                                  class="rounded-3 border-0 shadow-none p-0 {{ $coupon->is_active ? 'btn-warning' : 'btn-success' }} d-flex align-items-center justify-content-center mx-1"
                                  data-bs-toggle="tooltip"
                                  data-bs-placement="top"
                                  title="{{ $coupon->is_active ? __('Deactivate') : __('Activate') }}">
                            <i class="fal {{ $coupon->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                          </button>
                        </form>
                      @endif

                      @if($canDelete)
                        <form method="POST" action="{{ route('coupons.delete', $coupon->id) }}" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this coupon?') }}');">
                          @csrf
                          <button type="submit"
                                  class="rounded-3 border-0 shadow-none p-0 btn-danger d-flex align-items-center justify-content-center mx-1"
                                  data-bs-toggle="tooltip"
                                  data-bs-placement="top"
                                  title="{{ __('Delete') }}">
                            <i class="fal fa-trash-alt"></i>
                          </button>
                        </form>
                      @endif
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div><!-- table-responsive -->
        {{ $coupons->links() }}
      @else
        <div class="no_coupons_yet d-flex align-items-center justify-content-center flex-column py-5">
          <i class="fal fa-ticket-alt fa-3x text-muted mb-3"></i>
          <span class="d-block text-center mt-3 text-capitalize">{{ __('No coupons found. Create your first coupon to get started.') }}</span>
        </div><!-- no_coupons_yet -->
      @endif
    </div><!-- couponsArea -->


  </section><!-- couponsIndexPage -->

@endsection
