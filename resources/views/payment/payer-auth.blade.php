<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Card Authentication</div>

                <div class="card-body">
                    <p>Your card requires additional authentication. Please complete the verification below:</p>

                    <div id="cardinal-container">
                        <!-- This div will be populated with the Cardinal iframe -->
                    </div>

                    <form id="auth-form" method="POST" action="{{ route('payment.complete') }}">
                        @csrf
                        <input type="hidden" name="transaction_id" value="{{ $transactionId }}">
                        <input type="hidden" name="payment_token" value="{{ $paymentToken }}">
                        <input type="hidden" name="order_id" value="{{ $orderId }}">
                        <!-- Other hidden fields will be added via JavaScript -->
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
