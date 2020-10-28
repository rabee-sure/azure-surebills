<template>
    <div class="card dashboard-filled-line-chart mb-4">
        <div class="card-body ">
            <div class="float-left float-none-xs">
                <div class="d-inline-block">
                    <h5 class="d-inline">{{ __('The amount of the payments') }}</h5>
                </div>
            </div>
            <div class="btn-group float-right float-none-xs mt-2">
                <ul class="nav nav-tabs card-header-tabs " role="tablist">
                    <li class="nav-item">
                        <a :class="[type =='monthly' ? 'active' : '', 'btn btn-xs btn-outline-primary']" @click="changeTab('monthly')">
                         {{ __('monthly')}}
                       </a>
                    </li>
                    <li class="nav-item ml-2">
                        <a :class="[type =='weekly' ? 'active' : '', 'btn btn-xs btn-outline-primary']" @click="changeTab('weekly')">
                          {{ __('weekly')}}
                        </a>
                    </li>
                    <li class="nav-item ml-2">
                        <a :class="[type =='daily' ? 'active' : '', 'btn btn-xs btn-outline-primary']" @click="changeTab('daily')">
                          {{ __('daily')}}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    <div class="chart card-body pt-0">
        <div class="tab-content">
            <div  class="tab-pane fade show active">
                <line-chart  :chart-data="data_c" :options="this.options"></line-chart>
            </div>
        </div>
    </div>
    </div>
</template>

<script>
    import LineChart from "./LineChart.js";

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
                    plugins: {
                        labels: {},
                    }
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

