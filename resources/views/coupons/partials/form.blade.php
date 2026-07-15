{{-- Coupon Form Partial --}}
{{-- Reusable form fields for coupon creation/editing --}}

<<<<<<< HEAD

@push('css_styles')
  <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/select2/select2.css') }}?v={{ config('app.asset_version') }}" />
  <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/flatpickr/flatpickr.css') }}?v={{ config('app.asset_version') }}" />
@endpush

=======
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
@php
    $coupon = $coupon ?? null;
    $mechanisms = $mechanisms ?? \App\Enums\CouponMechanism::options();
    $isEdit = $coupon !== null;
    $couponName = $coupon ? $coupon->name : '';
    $couponMechanism = $coupon && $coupon->mechanism ? $coupon->mechanism->value() : '';
    $couponDiscountType = $coupon ? $coupon->discount_type : '';
    $couponDiscountValue = $coupon ? $coupon->discount_value : '';
<<<<<<< HEAD
    $defaultDateTime = \Carbon\Carbon::now()->startOfDay();
    $couponValidFrom = $coupon && $coupon->valid_from ? $coupon->valid_from->format('Y-m-d\TH:i') : $defaultDateTime->format('Y-m-d H:i:s');
    $couponValidTo = $coupon && $coupon->valid_to ? $coupon->valid_to->format('Y-m-d\TH:i') : $defaultDateTime->format('Y-m-d H:i:s');
=======
    $couponValidFrom = $coupon && $coupon->valid_from ? $coupon->valid_from->format('Y-m-d\TH:i') : '';
    $couponValidTo = $coupon && $coupon->valid_to ? $coupon->valid_to->format('Y-m-d\TH:i') : '';
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
    $couponMaxUsage = $coupon ? $coupon->max_usage : '';
    $couponMaxCustomerUsage = $coupon ? $coupon->max_customer_usage : '';
    $couponCodePattern = $coupon ? $coupon->code_pattern : '';
    $couponIsActive = $coupon ? $coupon->is_active : true;
@endphp

