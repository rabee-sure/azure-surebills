<template>
<div>
      <DatePicker  v-model="form.date_range" size="large" type="daterange" placeholder="Select date" style="width: 100%" @on-change="handleChangeDate"></DatePicker>
      <p :hidden="validDateRange" style="color:red;">{{__("invalid date range")}}</p>


  <div id="users_statistics">
    <div class="item" v-if="permissions.includes('show merchants')">
      <a  :href="analytics.users.link">
        <span>{{ __('Users') }}</span>
        <p>{{ analytics.users.count}}</p>
      </a>
    </div><!-- item -->
    <div class="item" v-if="permissions.includes('show bills')">
      <a :href="analytics.bills.link">
        <span>{{ __('Bills') }}</span>
        <p>{{ analytics.bills.count }}</p>
      </a>
    </div><!-- item -->

    <div class="item" v-if="permissions.includes('show bills')">
      <a :href="analytics.successful_bills.link">
        <span>{{ __('Successful Bills') }}</span>
        <p>{{ analytics.successful_bills.count}}</p>
      </a>
    </div>

    <div class="item" v-if="permissions.includes('show bills')">
      <a :href="analytics.refunded_bills.link">
        <span>{{ __('Refunded Bills') }}</span>
        <p>{{ analytics.refunded_bills.count}}</p>
      </a>
    </div><!-- item -->

    <div class="item" v-if="permissions.includes('show bills')">
      <a :href="analytics.total_transactions.link">
        <span>{{ __('Total Transactions') }}</span>
        <p>{{ analytics.total_transactions.count }}</p>
      </a>
    </div><!-- item -->

    <div class="item" v-if="permissions.includes('show bills')">
      <a :href="analytics.surebills_fees.link">
        <span>{{ __('SureBills Fees') }}</span>
        <p>{{ analytics.surebills_fees.count }}</p>
      </a>
    </div><!-- item -->
    <div class="item" v-if="permissions.includes('show bills')">
      <a :href="analytics.surebills_fees_vat.link">
        <span>{{ __('SureBills Fees Vat') }}</span>
        <p> {{ analytics.surebills_fees_vat.count }}</p>
      </a>
    </div><!-- item -->
    <div class="item" v-if="permissions.includes('show bills')">
      <a :href="analytics.total_due_merchants.link">
        <span>{{ __('Total due to traders') }}</span>
        <p>{{ analytics.total_due_merchants.count  }}</p>
      </a>
    </div><!-- item -->
    <div class="item" v-if="permissions.includes('show transfers')">
      <a  :href="analytics.total_transfers_merchants.link">
        <span>{{ __('Total transfers to merchants') }}</span>
        <p>{{ analytics.total_transfers_merchants.count }}</p>
      </a>
    </div><!-- item -->
  </div><!-- users_statistics -->
  </div>
</template>

<script>
export default {
    props: [
        'card',
        'resource',
        'resourceId',
        'resourceName',
    ],
    data: () => ({
        validDateRange: true,
        analytics: {},
        permissions: [],
        form: {
            date_range: [new Date().toISOString().slice(0, 10), new Date().toISOString().slice(0, 10)],
        },
    }),
    mounted() {
      this.handleChangeDate({});
    },
    methods: {
        handleChangeDate (date) {
          return Nova.request()
              .get('/api/v1/analytics', {
                params:{
                    from: this.form.date_range[0],
                    to: this.form.date_range[1],
                }
              })
              .then(response => {
                  this.analytics = response.data.data
              })
        },

        reportPermission(){
          return Nova.request().get('/user-permissions/admins')
              .then(response => {
                  this.permissions = response.data;
              })
        },
    }
}
</script>

<style lang="scss" scoped>
  #users_statistics {
    margin: 1rem auto;
    display: grid;
    gap: 1rem;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    .item {
      a {
        border-radius: 0.75rem;
        background: #fff;
        text-align: center;
        min-height: 130px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        box-shadow: 0 1px 15px rgba(0, 0, 0, 0.04), 0 1px 6px rgba(0, 0, 0, 0.04);
        text-decoration: none;
        color: #4099de;
        padding: .5rem;
        span {
          display: block;
          font-size: 15px;
          font-weight: 500;
          text-transform: capitalize;
          margin: 0 auto 10px;
          color: #8f8f8f;
        } /* span */
        p {
          display: block;
          font-size: 17px;
        } /* p */
      } /* a */
    } /* item */
  } /* users_statistics */
</style>
