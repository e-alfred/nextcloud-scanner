<template>
	<div>
		<label style="text-transform:capitalize;">
			<span class="icon-history"
				  style="cursor:pointer; display:inline-block"
				  v-on:click="resetDefault"></span>
			{{ name }}: {{ value }}
			<div v-if="type === 'select'">
				<select
						v-on:change="onInputChange"
				>
					<template v-for="(option) in options">
						<option :selected="option === value ? 'selected' : ''">
							{{ option }}
						</option>
					</template>
				</select>
			</div>
			<div v-else-if="type === 'range'">
				<input
						type="range"
						:min="options[0]"
						:max="options[1]"
						:value="value"
						v-on:change="onInputChange"
				>
			</div>
			<div v-else-if="type === 'readonly'">
			<input type="text" disabled readonly :value="value">
			</div>
		</label>
	</div>
</template>

<script>
	import L10nView from "./l10n.vue";
	import _ from 'lodash';

	export default {
		props: ['name', 'params'],
		created () {
			this.onInputChange = _.debounce(this.onInputChange, 250);
		},
		computed: {
			type: function () {
				if (this.params.options.split('..').length === 2) {
					return 'range';
				}

				if (this.params.options.split('|').length > 1) {
					return 'select';
				}
				return 'readonly'
			},
			options: function () {
				switch (this.type) {
					case 'select':
						let options = this.params.options.split('|');
						options[options.length - 1] = options[options.length - 1].replace('mm', '').replace('dpi', '');
						return options;
					case 'range':
						let range = this.params.options.split('..');
						//Strip out units
						range[1] = range[1].replace('mm', '').replace('dpi', '');
						return range;
					default:
						return this.params.options;
				}
			},
			value: function () {
				return this.$store.getters.scanParam(this.name);
			}
		},
		methods: {
			onInputChange (event) {
				this.$store.dispatch('setScanParam', {
					param: this.name,
					value: event.target.value
				});
			},
			resetDefault () {
				this.$store.dispatch('resetScanParam', this.name);
			}
		},
		components: {
			t: L10nView
		}
	}
</script>
