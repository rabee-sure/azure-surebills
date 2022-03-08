<template>
  <div class="chart_Block bg-white shadow-sm mb-3 rounded-3">
    <div class="title d-flex align-items-center justify-content-center justify-content-md-between flex-column flex-md-row flex-wrap mb-3">
      <span class="d-block fw-bold mb-2 mb-md-0 text-capitalize">{{ __('The amount of the payments') }}</span>
      <ul class="nav nav-tabs border-0 p-0 d-flex align-items-center justify-content-end" role="tablist">
        <li>
          <a class="d-flex align-items-center justify-content-center border rounded-pill" :class="[type =='monthly' ? 'active' : '']" @click="changeTab('monthly')">{{ __('monthly')}}</a>
        </li>
        <li>
          <a class="d-flex align-items-center justify-content-center border rounded-pill" :class="[type =='weekly' ? 'active' : '']" @click="changeTab('weekly')">{{ __('weekly')}}</a>
        </li>
        <li>
          <a class="d-flex align-items-center justify-content-center border rounded-pill" :class="[type =='daily' ? 'active' : '']" @click="changeTab('daily')">{{ __('daily')}}</a>
        </li>
      </ul>
    </div><!-- title -->
    <div class="tab-content">
      <div class="tab-pane fade show active">
        <line-chart  :chart-data="data_c" :options="this.options"></line-chart>
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
          maintainAspectRatio: false,
          legend: {
            display: false
          },
        },
            };
        },

        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
            axios.get('/api/v1/charts/bills_paid_amount', {
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
              axios.get('/api/v1/charts/bills_paid_amount', {
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