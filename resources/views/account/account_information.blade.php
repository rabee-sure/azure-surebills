@extends('layouts.app')

@section('title', 'Page Title')

@section('content')
	<div class="row">
		<div class="col-12">
			<h1>{{ __('Account Information')}}</h1>
			<div class="separator mb-5"></div>
		</div>

		<div class="col-12 col-sm-12">
			<div class="card mb-4">
				<div class="card-body">
					<form id="form" method="POST" action="{{ route('account.information') }}">
						@csrf 
						<div class="form-row">
							<div class="form-group col-md-6">
								<label for="inputEmail1">{{ __('Full Name')}}</label>
								<input name="name" type="text" class="form-control" id="inputEmail1" placeholder="{{ __('Full Name')}}" value="{{ $user->name }}">
							</div>
							<div class="form-group col-md-6">
								<label for="inputEmail2">{{ __('Email')}}</label>
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
									<option value="0" @if ($user->gender == 0)selected="selected"@endif>{{ __('Choose...')}}</option>
									<option value="1" @if ($user->gender == 1)selected="selected"@endif>{{ __('Male')}}</option>
									<option value="2" @if ($user->gender == 2)selected="selected"@endif>{{ __('female')}}</option>
								</select>
							</div>
						</div>
						<button type="submit" class="btn btn-primary d-block mt-2">{{ __('Save')}}</button>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection


@section('footer-scripts')
    {!! JsValidator::formRequest('App\Http\Requests\AccountInformationRequest', '#form') !!}
@endsection