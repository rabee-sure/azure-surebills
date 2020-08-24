@extends('account.account_complete')

@section('steps')
    <div class="col-12">
        <div class="card">
            <div id="smartWizardValidation">
                <ul class="card-header">
                    <li><a href="#step-0">1<br /><small>{{ __('My Information') }}</small></a></li>
                    <li  class="nav-item active"><a href="#step-1">2<br /><small>{{ __('Business Information') }}</small></a></li>
                    <li class="nav-item active"><a href="#step-2">3<br /><small>{{ __('Bank Information') }}</small></a></li>
                </ul>
                <form id="form" method="POST" action="{{ route('bank.information', ['redirectHome' => true]) }}" class="card-body">
                    @csrf
                    <div id="step-2">
                    <div class="form-row">
                    <div class="form-group col-12">
                    <label for="inputEmail5">{{__('Bank')}}</label>
                    <select name="bank" id="inputEmail5" class="form-control">
                    <option value="" disabled selected>{{__('Select your Bank')}}</option>

                    @foreach(getBanks() as $bank)
                    <option value="{{$bank['id']}}" @if ($user->bank == $bank['id'])selected="selected"@endif>
                    @if(app()->getLocale() == 'ar')
                    {{$bank['ar']}}
                    @else
                    {{$bank['en']}}
                    @endif
                    </option>
                    @endforeach
                    </select>
                    </div>
                    </div>
                    <div class="form-row">
                    <div class="form-group col-md-6">
                    <label for="inputEmail7">{{__('IBAN Number')}}</label>
                    <input value="{{ $user->iban_number }}"  name="iban_number" type="text" class="form-control" id="inputEmail7" placeholder="{{__('IBAN Number')}}">
                    </div>
                    <div class="form-group col-md-6">
                    <label for="inputEmail9">{{__('Beneficiary Name')}}</label>
                    <input value="{{ $user->beneficiary_name }}" name="beneficiary_name" type="text" class="form-control" id="inputEmail9" placeholder="{{__('Beneficiary Name')}}">
                    </div>
                    </div>
                    </div><!-- step-2 -->
                    <div class="btn-toolbar custom-toolbar text-center card-body pt-0">
                    <a class="btn btn-primary mx-2" href="/account?previous=2">{{__('Previous')}}</a>
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
