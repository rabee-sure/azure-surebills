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

{{-- Coupon Name --}}
<div class="form-group mb-3">
    <label for="name" class="d-block mb-1">{{ __('Coupon Name') }} <span class="requirement text-danger">*</span></label>
    <input name="name" type="text" 
           class="form-control shadow-none bg-white border w-100 rounded-3 text-body" 
           id="name" 
           placeholder="{{ __('Enter coupon name') }}" 
           value="{{ old('name', $couponName) }}" 
           required>
    @error('name')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

{{-- Mechanism Type --}}
<div class="form-group mb-3">
    <label for="mechanism" class="d-block mb-1">{{ __('Mechanism Type') }} <span class="requirement text-danger">*</span></label>
    <select name="mechanism" 
            id="mechanism" 
            class="form-control shadow-none bg-white border w-100 rounded-3 text-body"
            required>
        <option value="">{{ __('Select mechanism type') }}</option>
        @foreach($mechanisms as $mechanism)
            @php
                $enumCase = \App\Enums\CouponMechanism::tryFrom($mechanism['value']);
                $description = $enumCase ? $enumCase->description() : '';
            @endphp
            <option value="{{ $mechanism['value'] }}" 
                    {{ old('mechanism', $couponMechanism) === $mechanism['value'] ? 'selected' : '' }}
                    data-description="{{ $description }}">
                {{ $mechanism['label'] }}
            </option>
        @endforeach
    </select>
    <small class="text-muted d-block mt-1" id="mechanism-description"></small>
    @error('mechanism')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

{{-- Discount Type --}}
<div class="form-group mb-3">
    <label for="discount_type" class="d-block mb-1">{{ __('Discount Type') }} <span class="requirement text-danger">*</span></label>
    <select name="discount_type" 
            id="discount_type" 
            class="form-control shadow-none bg-white border w-100 rounded-3 text-body"
            required>
        <option value="fixed" {{ old('discount_type', $couponDiscountType) === 'fixed' ? 'selected' : '' }}>{{ __('Fixed Amount') }}</option>
        <option value="percentage" {{ old('discount_type', $couponDiscountType) === 'percentage' ? 'selected' : '' }}>{{ __('Percentage') }}</option>
    </select>
    @error('discount_type')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

{{-- Discount Value --}}
<div class="form-group mb-3">
    <label for="discount_value" class="d-block mb-1">
        {{ __('Discount Value') }} <span class="requirement text-danger">*</span>
        <span id="discount_unit" class="text-muted">({{ __('SAR') }})</span>
    </label>
    <input name="discount_value" 
           type="number" 
           step="0.01"
           min="0"
           class="form-control shadow-none bg-white border w-100 rounded-3 text-body" 
           id="discount_value" 
           placeholder="{{ __('Enter discount value') }}" 
           value="{{ old('discount_value', $couponDiscountValue) }}" 
           required>
    <small class="text-muted d-block mt-1" id="discount_hint"></small>
    @error('discount_value')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

{{-- Valid From --}}
<div class="form-group mb-3">
    <label for="valid_from" class="d-block mb-1">{{ __('Valid From') }}</label>
    <input name="valid_from" 
           type="datetime-local" 
           class="form-control shadow-none bg-white border w-100 rounded-3 text-body" 
           id="valid_from" 
           value="{{ old('valid_from', $couponValidFrom) }}">
    @error('valid_from')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

{{-- Valid To --}}
<div class="form-group mb-3">
    <label for="valid_to" class="d-block mb-1">{{ __('Valid To') }}</label>
    <input name="valid_to" 
           type="datetime-local" 
           class="form-control shadow-none bg-white border w-100 rounded-3 text-body" 
           id="valid_to" 
           value="{{ old('valid_to', $couponValidTo) }}">
    @error('valid_to')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

{{-- Mechanism-specific fields --}}

{{-- MAX_USAGE: Max Usage --}}
<div class="form-group mb-3" id="max_usage_field" style="display: none;">
    <label for="max_usage" class="d-block mb-1">{{ __('Max Total Usage') }}</label>
    <input name="max_usage" 
           type="number" 
           min="1"
           class="form-control shadow-none bg-white border w-100 rounded-3 text-body" 
           id="max_usage" 
           placeholder="{{ __('Enter maximum usage count') }}" 
           value="{{ old('max_usage', $couponMaxUsage) }}">
    <small class="text-muted d-block mt-1">{{ __('Total number of times this coupon can be used') }}</small>
    @error('max_usage')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