<<<<<<< HEAD
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-2 g-4">
  {{-- Coupon Name --}}
  <div class="col">
    <label for="name" class="form-label">{{ __('Coupon Name') }} <div class="text-danger d-inline-block">*</div></label>
    <input
      name="name"
      type="text"
      class="form-control"
      id="name"
      placeholder="{{ __('Enter coupon name') }}"
      value="{{ old('name', $couponName) }}"
      required
    >
    @error('name')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div><!-- col -->
  {{-- Mechanism Type --}}
  <div class="col">
    <label for="mechanism" class="form-label">{{ __('Mechanism Type') }} <div class="text-danger d-inline-block">*</div></label>
    <select
      name="mechanism"
      id="mechanism"
      class="form-select select2"
      data-minimum-results-for-search="Infinity"
      required
      aria-describedby="mechanismlHelp"
    >
      <option value="" disabled {{ old('mechanism', $couponMechanism) === '' ? 'selected' : '' }}>{{ __('Select mechanism type') }}</option>
      @foreach($mechanisms as $mechanism)
        @php
          $enumCase = \App\Enums\CouponMechanism::tryFrom($mechanism['value']);
          $description = $enumCase ? $enumCase->description() : '';
        @endphp
        <option
          value="{{ $mechanism['value'] }}"
          {{ old('mechanism', $couponMechanism) === $mechanism['value'] ? 'selected' : '' }}
          data-description="{{ $description }}"
        >
          {{ $mechanism['label'] }}
        </option>
      @endforeach
    </select>
    <div id="mechanismlHelp" class="form-text" id="mechanism-description"></div>
    @error('mechanism')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div><!-- col -->
  {{-- Discount Type --}}
  <div class="col">
    <label for="discount_type" class="form-label">{{ __('Discount Type') }} <div class="text-danger d-inline-block">*</div></label>
    <select
      name="discount_type"
      id="discount_type"
      class="form-select select2"
      data-minimum-results-for-search="Infinity"
      required
    >
      <option value="fixed" {{ old('discount_type', $couponDiscountType) === 'fixed' ? 'selected' : '' }}>{{ __('Fixed Amount') }}</option>
      <option value="percentage" {{ old('discount_type', $couponDiscountType) === 'percentage' ? 'selected' : '' }}>{{ __('Percentage') }}</option>
    </select>
    @error('discount_type')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div><!-- col -->
  {{-- Discount Value --}}
  <div class="col">
    <label for="discount_value" class="form-label">{{ __('Discount Value') }} <div class="text-danger d-inline-block">*</div></label>
    <input
      name="discount_value"
      type="number"
      step="0.01"
      min="0"
      class="form-control"
      id="discount_value"
      placeholder="{{ __('Enter discount value') }}"
      value="{{ old('discount_value', $couponDiscountValue) }}"
      required
      aria-describedby="discount_hint"
    >
    <div id="discount_hint" class="form-text"></div>
    @error('discount_value')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div><!-- col -->
  {{-- Valid From --}}
  <div class="col">
    <label for="valid_from" class="form-label">{{ __('Valid From') }}</label>
    <input
      type="text"
      class="form-control"
      id="valid_from_display"
      value="@php
        try {
          $date = old('valid_from', $couponValidFrom);
          echo $date ? \Carbon\Carbon::parse($date)->format('d/m/Y h:i A') : \Carbon\Carbon::now()->startOfDay()->format('d/m/Y h:i A');
        } catch (\Exception $e) {
          echo \Carbon\Carbon::now()->startOfDay()->format('d/m/Y h:i A');
        }
      @endphp"
      dir="ltr"
    >
    <input
      name="valid_from"
      type="hidden"
      id="valid_from"
      value="{{ old('valid_from', $couponValidFrom ?? \Carbon\Carbon::now()->startOfDay()->format('Y-m-d H:i:s')) }}"
    >
    @error('valid_from')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div><!-- col -->
  {{-- Valid To --}}
  <div class="col">
    <label for="valid_to" class="form-label">{{ __('Valid To') }}</label>
    <input
      type="text"
      class="form-control"
      id="valid_to_display"
      value="@php
        try {
          $date = old('valid_to', $couponValidTo);
          echo $date ? \Carbon\Carbon::parse($date)->format('d/m/Y h:i A') : \Carbon\Carbon::now()->startOfDay()->format('d/m/Y h:i A');
        } catch (\Exception $e) {
          echo \Carbon\Carbon::now()->startOfDay()->format('d/m/Y h:i A');
        }
      @endphp"
      dir="ltr"
    >
    <input
      name="valid_to"
      type="hidden"
      id="valid_to"
      value="{{ old('valid_to', $couponValidTo ?? \Carbon\Carbon::now()->startOfDay()->format('Y-m-d H:i:s')) }}"
    >
    @error('valid_to')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
