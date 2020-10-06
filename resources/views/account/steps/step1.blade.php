@extends('account.account_complete')

@section('steps')    
    <div class="col-12">
      <div class="card">
        <div id="smartWizardValidation">
          <ul class="card-header">
            <li><a href="#step-0">1<br /><small>{{ __('My Information') }}</small></a></li>
            <li><a href="#step-1">2<br /><small>{{ __('Business Information') }}</small></a></li>
            <li><a href="#step-2">3<br /><small>{{ __('Bank Information') }}</small></a></li>
          </ul>
			<form id="form" method="POST" action="{{ route('account.information') }}" class="card-body">
			@csrf
				<div id="step-0">
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
				</div><!-- step-0 -->

				<div class="btn-toolbar custom-toolbar text-center d-flex justify-content-center card-body pt-0">
					<button class="btn btn-primary prev-btn mx-2" type="button">{{__('Previous')}}</button>
					<button class="btn btn-primary next-btn mx-2" type="submit">{{__('Next')}}</button>
					<button class="btn btn-primary finish-btn mx-2" type="submit">{{__('Finish')}}</button>
				</div>
			</form>


            </div>
      </div>
    </div>
@endsection


@push('footer-scripts')
    {!! JsValidator::formRequest('App\Http\Requests\AccountInformationRequest', '#form') !!}
@endpush
