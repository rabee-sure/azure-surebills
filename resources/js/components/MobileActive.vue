<template>
    <div class="card-body">
        <form>
            <h2>{{ __('verify your phone number') }}</h2>
            <b>{{ __('we sent You SMS Message Contain Apin Code') }} </b>
            <br>
            <div class="form-row">
                <div class="form-group">
                    <input type="text" class="form-control" v-model="pin" :placeholder="__('PIN')">
                </div><!-- form-group -->
                <div v-if="error != null" class="invalid-feedback">
                     invalid PIN
                </div>
            </div><!-- form-row -->
            <b>{{ __('verification code /PIN') }}</b><hr>{{ __('didn’t Get The PIN') }} 
            <div v-if="timerCount > 0">
                {{ __('resending PIN in') }} {{ timerCount}}  {{__('Second') }}
            </div>
            <div v-else>
                <a href="">{{ __('Resend Code')}}</a>
            </div>
            <div class="d-flex justify-content-start mt-3">
                <button type="button" class="btn btn-primary btn-lg"  @click="sendPinCode" :disabled="is_loading">
                    <span v-if="is_loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span v-if="is_loading">{{ __('Loading...') }} </span>
                    <span v-if="!is_loading">{{ __('Verify') }}</span>
                </button>
            </div><!-- d-flex  -->
        </form>
    </div>
</template>

<script>
    export default {
        props: ['user'],
        data() {
            return {
                timerCount: 60,
                pin: '',
                error: null,
                is_loading: false,
            }
        },
        watch: {
            timerCount: {
                handler(value) {
                    if (value > 0) {
                        setTimeout(() => {
                            this.timerCount--;
                        }, 1000);
                    }
                },
                immediate: true // This ensures the watcher is triggered upon creation
            }
        },
        mounted() {
            var now = moment();
            var then = moment(this.user.mobile_sent_at);
            // this.timerCount = moment(now.diff(then), 'seconds').tz('Asia/Riyadh')._i

            console.log(this.timerCount)
        },
        methods: {
            sendPinCode() {
                this.is_loading = true
                axios.post('/mobile_verify',{
                    'pin': this.pin
                })
                .then(response => {
                    if(response.data.success){
                        window.location.href = 'home';
                    }else{
                        this.error = this.__('ffffff');
                    }
                });
                setTimeout(() => {
                    this.is_loading = false
                }, 1000);
            }
        }
    }
</script>
