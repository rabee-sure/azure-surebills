@extends('layouts.app')

@section('title', __('Bills'))

@section('content')
    <div class="row">
        <div class="col-12">
            <h1>{{ __('Transfers') }}</h1>
            <div class="top-right-button-container">
                <h3>{{ __('Amount') }} : {{ round($transfer->amount, 2) }} {{__('SAR')}}</h3>
            </div>
            <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb pt-0">
                    <li class="breadcrumb-item"><a href="/">{{ __('Home') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="/transfers">{{ __('Transfers') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ __('Transfer') }} {{$transfer->id}}
                    </li>
                </ol>
            </nav>
            <div class="mb-2"></div>
            <div class="separator mb-5"></div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped text-nowrap">
                    <thead>
                        <tr>
                            <th scope="col">{{__('Name') }}</th>
                            <th scope="col">{{__('Values') }}</th>
                            <th scope="col">{{__('Date created') }}</th>
                            <th scope="col" width="10%">{{__('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bills as $bill)
                            @include('bills.item')
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
