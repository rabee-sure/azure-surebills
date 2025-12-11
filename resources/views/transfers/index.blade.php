@extends('layouts.app')

@section('title', __('Transfers'))

@section('content')

<div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
  <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
  <i>/</i>
  <span>{{ __('Transfers') }}</span>
</div><!-- breadcrump -->

<section id="transfersIndexPage">

  <div class="title mb-4 d-flex align-items-center justify-content-between flex-wrap">
    <h1 class="d-block fw-bold m-0 fs-5">{{ __('Transfers') }}</h1>
    <h2 class="d-flex align-items-end fs-6 justify-content-end flex-column m-0 gap-1">
        @can('create transfer')
        @if(!auth()->user()->auto_trnasfer && auth()->user()->verified)
        @include('transfers.request_transfer')
        @endif
        @endcan
        <div class="d-flex align-items-center justify-content-end gap-1">
          {{ __('Balance') }} :
          <div class="d-flex align-items-center justify-content-center gap-1 fw-bold rtl flex-shrink-0">
            {{  round2(auth()->user()->balance)  }}  <span class="riyal-symbol-font">$</span>
          </div><!-- d-flex -->
        </div>
    </h2>
  </div><!-- title -->

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="m-0 p-0">
        @foreach ($errors->all() as $key => $error)
          @if($key == 'transfer_minimum')
          <li>
            <div class="d-flex align-items-center justify-content-start gap-1">
                <div class="d-flex align-items-center gap-1 m-0 {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}}">{{ $error }}</div>
            </div>
          </li>
          @else
            <li>{{ $error }}</li>
          @endif
        @endforeach
      </ul>
    </div><!-- alert -->
  @endif

  <div class="blockArea bg-white shadow-sm rounded-3 overflow-hidden mb-3">
    <div class="table-responsive">
      <table class="table table-striped table-hover text-nowrap">
        <thead>
          <tr>
            <th class="text-center">{{__('Id')}}</th>
            <th class="text-center">{{__('Amount')}}</th>
            <th class="text-center">{{__('Transfer Fees')}}</th>
            <th class="text-center">{{__('Net Amount')}}</th>
            <th class="text-center">{{__('Note')}}</th>
            <th class="text-center">{{__('Cycle Date')}}</th>
            <th class="text-center">{{__('Created At')}}</th>
            <th class="text-center">{{__('Status')}}</th>
            <th class="text-center">{{__('Statement')}}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($transfers as $transfer)
            <tr>
              <td class="text-center">{{$transfer->id}}</td>
              <td class="text-center">
                <div class="d-flex align-items-center justify-content-center gap-1 fw-bold rtl flex-shrink-0">
                  {{$transfer->amount}}  <span class="riyal-symbol-font">$</span>
                </div><!-- d-flex -->
              </td>
              <td class="text-center">
                <div class="d-flex align-items-center justify-content-center gap-1 fw-bold rtl flex-shrink-0">
                  {{$transfer->transfer_fees}}  <span class="riyal-symbol-font">$</span>
                </div><!-- d-flex -->
              </td>
              <td class="text-center">
                <div class="d-flex align-items-center justify-content-center gap-1 fw-bold rtl flex-shrink-0">
                  {{$transfer->net_amount}}  <span class="riyal-symbol-font">$</span>
                </div><!-- d-flex -->
              </td>
              <td class="text-center">{{$transfer->note}}</td>
              <td class="text-center">{{$transfer->date_from_to}}</td>
              <td class="text-center">{{$transfer->created_at}}</td>
              <td class="text-center">
                @if($transfer->status == 'completed')
                    <div class="badge badge-pill badge-success bill_status_badge d-flex align-items-center justify-content-center" role="alert">
                        {{__('Transfer ' .$transfer->status)}}
                    </div>
                @elseif($transfer->status == 'pending' || $transfer->status == 'send_to_sps')
                    <div class="badge badge-pill badge-warning bill_status_badge d-flex align-items-center justify-content-center" role="alert">
                        {{__('Transfer ' .'pending')}}
                    </div>
                @elseif($transfer->status == 'canceled')
                  <div class="badge badge-pill badge-danger bill_status_badge d-flex align-items-center justify-content-center" role="alert">
                      {{__('Transfer ' .$transfer->status)}}
                  </div>
                @endif
              </td>
              <td class="text-center">
                <div class="d-flex align-items-center justify-content-center">
                  <a href="transfers/{{$transfer->id }}/transactions" class="d-flex align-items-center justify-content-center btn-primary rounded-3 border-0 shadow-none"><i class="far fa-eye"></i></a>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div><!-- blockArea -->

</section><!-- transfersIndexPage -->
@endsection
