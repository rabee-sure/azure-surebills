import ViewUI from 'view-design';
import 'view-design/dist/styles/iview.css';
import locale from 'view-design/dist/locale/en-US';
// import locale from 'view-design/dist/locale/ar-SA';
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

import JsonExcel from "vue-json-excel";

<<<<<<< HEAD
import VueSweetalert2 from 'vue-sweetalert2';
// If you don't need the styles, do not connect
import 'sweetalert2/dist/sweetalert2.min.css';


=======
// const Swal = require('sweetalert2')
>>>>>>> 0143250c2f25fb65f8c2dd91910b937f8b99c72c
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