{{-- MAX_CUSTOMER_USAGE: Max Usage Per Customer --}}
<div class="form-group mb-3" id="max_customer_usage_field" style="display: none;">
    <label for="max_customer_usage" class="d-block mb-1">{{ __('Max Usage Per Customer') }}</label>
    <input name="max_customer_usage" 
           type="number" 
           min="1"
           class="form-control shadow-none bg-white border w-100 rounded-3 text-body" 
           id="max_customer_usage" 
           placeholder="{{ __('Enter max usage per customer') }}" 
           value="{{ old('max_customer_usage', $couponMaxCustomerUsage) }}">
    <small class="text-muted d-block mt-1">{{ __('Maximum number of times each customer can use this coupon') }}</small>
    @error('max_customer_usage')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

{{-- Code Pattern (for MAX_USAGE reusable code or ONE_TIME_USAGE pattern) --}}
<div class="form-group mb-3" id="code_pattern_field" style="display: none;">
    <label for="code_pattern" class="d-block mb-1">
        <span id="code_pattern_label">{{ __('Code Pattern') }}</span>
    </label>
    <input name="code_pattern" 
           type="text" 
           class="form-control shadow-none bg-white border w-100 rounded-3 text-body" 
           id="code_pattern" 
           placeholder="{{ __('Enter code or pattern') }}" 
           value="{{ old('code_pattern', $couponCodePattern) }}">
    <small class="text-muted d-block mt-1" id="code_pattern_hint">
        {{ __('For MAX_USAGE: Enter the reusable code. For ONE_TIME_USAGE: Enter pattern like {RANDOM:8}, {ALPHA:4}-{NUMBER:4}') }}
    </small>
    @error('code_pattern')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

{{-- Active Toggle --}}
<div class="form-group mb-3">
    <div class="form-check form-switch">
        <input name="is_active" 
               type="checkbox" 
               class="form-check-input" 
               id="is_active" 
               value="1"
               {{ old('is_active', $couponIsActive) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">
            {{ __('Active') }}
        </label>
    </div>
    @error('is_active')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

{{-- JavaScript for dynamic form behavior --}}
@push('footer-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const mechanismSelect = document.getElementById('mechanism');
    const discountTypeSelect = document.getElementById('discount_type');
    const discountValueInput = document.getElementById('discount_value');
    const discountUnitSpan = document.getElementById('discount_unit');
    const discountHintSpan = document.getElementById('discount_hint');
    const mechanismDescriptionSpan = document.getElementById('mechanism-description');
    
    // Mechanism change handler
    if (mechanismSelect) {
        mechanismSelect.addEventListener('change', function() {
            const mechanism = this.value;
            const selectedOption = this.options[this.selectedIndex];
            const description = selectedOption.dataset.description || '';
            
            // Show mechanism description
            mechanismDescriptionSpan.textContent = description;
            
            // Show/hide mechanism-specific fields
            document.getElementById('max_usage_field').style.display = 
                mechanism === 'max_usage' ? 'block' : 'none';
            document.getElementById('max_customer_usage_field').style.display = 
                mechanism === 'max_customer_usage' ? 'block' : 'none';
            document.getElementById('code_pattern_field').style.display = 
                (mechanism === 'max_usage' || mechanism === 'max_customer_usage' || mechanism === 'one_time_usage') ? 'block' : 'none';
            
            // Update code pattern label and hint
            const codePatternLabel = document.getElementById('code_pattern_label');
            const codePatternHint = document.getElementById('code_pattern_hint');
            if (mechanism === 'max_usage' || mechanism === 'max_customer_usage') {
                codePatternLabel.textContent = '{{ __('Reusable Code') }}';
                codePatternHint.textContent = '{{ __('Enter the code customers will use (e.g., SAVE2024)') }}';
            } else if (mechanism === 'one_time_usage') {
                codePatternLabel.textContent = '{{ __('Code Pattern') }}';
                codePatternHint.textContent = '{{ __('Pattern for generating codes. Use {RANDOM:8}, {ALPHA:4}-{NUMBER:4}, etc.') }}';
            }
            
            // Set max for discount_value based on type
            updateDiscountField();
        });
        
        // Trigger on load
        mechanismSelect.dispatchEvent(new Event('change'));
    }
    
    // Discount type change handler
    if (discountTypeSelect) {
        discountTypeSelect.addEventListener('change', updateDiscountField);
    }
    
    function updateDiscountField() {
        const discountType = discountTypeSelect.value;
        const mechanism = mechanismSelect.value;
        
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
</script>
@endpush
