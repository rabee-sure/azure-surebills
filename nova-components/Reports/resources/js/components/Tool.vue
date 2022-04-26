<template>
  <div id="reports">
    <div class="item">
      <a :href="'/nova/resources/merchants-outstanding-reports'">
        <span>Merchants Outstanding</span>
      </a>
    </div><!-- item -->
    <div class="item">
      <a :href="'/nova/resources/bill-reports'">
        <span>Bills</span>
      </a>
    </div><!-- item -->
    <div class="item">
      <a :href="'/nova/resources/auto-transfers'">
        <span>AutoTransfers</span>
      </a>
    </div><!-- item -->
  </div><!-- reports -->
</template>

<script>
export default {
    metaInfo() {
        return {
          title: 'Reports',
        }
    },
    mounted() {
        //
    },
}
</script>

<style lang="scss" scoped>
  #reports {
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
  } /* reports */
</style>