=======
<div class="row">
  {{-- Coupon Name --}}
  <div class="col-12 col-md-6">
    <div class="form-group mb-3">
      <label for="name" class="d-block mb-1">{{ __('Coupon Name') }} <span class="requirement text-danger">*</span></label>
      <input
        name="name"
        type="text"
        class="form-control shadow-none bg-white border w-100 rounded-3 text-body"
        id="name"
        placeholder="{{ __('Enter coupon name') }}"
        value="{{ old('name', $couponName) }}"
        required
      >
      @error('name')
        <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
    </div><!-- form-group -->
  </div><!-- col -->
  {{-- Mechanism Type --}}
  <div class="col-12 col-md-6">
    <div class="form-group mb-3">
      <label for="mechanism" class="d-block mb-1">{{ __('Mechanism Type') }} <span class="requirement text-danger">*</span></label>
      <select
        name="mechanism"
        id="mechanism"
        class="form-control rounded-3 shadow-none border select2-single"
        data-minimum-results-for-search="Infinity"
        required
      >
        <option value="" disabled {{ old('mechanism', $couponMechanism) === '' ? 'selected' : '' }}>{{ __('Select mechanism type') }}</option>
        @foreach($mechanisms as $mechanism)
          @php
            $enumCase = \App\Enums\CouponMechanism::tryFrom($mechanism['value']);
            $description = $enumCase ? $enumCase->description() : '';
          @endphp
          <option
            value="{{ $mechanism['value'] }}"
            {{ old('mechanism', $couponMechanism) === $mechanism['value'] ? 'selected' : '' }}
            data-description="{{ $description }}"
          >
            {{ $mechanism['label'] }}
          </option>
        @endforeach
      </select>
      <small class="text-muted d-block mt-1" id="mechanism-description"></small>
      @error('mechanism')
        <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
    </div><!-- form-group -->
  </div><!-- col -->
  {{-- Discount Type --}}
  <div class="col-12 col-md-6">
    <div class="form-group mb-3">
      <label for="discount_type" class="d-block mb-1">{{ __('Discount Type') }} <span class="requirement text-danger">*</span></label>
      <select
        name="discount_type"
        id="discount_type"
        class="form-control rounded-3 shadow-none border select2-single"
        data-minimum-results-for-search="Infinity"
        required
      >
        <option value="fixed" {{ old('discount_type', $couponDiscountType) === 'fixed' ? 'selected' : '' }}>{{ __('Fixed Amount') }}</option>
        <option value="percentage" {{ old('discount_type', $couponDiscountType) === 'percentage' ? 'selected' : '' }}>{{ __('Percentage') }}</option>
      </select>
      @error('discount_type')
        <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
    </div><!-- form-group -->
  </div><!-- col -->
  {{-- Discount Value --}}
  <div class="col-12 col-md-6">
    <div class="form-group mb-3">
      <label for="discount_value" class="d-block mb-1">{{ __('Discount Value') }} <span class="requirement text-danger">*</span></label>
      <input
        name="discount_value"
        type="number"
        step="0.01"
        min="0"
        class="form-control shadow-none bg-white border w-100 rounded-3 text-body"
        id="discount_value"
        placeholder="{{ __('Enter discount value') }}"
        value="{{ old('discount_value', $couponDiscountValue) }}"
        required
      >
      <small class="text-muted d-block mt-1" id="discount_hint"></small>
      @error('discount_value')
        <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
    </div><!-- form-group -->
  </div><!-- col -->
  {{-- Valid From --}}
  <div class="col-12 col-md-6">
    <div class="form-group mb-3">
      <label for="valid_from" class="d-block mb-1">{{ __('Valid From') }}</label>
      <input
        type="text"
        class="form-control rounded-3 shadow-none border expiryDate"
        id="valid_from_display"
        value="@php
          try {
            $date = old('valid_from', $couponValidFrom);
            echo $date ? \Carbon\Carbon::parse($date)->format('d/m/Y h:i A') : '';
          } catch (\Exception $e) {
            echo '';
          }
        @endphp"
        dir="ltr"
      >
      <input
        name="valid_from"
        type="hidden"
        id="valid_from"
        value="{{ old('valid_from', $couponValidFrom) }}"
      >
      @error('valid_from')
        <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
    </div><!-- form-group -->
  </div><!-- col -->
  {{-- Valid To --}}
  <div class="col-12 col-md-6">
    <div class="form-group mb-3">
      <label for="valid_to" class="d-block mb-1">{{ __('Valid To') }}</label>
      <input
        type="text"
        class="form-control rounded-3 shadow-none border expiryDate"
        id="valid_to_display"
        value="@php
          try {
            $date = old('valid_to', $couponValidTo);
            echo $date ? \Carbon\Carbon::parse($date)->format('d/m/Y h:i A') : '';
          } catch (\Exception $e) {
            echo '';
          }
        @endphp"
        dir="ltr"
      >
      <input
        name="valid_to"
        type="hidden"
        id="valid_to"
        value="{{ old('valid_to', $couponValidTo) }}"
      >
      @error('valid_to')
        <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
    </div><!-- form-group -->
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
  </div><!-- col -->

  {{-- Mechanism-specific fields --}}

  {{-- MAX_USAGE: Max Usage --}}
