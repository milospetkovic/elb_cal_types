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
				loading: false,
				date: Date.now() + 86400000 * 3,
				date2: Date.now() + 86400000 * 3 + Math.floor(Math.random() * 86400000 / 2),
				show: false,
				starred: false,
			}
		},
		methods: {
			addOption(val) {
				this.options.push(val)
				this.select.push(val)
			},
			previous(data) {
				console.debug(data)
			},
			next(data) {
				console.debug(data)
			},
			close(data) {
				console.debug(data)
			},
			newCalendarType(e) {
				alert('let\'s gooooo!!!');
				//console.debug(e)
			},
			log(e) {
				console.debug(e)
			},
		},
	}
</script>