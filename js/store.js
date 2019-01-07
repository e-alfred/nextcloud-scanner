import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);
/**
 * At least my HP scanner backend defaults to 'Lineart' which is inconvenient.
 * Set it to Color instead
 *
 * Also set up defaults to scan DIN A4 letter size
 */
const initialScanParams = {
	mode: 'Color',
	l: 0,
	t: 0,
	x: 211,
	y: 297
}
export default new Vuex.Store({
	state: {
		appState: {
			loaded: false,
			fetchingPreview: false
		},
		selectedBackend: 0,
		previews: [],
		backends: [],
		scanParams: [],
	},
	mutations: {
		setPreviewAppState (state, payload) {
			Vue.set(state.appState, 'fetchingPreview', !!payload);
		},
		setBackends (state, payload) {
			state.backends = payload;

			state.scanParams = Array(payload.length).fill(initialScanParams);
			state.previews = Array(payload.length).fill(null)

		},
		setPreview (state, payload) {
			//TODO: add scanner id to payload and insert correctly
			Vue.set(state.previews, state.selectedBackend, payload)
		},
		setScanParam (state, payload) {
			let params = state.scanParams[state.selectedBackend];
			Vue.set(state.scanParams, state.selectedBackend, {
				...params,
				[payload.param]: payload.value
			})
		},
		resetScanParam (state, payload) {
			let params = state.scanParams[state.selectedBackend];
			let backend = state.backends[state.selectedBackend];
			let defaultVal = backend.params[payload].default;
			if (initialScanParams[payload] !== undefined) {
				defaultVal = initialScanParams[payload];
			}
			Vue.set(state.scanParams, state.selectedBackend, {
				...params,
				[payload]: defaultVal
			})
		}
	},
	getters: {
		currentBackendParams: state => {
			return state.backends[state.selectedBackend].params;
		},
		scanBedSize: (state, getters) => {
			let {x, y} = getters.currentBackendParams;
			return [x.default, y.default];
		},
		preview: (state) => {
			return state.previews[state.selectedBackend];
		},
		scanParams: (state) => {
			return state.scanParams[state.selectedBackend];
		},
		scanParam: (state, getters) => key => {
			if (getters.scanParams[key]) {
				return getters.scanParams[key]
			}
			return getters.currentBackendParams[key].default;
		}
	},
	actions: {
		loadBackends ({commit}) {
			return jQuery.get(OC.generateUrl('/apps/scanner/backends')).then(response => commit('setBackends', response));
		},
		fetchPreview ({commit, getters}) {
			commit('setPreviewAppState', true);
			return jQuery.get(OC.generateUrl('/apps/scanner/preview'), {scanOptions: getters.scanParams}).then(response => {
				commit('setPreview', response);
				commit('setPreviewAppState', false);
			});
		},
		setScanParam ({commit}, payload) {
			commit('setScanParam', payload)
		},
		resetScanParam ({commit}, payload) {
			commit('resetScanParam', payload)
		}

	}
});
