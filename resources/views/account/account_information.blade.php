@extends('layouts.app')

@section('title', __('Account Information'))

@section('content')
	<div class="row">
		<div class="col-12">
			<h1>{{ __('Account Information')}}</h1>
      <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
        <ol class="breadcrumb pt-0">
          <li class="breadcrumb-item"><a href="{{ url('/') }}" title="{{__('Home')}}">{{__('Home')}}</a></li>
          <li class="breadcrumb-item"><a href="{{ url('account') }}" title="{{__('Account')}}">{{__('Account')}}</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{ __('Account Information')}}</li>
        </ol>
      </nav>
			<div class="separator mb-5"></div>
		</div>

		<div class="col-12 col-sm-12">
			<div class="card mb-4">
				<div class="card-body">
					<form id="form" method="POST" action="{{ route('account.information') }}">
						@csrf 
						<div class="form-row">
							<div class="form-group col-md-6">
								<label for="inputEmail1">{{ __('Full Name')}} <span class="requirement">*</span></label>
								<input name="name" type="text" class="form-control" id="inputEmail1" placeholder="{{ __('Full Name')}}" value="{{ $user->name }}">
							</div>
							<div class="form-group col-md-6">
								<label for="inputEmail2">{{ __('Email')}} <span class="requirement">*</span></label>
								<input name="email" type="email" class="form-control" id="inputEmail2" placeholder="{{ __('Email')}}" value="{{ $user->email }}" >
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-md-6">
								<label for="inputEmail3">{{ __('Mobile Number')}}</label>
								<input name="mobile" type="tel" class="form-control" id="inputEmail3" placeholder="{{ __('Mobile Number')}}" disabled="" value="{{ $user->mobile }}">
							</div>
							<div class="form-group col-md-6">
								<label for="inputEmail4">{{ __('Gander')}}</label>
								<select name="gender" id="inputEmail4" class="form-control">
									<option value="0" @if ($user->gender == 0)selected="selected"@endif>{{ __('Choose Gender')}}</option>
									<option value="1" @if ($user->gender == 1)selected="selected"@endif>{{ __('Male')}}</option>
									<option value="2" @if ($user->gender == 2)selected="selected"@endif>{{ __('female')}}</option>
								</select>
							</div>

							<div class="form-group  col-md-6">
                            <label for="bullding_no">{{__('bullding_no')}}</label>
                            <input name="bullding_no" type="text" class="form-control" id="bullding_no" placeholder="{{__('bullding_no')}}"  value="{{ $user->bullding_no }}">
                        </div> 
                        <div class="form-group  col-md-6">
                            <label for="street_name">{{__('street_name')}}</label>
                            <input name="street_name" type="text" class="form-control" id="street_name" placeholder="{{__('street_name')}}"  value="{{ $user->street_name }}">
                        </div>
                        <div class="form-group  col-md-6">
                            <label for="district">{{__('district')}}</label>
                            <input name="district" type="text" class="form-control" id="district" placeholder="{{__('district')}}"  value="{{ $user->district }}">
                        </div>
                        <div class="form-group  col-md-6">
                            <label for="city">{{__('city')}}</label>
                            <input name="city" type="text" class="form-control" id="city" placeholder="{{__('city')}}"  value="{{ $user->city }}">
                        </div>
                        <div class="form-group  col-md-6">
                            <label for="postal_code">{{__('postal_code')}}</label>
                            <input name="postal_code" type="text" class="form-control" id="postal_code" placeholder="{{__('postal_code')}}"  value="{{ $user->postal_code }}">
                        </div>
                        <div class="form-group  col-md-6">
                            <label for="additional_no">{{__('additional_no')}}</label>
                            <input name="additional_no" type="text" class="form-control" id="additional_no" placeholder="{{__('additional_no')}}"  value="{{ $user->additional_no }}">
                        </div>
                        <div class="form-group  col-md-6">
                            <label for="other_buyer_id">{{__('other_buyer_id')}}</label>
                            <input name="other_buyer_id" type="text" class="form-control" id="other_buyer_id" placeholder="{{__('other_buyer_id')}}"  value="{{ $user->other_buyer_id }}">
                        </div>
						</div>
						<button type="submit" class="btn btn-primary d-block mt-2">{{ __('Save')}}</button>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection


@push('footer-scripts')
    {!! JsValidator::formRequest('App\Http\Requests\AccountInformationRequest', '#form') !!}
@endpush