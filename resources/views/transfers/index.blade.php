 @extends('layouts.app')

@section('title', 'Settlements')

@section('content')
    <div class="row">
      <div class="col-12">
        <h1>Statement</h1>
        <div class="top-right-button-container">
         <h3>Balance : 177.96 SAR</h3>
        </div>
        <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
          <ol class="breadcrumb pt-0">
            <li class="breadcrumb-item">
              <a href="index.html">Home</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Statement</li>
          </ol>
        </nav>
        <div class="mb-2">x
        </div>
        <div class="separator mb-5"></div>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-12 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped text-center">
                <thead>
                  <tr>
                    <th>Id</th>
                    <th>bills paid from</th>
                    <th>bills paid to</th>
                    <th>total number of bills</th>
                    <th>total amount of bills</th>
                    <th>total paid amount</th>
                    <th>total fees amount</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach($transfers as $transfer)
                        <tr>
                            <td>{{$transfer->id}}</td>
                            <td>{{$transfer->bills_paid_from}}</td>
                            <td>{{$transfer->bills_paid_to}}</td>
                            <td>{{$transfer->total_number_of_bills}}</td>
                            <td>{{$transfer->total_amount_of_bills}}</td>
                            <td>{{$transfer->total_paid_amount}}</td>
                            <td>{{$transfer->total_fees_amount}}</td>
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