import ViewUI from 'view-design';
import 'view-design/dist/styles/iview.css';
import locale from 'view-design/dist/locale/en-US';
// import locale from 'view-design/dist/locale/ar-SA';

import JsonExcel from "vue-json-excel";
 

Nova.booting((Vue, router, store) => {
  Vue.component("downloadExcel", JsonExcel);
  Vue.use(ViewUI, { locale });
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
