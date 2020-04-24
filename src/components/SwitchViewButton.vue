<template>
	<AppNavigationNew
		:text="t('elbcaltypes', 'Switch view')"
		:title="t('elbcaltypes', 'Click button to toggle between super admin and group folder admin view')"
		:disabled="false"
		button-id="new-caltype-button"
		button-class="icon-toggle"
		@click="changeView" />
</template>

<script>

import { AppNavigationNew } from 'nextcloud-vue'

export default {
	name: 'SwitchViewButton',
	components: {
		AppNavigationNew,
	},
	methods: {
		changeView() {
			this.$emit('perform-switch')
		},
	},
}
</script>
