<template>
  <div class="card card-action m-0">
    <div class="card-header d-flex align-items-center justify-between">
      <h5 class="card-action-title mb-0 flex-grow-1">{{ __('Total bills') }}</h5>
      <div class="card-action-element ms-auto py-0">
        <div class="dropdown">
          <button type="button" class="btn dropdown-toggle p-0" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="icon-base ti ti-calendar"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <button
                type="button"
                class="btn d-flex align-items-center justify-content-start w-100 waves-effect"
                :class="[type =='monthly' ? 'btn-label-primary' : '']"
                @click="changeTab('monthly')"
                role="tab"
                data-bs-toggle="tab"
                data-bs-target="#navs1-tab-monthly"
                aria-controls="navs1-tab-monthly"
                aria-selected="true"
              >
                {{ __('monthly')}}
              </button>
            </li>
            <li>
              <button
                type="button"
                class="btn d-flex align-items-center justify-content-start w-100 waves-effect"
                :class="[type =='weekly' ? 'btn-label-primary' : '']"
                @click="changeTab('weekly')"
                role="tab"
                data-bs-toggle="tab"
                data-bs-target="#navs1-tab-weekly"
                aria-controls="navs1-tab-weekly"
                aria-selected="false"
              >
                {{ __('weekly')}}
              </button>
            </li>
            <li>
              <button
                type="button"
                class="btn d-flex align-items-center justify-content-start w-100 waves-effect"
                :class="[type =='daily' ? 'btn-label-primary' : '']"
                @click="changeTab('daily')"
                role="tab"
                data-bs-toggle="tab"
                data-bs-target="#navs1-tab-daily"
                aria-controls="navs1-tab-daily"
                aria-selected="false"
              >
                {{ __('daily')}}
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div><!-- card-header -->
    <div class="card-body">
      <div class="tab-content p-0">
        <div class="tab-pane fade show active">
          <line-chart :chart-data="data_c" :options="this.options"></line-chart>
        </div><!-- tab-pane -->
      </div><!-- tab-content -->
    </div><!-- card-body -->
  </div><!-- card -->
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
            axios.get('/api/v1/charts/bills_count', {
              params: {
                'user_id': this.user.id,
                'lang': window._locale,
                'mode' : 'monthly'
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
              axios.get('/api/v1/charts/bills_count', {
              params: {
                'user_id': this.user.id,
                'lang': window._locale,
                'mode' : type
              }
            }).then(response => {
              this.daily = response.data.daily
              this.weekly = response.data.weekly
              this.monthly = response.data.monthly
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
            })
          },
        }
    }
</script>
