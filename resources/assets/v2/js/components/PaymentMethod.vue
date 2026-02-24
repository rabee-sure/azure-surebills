<template>
    <div class="bill_payment">
        <div class="item">
            <input type="radio" id="visa_pay" name="payment_method" value="visa_pay" v-model="payment_method" @change="onChange">
            <label for="visa_pay">
            <p>Credit Card - made</p>
            <div class="icon_mada"></div>
            <div class="checkmark"></div>
            </label>
            <div class="visa_pay_content" v-show="payment_method == 'visa_pay'" v-html="payment_iframe">
                {{ payment_iframe }}
            </div><!-- visa_pay_content -->
        </div><!-- item -->
        <div class="item disable">
            <input type="radio" id="apple_pay" name="payment_method" value="apple_pay" v-model="payment_method" @change="onChange">
            <label for="pay_2">
            <p>Apple Pay</p>
            <div class="icon_apple"></div>
            <div class="checkmark"></div>
            </label>
        </div><!-- item -->
        <div class="item disable">
            <input type="radio" id="stc_pay" name="payment_method" value="stc_pay" v-model="payment_method" @change="onChange">
            <label for="pay_3">
                <p>STC Pay</p>
                <div class="icon_stc"></div>
                <div class="checkmark"></div>
            </label>
        </div><!-- item -->
    </div><!-- bill_payment -->
</template>

<script>
    export default {
        props: ['bill_id'],
        data() {
            return {
                payment_method: null,
                payment_iframe: '',
            };
        },
        methods: {
            onChange(event) {
                var payment_method = event.target.value;
                if(payment_method == "visa_pay"){
                    axios.get('/bills/payment_iframe/'+this.bill_id)
                            .then(response => {
                                this.payment_iframe = response.data;
                            });
                }

            },
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
