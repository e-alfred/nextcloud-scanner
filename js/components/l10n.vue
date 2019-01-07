<template>
	<span>{{ translated }}</span>
</template>

<script>
	export default {
		props: [
			'text'
		],
		data: function() {
			return {
				translated: t('scanner', this.text)
			};
		}
	}
</script>
