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
              <th scope="col" class="fw-bold" width="5%">{{__('Actions')}}</th>
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
                  <div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                      <i class="icon-base ti ti-dots-vertical"></i>
                    </button>
                    <div class="dropdown-menu">
                      <a class="dropdown-item" href="{{ route('coupons.show', $coupon->id)}}">
                        <i class="icon-base ti ti-eye me-1"></i> {{ __('View') }}
                      </a>
                      @if($coupon->mechanism && $coupon->mechanism->value() === 'one_time_usage' && $isValid)
                        <a class="dropdown-item" href="{{ route('coupons.bulk-generate', $coupon->id)}}">
                          <i class="icon-base ti ti-circle-plus me-1"></i> {{ __('Generate Codes') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('coupons.show-export', $coupon->id)}}">
                          <i class="icon-base ti ti-download me-1"></i> {{ __('Export') }}
                        </a>
                      @endif
                      @if($withinValidPeriod)
                        <form method="POST" action="{{ route('coupons.toggle-status', $coupon->id) }}">
                          @csrf
                          <button
                            type="submit"
                            class="dropdown-item"
                          >
                            <i class="icon-base ti ti-toggle-right me-1"></i> {{ $coupon->is_active ? __('Deactivate') : __('Activate') }}
                          </button>
                        </form>
                      @endif
                      @if($canDelete)
                        <button
                          type="button"
                          class="dropdown-item"
                          data-bs-toggle="modal"
                          data-bs-target="#delete_coupon_Modal_{{ $coupon->id }}"
                        >
                          <i class="icon-base ti ti-trash me-1"></i> {{ __('Delete') }}
                        </button>
                      @endif
                    </div>
                  </div>
                  @if($canDelete)
                    <div class="modal fade" id="delete_coupon_Modal_{{ $coupon->id }}" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <div class="d-flex align-items-center justify-content-center text-warning mb-3">
                              <i class="icon-base ti ti-info-triangle icon-50px"></i>
                            </div>
                            <h5 class="m-0 text-center">{{ __('Are you sure you want to delete this coupon?') }}</h5>
                          </div>
                          <form action="{{ route('coupons.delete', $coupon->id) }}" method="post" class="modal-footer form-delete-coupon">
                            @csrf
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                            <button type="submit" class="btn btn-danger btn-submit-with-spinner" data-loading-text="{{ __('Deleting...') }}">
                              <span class="btn-spinner d-none me-2" role="status">
                                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                              </span>
                              <span class="btn-text">{{ __('Delete') }}</span>
                            </button>
                          </form>
                        </div>
                      </div>
                    </div>
                  @endif
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

@push('footer-scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form.classList.contains('form-delete-coupon')) return;

        const btn = form.querySelector('.btn-submit-with-spinner');
        if (!btn || btn.disabled) return;

        const btnText = btn.querySelector('.btn-text');
        const btnSpinner = btn.querySelector('.btn-spinner');
        const originalText = btnText ? btnText.textContent : '{{ __("Delete") }}';

        function resetButton() {
          btn.disabled = false;
          if (btnText && btnSpinner) {
            btnText.textContent = originalText;
            btnSpinner.classList.add('d-none');
          }
        }

        btn.disabled = true;
        if (btnText && btnSpinner) {
          btnText.textContent = btn.dataset.loadingText || 'Deleting...';
          btnSpinner.classList.remove('d-none');
        }
        setTimeout(resetButton, 8000);
      });
    });
  </script>
@endpush