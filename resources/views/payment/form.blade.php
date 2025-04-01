<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Payment Information</div>

                <div class="card-body">
                    <form id="payment-form" method="POST" action="{{ route('payment.process') }}">
                        @csrf

                        <!-- Order Information -->
                        <input type="hidden" name="amount" value="{{ $amount }}">
                        <input type="hidden" name="currency" value="{{ $currency }}">
                        <input type="hidden" name="order_id" value="{{ $orderId }}">

                        <div class="form-group mb-3">
                            <label for="card-number">Card Number</label>
                            <input type="text" id="card-number" name="card_number" class="form-control" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="expiration-month">Expiration Month</label>
                                <select id="expiration-month" name="expiration_month" class="form-control" required>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="expiration-year">Expiration Year</label>
                                <select id="expiration-year" name="expiration_year" class="form-control" required>
                                    @for ($i = date('Y'); $i <= date('Y') + 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="card-cvv">CVV</label>
                            <input type="text" id="card-cvv" name="card_cvv" class="form-control" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="cardholder-name">Cardholder Name</label>
                            <input type="text" id="cardholder-name" name="cardholder_name" class="form-control"
                                required>
                        </div>

                        <div class="form-group mb-4">
                            <button type="submit" class="btn btn-primary">Pay Now</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
