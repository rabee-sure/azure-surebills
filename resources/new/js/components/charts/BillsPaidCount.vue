<template>
  <div class="chart_Block bg-white border shadow-sm mb-3 rounded-3">
    <div class="title d-flex align-items-center justify-content-center justify-content-md-between flex-column flex-md-row flex-wrap mb-3">
      <span class="d-block fw-bold">{{ __('The number of bills paid') }}</span>
      <ul class="nav nav-tabs border-0 p-0 d-flex align-items-center justify-content-end" role="tablist">
        <li class="nav-item">
          <a :class="[type =='monthly' ? 'active' : '']" @click="changeTab('monthly')">{{ __('monthly')}}</a>
        </li>
        <li class="nav-item ml-2">
          <a :class="[type =='weekly' ? 'active' : '']" @click="changeTab('weekly')">{{ __('weekly')}}</a>
        </li>
        <li class="nav-item ml-2">
          <a :class="[type =='daily' ? 'active' : '']" @click="changeTab('daily')">{{ __('daily')}}</a>
        </li>
      </ul>
    </div><!-- title -->
    <div class="tab-content">
      <div class="tab-pane fade show active">
        <line-chart :chart-data="data_c" :options="this.options"></line-chart>
      </div><!-- tab-pane -->
    </div><!-- tab-content -->
  </div><!-- chart_Block -->
</template>

<script>
    import LineChart from "./ChartLine.js";

    export default {
        props: ['user'],
        components: {
            LineChart,
        },
        /*
         * The component's data.
         */
        data() {
            return {
                type: 'monthly',
                daily: {},
                weekly: {},
                monthly: {},
                data_c: {},
                options: {
                  legend: {
                      display: false
                  },
                  tooltips: {
                      callbacks: {
                        label: function(tooltipItem) {
                                return tooltipItem.yLabel;
                        }
                      }
                  }
              },
            };
        },

        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
            axios.get('/api/v1/charts/bills_paid_count', {
              params: {
                'user_id': this.user.id,
                'lang': window._locale,
              }
            }).then(response => {
                    this.daily = response.data.daily
                    this.weekly = response.data.weekly
                    this.monthly = response.data.monthly
                    let ffff = {}
                ffff.labels = this.monthly.labels;
                ffff.datasets = this.monthly.datasets;
                this.data_c = ffff
                })
        },
        methods: {
          changeTab(type){
              axios.get('/api/v1/charts/bills_paid_count', {
              params: {
                'user_id': this.user.id,
                'lang': window._locale,
              }
            }).then(response => {
                    this.daily = response.data.daily
                    this.weekly = response.data.weekly
                    this.monthly = response.data.monthly
                })
            this.type = type
            let ffff = {}
            switch(type) {
              case 'daily':
                ffff.labels = this.daily.labels;
                ffff.datasets = this.daily.datasets;
                this.data_c = ffff
                break;
              case 'weekly':
                ffff.labels = this.weekly.labels;
                ffff.datasets = this.weekly.datasets;
                this.data_c = ffff
                break;              
              case 'monthly':
                ffff.labels = this.monthly.labels;
                ffff.datasets = this.monthly.datasets;
                this.data_c = ffff
                break;
            }
          },
        }
    }
</script>

<style lang="scss">
  .chart_Block {
    padding: 15px;
    .title {
      span {
        font-size: 17px;
        color: #3a3a3a;
      } /* span */
      ul {
        li {
          margin: 0 10px 0 0;
          &:first-child {
            margin: 0;
          } /* first-child */
        }/* li */
      } /* ul */
    } /* title */
  } /* chart_Block */
</style>