@extends('layouts.app')

@section('title', 'Page Title')

@section('content')
<div class="row">
	<div class="col-12">
		<h1>{{ __('Change Password') }}</h1>
		<div class="separator mb-5"></div>
	</div>

	<div class="col-12">
		<div class="card mb-4">
			<div class="card-body">
				<form id="form" method="POST" action="{{ route('change.password') }}">
                    @csrf 
					<div class="form-row">
						<div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
							<div class="form-group">
								<label for="password">{{ __('Current Password') }}</label>
								<input id="password" type="password" name="current_password" autocomplete="current-password" class="form-control" placeholder="{{ __('Current Password') }}">
							</div>
							<div class="form-group">
								<label for="_confirmation">{{ __('New Password') }}</label>
								<input id="new_password" type="password" class="form-control" name="new_password" autocomplete="current-password" placeholder="{{ __('New Password') }}">
							</div>
							<div class="form-group">
								<label for="new_password_confirmation">{{ __('Re-type New Password') }}</label>
								<input id="new_password_confirmation" type="password" class="form-control" name="new_password_confirmation" autocomplete="current-password" placeholder="{{__('Re-type New Password') }}">
							</div>
						</div>
					</div>
					<button type="submit" class="btn btn-primary d-block mt-2">{{ __('Save') }}</button>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection

@section('footer-scripts')
    {!! JsValidator::formRequest('App\Http\Requests\ChangePasswordRequest', '#form') !!}
@endsection