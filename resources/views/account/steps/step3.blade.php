@extends('account.account_complete')

@section('steps')
  <div class="stepsArea d-flex align-items-end justify-content-between position-relative mb-5">
    <div class="item d-flex align-items-center justify-content-center flex-column done">
      <span class="border rounded-circle fw-bold d-flex align-items-center justify-content-center position-relative bg-light shadow-sm"><i class="fal fa-check"></i></span>
      <p class="d-block text-center mb-0 mt-2">{{ __('My Information') }}</p>
    </div><!-- item -->
    <div class="item d-flex align-items-center justify-content-center flex-column done">
      <span class="border rounded-circle fw-bold d-flex align-items-center justify-content-center position-relative bg-light shadow-sm"><i class="fal fa-check"></i></span>
      <p class="d-block text-center mb-0 mt-2">{{ __('Business Information') }}</p>
    </div><!-- item -->
    <div class="item d-flex align-items-center justify-content-center flex-column active">
      <span class="border rounded-circle fw-bold d-flex align-items-center justify-content-center position-relative bg-light shadow-sm">3</span>
      <p class="d-block text-center mb-0 mt-2">{{ __('Bank Information') }}</p>
    </div><!-- item -->
  </div><!-- stepsArea -->

    <div class="col-12">
        <div class="card">
            <div id="smartWizardValidation">
               
            
                <form id="form" role="form" method="POST" action="{{ route('bank.information', ['redirectHome' => true]) }}" class="card-body">
                    @csrf
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div id="step-2">
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="inputEmail5">{{__('Bank')}}<i class="text-danger">*</i></label>
                                <select name="bank_id" id="inputEmail5" class="form-control">
                                    <option value="" disabled selected>{{__('Select your Bank')}}</option>
                                    @foreach(App\Models\Bank::active()->get() as $bank)
                                        <option value="{{$bank->id}}" @if ($user->bank_id == $bank->id)selected="selected"@endif>
                                            {{$bank->name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputEmail7">{{__('IBAN Number')}}<i class="text-danger">*</i></label>
                                <input value="{{ $user->iban_number }}"  name="iban_number" type="text" class="form-control" id="inputEmail7" placeholder="رقم آيبان مثلاً : SA2720000000000000001212 *">
                                  <small id="emailHelp" class="form-text text-muted">هذا الحساب سيستخدم لتسوية المدفوعات الواصلة لك عبر أجهزة نقاط البيع</small>
                            </div>
                            <div class="form-group col-md-6">
                              <label for="inputEmail9">{{__('Beneficiary Name')}}<i class="text-danger">*</i></label>
                              <input value="{{ $user->beneficiary_name }}" name="beneficiary_name" type="text" class="form-control onlyEng" id="inputEmail9" placeholder="{{__('Beneficiary Name')}}">
                              <small id="emailHelp" class="form-text text-muted">اكتب اسم صاحب الحساب باللغة الانجليزيه كما هو مسجل في البنك</small>
                            </div>
                        </div>

                        <h5 class="mb-2 mt-2">{{ __('Upload the required documents') }}</h5>
                        <p class="">{{ __('Upload a copy of the IBAN card or an account statement showing the IBAN number and the name of the facility') }}</p>

                        @include('components.dropzone',[
                            'documents' => auth()->user()->bank_documents->toArray()
                        ])

                    </div><!-- step-2 -->
                    <div class="btn-toolbar custom-toolbar text-center d-flex justify-content-center card-body pt-4">
                        <a  id="previous" class="btn btn-primary mx-2" href="/account?previous=2">{{__('Previous')}}</a>
                        <button class="btn btn-primary next-btn mx-2" type="submit">{{__('Finish')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('footer-scripts')
  {!! JsValidator::formRequest('App\Http\Requests\BankInformationRequest', '#form') !!}
@endpush