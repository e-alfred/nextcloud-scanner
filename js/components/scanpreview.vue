<template>
	<div style="height:90%">
		<VueCropper
				ref="cropper"
				:src="scanPreview.preview"
				alt="Source Image"
				:viewMode="2"
				:autoCrop="false"
				:zoomable="false"
				:movable="false"
				:scalable="false"
				:rotatable="false"
				:crop="cropImage"
				:ready="ready"
		>
		</VueCropper>
		<button
				class="primary"
				v-on:click="fetchPreview"
				title="Generate a preview with the current settings"
				v-if="!fetchingPreview"
		>Preview</button>
		<Spinner color="silver" v-else></Spinner>
	</div>
</template>

<script>
	import VueCropper from 'vue-cropperjs';
	import Spinner from 'vue-spinner/src/SyncLoader.vue'

	const FALLBACK_DPI = 96;

	function mm2px (mm, dpi) {
		return Math.round(mm / 25.4 * dpi);
	}

	function px2mm (px, dpi) {
		return Math.round((px / dpi) * 25.4);
	}

	function fallbackPreview (x, y) {
		let preview = {};
		preview.dpi = FALLBACK_DPI;
		let width = mm2px(x, preview.dpi), height = mm2px(y, preview.dpi),
			a4Width = mm2px(211, preview.dpi),
			a4Height = mm2px(297, preview.dpi);
		let svg = `<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="${width}" height="${height}" viewBox="0 0 ${width} ${height}">
<rect width="${a4Width}" height="${a4Height}" style="fill:rgba(255,255,255,0.3);stroke-width:3;stroke:rgb(0,0,0)" />
<rect x="${width - a4Width}" y="${height - a4Height}" width="${a4Width}" height="${a4Height}" style="fill:rgba(255,255,255,0.3);stroke-width:3;stroke:rgb(0,0,0)" />
<text x="${width / 2}" y="${height / 2}" fill="black" text-anchor="middle" alignment-baseline="central" font-size="45"></text>
</svg>`;
		preview.preview = 'data:image/svg+xml;base64,' + btoa(svg);
		return preview;
	}

	export default {
		created () {
			this.silent = false;
			this.cropImage = _.debounce(this.cropImage, 250);
		},
		mounted () {
			// Upon mounting of the component, we accessed the .bind({...})
			// function to put an initial image on the canvas.
			// this.fetchPreview();
		},
		computed: {
			dpi () {
				let currentPreview = this.$store.getters.preview;
				if (!currentPreview) {
					return FALLBACK_DPI;
				}
				return this.scanPreview.dpi;
			},
			scanPreview () {

				let currentPreview = this.$store.getters.preview;
				if (!currentPreview) {
					return fallbackPreview(...this.$store.getters.scanBedSize);
				}
				return currentPreview;
			},
			geometry () {
				return {
					x: mm2px(this.$store.getters.scanParam('l'), this.scanPreview.dpi),
					y: mm2px(this.$store.getters.scanParam('t'), this.scanPreview.dpi),
					width: mm2px(this.$store.getters.scanParam('x'), this.scanPreview.dpi),
					height: mm2px(this.$store.getters.scanParam('y'), this.scanPreview.dpi),
				}
			},
			fetchingPreview(){
				return this.$store.state.appState.fetchingPreview;
			}

		},
		watch: {
			scanPreview (newVal, oldVal) {
				this.$refs.cropper.replace(newVal.preview);
			},
			geometry (newVal, oldVal) {
				if (_.isEqual(newVal, oldVal)) {
					return;
				}
				if (this.silent) {
					return;
				}
				this.silent = true;
				this.$refs.cropper.setData(newVal);
				this.silent = false;
			}
		},
		methods: {
			ready () {
				const initialData = {...this.geometry};
				this.$refs.cropper.initCrop();
				this.$refs.cropper.setData(initialData);
			},
			cropImage (event) {
				if (!event) {
					return;
				}
				if (this.silent) {
					return;
				}
				this.silent = true;
				this.$store.dispatch('setScanParam', {
					param: 'x',
					value: px2mm(event.detail.width, this.scanPreview.dpi)
				});

				this.$store.dispatch('setScanParam', {
					param: 'y',
					value: px2mm(event.detail.height, this.scanPreview.dpi)
				});

				this.$store.dispatch('setScanParam', {
					param: 'l',
					value: px2mm(event.detail.x, this.scanPreview.dpi)
				});

				this.$store.dispatch('setScanParam', {
					param: 't',
					value: px2mm(event.detail.y, this.scanPreview.dpi)
				});
				this.silent = false;

			},
			fetchPreview () {
				this.$store.dispatch('fetchPreview');
			}
		},
		components: {VueCropper, Spinner}
	}
</script>
