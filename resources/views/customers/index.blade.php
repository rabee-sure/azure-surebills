@extends('layouts.app')

@section('title', __('Customers'))

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
  <div class="row">
    <div class="col-12">
      <div class="mb-3">
        <h1>{{ __('Customers')}}</h1>
        <div class="top-right-button-container">
        @include('customers.create')
      </div>
      <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
        <ol class="breadcrumb pt-0">
          <li class="breadcrumb-item">
            <a href="{{ url('/') }}">{{ __('Home')}}</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">{{ __('Customers')}}</li>
        </ol>
      </nav>
      <div class="separator mb-5"></div>
      </div>
    </div>
  </div>
  <div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          @if($customers->count())
          <table class="table table-striped">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">{{__('Name')}}</th>
                <th scope="col">{{__('Mobile')}}</th>
                <th scope="col">{{__('Email')}}</th>
                <th scope="col">{{__('Bills')}}</th>
                <th scope="col">{{__('Date created')}}</th>
                <th scope="col">{{__('Actions')}}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($customers as $customer)
                <tr>
                  <th scope="row">{{ $customer->id }}</th>
                  <td>{{ $customer->name }}</td>
                  <td>{{ $customer->mobile }}</td>
                  <td>{{ $customer->email }}</td>
                  <td>{{ $customer->bills->count() }}</td>
                  <td>{{ $customer->created_at }}</td>
                  <td>
                    <a href="{{ route('customers.edit', $customer->id)}}" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-original-title="{{ __('Edit') }}">
                    <!-- {{ __('Edit') }} --><svg version="1.1" id="Capa_1" style="width: 15px;height: auto;fill: #fff;" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
<g>
	<g>
		<path d="M476.828,302.249c-10.794,0-19.542,8.748-19.542,19.542v151.125H39.087V54.718h151.125
			c10.794,0,19.542-8.748,19.542-19.542c0-10.794-8.748-19.542-19.542-19.542H19.545c-10.794,0-19.542,8.748-19.542,19.542v457.282
			C0.003,503.252,8.752,512,19.545,512h457.282c10.794,0,19.542-8.748,19.542-19.542V321.791
			C496.37,310.998,487.621,302.249,476.828,302.249z"/>
	</g>
</g>
<g>
	<g>
		<path d="M506.271,75.426l-69.693-69.7C432.917,2.058,427.947,0,422.762,0c-5.185,0-10.149,2.058-13.816,5.726L178.35,236.321
			c-2.371,2.365-4.084,5.296-4.996,8.514l-27.359,97.059c-1.915,6.807-0.006,14.116,4.996,19.119
			c3.713,3.713,8.703,5.726,13.816,5.726c1.765,0,3.55-0.241,5.296-0.73l97.059-27.359c3.224-0.912,6.156-2.632,8.52-4.996
			l230.589-230.595C513.905,95.43,513.905,83.053,506.271,75.426z M251.658,302.412l-58.58,16.506l16.513-58.567L422.762,47.181
			l42.061,42.061L251.658,302.412z"/>
	</g>
</g>
<g>
	<g>
		
			<rect x="208.103" y="235.027" transform="matrix(0.7071 -0.7071 0.7071 0.7071 -134.3583 244.2405)" width="39.084" height="98.556"/>
	</g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
</svg></a>
                    @include('customers.delete', ['customer' => $customer])
                  </td>
                </tr>
              @endforeach

            </tbody>
          </table>
          @else
            <div class="no_customers_yet">
              <svg xmlns="http://www.w3.org/2000/svg" height="512" viewBox="0 0 24 24" width="512" fill="#999" xmlns:v="https://vecta.io/nano"><path d="M20 12c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm0-3a.94.94 0 0 0-1 1 .94.94 0 0 0 1 1 .94.94 0 0 0 1-1 .94.94 0 0 0-1-1zm3.5 8a.47.47 0 0 1-.5-.5v-1a1.54 1.54 0 0 0-1.5-1.5h-3a.47.47 0 0 1-.5-.5.47.47 0 0 1 .5-.5h3c1.4 0 2.5 1.1 2.5 2.5v1a.47.47 0 0 1-.5.5zM4 12c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm0-3a.94.94 0 0 0-1 1 .94.94 0 0 0 1 1 .94.94 0 0 0 1-1 .94.94 0 0 0-1-1zM.5 17a.47.47 0 0 1-.5-.5v-1C0 14.1 1.1 13 2.5 13h3a.47.47 0 0 1 .5.5.47.47 0 0 1-.5.5h-3A1.54 1.54 0 0 0 1 15.5v1a.47.47 0 0 1-.5.5zM12 12.5c-1.7 0-3-1.3-3-3s1.3-3 3-3 3 1.3 3 3-1.3 3-3 3zm0-5c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zM17 19a.47.47 0 0 1-.5-.5v-2A1.54 1.54 0 0 0 15 15H9a1.54 1.54 0 0 0-1.5 1.5v2a.47.47 0 0 1-.5.5.47.47 0 0 1-.5-.5v-2C6.5 15.1 7.6 14 9 14h6c1.4 0 2.5 1.1 2.5 2.5v2a.47.47 0 0 1-.5.5z"/></svg>
              <span>{{ __('No Customer matched the given criteria.') }}</span>
            </div><!-- no_customers_yet -->
          @endif
          {{ $customers->links() }}
        </div>
      </div>
    </div>
  </div>
  </div>
@endsection

@push('footer-scripts')
    {!! JsValidator::formRequest('App\Http\Requests\CustomerRequest', '#customers_store') !!}
@endpush