<<<<<<< HEAD
  <div class="col" id="max_usage_field" style="display: none;">
    <label for="max_usage" class="form-label">{{ __('Max Total Usage') }}</label>
    <input
      name="max_usage"
      type="number"
      min="1"
      class="form-control"
      id="max_usage"
      placeholder="{{ __('Enter maximum usage count') }}"
      value="{{ old('max_usage', $couponMaxUsage) }}"
    >
    <div class="form-text">{{ __('Total number of times this coupon can be used') }}</div>
    @error('max_usage')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div><!-- col -->
  {{-- MAX_CUSTOMER_USAGE: Max Usage Per Customer --}}
  <div class="col" id="max_customer_usage_field" style="display: none;">
    <label for="max_customer_usage" class="form-label">{{ __('Max Usage Per Customer') }}</label>
    <input
      name="max_customer_usage"
      type="number"
      min="1"
      class="form-control"
      id="max_customer_usage"
      placeholder="{{ __('Enter max usage per customer') }}"
      value="{{ old('max_customer_usage', $couponMaxCustomerUsage) }}"
    >
    <div class="form-text">{{ __('Maximum number of times each customer can use this coupon') }}</div>
    @error('max_customer_usage')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div><!-- col -->
  {{-- Code Pattern (for MAX_USAGE reusable code or ONE_TIME_USAGE pattern) --}}
  <div class="col" id="code_pattern_field" style="display: none;">
    <label for="code_pattern" class="form-label">{{ __('Code Pattern') }}</label>
    <input
      name="code_pattern"
      type="text"
      class="form-control"
      id="code_pattern"
      placeholder="{{ __('Enter code or pattern') }}"
      value="{{ old('code_pattern', $couponCodePattern) }}"
    >
    <div class="form-text" id="code_pattern_hint">
      {{ __('Enter the code customers will use (e.g., SAVE2024)') }}
    </div>
  </div><!-- col -->
  {{-- Active Toggle --}}
  <div class="col-12">
    <label class="switch switch-success" for="is_active">
      <input type="checkbox" class="switch-input" name="is_active" id="is_active" value="1" {{ old('is_active', $couponIsActive) ? 'checked' : '' }}>
      <span class="switch-toggle-slider">
        <span class="switch-on">
          <i class="icon-base ti ti-check"></i>
        </span>
        <span class="switch-off">
          <i class="icon-base ti ti-x"></i>
        </span>
      </span>
      <span class="switch-label">{{ __('Active') }}</span>
    </label>
    @error('is_active')
      <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
=======
  <div class="col-12 col-md-6" id="max_usage_field" style="display: none;">
    <div class="form-group mb-3">
    <label for="max_usage" class="d-block mb-1">{{ __('Max Total Usage') }}</label>
    <input
      name="max_usage"
        type="number"
        min="1"
        class="form-control rounded-3 shadow-none border"
        id="max_usage"
        placeholder="{{ __('Enter maximum usage count') }}"
        value="{{ old('max_usage', $couponMaxUsage) }}"
      >
      <small class="text-muted d-block mt-1">{{ __('Total number of times this coupon can be used') }}</small>
      @error('max_usage')
        <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
    </div><!-- form-group -->
  </div><!-- col -->
  {{-- MAX_CUSTOMER_USAGE: Max Usage Per Customer --}}
  <div class="col-12 col-md-6" id="max_customer_usage_field" style="display: none;">
    <div class="form-group mb-3">
      <label for="max_customer_usage" class="d-block mb-1">{{ __('Max Usage Per Customer') }}</label>
      <input
        name="max_customer_usage"
        type="number"
        min="1"
        class="form-control rounded-3 shadow-none border"
        id="max_customer_usage"
        placeholder="{{ __('Enter max usage per customer') }}"
        value="{{ old('max_customer_usage', $couponMaxCustomerUsage) }}"
      >
      <small class="text-muted d-block mt-1">{{ __('Maximum number of times each customer can use this coupon') }}</small>
      @error('max_customer_usage')
        <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
    </div><!-- form-group -->
  </div><!-- col -->
  {{-- Code Pattern (for MAX_USAGE reusable code or ONE_TIME_USAGE pattern) --}}
  <div class="col-12 col-md-6" id="code_pattern_field" style="display: none;">
    <div class="form-group mb-3">
      <label for="code_pattern" class="d-block mb-1">{{ __('Code Pattern') }}</label>
      <input
        name="code_pattern"
        type="text"
        class="form-control rounded-3 shadow-none border"
        id="code_pattern"
        placeholder="{{ __('Enter code or pattern') }}"
        value="{{ old('code_pattern', $couponCodePattern) }}"
      >
      <small class="text-muted d-block mt-1" id="code_pattern_hint">
        {{ __('Enter the code customers will use (e.g., SAVE2024)') }}
      </small>
    </div><!-- form-group -->
  </div><!-- col -->
  {{-- Active Toggle --}}
  <div class="col-12">
    <div class="form-group mb-3">
      <label for="is_active" class="checkboxItem position-relative">
        <input name="is_active" class="position-absolute top-0 strat-0 w-100 h-100" id="is_active" type="checkbox" value="1" {{ old('is_active', $couponIsActive) ? 'checked' : '' }}>
        <span class="d-flex align-items-center justify-content-start">
          <i class="d-block rounded-pill position-relative"></i>
          {{ __('Active') }}
        </span>
      </label>
      @error('is_active')
        <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
    </div><!-- form-group -->
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
  </div><!-- col -->
