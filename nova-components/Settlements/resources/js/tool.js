import ViewUI from 'view-design';
import 'view-design/dist/styles/iview.css';
import locale from 'view-design/dist/locale/en-US';
// import locale from 'view-design/dist/locale/ar-SA';
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

import JsonExcel from "vue-json-excel";


// const Swal = require('sweetalert2')
Nova.booting((Vue, router, store) => {
  Vue.component("downloadExcel", JsonExcel);
  Vue.use(ViewUI, { locale });
  Vue.use(VueSweetalert2);
  router.addRoutes([
    {
      name: 'settlements',
      path: '/settlements',
      component: require('./components/Index'),
    },
    {
      name: 'create-settlement',
      path: '/settlements/:id/create',
      component: require('./components/Create'),
    },
  ])
})
