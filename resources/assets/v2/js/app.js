import Vue from 'vue'
require('./bootstrap');

window.Vue = require('vue');
Vue.mixin(require('./trans'))
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component(
    'payment-method',
    require('./components/PaymentMethod.vue').default
);

Vue.component(
    'bills-count',
    require('./components/charts/BillsCount.vue').default
);

Vue.component(
    'bills-paid-count',
    require('./components/charts/BillsPaidCount.vue').default
);

Vue.component(
    'bills-paid-amount',
    require('./components/charts/BillsPaidAmount.vue').default
);

Vue.component(
    'applications',
    require('./components/Applications.vue').default
);

Vue.component(
    'channel-applications',
    require('./components/ChannelApplications.vue').default
);

Vue.component(
    'pricing',
    require('./components/Pricing.vue').default
); 
Vue.component(
	'example-component', 
	require('./components/ExampleComponent.vue').default
);

// Vue.component(
//     'passport-clients',
//     require('./components/passport/Clients.vue').default
// );

// Vue.component(
//     'passport-authorized-clients',
//     require('./components/passport/AuthorizedClients.vue').default
// );

// Vue.component(
//     'passport-personal-access-tokens',
//     require('./components/passport/PersonalAccessTokens.vue').default
// );

Vue.component(
    'mobile-active',
    require('./components/MobileActive.vue').default
);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */


const app = new Vue({
    el: '#app',
});
