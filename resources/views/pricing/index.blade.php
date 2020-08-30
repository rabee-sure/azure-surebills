@extends('layouts.app')

@section('title', __('pricing'))

@section('content')

<div class="row">
  <div class="col-12">
    <h1>{{ __('Pricing') }}</h1>
    <div class="separator mb-5"></div>
  </div><!-- col-12 -->
  <div class="col-12">
    <div class="row justify-content-center icon-cards-row mx-n3">
      <div class="col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8">
        <div class="pricing_item">
          <div class="visa_master_icons">
            <span class="visa"></span>
            <span class="master"></span>
            <span class="mada_icon"></span>
            <span class="apple_pay"></span>
          </div><!-- visa_master_icons -->
          <ul>
            <li>{{ __('2.95% + 1 SAR on each transaction') }}</li>
            <li>{{ __('No incorporation fees') }}</li>
            <li>{{ __('No monthly fees') }}</li>
            <li>{{ __('Transfer every 7 days after deducting the transfer fee') }}</li>
          </ul>
        </div><!-- pricing_item -->
        <div class="pricing_item choose_payment">
          <div class="choose_radio">
            <div class="custom-control custom-radio">
              <input type="radio" id="customRadio1" value="account" name="credit_cards_pay_fees" class="custom-control-input">
              <label class="custom-control-label" for="customRadio1">{{ __('The merchant bears the payment fee') }}</label>
            </div>
            <div class="custom-control custom-radio">
              <input type="radio" id="customRadio2"  value="customer" name="credit_cards_pay_fees" class="custom-control-input">
              <label class="custom-control-label" for="customRadio2">{{ __('The customer bears the payment fee') }}</label>
            </div>
          </div>
          <!-- <div class="choose_radio">
            <div class="custom-control custom-radio">
              <input type="radio" id="customRadio1" value="account" name="credit_cards_pay_fees" class="custom-control-input"  v-model="pricing.credit_cards_pay_fees" v-on:change="update">
              <label class="custom-control-label" for="customRadio1">{{ __('I will pay fees') }}</label>
            </div>
            <div class="custom-control custom-radio">
              <input type="radio" id="customRadio2"  value="customer" name="credit_cards_pay_fees" class="custom-control-input"  v-model="pricing.credit_cards_pay_fees" v-on:change="update">
              <label class="custom-control-label" for="customRadio2">{{ __('My customer will pay fees') }}</label>
            </div>
          </div> -->
        </div><!-- pricing_item -->

      </div><!-- col-12 -->

    </div><!-- row -->
  </div><!-- col-12 -->
</div><!-- row -->

@endsection
