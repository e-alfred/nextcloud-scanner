<template>
	<div>
		<label style="text-transform:capitalize; white-space: nowrap;"
			   :title=description>
			<span>{{ name }}</span>
			<span class="icon-history"
				  style="cursor:pointer; display:inline-block; float:right;"
				  v-on:click="resetDefault"></span>
			<span v-if="type === 'list'" class="select-wrap">
				<select
						v-model="value"
				>
					<template v-for="(option) in options">
						<option :selected="option === value ? 'selected' : ''">
							{{ option }}
						</option>
					</template>
				</select>
			</span>
			<span v-else-if="type === 'range'" style="padding:20px">
				<VueSliderComponent
						width="90%"
						:min="options[0]"
						:max="options[1]"
						v-model="value"
				></VueSliderComponent>
			</span>
			<span v-else-if="type === 'readonly'">
				<input type="text" disabled readonly :value="value">
			</span>

		</label>
	</div>
</template>

<script>
	import L10nView from "./l10n.vue";
	import VueSliderComponent from 'vue-slider-component'

	export default {
		props: ['name', 'params'],
		computed: {
			type: function () {
				return this.params.type
			},
			description: function () {
				return this.params.description
			},
			options: function () {
				switch (this.name) {
					case 'l':
						return [this.params.options[0], Math.round(Math.abs(this.params.options[1] - this.$store.getters.scanParam('x')))];
					case 't':
						return [this.params.options[0], Math.round(Math.abs(this.params.options[1] - this.$store.getters.scanParam('y')))];
					case 'x':
						return [this.params.options[0], Math.round(Math.abs(this.params.options[1] - this.$store.getters.scanParam('l')))];
					case 'y':
						return [this.params.options[0], Math.round(Math.abs(this.params.options[1] - this.$store.getters.scanParam('t')))];
				}
				return this.params.options;

			},
			value: {
				get: function () {
					return this.$store.getters.scanParam(this.name);
				},
				set: function (value) {
					this.$store.dispatch('setScanParam', {
						param: this.name,
						value
					});
				}
			}
		},
		methods: {
			resetDefault () {
				this.$store.dispatch('resetScanParam', this.name);
			}
		},
		components: {
			t: L10nView,
			VueSliderComponent
		}
	}
</script>
<style scoped lang="scss">
	/*.select-wrap {*/
	/*border: 1px solid #777;*/
	/*border-radius: 4px;*/
	/*padding: 0 5px;*/
	/*width:200px;*/
	/*background-color:#fff;*/
	/*position:relative;*/
	/*}*/
	/*.select-wrap label{*/
	/*font-size:8px;*/
	/*text-transform: uppercase;*/
	/*color: #777;*/
	/*padding: 0 8px;*/
	/*position: absolute;*/
	/*top:6px;*/
	/*}*/

	/*select{*/
	/*background-color: #fff;*/
	/*border:0px;*/
	/*height:50px;*/
	/*font-size: 16px;*/
	/*}*/
</style>

