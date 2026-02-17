@extends('layouts.app')

@section('title', __('Transfers'))

@section('content')

  <div class="d-flex align-items-center justify-content-between gap-2 mb-6">
    <h4 class="m-0">{{ __('Transfers') }}</h4>
    @can('create transfer')
      @if(!auth()->user()->auto_trnasfer && auth()->user()->verified)
        @include('transfers.request_transfer')
      @endif
    @endcan
  </div><!-- d-flex -->

  @if ($errors->any())
    <ul class="list-group mb-6">
      @foreach ($errors->all() as $key => $error)
        @if($key == 'transfer_minimum')
          <li class="list-group-item list-group-item-danger">{{ $error }}</li>
        @else
          <li class="list-group-item list-group-item-danger">{{ $error }}</li>
        @endif
      @endforeach
    </ul>
  @endif

  <div class="d-flex align-items-center justify-content-end mb-6">
    <h6 class="text-success bg-label-success rounded-3 py-2 px-3 m-0 d-flex align-items-center justify-content-end gap-1">
      {{ __('Balance') }} :
      <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 m-0">
        {{  round2(auth()->user()->balance)  }} <i class="sar-icon"></i>
      </span>
    </h6>
  </div>


  <div class="card">
    <div class="table-responsive text-nowrap">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th class="fw-bold">{{__('Id')}}</th>
            <th class="fw-bold">{{__('Amount')}}</th>
            <th class="fw-bold">{{__('Transfer Fees')}}</th>
            <th class="fw-bold">{{__('Net Amount')}}</th>
            <th class="fw-bold">{{__('Note')}}</th>
            <th class="fw-bold">{{__('Cycle Date')}}</th>
            <th class="fw-bold">{{__('Created At')}}</th>
            <th class="fw-bold">{{__('Status')}}</th>
            <th class="fw-bold">{{__('Statement')}}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($transfers as $transfer)
            <tr>
              <td>{{$transfer->id}}</td>
              <td>
                <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1">
                  {{$transfer->amount}} <i class="sar-icon"></i>
                </span>
              </td>
              <td>
                <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1">
                  {{$transfer->transfer_fees}} <i class="sar-icon"></i>
                </span>
              </td>
              <td>
                <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1">
                  {{$transfer->net_amount}} <i class="sar-icon"></i>
                </span>
              </td>
              <td>{{$transfer->note}}</td>
              <td>{{$transfer->date_from_to}}</td>
              <td>{{$transfer->created_at}}</td>
              <td>
                @if($transfer->status == 'completed')
                  <div class="badge bg-label-success">
                    {{__('Transfer ' .$transfer->status)}}
                  </div>
                @elseif($transfer->status == 'pending' || $transfer->status == 'send_to_sps')
                  <div class="badge bg-label-warning">
                    {{__('Transfer ' .'pending')}}
                  </div>
                @elseif($transfer->status == 'canceled')
                  <div class="badge bg-label-danger">
                    {{__('Transfer ' .$transfer->status)}}
                  </div>
                @endif
              </td>
              <td>
                <a href="transfers/{{$transfer->id }}/transactions" class="btn btn-icon btn-sm text-white btn-primary waves-effect waves-light">
                  <span class="icon-base ti ti-eye icon-18px"></span>
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div><!-- table-responsive -->
  </div><!-- card -->

@endsection