</div><!-- row -->

{{-- JavaScript for dynamic form behavior --}}
@push('footer-scripts')
<<<<<<< HEAD
  <script type="text/javascript" src="{{ asset('assets/v2/vendor/libs/select2/select2.js') }}?v={{ config('app.asset_version') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/v2/vendor/libs/flatpickr/flatpickr.js') }}?v={{ config('app.asset_version') }}"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      // Select2
      $('.select2').select2();
    });

=======
  <script src="{{ asset('new/js/daterangepicker/moment.min.js') }}?v={{ config('app.asset_version') }}"></script>
  <script src="{{ asset('new/js/daterangepicker/daterangepicker.min.js') }}?v={{ config('app.asset_version') }}"></script>
  <script src="{{ asset('new/js/select2/select2.full.js') }}?v={{ config('app.asset_version') }}"></script>
  <script>
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
    document.addEventListener('DOMContentLoaded', function() {
      const mechanismSelect = document.getElementById('mechanism');
      const discountTypeSelect = document.getElementById('discount_type');
      const discountValueInput = document.getElementById('discount_value');
      const discountUnitSpan = document.getElementById('discount_unit');
      const discountHintSpan = document.getElementById('discount_hint');
      const mechanismDescriptionSpan = document.getElementById('mechanism-description');

      // Initialize Select2 for mechanism select
      if (mechanismSelect && typeof jQuery !== 'undefined' && jQuery.fn.select2) {
        jQuery(mechanismSelect).select2({
          minimumResultsForSearch: Infinity
        });

        // Mechanism change handler - use both change and select2:select events
        jQuery(mechanismSelect).on('change select2:select', function() {
          const mechanism = jQuery(this).val();
          const selectedOption = this.options[this.selectedIndex];
          const description = selectedOption ? (selectedOption.dataset.description || '') : '';

          // Show mechanism description
          if (mechanismDescriptionSpan) {
            mechanismDescriptionSpan.textContent = description;
          }

          // Show/hide mechanism-specific fields
          const maxUsageField = document.getElementById('max_usage_field');
          const maxCustomerUsageField = document.getElementById('max_customer_usage_field');
          const codePatternField = document.getElementById('code_pattern_field');

          if (maxUsageField) {
            maxUsageField.style.display = mechanism === 'max_usage' ? 'block' : 'none';
          }
          if (maxCustomerUsageField) {
            maxCustomerUsageField.style.display = mechanism === 'max_customer_usage' ? 'block' : 'none';
          }
          if (codePatternField) {
            codePatternField.style.display =
              (mechanism === 'max_usage' || mechanism === 'max_customer_usage' || mechanism === 'one_time_usage')
              ? 'block' : 'none';
          }

          // Update code pattern label and hint
          const codePatternLabel = document.getElementById('code_pattern_label');
          const codePatternHint = document.getElementById('code_pattern_hint');
          if (mechanism === 'max_usage' || mechanism === 'max_customer_usage') {
            if (codePatternLabel) codePatternLabel.textContent = '{{ __('Reusable Code') }}';
            if (codePatternHint) codePatternHint.textContent = '{{ __('Enter the code customers will use (e.g., SAVE2024)') }}';
          } else if (mechanism === 'one_time_usage') {
            if (codePatternLabel) codePatternLabel.textContent = '{{ __('Code Pattern') }}';
            if (codePatternHint) codePatternHint.textContent = '{{ __('Pattern for generating codes. Use {RANDOM:8}, {ALPHA:4}-{NUMBER:4}, etc.') }}';
          }

          // Set max for discount_value based on type
          updateDiscountField();
        });

        // Trigger on load
        jQuery(mechanismSelect).trigger('change');
      } else if (mechanismSelect) {
        // Fallback if Select2 is not available
        mechanismSelect.addEventListener('change', function() {
          const mechanism = this.value;
          const selectedOption = this.options[this.selectedIndex];
          const description = selectedOption ? (selectedOption.dataset.description || '') : '';

          if (mechanismDescriptionSpan) {
            mechanismDescriptionSpan.textContent = description;
          }

          const maxUsageField = document.getElementById('max_usage_field');
          const maxCustomerUsageField = document.getElementById('max_customer_usage_field');
          const codePatternField = document.getElementById('code_pattern_field');

          if (maxUsageField) {
            maxUsageField.style.display = mechanism === 'max_usage' ? 'block' : 'none';
          }
          if (maxCustomerUsageField) {
            maxCustomerUsageField.style.display = mechanism === 'max_customer_usage' ? 'block' : 'none';
          }
          if (codePatternField) {
            codePatternField.style.display =
              (mechanism === 'max_usage' || mechanism === 'max_customer_usage' || mechanism === 'one_time_usage')
              ? 'block' : 'none';
          }

          const codePatternLabel = document.getElementById('code_pattern_label');
          const codePatternHint = document.getElementById('code_pattern_hint');
          if (mechanism === 'max_usage' || mechanism === 'max_customer_usage') {
            if (codePatternLabel) codePatternLabel.textContent = '{{ __('Reusable Code') }}';
            if (codePatternHint) codePatternHint.textContent = '{{ __('Enter the code customers will use (e.g., SAVE2024)') }}';
          } else if (mechanism === 'one_time_usage') {
            if (codePatternLabel) codePatternLabel.textContent = '{{ __('Code Pattern') }}';
            if (codePatternHint) codePatternHint.textContent = '{{ __('Pattern for generating codes. Use {RANDOM:8}, {ALPHA:4}-{NUMBER:4}, etc.') }}';
          }

          updateDiscountField();
        });

        mechanismSelect.dispatchEvent(new Event('change'));
      }

      // Discount type change handler
      if (discountTypeSelect) {
        discountTypeSelect.addEventListener('change', updateDiscountField);
      }

      function updateDiscountField() {
        if (!discountTypeSelect || !discountValueInput || !discountUnitSpan || !discountHintSpan) return;

        const discountType = discountTypeSelect.value;
        const mechanism = mechanismSelect ? mechanismSelect.value : '';

        if (discountType === 'percentage') {
          discountUnitSpan.textContent = '(%)';
          discountValueInput.setAttribute('max', '100');
          discountHintSpan.textContent = '{{ __('Enter percentage (0-100)') }}';
        } else {
          discountUnitSpan.textContent = '({{ __('SAR') }})';
          discountValueInput.removeAttribute('max');
          discountHintSpan.textContent = '{{ __('Enter fixed amount in SAR') }}';
        }
      }

      // Trigger on load
      updateDiscountField();
    });

