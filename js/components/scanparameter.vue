<template>
	<div>
		<label style="text-transform:capitalize; white-space: nowrap;"
			   :title=description>
			<span>{{ name }}</span>
			<span v-if="type === 'list'">
				<select
						v-on:change="onInputChange"
				>
					<template v-for="(option) in options">
						<option :selected="option === value ? 'selected' : ''">
							{{ option }}
						</option>
					</template>
				</select>
			</span>
			<span v-else-if="type === 'range'">
				<input
						type="range"
						:min="options[0]"
						:max="options[1]"
						:value="value"
						v-on:change="onInputChange"
				>
				{{ value }}
			</span>
			<span v-else-if="type === 'readonly'">
				<input type="text" disabled readonly :value="value">
			</span>
			<span class="icon-history"
				  style="cursor:pointer; display:inline-block; float:right;"
				  v-on:click="resetDefault"></span>
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
				return this.params.type
			},
			description: function () {
				return this.params.description
			},
			options: function () {
				return this.params.options;

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
<style scoped lang="scss">

