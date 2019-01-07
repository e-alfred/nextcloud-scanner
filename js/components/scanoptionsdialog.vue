<template>
	<div v-if="backends.length > 0"
		 style="display: flex; min-width: 50vw; max-height: 80vh;">
		<section
				style="flex: 50%; overflow-y: scroll;overflow-x: hidden;padding:0 30px">
			<!--<label>-->
			<!--Backend:-->
			<!--<select>-->
			<!--<template v-for="(backend) in backends">-->
			<!--<option>{{ backend.id }}</option>-->
			<!--</template>-->
			<!--</select>-->
			<!--</label>-->
			<template
					v-for="(params,name) in backends[selectedBackend].params">
				<ScanParam :name="name" :params="params"></ScanParam>
			</template>
		</section>
		<section style="flex: 50%;padding:0 30px">
			<ScanPreview></ScanPreview>
		</section>

	</div>
	<div v-else>
		<Spinner style="margin:0 auto;text-align:center"
				 color="silver"></Spinner>
	</div>
</template>

<script>
	import L10nView from "./l10n.vue";
	import ScanParam from "./scanparameter.vue";
	import ScanPreview from "./scanpreview.vue";
	import Spinner from 'vue-spinner/src/SyncLoader.vue'
	import {mapState} from 'vuex';

	export default {
		computed: mapState([
			'backends', 'selectedBackend'
		]),
		components: {
			t: L10nView,
			ScanParam,
			ScanPreview,
			Spinner
		}
	}
</script>
