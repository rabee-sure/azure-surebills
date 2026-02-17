@extends('layouts.app')

@section('title', __('Transactions'))

@section('content')

  <div class="d-flex align-items-center justify-content-between gap-2 mb-6">
    <div class="d-flex flex-column gap-1">
      <h4 class="mb-0">{{ __('Transfer Transactions') }}</h4>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-custom-icon m-0">
          <li class="breadcrumb-item">
            <a href="{{ url('/transfers') }}" title="{{ __('Transfers') }}">{{ __('Transfers')}}</a>
            <i class="breadcrumb-icon icon-base ti ti-chevron-right align-middle icon-xs"></i>
          </li>
          <li class="breadcrumb-item active">{{ __('Transfer Transactions') }} : {{$transfer->id}}</li>
        </ol>
      </nav>
    </div>
    <a href="{{ route('transfer.export_bills', ['transfer' => $transfer])}}" title="{{ __('Export Transfer Bills')}}" class="btn btn-success waves-effect waves-light">
      <span class="icon-xs icon-base ti ti-file-export me-2"></span> {{ __('Export Transfer Bills')}}
    </a>
  </div>

  @if ($errors->any())
    <ul class="list-group mb-6">
      @foreach ($errors->all() as $error)
        <li class="list-group-item list-group-item-danger">{{ $error }}</li>
      @endforeach
    </ul>
  @endif

  @if(session()->has('success'))
    <div class="alert alert-success d-flex align-items-center mb-6" role="alert">
      <span class="alert-icon rounded">
        <i class="icon-base ti ti-check icon-md"></i>
      </span>
      {{ session()->get('success') }}
    </div>
  @endif

  @if($transactions->count())
    <div class="card">
      <div class="table-responsive text-nowrap">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th class="fw-bold">{{ __('Payment Date') }}</th>
              <th class="fw-bold">{{ __('Description') }}</th>
              <th class="fw-bold">{{ __('Reference') }}</th>
              <th class="fw-bold">{{ __('Receipt') }}</th>
              <th class="fw-bold">{{ __('Card') }}</th>
              <th class="fw-bold">{{ __('Debit') }}</th>
              <th class="fw-bold">{{ __('Credit') }}</th>
              <th class="fw-bold">{{ __('Balance') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($transactions as $transaction)
              <tr>
                <td>{{ $transaction->created_at }}</td>
                <td>{{ $transaction->description }}</td>
                <td>{{ $transaction->reference }}</td>
                <td>{{ $transaction->receipt }}</td>
                <td>
                  <div class="d-flex align-items-center justify-content-start gap-1">
                    @if ($transaction->card_brand == 'VISA')
                      <img alt="visa" src="/images/cards/visa.gif" width="18px">
                    @elseif ($transaction->card_brand == 'MASTER')
                      <img alt="mastercard" src="/images/cards/mastercard.gif" width="18px">
                    @elseif ($transaction->card_brand == 'MADA')
                      <img alt="mada" src="/images/cards/mada.gif" width="18px">
                    @elseif ($transaction->card_brand == 'APPLEPAY')
                      <img alt="applepay" src="/images/cards/applepay.gif" width="18px">
                    @endif
                    {{ $transaction->card }}
                  </div>
                </td>
                <td>
                  <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 m-0 text-danger">
                    {{ $transaction->type == 'debit' ? round2($transaction->amount) : '0' }} <i class="sar-icon"></i>
                  </span>
                </td>
                <td>
                  <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 m-0 text-success">
                    {{ $transaction->type == 'credit' ? round2($transaction->amount) : '0' }} <i class="sar-icon"></i>
                  </span>
                </td>
                <td>
                  <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 m-0">
                    {{ fact_number(round($transaction->balance, 2)) }} <i class="sar-icon"></i>
                  </span>
                </td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td colspan="5" class="fw-bold">{{ __('Total')}}</td>
              <td>
                <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 m-0 text-danger fw-bold">
                  {{ $totals['debit'] ?? 0 }} <i class="sar-icon"></i>
                </span>
              <td>
                <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 m-0 text-success fw-bold">
                  {{ $totals['credit'] ?? 0 }} <i class="sar-icon"></i>
                </span>
              </td>
              <td>
                <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 m-0 fw-bold">
                  {{ $totals['all'] ?? 0 }} <i class="sar-icon"></i>
                </span>
              </td>
            </tr>
          </tfoot>
        </table>
      </div><!-- table-responsive -->
    </div><!-- card -->
    <div class="d-flex align-items-center justify-content-center mt-3">
      {{ $transactions->links() }}
    </div><!-- d-flex -->
  @else
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center flex-column py-5">
        <i class="ti ti-transfer ti-xl"></i>
          <span class="d-block text-center mt-3 text-capitalize">{{ __('No Bill Matched The Given Criteria.') }}</span>
        </div><!-- d-flex -->
      </div><!-- card-body -->
    </div><!-- card -->
  @endif

@endsection