<<<<<<< HEAD
    // Flatpickr for Valid From / Valid To (replaces daterangepicker)
    $(function() {
      var validFromDisplay = $('#valid_from_display');
      var validFromHidden = $('#valid_from');
      var validToDisplay = $('#valid_to_display');
      var validToHidden = $('#valid_to');

      var locale = {
        weekdays: {
          shorthand: ['{{ __("Sun") }}','{{ __("Mon") }}','{{ __("Tue") }}','{{ __("Wed") }}','{{ __("Thu") }}','{{ __("Fri") }}','{{ __("Sat") }}'],
          longhand: ['{{ __("Sunday") }}','{{ __("Monday") }}','{{ __("Tuesday") }}','{{ __("Wednesday") }}','{{ __("Thursday") }}','{{ __("Friday") }}','{{ __("Saturday") }}']
        },
        months: {
          shorthand: ['{{ __("Jan") }}','{{ __("Feb") }}','{{ __("Mar") }}','{{ __("Apr") }}','{{ __("May") }}','{{ __("Jun") }}','{{ __("Jul") }}','{{ __("Aug") }}','{{ __("Sep") }}','{{ __("Oct") }}','{{ __("Nov") }}','{{ __("Dec") }}'],
          longhand: ['{{ __("January") }}','{{ __("February") }}','{{ __("March") }}','{{ __("April") }}','{{ __("May") }}','{{ __("June") }}','{{ __("July") }}','{{ __("August") }}','{{ __("September") }}','{{ __("October") }}','{{ __("November") }}','{{ __("December") }}']
        },
        firstDayOfWeek: {{ app()->getLocale() == 'ar' ? 6 : 0 }},
        rangeSeparator: ' {{ __("to") }} ',
        weekAbbreviation: '{{ __("week") }}'
      };

      function parseForFlatpickr(val) {
        if (!val || !String(val).trim()) return null;
        var s = String(val).trim();
        var m = s.match(/^(\d{4})-(\d{2})-(\d{2})(?:[T\s](\d{1,2}):(\d{2})(?::(\d{2}))?)?/);
        if (!m) return null;
        var d = new Date(parseInt(m[1],10), parseInt(m[2],10)-1, parseInt(m[3],10), parseInt(m[4]||0,10), parseInt(m[5]||0,10), parseInt(m[6]||0,10));
        return isNaN(d.getTime()) ? null : d;
      }

      function toLaravelFormat(date) {
        var y = date.getFullYear();
        var m = ('0' + (date.getMonth()+1)).slice(-2);
        var d = ('0' + date.getDate()).slice(-2);
        var h = ('0' + date.getHours()).slice(-2);
        var i = ('0' + date.getMinutes()).slice(-2);
        var s = ('0' + date.getSeconds()).slice(-2);
        return y + '-' + m + '-' + d + ' ' + h + ':' + i + ':' + s;
      }

      var fpFrom = null, fpTo = null;

      if (validFromDisplay.length && $.fn.flatpickr) {
        validFromDisplay.flatpickr({
          enableTime: true,
          dateFormat: 'd/m/Y h:i K',
          time_24hr: false,
          locale: locale,
          defaultDate: parseForFlatpickr(validFromHidden.val()),
          onChange: function(selectedDates) {
            if (selectedDates.length) {
              validFromHidden.val(toLaravelFormat(selectedDates[0]));
              if (fpTo) fpTo.set('minDate', selectedDates[0]);
            }
          }
        });
        fpFrom = validFromDisplay[0]._flatpickr;
      }

      if (validToDisplay.length && $.fn.flatpickr) {
        validToDisplay.flatpickr({
          enableTime: true,
          dateFormat: 'd/m/Y h:i K',
          time_24hr: false,
          locale: locale,
          defaultDate: parseForFlatpickr(validToHidden.val()),
          onChange: function(selectedDates) {
            if (selectedDates.length) {
              validToHidden.val(toLaravelFormat(selectedDates[0]));
              if (fpFrom) fpFrom.set('maxDate', selectedDates[0]);
            }
          }
        });
        fpTo = validToDisplay[0]._flatpickr;
      }

      if (fpFrom && fpTo) {
        if (fpFrom.selectedDates && fpFrom.selectedDates[0]) fpTo.set('minDate', fpFrom.selectedDates[0]);
        if (fpTo.selectedDates && fpTo.selectedDates[0]) fpFrom.set('maxDate', fpTo.selectedDates[0]);
      }
    });

    (function() {
      var btn = document.querySelector('.btn-submit-with-spinner');
      if (btn) {
        var form = btn.closest('form');
        if (form) {
          var btnText = btn.querySelector('.btn-text');
          var btnSpinner = btn.querySelector('.btn-spinner');
          var originalText = btnText ? btnText.textContent.trim() : '{{ __("Save") }}';
          function resetButton() {
            btn.disabled = false;
            if (btnText) btnText.textContent = originalText;
            if (btnSpinner) btnSpinner.classList.add('d-none');
          }
          form.addEventListener('submit', function(e) {
            if (e.defaultPrevented || btn.disabled) return;
            btn.disabled = true;
            if (btnText) btnText.textContent = btn.dataset.loadingText || '{{ __("Saving...") }}';
            if (btnSpinner) btnSpinner.classList.remove('d-none');
            setTimeout(resetButton, 8000);
          });
          if (typeof $ !== 'undefined') $(form).on('invalid-form.validate', resetButton);
        }
      }
    })();
