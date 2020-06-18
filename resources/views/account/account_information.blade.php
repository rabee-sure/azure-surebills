@extends('layouts.app')

@section('title', 'Page Title')

@section('content')
	<div class="row">
		<div class="col-12">
			<h1>Account Information</h1>
			<div class="separator mb-5"></div>
		</div>

		<div class="col-12 col-sm-12">
			<div class="card mb-4">
				<div class="card-body">
					<form id="form">
						<div class="form-row">
							<div class="form-group col-md-6">
								<label for="inputEmail1">Full Name</label>
								<input type="text" class="form-control" id="inputEmail1" placeholder="Full Name">
							</div>
							<div class="form-group col-md-6">
								<label for="inputEmail2">Email</label>
								<input type="email" class="form-control" id="inputEmail2" placeholder="Email">
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-md-6">
								<label for="inputEmail3">Mobile Number</label>
								<input type="tel" class="form-control" id="inputEmail3" placeholder="Mobile Number">
							</div>
							<div class="form-group col-md-6">
								<label for="inputEmail4">Gander</label>
								<select id="inputEmail4" class="form-control">
									<option selected>Choose...</option>
									<option value="1">Male</option>
									<option value="2">female</option>
								</select>
							</div>
						</div>
						<button type="submit" class="btn btn-primary d-block mt-2">Save</button>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection


@section('footer-scripts')
    {!! JsValidator::formRequest('App\Http\Requests\AccountInformationRequest', '#form') !!}
@endsection