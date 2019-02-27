<template>
	<div class="app-container">
		<div class="content">
			<transition name="slide-fade" mode="out-in">
				<div v-if="status==='ready'" v-bind:key="status">
					<ScanOptionsDialog></ScanOptionsDialog>
				</div>
				<div v-else-if="status==='error'" v-bind:key="status">
					<div class="container">
						<div class="inner">
							<span class="icon-error big-icon-error"></span>
							<p>There was an error while performing the following task:</p>
							<p>{{statusMessage}}</p>
						</div>
					</div>
				</div>
				<div v-else v-bind:key="status">
					<div class="container">
						<div class="inner">
							<Spinner style="
						margin:0 auto;
						text-align:center;
						"
									 color="silver"></Spinner>
							<p>{{statusMessage}}</p>
						</div>
					</div>
				</div>
			</transition>
		</div>
		<div class="footer">
			<!--<button @click="addError"></button>-->
			<transition-group name="list" tag="ul" class="error-container">
				<li
						v-for="(error,i) in errors"
						class="error"
						v-bind:key="error"
						v-bind:data-index="i"
				>
					<span class="icon-error"></span>
					{{ error }}
					<span class="icon-delete"
						  v-on:click="removeError(i)"></span>
				</li>
			</transition-group>
		</div>
	</div>
</template>

<script>
	import L10nView from "./l10n.vue";
	import ScanOptionsDialog from "./scanoptionsdialog.vue";
	import Spinner from 'vue-spinner/src/SyncLoader.vue'
	import {mapState} from 'vuex';

	export default {
		computed: mapState({
			status: state => state.appState.status,
			statusMessage: state => state.appState.statusMessage,
			errors: state => state.errors,
		}),
		components: {
			ScanOptionsDialog,
			t: L10nView,
			Spinner
		},
		methods: {
			addError: function () {
				this.$store.dispatch('addError', 'Hurrrrrrrr');
			},
			removeError: function (index) {
				this.$store.dispatch('removeError', index);
			}
		}
	}
</script>

<style scoped lang="scss">
	.app-container {
		width: 70vw;
		height: 80vh;
		display: flex;
		flex-direction: column;
	}

	.content {
		flex: 8;
		overflow-y: scroll;
		> * {
			height: 100%;
		}
	}

	.footer {
		/*flex: 1;*/
	}

	.container {
		display: flex;
		align-items: center;
		justify-content: center;
		text-align: center;
		height: 100%
	}

	.inner {
		margin: 0 auto;
	}

	.error-container {
	}

	.error {
		color: red;
		padding: 1em;
		background: white;
		border: 1px solid;
		transition: all 0.4s;
	}

	.icon-error {
		display: inline-block
	}

	.icon-delete {
		cursor: pointer;
		display: inline-block;
		float: right;
	}

	.big-icon-error {
		display: inline-block;
		min-width: 90px;
		min-height: 90px;
		background-size: contain;
	}

	/*Transitions*/

	.slide-fade-enter-active {
		transition: all .3s ease;
	}

	.slide-fade-leave-active {
		transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
	}

	.slide-fade-enter, .slide-fade-leave-to
		/* .slide-fade-leave-active below version 2.1.8 */
	{
		transform: translateX(10px);
		opacity: 0;
	}

	.list-enter,
	.list-leave-to {
		opacity: 0;
	}

	.list-enter {
		transform: translateY(30%);
	}

	.list-leave-to {
		/*transform: scale(0.7);*/
	}

	.list-leave-active {
		position: absolute;
		width:100%;
	}
</style>
