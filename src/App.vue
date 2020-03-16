<template>
    <content>
        <AppNavigation>
            <AppNavigationNew v-if="!loading"
                              :text="t('elbcaltypes', 'New calendar type')"
                              :disabled="false"
                              button-id="new-caltype-button"
                              button-class="icon-add"
                              @click="newCalendarType" />

            <AppNavigationSettings>
                Example settings
            </AppNavigationSettings>
        </AppNavigation>
        <AppContent>
            <div v-if="currentCalType">
                <input ref="title"
                       v-model="currentCalType.title"
                       type="text"
                       :disabled="updating">
                <textarea ref="content" v-model="currentCalType.content" :disabled="updating" />
                <input type="button"
                       class="primary"
                       :value="t('elbcaltypes', 'Save')"
                       :disabled="updating || !savePossible"
                       @click="saveCalType">
            </div>
            <div v-else id="emptycontent">
                <div class="icon-file" />
                <h2>{{ t('elbcaltypes', 'Create a new calendar type to get started') }}</h2>
            </div>
        </AppContent>

    </content>

</template>

<script>
	import Content from '@nextcloud/vue/dist/Components/Content'
	import AppContent from '@nextcloud/vue/dist/Components/AppContent'
	import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation'
	import AppNavigationItem from '@nextcloud/vue/dist/Components/AppNavigationItem'
	import AppNavigationNew from '@nextcloud/vue/dist/Components/AppNavigationNew'
	import AppNavigationSettings from '@nextcloud/vue/dist/Components/AppNavigationSettings'
	import AppSidebar from '@nextcloud/vue/dist/Components/AppSidebar'
	import AppSidebarTab from '@nextcloud/vue/dist/Components/AppSidebarTab'
	import AppNavigationCounter from '@nextcloud/vue/dist/Components/AppNavigationCounter'
	import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
	import ActionLink from '@nextcloud/vue/dist/Components/ActionLink'
	import AppNavigationIconBullet from '@nextcloud/vue/dist/Components/AppNavigationIconBullet'
	import ActionCheckbox from '@nextcloud/vue/dist/Components/ActionCheckbox'
	import ActionInput from '@nextcloud/vue/dist/Components/ActionInput'
	import ActionRouter from '@nextcloud/vue/dist/Components/ActionRouter'
	import ActionText from '@nextcloud/vue/dist/Components/ActionText'
	import ActionTextEditable from '@nextcloud/vue/dist/Components/ActionTextEditable'

	import axios from '@nextcloud/axios'

	export default {
		name: 'App',
		components: {
			Content,
			AppContent,
			AppNavigation,
			AppNavigationItem,
			AppNavigationNew,
			AppNavigationSettings,
			AppSidebar,
			AppSidebarTab,
			AppNavigationCounter,
			ActionButton,
			ActionLink,
			AppNavigationIconBullet,
			ActionCheckbox,
			ActionInput,
			ActionRouter,
			ActionText,
			ActionTextEditable,
		},
		data: function() {
			return {
				currentCalType: null,
				calTypes: [],
				loading: true,
				show: false,
				starred: false,
			}
		},
		/**
		 * Fetch list of calendar types when the component is loaded
		 */
		async mounted() {
			//alert('poziva mounted?')
			try {
				const response = await axios.get(OC.generateUrl('/apps/elb_cal_types/caltypes'))
                console.log('Response:', response)
				this.calTypes = response.data
			} catch (e) {
				console.error(e)
				OCP.Toast.error(t('elbcaltypes', 'Could not fetch calendar types'))
			}
			this.loading = false
		},
		methods: {
			newCalendarType(e) {
				alert('let\'s gooooo!!!');
			}
		},
	}
</script>