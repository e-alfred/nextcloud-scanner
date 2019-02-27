import Vue from 'vue';
import Vuex from 'vuex';

const fetchOptions = {
	headers: {
		'requesttoken': OC.requestToken,
	},
	credentials: 'include'
};
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
			status: 'initializing',
			statusMessage: 'Initializing',
			fetchingPreview: false
		},
		errors: [],
		selectedBackend: 0,
		previews: [],
		backends: [],
		scanParams: [],
	},
	mutations: {
		setAppStatus (state, payload) {
			Vue.set(state.appState, 'status', payload);
		},
		setAppStatusMessage (state, payload) {
			Vue.set(state.appState, 'statusMessage', payload);
		},
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
		},
		addError (state, payload) {
			state.errors.push(payload)
		},
		removeError (state, payload) {
			state.errors.splice(payload, 1);
		}
	},
	getters: {
		currentBackendParams: state => {
			return state.backends[state.selectedBackend].params;
		},
		scanBedSize: (state, getters) => {
			let {x, y} = getters.currentBackendParams;
			return [x.options[1], y.options[1]];
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
		init ({commit, dispatch}) {
			commit('setAppStatusMessage', 'Executing self-test');
			jQuery.get(OC.generateUrl('/apps/scanner/selfcheck')).then(response => {
				commit('setAppStatusMessage', 'Fetching SANE backends');
				dispatch('loadBackends');
			}).fail(response => {
				if (!response.responseJSON.length) {
					dispatch('addError', 'Unknown error during selfcheck');
					commit('setAppStatus', 'error');
					return;
				}
				response.responseJSON.forEach(error => {
					dispatch('addError', error);
					commit('setAppStatus', 'error')
				})
			});
		},
		addError ({commit}, payload) {
			console.warn(payload);
			commit('addError', payload)
		},
		removeError ({commit}, payload) {
			commit('removeError', payload)
		},
		loadBackends ({commit, dispatch}) {
			return jQuery.get(OC.generateUrl('/apps/scanner/backends')).then(response => {
				if (!response.length) {
					commit('addError', 'There are no available SANE backends');
					commit('setAppStatus', 'error');
					return;
				}
				commit('setAppStatusMessage', 'Initializing Scan options');
				commit('setBackends', response);
				commit('setAppStatus', 'ready');
			});
		},
		fetchPreview ({commit, getters, dispatch}) {
			commit('setPreviewAppState', true);
			return jQuery.get(OC.generateUrl('/apps/scanner/preview'), {scanOptions: getters.scanParams}).then(response => {
				commit('setPreview', response);
				commit('setPreviewAppState', false);
			}).fail(response => {
				dispatch('addError', 'Could not fetch preview');
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
