@extends('account.account_complete')

@section('steps')
  <div class="stepsArea d-flex align-items-start justify-content-between position-relative mb-5">
    <div class="item d-flex align-items-center justify-content-center flex-column active">
      <span class="border rounded-circle fw-bold d-flex align-items-center justify-content-center position-relative bg-light shadow-sm">1</span>
      <p class="d-block text-center mb-0 mt-2">{{ __('My Information') }}</p>
    </div><!-- item -->
    <div class="item d-flex align-items-center justify-content-center flex-column">
      <span class="border rounded-circle fw-bold d-flex align-items-center justify-content-center position-relative bg-light shadow-sm">2</span>
      <p class="d-block text-center mb-0 mt-2">{{ __('Business Information') }}</p>
    </div><!-- item -->
    @if(auth()->user()->source == 'sure bills')
    <div class="item d-flex align-items-center justify-content-center flex-column">
      <span class="border rounded-circle fw-bold d-flex align-items-center justify-content-center position-relative bg-light shadow-sm">3</span>
      <p class="d-block text-center mb-0 mt-2">{{ __('Bank Information') }}</p>
    </div><!-- item -->
    @endif
  </div><!-- stepsArea -->
  <div class="blockStep1 bg-white rounded-3 shadow-sm p-3">
    <form id="form" method="POST" action="{{ route('account.information') }}" class="m-0">
      @csrf
      <div class="row">
        <div class="col-12 col-md-6">
          <div class="form-group mb-3">
            <label for="inputEmail1" class="d-block mb-2">{{ __('Full Name')}} <div class="text-danger d-inline-block">*</div></label>
            <input name="name" type="text" class="form-control rounded-3 shadow-none border" id="inputEmail1" placeholder="{{ __('Full Name')}}" value="{{ $user->name }}">
          </div><!-- form-group -->
        </div><!-- col-12 -->
        <div class="col-12 col-md-6">
          <div class="form-group mb-3">
            <label for="inputEmail2" class="d-block mb-2">{{ __('Email')}} <div class="text-danger d-inline-block">*</div></label>
            <input name="email" type="email" class="form-control rounded-3 shadow-none border" id="inputEmail2" placeholder="{{ __('Email')}}" value="{{ $user->email }}" >
          </div><!-- form-group -->
        </div><!-- col-12 -->
        <div class="col-12 col-md-6">
          <div class="form-group mb-3">
            <label for="inputEmail2" class="d-block mb-2">{{ __('Mobile Number')}}</label>
            <input name="mobile" type="tel" class="form-control" id="inputEmail3" placeholder="+966 {{ __('Mobile Number')}}" disabled="" value="+966 {{ $user->mobile }}" dir="ltr">
          </div><!-- form-group -->
        </div><!-- col-12 -->
        <div class="col-12 col-md-6">
          <div class="form-group mb-3">
            <label for="inputEmail2" class="d-block mb-2">{{ __('Gander')}}</label>
            <select name="gender" id="inputEmail4" class="form-control">
              <option value="0" @if ($user->gender == 0)selected="selected"@endif>{{ __('Choose Gender')}}</option>
              <option value="1" @if ($user->gender == 1)selected="selected"@endif>{{ __('Male')}}</option>
              <option value="2" @if ($user->gender == 2)selected="selected"@endif>{{ __('female')}}</option>
            </select>
          </div><!-- form-group -->
        </div><!-- col-12 -->
      </div><!-- row -->
      <div class="btnsArea d-flex align-items-center justify-content-center flex-wrap border-top pt-3">
        <button id="previous" class="d-flex align-items-center justify-content-center btn-primary rounded-3 shadow-none fw-bold border-0" type="button">{{__('Previous')}}</button>
        <button id="next" class="d-flex align-items-center justify-content-center btn-primary rounded-3 shadow-none fw-bold border-0 mx-3" type="submit">{{__('Next')}}</button>
        <!-- <button id="finish" class="d-flex align-items-center justify-content-center btn-primary rounded-3 shadow-none fw-bold border-0" type="submit">{{__('Finish')}}</button> -->
      </div><!-- btnsArea -->
    </form>
  </div><!-- blockStep1 -->
@endsection

@push('footer-scripts')
  {!! JsValidator::formRequest('App\Http\Requests\AccountInformationRequest', '#form') !!}
@endpush