=======


    // Single Daterangepicker
    $(function() {
      // Initialize valid_from
      const validFromDisplay = $('#valid_from_display');
      const validFromHidden = $('#valid_from');

      if (validFromDisplay.length) {
        validFromDisplay.daterangepicker({
          "singleDatePicker": true,
          "timePicker": true,
          "autoApply": true,
          "maxSpan": {
            "days": 7
          },
          locale: {
            format: 'DD/MM/YYYY HH:mm A',
            daysOfWeek: [
              '{{__('Sun')}}',
              '{{__('Mon')}}',
              '{{__('Tue')}}',
              '{{__('Wed')}}',
              '{{__('Thur')}}',
              '{{__('Fri')}}',
              '{{__('Sat')}}'
            ],
            monthNames: [
              '{{__('January')}}',
              '{{__('February')}}',
              '{{__('March')}}',
              '{{__('April')}}',
              '{{__('May')}}',
              '{{__('June')}}',
              '{{__('July')}}',
              '{{__('August')}}',
              '{{__('September')}}',
              '{{__('October')}}',
              '{{__('November')}}',
              '{{__('December')}}'
            ],
            fromLabel: '{{__('from')}}',
            toLabel: '{{__('to')}}',
            applyLabel: '{{__('apply')}}',
            cancelLabel:'{{__('cancel')}}',
            customRangeLabel: '{{__('custom Range')}}',
            weekLabel: '{{__('week')}}',
          },
        }, function(start, end, label) {
          // Convert to Laravel date format (Y-m-d H:i:s)
          const formattedDate = start.format('YYYY-MM-DD HH:mm:ss');
          validFromHidden.val(formattedDate);
        });

        // Initialize with existing value if present
        if (validFromHidden.val()) {
          const existingDate = moment(validFromHidden.val(), 'YYYY-MM-DD HH:mm:ss');
          if (existingDate.isValid()) {
            validFromDisplay.data('daterangepicker').setStartDate(existingDate);
            validFromDisplay.data('daterangepicker').setEndDate(existingDate);
          }
        }
      }

      // Initialize valid_to
      const validToDisplay = $('#valid_to_display');
      const validToHidden = $('#valid_to');

      if (validToDisplay.length) {
        validToDisplay.daterangepicker({
          "singleDatePicker": true,
          "timePicker": true,
          "autoApply": true,
          "maxSpan": {
            "days": 7
          },
          locale: {
            format: 'DD/MM/YYYY HH:mm A',
            daysOfWeek: [
              '{{__('Sun')}}',
              '{{__('Mon')}}',
              '{{__('Tue')}}',
              '{{__('Wed')}}',
              '{{__('Thur')}}',
              '{{__('Fri')}}',
              '{{__('Sat')}}'
            ],
            monthNames: [
              '{{__('January')}}',
              '{{__('February')}}',
              '{{__('March')}}',
              '{{__('April')}}',
              '{{__('May')}}',
              '{{__('June')}}',
              '{{__('July')}}',
              '{{__('August')}}',
              '{{__('September')}}',
              '{{__('October')}}',
              '{{__('November')}}',
              '{{__('December')}}'
            ],
            fromLabel: '{{__('from')}}',
            toLabel: '{{__('to')}}',
            applyLabel: '{{__('apply')}}',
            cancelLabel:'{{__('cancel')}}',
            customRangeLabel: '{{__('custom Range')}}',
            weekLabel: '{{__('week')}}',
          },
        }, function(start, end, label) {
          // Convert to Laravel date format (Y-m-d H:i:s)
          const formattedDate = start.format('YYYY-MM-DD HH:mm:ss');
          validToHidden.val(formattedDate);
        });

        // Initialize with existing value if present
        if (validToHidden.val()) {
          const existingDate = moment(validToHidden.val(), 'YYYY-MM-DD HH:mm:ss');
          if (existingDate.isValid()) {
            validToDisplay.data('daterangepicker').setStartDate(existingDate);
            validToDisplay.data('daterangepicker').setEndDate(existingDate);
          }
        }
      }
    });
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
  </script>
@endpush