<style lang="scss">
.charts_area {
  display: flex;
  align-items: flex-start;
  justify-content: flex-start;
  flex-wrap: wrap;
  margin: 0 -10px;
  .pieChart_area {
    width: 33.3333%;
    padding: 0 10px;
    margin: 15px 0;
    align-self: stretch;
    @media (min-width: 320px) and (max-width: 767px) {
      width: 100%;
    } /* media */
    @media (min-width: 768px) and (max-width: 991px) {
      width: 33.33333%;
    } /* media */
    .inside_area {
      background: #fff;
      padding: 15px;
      border-radius: 5px;
      border: 1px solid #ddd;
      height: 100%;
      .title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        margin: 0 0 15px;
        span {
          display: block;
          font-size: 15px;
          margin: 0 0 5px;
          b {
            display: inline-block;
            font-weight: normal;
            margin: 0 5px 0 0;
          } /* b */
        } /* span */
        p {
          display: block;
          font-size: 15px;
          margin: 0 0 5px;
        } /* p */
      } /* title */
      .chart-with-occupied-percentage {
        position: relative;
        margin: 17px auto;
        #chart-with-occupied-percentage-content {
          position: absolute;
          right: 0;
          left: 0;
          margin: 0 auto;
          text-align: center;
          display: flex;
          align-items: center;
          justify-content: center;
          flex-direction: column;
          height: 100%;
          p {
            display: block;
            font-size: 45px;
            line-height: 1;
            @media (min-width: 768px) and (max-width: 991px) {
              font-size: 20px;
            } /* media */
          } /* p */
          span {
            display: block;
            font-size: 22px;
            margin: 5px auto 0;
            @media (min-width: 768px) and (max-width: 991px) {
              font-size: 16px;
            } /* media */
          } /* span */
        } /* chart-with-occupied-percentage-content  */
        canvas#doughnut-chart {
          max-width: 100%;
          max-height: 360px;
          width: auto !important;
          height: auto !important;
          margin: 0 auto;
          position: relative;
          z-index: 9;
        } /* canvas */
      } /* chart-with-occupied-percentage */
    } /* inside_area */
  } /* pieChart_area */



  .service_condition {
    width: 33.3333%;
    padding: 0 10px;
    margin: 15px 0;
    align-self: stretch;
    @media (min-width: 320px) and (max-width: 767px) {
      width: 100%;
    } /* media */
    @media (min-width: 768px) and (max-width: 991px) {
      width: 33.33333%;
    } /* media */
    .inside_area {
      background: #fff;
      padding: 15px;
      border-radius: 5px;
      border: 1px solid #ddd;
      height: 100%;
      .title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        margin: 0 0 15px;
        span {
          display: block;
          font-size: 15px;
          margin: 0 0 5px;
          b {
            display: inline-block;
            font-weight: normal;
            margin: 0 5px 0 0;
          } /* b */
        } /* span */
      } /* title */
      .content_area {
        canvas {
          max-width: 100%;
          max-height: 360px;
          width: auto !important;
          height: auto !important;
          margin: 0 auto;
          background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' height='512' viewBox='0 0 300 300' width='512' fill='%23999' xmlns:v='https://vecta.io/nano'%3E%3Cpath d='M247.663 119.264L227.208 98.8l.674-2.83 22.367-6.617a4 4 0 0 0 0-7.672l-22.367-6.617-4.875-20.473a4 4 0 0 0-7.782 0l-4.87 20.473-5.3 1.57L173.107 44.7c-5.906-5.908-13.762-9.162-22.113-9.162s-16.207 3.254-22.113 9.162l-74.566 74.562c-12.195 12.193-12.195 32.037 0 44.23l3.734 3.734-4.4 15.584c-2.305 12.135-16.402 33-16.543 33.2a4 4 0 0 0 .48 5.076l44.352 44.352c.773.773 1.797 1.172 2.828 1.172a3.98 3.98 0 0 0 2.172-.643l23.832-15.426c6.12-3.962 11.98-8.337 17.6-13.007l.512.5c5.906 5.908 13.758 9.16 22.113 9.16a31.06 31.06 0 0 0 22.113-9.16l74.566-74.562c12.193-12.195 12.193-32.035.002-44.23zM214.842 82.1a4 4 0 0 0 2.758-2.91l1.516-6.373 1.52 6.373c.332 1.395 1.383 2.504 2.758 2.9l11.62 3.438-11.62 3.438a4 4 0 0 0-2.758 2.91l-1.52 6.373-1.516-6.373c-.332-1.395-1.383-2.504-2.758-2.9l-11.62-3.438zM106.42 243.838l-21.113 13.668-39.762-39.762c4.117-6.396 13.852-22.35 15.883-33.1l17.055-60.535c.668-3.508 3.78-5.918 7.406-5.562 1.848.176 3.512 1.066 4.684 2.508a6.8 6.8 0 0 1 1.516 4.891l-7.953 34.465c-.398 1.74.398 3.533 1.96 4.398 1.55.865 3.5.594 4.766-.67l57.426-57.424c2.238-2.236 6.117-2.23 8.348 0 1.113 1.115 1.73 2.598 1.73 4.174a5.88 5.88 0 0 1-1.73 4.176l-42.25 42.252a4 4 0 0 0 5.656 5.656l53.922-53.924a5.92 5.92 0 0 1 8.352 0 5.87 5.87 0 0 1 1.727 4.176c0 1.576-.613 3.06-1.727 4.174L128.4 171.322a4 4 0 0 0 0 5.656 3.99 3.99 0 0 0 5.656 0l39.83-39.827a6.05 6.05 0 0 1 8.535.003 6.05 6.05 0 0 1 0 8.539l-41.972 41.97a4 4 0 0 0 5.656 5.656l30.488-30.487c2.176-2.178 5.977-2.184 8.16.002 1.1 1.088 1.7 2.537 1.7 4.078s-.602 3-1.7 4.08l-49.688 49.7a175.43 175.43 0 0 1-28.635 23.157zm135.586-86L167.44 232.4c-4.398 4.396-10.242 6.816-16.457 6.816s-12.062-2.42-16.457-6.818l-.1-.1c2.137-1.943 4.243-3.92 6.283-5.96l49.688-49.7a13.68 13.68 0 0 0 4.035-9.736 13.67 13.67 0 0 0-4.035-9.734c-1.443-1.446-3.197-2.448-5.07-3.1l2.74-2.74a14.06 14.06 0 0 0 0-19.854c-1.535-1.532-3.35-2.6-5.273-3.262l5.18-5.18a13.81 13.81 0 0 0 4.07-9.83 13.82 13.82 0 0 0-4.07-9.834c-5.434-5.424-14.246-5.414-19.664.002l-2.805 2.805c-.67-1.874-1.712-3.642-3.2-5.14-5.246-5.252-14.406-5.26-19.66 0L95.07 148.62l4.875-21.127a4.28 4.28 0 0 0 .078-.457 14.78 14.78 0 0 0-3.242-11.029 14.77 14.77 0 0 0-10.133-5.426c-7.72-.727-14.582 4.44-15.945 11.695L60.526 158.4l-.565-.565c-9.074-9.076-9.074-23.842 0-32.918l74.566-74.562c4.395-4.396 10.238-6.818 16.457-6.818s12.06 2.422 16.457 6.818l28.873 28.87-8.33 2.464a4 4 0 0 0 0 7.672l22.37 6.617 4.87 20.473a4 4 0 0 0 7.782 0l2.025-8.506 16.975 16.973c9.068 9.076 9.068 23.842-.003 32.918zM58.983 49.38a4 4 0 0 0 4-4v-8a4 4 0 1 0-8 0v8a4 4 0 0 0 4 4zm0 8a4 4 0 0 0-4 4v8a4 4 0 1 0 8 0v-8a4 4 0 0 0-4-4zm-16 0h8a4 4 0 1 0 0-8h-8a4 4 0 1 0 0 8zm24 0h8a4 4 0 1 0 0-8h-8a4 4 0 1 0 0 8zm162 172a14.02 14.02 0 0 0-14 14 14.02 14.02 0 0 0 14 14 14.02 14.02 0 0 0 14-14 14.02 14.02 0 0 0-14-14zm0 20a6.01 6.01 0 0 1-6-6 6.01 6.01 0 0 1 6-6 6.01 6.01 0 0 1 6 6 6.01 6.01 0 0 1-6 6zm30.327-45.885c-2.362 0-4.276 1.915-4.276 4.276a4.28 4.28 0 0 0 4.276 4.277c2.362 0 4.276-1.915 4.276-4.277s-1.914-4.276-4.276-4.276z'/%3E%3C/svg%3E");
          background-size: 120px;
          background-position: center center;
          background-repeat: no-repeat;
        } /* canvas */
        .labels_items {
          margin: 15px auto 0;
          ul {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            li {
              margin: 0 0 0 20px;
              font-size: 15px;
              line-height: 15px;
              [dir="ltr"] & {
                margin: 0 20px 0 0;
              } /* ltr */
              i {
                float: right;
                height: 15px;
                width: 15px;
                margin: 0 0 0 5px;
                [dir="ltr"] & {
                  float: left;
                  margin: 0 5px 0 0;
                } /* ltr */
              } /* i */
              &.empty i {background: #50c669;}
              &.cleanliness i {background: #ff9019;}
              &.maintenance i {background: #b3c0c7;}
            } /* li */
          } /* ul */
        } /* labels_items */
      } /* content_area */
    } /* inside_area */
  } /* service_condition */

} /* charts_area */

.lineChart_area {
  width: 100%;
  margin: 15px 0;
  align-self: stretch;
  .inside_area {
    background: #fff;
    padding: 15px;
    border-radius: .5rem;
    border: 1px solid #ddd;
    height: 100%;
    .title {
      margin: 0 0 20px;
      font-size: 15px;
    } /* title */
    canvas#bar-chart {
      max-width: 100%;
      max-height: 100%;
      width: 100% !important;
      height: 100% !important;
      margin: 0 auto;
    } /* canvas */
    .labels_items {
      margin: 15px auto 0;
      ul {
        display: flex;
        flex-wrap: wrap;
        li {
          margin: 0 0 0 20px;
          font-size: 15px;
          line-height: 15px;
          [dir="ltr"] & {
            margin: 0 20px 0 0;
          } /* ltr */
          @media (min-width: 320px) and (max-width: 767px) {
            margin: 10px;
          }
          i {
            float: right;
            height: 15px;
            width: 15px;
            margin: 0 0 0 5px;
            [dir="ltr"] & {
              float: left;
              margin: 0 5px 0 0;
            } /* ltr */
          } /* i */
          &.empty i {background: #50c669;}
          &.checkedin i {background: #f6574b;}
          &.reserved i {background: #126ee8;}
          &.onlinewaiting i {background: #9B59B6;}
          &.cleanliness i {background: #ff9019;}
          &.maintenance i {background: #b3c0c7;}
        } /* li */
      } /* ul */
    } /* labels_items */
  } /* inside_area */
} /* lineChart_area */
.dashboard-filled-line-chart .chart{
  height: auto !important; 
}
.tab-pane .chartjs-render-monitor{
    height: 280px !important;
    top: -20px;
    position: relative;
}
</style>