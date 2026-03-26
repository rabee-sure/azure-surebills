{{-- Coupon Form Partial --}}
{{-- Reusable form fields for coupon creation/editing --}}

@php
    $coupon = $coupon ?? null;
    $mechanisms = $mechanisms ?? \App\Enums\CouponMechanism::options();
    $isEdit = $coupon !== null;
    $couponName = $coupon ? $coupon->name : '';
    $couponMechanism = $coupon && $coupon->mechanism ? $coupon->mechanism->value() : '';
    $couponDiscountType = $coupon ? $coupon->discount_type : '';
    $couponDiscountValue = $coupon ? $coupon->discount_value : '';
    $couponValidFrom = $coupon && $coupon->valid_from ? $coupon->valid_from->format('Y-m-d\TH:i') : '';
    $couponValidTo = $coupon && $coupon->valid_to ? $coupon->valid_to->format('Y-m-d\TH:i') : '';
    $couponMaxUsage = $coupon ? $coupon->max_usage : '';
    $couponMaxCustomerUsage = $coupon ? $coupon->max_customer_usage : '';
    $couponCodePattern = $coupon ? $coupon->code_pattern : '';
    $couponIsActive = $coupon ? $coupon->is_active : true;
@endphp

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
  </div><!-- col -->

  {{-- Mechanism-specific fields --}}

  {{-- MAX_USAGE: Max Usage --}}
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
  </div><!-- col -->
</div><!-- row -->

{{-- JavaScript for dynamic form behavior --}}
@push('footer-scripts')
  <script src="{{ asset('new/js/daterangepicker/moment.min.js') }}?v={{ config('app.asset_version') }}"></script>
  <script src="{{ asset('new/js/daterangepicker/daterangepicker.min.js') }}?v={{ config('app.asset_version') }}"></script>
  <script src="{{ asset('new/js/select2/select2.full.js') }}?v={{ config('app.asset_version') }}"></script>
  <script>
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
              (mechanism === 'max_usage' || mechanism === 'max_customer_usage')
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
  </script>
@endpush
