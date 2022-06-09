<template>
  <div id="users_statistics" v-if="permissions.includes('show statements') || permissions.includes('create settlement') || permissions.includes('show bills')">
    <div class="item" v-if="permissions.includes('show statements') || permissions.includes('create settlement')">
      <a :href="'/nova/resources/statements?statements_page=1&statements_filter='+user.stats.filter_user_id">
        <span>{{ __('Balance') }}</span>
        <p v-if="user">{{ user.balance }}</p>
        <p v-if="user && permissions.includes('create settlement')"><a :href="'/nova/settlements/'+user.id+'/create'" target="_blank" style="min-height: 0px;"> {{ __('Create Transfer') }}</a></p>
      </a>
    </div><!-- item -->
    <div class="item" v-if="permissions.includes('show statements')">
      <a :href="'/nova/resources/statements?statements_page=1&statements_filter='+user.stats.filter_user_id">
        <span>{{ __('Total Paid') }}</span>
        <p v-if="user.stats">{{ user.stats.total_paid }}</p>
      </a>
    </div><!-- item -->
    <div class="item" v-if="permissions.includes('show bills')">
      <a  :href="'/nova/resources/bills?bills_page=1&bills_filter='+user.stats.filter_user_id">
        <span>{{ __('Total Bills') }}</span>
        <p v-if="user.stats">{{ user.stats.total_bills }}</p>
      </a>
    </div><!-- item -->
    <div class="item" v-if="permissions.includes('show bills')">
      <a :href="'/nova/resources/bills?bills_page=1&bills_filter='+user.stats.filter_user_id_and_bill_status_paid">
        <span>{{ __('Total Paid Bills') }}</span>
        <p v-if="user.stats">{{ user.stats.total_paid_bills }}</p>
      </a>
    </div><!-- item -->
  </div><!-- users_statistics -->
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
        user: {},
        permissions: [],
    }),
    mounted() {
      this.handleChangeDate();
      this.reportPermission();
    },
     methods: {
       handleChangeDate () {
        return Nova.request().get('/api/v1/users/' + this.resourceId + '/stats').then(response => {
          this.user = response.data.data
        })
      },
       reportPermission(){
         return Nova.request().get('/user-permissions/admins').then(response => {
           this.permissions = response.data;
          })
        },
    },
}
</script>

<style lang="scss" scoped>
  #users_statistics {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin: 0 -15px;
    .item {
      padding: 0 15px;
      flex: 0 0 25%;
      max-width: 25%;
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
        span {
          display: block;
          font-size: 17px;
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
