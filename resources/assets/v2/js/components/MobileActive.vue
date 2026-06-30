<template>
  <div>

    <h4 class="mb-1 text-capitalize">{{ __('verify your phone number') }}</h4>
  <p class="mb-6 text-capitalize">{{ __('we sent You SMS Message Contain Apin Code') }}</p>
      <h3 class="d-block text-center text-secondary mb-3 text-capitalize">{{ user.mobile }}</h3>
      <div class="form-group mb-3">
        <div class="otp-fields d-flex justify-content-center gap-2 mb-1" dir="ltr">
          <input
            v-for="(digit, index) in digits"
            :key="index"
            :ref="'otp' + index"
            type="tel"
            class="form-control rounded-3 shadow-none text-body text-center p-0 otp-box"
            maxlength="1"
            v-model="digits[index]"
            @keyup="onDigitKeyup($event, index)"
            @keydown="onDigitKeydown($event, index)"
            @paste.prevent="onPaste($event)"
          >
        </div>
        <div v-if="error != null" class="invalid-pin text-center text-danger mt-1 d-block fs-6 text-capitalize">{{__('invalid PIN') }}</div>
      </div><!-- form-group -->


      <div class="mb-6 text-capitalize">
        <button type="button" class="btn btn-primary d-grid w-100 waves-effect waves-light"  @click="sendPinCode" :disabled="is_loading">
          <i v-if="is_loading" class="fad fa-spinner fa-spin"></i>
          <span v-if="is_loading">{{ __('Loading') }} ...</span>
          <span v-if="!is_loading">{{ __('Verify') }}</span>
        </button>
      </div>

      <p class="text-center text-capitalize">
        <span>{{ __('didn’t Get The PIN') }}</span>
        <span v-if="timerCount > 0">
          {{ __('resending PIN in') }} {{ timerCount}}  {{__('Second') }}
        </span>
        <span v-else>
          <a href="" @click.prevent="resendCode" >{{ __('Resend Code')}}</a>
        </span>
      </p>

  </div><!-- col-12 -->
</template>

<script>
    export default {
        props: ['user'],
        data() {
            return {
                timerCount: 60,
                digits: ['', '', '', ''],
                error: null,
                is_loading: false,
            }
        },
        computed: {
            pin() {
                return this.digits.join('');
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
        },
        mounted() {
            this.timerCount = this.user.diff_in_sec
            console.log(this.timerCount)
        },
        methods: {
            sanitizeDigit(val) {
                // convert Arabic-Indic / Extended Arabic-Indic numerals to ASCII
                return val
                    .replace(/[٠١٢٣٤٥٦٧٨٩]/g, d => d.charCodeAt(0) - 1632)
                    .replace(/[۰۱۲۳۴۵۶۷۸۹]/g, d => d.charCodeAt(0) - 1776)
                    .replace(/[^\d]/g, '')
                    .slice(0, 1);
            },
            onDigitKeyup(event, index) {
                const sanitized = this.sanitizeDigit(event.target.value);
                this.$set(this.digits, index, sanitized);
                if (sanitized && index < 3) {
                    this.$refs['otp' + (index + 1)][0].focus();
                }
                if (index === 3 && this.pin.length === 4) {
                    this.sendPinCode();
                }
            },
            onDigitKeydown(event, index) {
                if (event.key === 'Backspace' && !this.digits[index] && index > 0) {
                    this.$refs['otp' + (index - 1)][0].focus();
                }
            },
            onPaste(event) {
                const text = (event.clipboardData || window.clipboardData).getData('text');
                const sanitized = text
                    .replace(/[٠١٢٣٤٥٦٧٨٩]/g, d => d.charCodeAt(0) - 1632)
                    .replace(/[۰۱۲۳۴۵۶۷۸۹]/g, d => d.charCodeAt(0) - 1776)
                    .replace(/[^\d]/g, '')
                    .slice(0, 4);
                sanitized.split('').forEach((ch, i) => {
                    this.$set(this.digits, i, ch);
                });
                const nextEmpty = Math.min(sanitized.length, 3);
                this.$nextTick(() => this.$refs['otp' + nextEmpty][0].focus());
            },
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
.otp-fields {
  .otp-box {
    width: 56px;
    height: 56px;
    font-size: 22px;
    font-weight: bold;
    text-align: center;
    letter-spacing: 0;
    padding: 0;
    &:focus {
      background-color: var(--hoverBg) !important;
      border-color: var(--mainColor) !important;
    }
  }
}

.verifyPhonePage {
  padding: 15px;
  h1 {
    font-size: 25px;
  } /* h1 */
  h2 {
    font-size: 16px;
  } /* h2 */
  h3 {
    direction: ltr;
    font-size: 17px;
  } /* h3 */
  .form-group {
    input {
      height: 50px;
      outline: none;
      font-size: 20px;
      letter-spacing: 10px;
      font-weight: bold;
      &::-webkit-input-placeholder {
        font-size: 16px;
        letter-spacing: 0;
        font-weight: normal;
      }
      &:-ms-input-placeholder {
        font-size: 16px;
        letter-spacing: 0;
        font-weight: normal;
      }
      &::placeholder {
        font-size: 16px;
        letter-spacing: 0;
        font-weight: normal;
      }
      &:focus {
        background-color: var(--hoverBg) !important;
        border-color: var(--mainColor) !important;
      } /* focus */
      &.is-valid {
        background-position: 10px  center;
        border-color: #198754 !important;
        [dir="ltr"] & {
          background-position: right 10px  center;
        } /* ltr */
      } /* is-valid */
      &.is-invalid {
        background-position: 10px  center;
        border-color: #dc3545 !important;
        [dir="ltr"] & {
          background-position: right 10px  center;
        } /* ltr */
      } /* is-invalid */
    } /* input */
  } /* form-group */
  a {
    color: var(--mainColor);
    &:hover {
      color: var(--mainColorHover);
    } /* hover */
  } /* a */
  button {
    height: 45px;
    outline: none;
    i {
      margin: 0 0 0 7px;
      [dir="ltr"] & {
        margin: 0 7px 0 0;
      } /* ltr */
    } /* i */
    &[disabled="disabled"] {
      opacity: 0.6;
      cursor: not-allowed;
      pointer-events: none;
    } /* disabled="disabled" */
  } /* button */
} /* verifyPhonePage */


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
    direction: ltr;
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
