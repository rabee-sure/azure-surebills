<template>
  <div class="col-12 col-sm-10 col-md-6 col-lg-4 col-xl-4">
    <div class="card">
      <div class="card-body">
        <div class="verify_phone_page">

            <div class="title">{{ __('verify your phone number') }}</div>
            <div class="desc">{{ __('we sent You SMS Message Contain Apin Code') }}</div>
            <div class="form-group">
              <input @keyup.enter="sendPinCode" type="tel" class="form-control" v-model="pin" :placeholder="__('PIN')" maxlength="4">
              <div v-if="error != null" class="invalid-pin">invalid PIN</div>
            </div><!-- form-group -->
            <!-- <b>{{ __('verification code /PIN') }}</b> -->
            <hr>
            <div class="didnt_get_pin">
              {{ __('didn’t Get The PIN') }}
              <div v-if="timerCount > 0">
                {{ __('resending PIN in') }} {{ timerCount}}  {{__('Second') }}
              </div>
              <div v-else>
                <a href="" @click.prevent="resendCode" >{{ __('Resend Code')}}</a>
              </div>
            </div><!-- didnt_get_pin -->
            <div class="d-flex justify-content-center">
              <button type="button" class="btn btn-primary btn-lg w-100"  @click="sendPinCode" :disabled="is_loading">
                  <span v-if="is_loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                  <span v-if="is_loading">{{ __('Loading...') }} </span>
                  <span v-if="!is_loading">{{ __('Verify') }}</span>
              </button>
            </div><!-- d-flex  -->

        </div><!-- verify_phone_page -->
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-12 -->
</template>

<script>
    export default {
        props: ['user'],
        data() {
            return {
                timerCount: 60,
                pin: null,
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
            },
          pin(num) {
              this.pin = num.replace(/([٠١٢٣٤٥٦٧٨٩])|([۰۱۲۳۴۵۶۷۸۹])/g, (m, $1, $2) => m.charCodeAt(0) - ($1 ? 1632 : 1776))
              this.pin = this.pin.replace(/[^\d.\d]/g,'')
          }
        },
        mounted() {
            this.timerCount = this.user.diff_in_sec
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
            },     
            resendCode() {
                this.is_loading = true
                axios.post('/mobile_verify/resendCode')
                .then(response => {
                  this.timerCount = response.data.data.diff_in_sec
                });
                setTimeout(() => {
                    this.is_loading = false
                }, 1000);
            }
        }
    }
</script>

<style lang="scss">
.verify_phone_page {
  .title {
    color: #000;
    font-size: 23px;
    text-transform: capitalize;
    text-align: center;
    font-weight: bold;
    margin: 0 auto 5px;
    [class="body-dark-mode"] & {
      color: #999999;
    } /* Dark Mode */
  } /* title */
  .desc {
    display: block;
    font-size: 13px;
    text-transform: capitalize;
    color: #333;
    text-align: center;
    margin: 0 auto 20px;
    [class="body-dark-mode"] & {
      color: #888888;
    } /* Dark Mode */
  } /* desc */
  .form-group {
    width: 200px;
    margin: 0 auto 15px;
    input[type="tel"] {
      text-align: center;
      font-size: 25px;
      color: #000;
      height: 45px;
      padding: 0;
      letter-spacing: 10px;
      font-weight: bold;
      &::-webkit-input-placeholder {color: #555;font-size: 16px;letter-spacing: 1px;}
      &:-ms-input-placeholder {color: #555;font-size: 16px;letter-spacing: 1px;}
      &::placeholder {color: #555;font-size: 16px;letter-spacing: 1px;}
      [class="body-dark-mode"] & {
        color: #999999;
      } /* Dark Mode */
    } /* input[type="tel"] */
    .invalid-pin {
      color: #ff0000;
      text-transform: capitalize;
      margin: 5px auto 0;
      font-size: 14px;
    } /* invalid-pin */
  } /* form-group */
  .didnt_get_pin {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    text-transform: capitalize;
    color: #333;
    font-size: 13px;
    margin: 0 auto 30px;
    [class="body-dark-mode"] & {
      color: #999999;
    } /* Dark Mode */
    div {
      display: block;
      background: #ddd;
      border-radius: 100px;
      color: #000;
      padding: 3px 15px;
      margin: 0 0 0 5px;
      [class="body-dark-mode"] & {
        background: #313131;
      } /* Dark Mode */
    } /* div */
  } /* didnt_get_pin */
  button.btn {
    color: #fff;
  } /* button */
} /* verify_phone_page */
</style>