import ViewUI from 'view-design';
import 'view-design/dist/styles/iview.css';
import locale from 'view-design/dist/locale/en-US';

Nova.booting((Vue, router, store) => {
	 Vue.use(ViewUI, { locale });
  	Vue.component('home-analytics', require('./components/Card'))
})
