<div class="card">
  <div class="card-header p-3 d-flex align-items-center justify-content-between">
      <span class="d-block flex-grow-1 fw-bold">{{ trans('Pay With Your Credit Card') }}</span>
      <div class="payments-icons flex-shrink-0 d-flex align-items-center justify-content-end gap-2">
          <img src="{{ asset('images/payment_page/payment-1.webp') }}" alt="visa">
          <img src="{{ asset('images/payment_page/payment-2.webp') }}" alt="master card">
          <img src="{{ asset('images/payment_page/payment-4.webp') }}" alt="mada">
      </div>
  </div>

  <div class="card-body p-3">
      <div id="error-message" class="alert alert-danger d-none" dir="auto"></div>

      <form id="payment-form" method="POST">
          <div class="form-group">
              <label for="card_number" class="d-block mb-1 text-body text-capitalize">{{ trans('Card Number') }}</label>
              
              @if($microformSessionToken)
                  <div id="card-number-container"></div>
              @else
                  <input type="text" id="card_number" name="card_number" class="w-100 px-2 rounded-2 border" dir="ltr" placeholder="xxxx xxxx xxxx xxxx" maxlength="19" autocomplete="off">
              @endif
          </div>

          <div class="bottom-columns">
              <div class="col-item">
                  <div class="form-group m-0">
                      <label for="exp_month" class="d-block mb-1 text-body text-capitalize">{{ trans('Expiration Month') }}</label>
                      <select id="exp_month" name="card_expiration_month" class="w-100 px-2 rounded-2 border">
                          <option value="" disabled selected>MM</option>
                          @for ($i = 1; $i <= 12; $i++)
                              @if($microformSessionToken)
                                <option>{{ sprintf('%02d', $i) }}</option>
                              @else 
                                <option value="{{ intval(sprintf('%02d', $i)) }}">{{ sprintf('%02d', $i) }}</option>
                              @endif
                          @endfor
                      </select>
                  </div>
              </div>

              <div class="col-item">
                  <div class="form-group m-0">
                      <label for="exp_year" class="d-block mb-1 text-body text-capitalize">{{ trans('Expiration Year') }}</label>
                      <select id="exp_year" name="card_expiration_year" class="w-100 px-2 rounded-2 border">
                          <option value="" disabled selected>YYYY</option>
                          @foreach ($years as $year)
                            @if($microformSessionToken)
                              <option>{{ $year }}</option>
                            @else
                              <option value="{{ intval($year) }}">{{ $year }}</option>
                            @endif
                          @endforeach
                      </select>
                  </div>
              </div>

              <div class="col-item">
                  <div class="form-group m-0">
                      <label for="cvv" class="d-block mb-1 text-body text-capitalize">{{ trans('security code') }}</label>

                      @if($microformSessionToken)
                          <div id="cvv-container"></div>
                      @else
                          <input type="text" id="cvv" name="card_cvv" class="w-100 px-2 rounded-2 border" dir="ltr" placeholder="CVV" maxlength="4" autocomplete="off">
                      @endif
                  </div>
              </div>
          </div>

          <button id="pay-button" type="submit" class="d-flex align-items-center justify-content-center rounded-2 text-white fw-bold border-0 w-100 p-0 mt-3">
            {{ trans('Pay') }}
          </button>
      </form>
  </div>
</div>

@if($microformSessionToken)
  @push('footer-scripts')
    @if(config('cybersource.environment') != 'production')
        <script src="https://testflex.cybersource.com/microform/bundle/v2/flex-microform.min.js"></script>
    @else
        <script src="https://flex.cybersource.com/microform/bundle/v2/flex-microform.min.js"></script>
    @endif
  @endpush

  <style>
    #card-number-container, #cvv-container {
        width: 100% !important;
        padding: 0.5rem !important;
        border-radius: 0.25rem !important;
        border: 1px solid #dee2e6 !important;
        height: 40px !important;
        direction: rtl !important;
    }
  </style>
@endif