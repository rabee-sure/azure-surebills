@extends('layouts.app')

@section('title', __('Coupons'))

@section('content')

  <div class="d-flex align-items-center justify-content-between flex-wrap gap-4 mb-6">
    <div class="d-flex flex-column gap-1">
      <h4 class="mb-0">{{ __('Coupons')}}</h4>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-custom-icon m-0">
          <li class="breadcrumb-item">
            <a href="/account" title="{{ __('Settings') }}">{{ __('Settings')}}</a>
            <i class="breadcrumb-icon icon-base ti ti-chevron-right align-middle icon-xs"></i>
          </li>
          <li class="breadcrumb-item active">{{ __('Coupons')}}</li>
        </ol>
      </nav>
    </div>
    <div>
      <a href="{{ route('coupons.create')}}" title="{{ __('Add Coupon') }}" class="btn btn-primary waves-effect waves-light"><span class="icon-xs icon-base ti ti-plus me-2"></span> {{ __('Add Coupon')}}</a>
    </div>
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible mb-6" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if ($errors->any())
    <ul class="list-group mb-6">
      @foreach ($errors->all() as $error)
        <li class="list-group-item list-group-item-danger">{{ $error }}</li>
      @endforeach
    </ul>
  @endif


  <div class="card">
    @if($coupons->count())
      <div class="table-responsive text-nowrap">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th scope="col" class="fw-bold">#</th>
              <th scope="col" class="fw-bold">{{__('Name')}}</th>
              <th scope="col" class="fw-bold">{{__('Mechanism')}}</th>
              <th scope="col" class="fw-bold">{{__('Discount')}}</th>
              <th scope="col" class="fw-bold">{{__('Valid Period')}}</th>
              <th scope="col" class="fw-bold">{{__('Usage Progress')}}</th>
              <th scope="col" class="fw-bold">{{__('Status')}}</th>
              <th scope="col" class="fw-bold" width="10%">{{__('Actions')}}</th>
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
              @endphp
              <tr>
                <td>{{ $coupon->id }}</td>
                <td>{{ $coupon->name }}</td>
                <td>@include('coupons.partials.mechanism-badge', ['coupon' => $coupon])</td>
                <td>
                  @if($coupon->discount_type === 'percentage')
                    {{ number_format($coupon->discount_value, 2) }}%
                  @else
                    <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1">
                      {{ number_format($coupon->discount_value, 2) }} <i class="sar-icon"></i>
                    </span>
                  @endif
                </td>
                <td>
                  @if($coupon->valid_from && $coupon->valid_to)
                    <div class="d-flex align-items-start justify-content-center gap-1 flex-column">
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
                <td>

                  @if($limit)
                    <div class="d-flex align-items-start flex-column gap-1">
                      <span class="d-block text-capitalize">{{ $totalUsage }}/{{ $limit }}</span>
                      @if($totalUsage > 0)
                        <div class="progress flex-grow-1 w-100" style="height: 8px;">
                          <div
                            class="progress-bar progress-bar-striped progress-bar-animated {{ $remaining === 0 ? 'bg-danger' : 'bg-primary' }}"
                            role="progressbar"
                            style="width: {{ min(100, ($totalUsage / $limit) * 100) }}%"
                            aria-valuenow="{{ min(100, ($totalUsage / $limit) * 100) }}"
                            aria-valuemin="0"
                            aria-valuemax="100"
                          >
                          </div>
                        </div>
                      @endif
                    </div>
                  @elseif($coupon->mechanism && $coupon->mechanism->value() === 'one_time_usage')
                    <span>{{ $totalUsage }} {{ __('used') }}</span>
                  @else
                    <span>{{ $totalUsage }} {{ __('uses') }}</span>
                  @endif
                </td>
                <td>
                  @if($isExpired)
                    <span class="badge bg-label-danger">{{ __('Expired') }}</span>
                  @elseif(!$coupon->is_active)
                    <span class="badge bg-label-secondary">{{ __('Inactive') }}</span>
                  @elseif(!$isValid)
                    <span class="badge bg-label-warning">{{ __('Exhausted') }}</span>
                  @else
                    <span class="badge bg-label-success">{{ __('Active') }}</span>
                  @endif
                </td>
                <td>
                  <div class="d-flex align-items-center justify-content-start gap-2">
                    <a
                      href="{{ route('coupons.show', $coupon->id)}}"
                      data-bs-toggle="tooltip"
                      data-bs-placement="top"
                      title="{{ __('View') }}"
                      class="btn btn-icon text-white btn-sm btn-primary waves-effect waves-light"
                    >
                      <span class="icon-base ti ti-eye icon-18px"></span>
                    </a>
                    @if($coupon->mechanism && $coupon->mechanism->value() === 'one_time_usage' && $isValid)
                      <a
                        href="{{ route('coupons.bulk-generate', $coupon->id)}}"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="{{ __('Generate Codes') }}"
                        class="btn btn-icon text-white btn-sm btn-info waves-effect waves-light"
                      >
                        <span class="icon-base ti ti-circle-plus icon-18px"></span>
                      </a>
                      <a
                        href="{{ route('coupons.show-export', $coupon->id)}}"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="{{ __('Export') }}"
                        class="btn btn-icon text-white btn-sm btn-secondary waves-effect waves-light"
                      >
                        <span class="icon-base ti ti-download icon-18px"></span>
                      </a>
                    @endif
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div><!-- table-responsive -->
      <div class="d-flex align-items-center justify-content-center mt-4">
        {{ $coupons->links() }}
      </div>
    @else
      <div class="no_bills_yet d-flex align-items-center justify-content-center flex-column py-5">
        <i class="ti ti-ticket ti-xl"></i>
        <span class="d-block text-center mt-3 text-capitalize">{{ __('No coupons found. Create your first coupon to get started.') }}</span>
      </div><!-- no_bills_yet -->
    @endif
  </div><!-- card -->

  @endsection
