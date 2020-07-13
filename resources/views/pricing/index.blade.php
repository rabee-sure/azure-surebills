@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

<div class="row">
  <div class="col-12">
    <h1>{{ __('Pricing') }}</h1>
    <div class="separator mb-5"></div>
  </div>
  <div class="col-12">
    <div class="row  justify-content-center icon-cards-row mx-n3">
      <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-4">
        <div class="pricing_item">
          <div class="visa_master_icons">
            <span class="visa"></span>
            <span class="master"></span>
          </div><!-- visa_master_icons -->
          <b> {{ __('Credit Cards') }}</b>
          <p>2.9 % per transaction + 1 Riyal</p>
          <div class="choose_radio">
            <div class="custom-control custom-radio">
              <input type="radio" id="customRadio1" name="customRadio" class="custom-control-input">
              <label class="custom-control-label" for="customRadio1">{{ __('I will pay fees') }}</label>
            </div>
            <div class="custom-control custom-radio">
              <input type="radio" id="customRadio2" name="customRadio" class="custom-control-input">
              <label class="custom-control-label" for="customRadio2">{{ __('My customer will pay fees') }}</label>
            </div>
          </div><!-- choose_radio -->
        </div><!-- pricing_item -->
      </div>
      <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-4">
        <div class="pricing_item">
          <div class="mada_icon"></div>
          <b>{{ __('Mada') }}</b>
          <p>1.9 % per transaction + 1 Riyal</p>
          <div class="choose_radio">
            <div class="custom-control custom-radio">
              <input type="radio" id="customRadio3" name="customRadio" class="custom-control-input">
              <label class="custom-control-label" for="customRadio3">{{ __('I will pay fees') }}</label>
            </div>
            <div class="custom-control custom-radio">
              <input type="radio" id="customRadio4" name="customRadio" class="custom-control-input">
              <label class="custom-control-label" for="customRadio4">{{ __('My customer will pay fees') }}</label>
            </div>
          </div><!-- choose_radio -->
        </div><!-- pricing_item -->
      </div>
    </div>
  </div>
</div>

@endsection