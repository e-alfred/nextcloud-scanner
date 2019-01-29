import Vue from "vue";
import Wrapper from './components/wrapper.vue'
import store from './store';

export class App {
	constructor (targelEl) {
		this.targelEl = targelEl;

	}

	start () {
		Vue.mixin({
			t: str => t('mail', str)
		});
		this.appView = new Vue({
			el: this.targelEl,
			store,
			components: {
				Wrapper,
			},
			render (h) {
				return h('Wrapper')
			}
		})
		this.initialStateCopy = JSON.parse(JSON.stringify(store.state))
		// const View = Vue.extend(ScanDialog);
		// new View({store}).$mount(this.targelEl)
		store.dispatch('init');
	}

	getScanParams () {
		return store.getters.scanParams;
	}

	destroy () {
		store.replaceState(JSON.parse(JSON.stringify(this.initialStateCopy)));
		this.appView.$destroy();
	}
}
