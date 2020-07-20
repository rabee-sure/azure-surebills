@extends('layouts.app')

@section('title', __('My Account'))

@section('content')
	<div class="row">
		<div class="col-12">
			<h1>{{ __('Account Information') }}</h1>
			<div class="separator mb-5"></div>
		</div>

		<div class="col-12 col-sm-12">
			<div class="card mb-4">
				<div class="card-body">
					<ul class="list-unstyled" data-link="account">
				        <li>
				          <a href="{{ route('account_information') }}">
				            <i class="iconsminds-id-card"></i> <span class="d-inline-block">{{ __('Account Information') }}</span>
				          </a>
				        </li>
				        <li>
				          <a href="{{ route('bank_information') }}">
				            <i class="iconsminds-bank"></i> <span class="d-inline-block">{{ __('Bank Information') }}</span>
				           </a>
				        </li>
				        <li>
				          <a href="{{ route('business_information') }}">
				            <i class="iconsminds-management"></i> <span class="d-inline-block">{{ __('Business Information') }}</span>
				          </a>
				        </li>
				        <li>
				          <a href="{{ route('change_password') }}">
				            <i class="iconsminds-type-pass"></i> <span class="d-inline-block">{{ __('Change Password') }}</span>
				          </a>
				        </li>
				    </ul>     
				</div>
			</div>
		</div>
	</div>
@endsection


@section('footer-scripts')
    {!! JsValidator::formRequest('App\Http\Requests\AccountInformationRequest', '#form') !!}
@endsection
