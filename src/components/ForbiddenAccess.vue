<template>
	<div id="content3" class="app-elb-cal-types-forbidden-access">
		<AppNavigation>
			<h4>Sorry, you dont have assigned permission for this module</h4>
		</AppNavigation>
		<AppContent>
		</AppContent>
	</div>
</template>

<script>
import {
	AppNavigation,
	AppContent,
} from 'nextcloud-vue'

export default {
	name: 'ForbiddenAccess',
	components: {
		AppNavigation,
		AppContent,
	},
}
</script>
