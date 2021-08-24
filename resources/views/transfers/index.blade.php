@extends('layouts.app')

@section('title', 'Settlements')

@section('content')
    <div class="row">
        <div class="col-12">
            <h1>{{ __('Transfers') }}</h1>

            <div class="top-right-button-container d-flex align-items-center justify-content-center flex-column">
                @if(!auth()->user()->auto_trnasfer && auth()->user()->verified)
                    @include('transfers.request_transfer')
                @endif

                <h3>{{ __('Balance') }} : {{  round2(auth()->user()->balance)  }} {{__('SAR')}}</h3>
            </div>
            <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb pt-0">
                    <li class="breadcrumb-item"><a href="/">{{ __('Home') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Transfers') }}</li>
                </ol>
            </nav>
            <div class="mb-2"></div>

            <div class="separator mb-5"></div>
        </div>
    </div>
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped text-center">
                            <thead>
                                <tr>
                                    <th>{{__('Id')}}</th>
                                    <th>{{__('Amount')}}</th>
                                    <th>{{__('Transfer Fees')}}</th>
                                    <th>{{__('Net Amount')}}</th>
                                    <th>{{__('Note')}}</th>
                                    <th>{{__('Cycle Date')}}</th>
                                    <th>{{__('Created At')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th class="text-center">{{__('Statement')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transfers as $transfer)
                                <tr>
                                    <td>{{$transfer->id}}</td>
                                    <td>{{$transfer->amount}}</td>
                                    <td>{{$transfer->transfer_fees}}</td>
                                    <td>{{$transfer->net_amount}}</td>
                                    <td>{{$transfer->note}}</td>
                                    <td>{{$transfer->date_from_to}}</td>
                                    <td>{{$transfer->created_at}}</td>
                                    <td>
                                        @if($transfer->status == 'completed')
                                            <div class="badge badge-pill badge-success bill_status_badge" role="alert">
                                                {{__('Transfer ' .$transfer->status)}}
                                            </div>
                                        @elseif($transfer->status == 'pending')
                                            <div class="badge badge-pill badge-warning bill_status_badge" role="alert">
                                                {{__('Transfer ' .$transfer->status)}}
                                            </div>
                                        @elseif($transfer->status == 'canceled')
                                            <div class="badge badge-pill badge-danger bill_status_badge" role="alert">
                                                {{__('Transfer ' .$transfer->status)}}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center"  >
                                        <a href="transfers/{{$transfer->id }}/transactions"  data-toggle="tooltip" data-placement="top" data-original-title="{{ __('Statement') }}">
                                            <div class="glyph-icon simple-icon-eye" style="font-size: 25px"></div>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>    
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
