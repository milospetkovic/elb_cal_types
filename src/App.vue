<template>
    <content>
        <AppNavigation>
            <AppNavigationNew v-if="!loading"
                              :text="t('elbcaltypes', 'New calendar type')"
                              :disabled="false"
                              button-id="new-caltype-button"
                              button-class="icon-add"
                              @click="newCalendarType" />

            <ul>
                <AppNavigationItem v-for="calType in calTypes" :key="calType.id" :item="calTypeEntry(calType)" />
            </ul>

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
                <textarea ref="content" v-model="currentCalType.description" :disabled="updating" />
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
	import {
		AppContent,
		AppNavigation,
		AppNavigationItem,
		AppNavigationNew,
		AppNavigationSettings
	} from 'nextcloud-vue'

	import axios from '@nextcloud/axios'

	export default {
		name: 'App',
		components: {
			AppContent,
			AppNavigation,
			AppNavigationItem,
			AppNavigationNew,
			AppNavigationSettings
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
        computed: {
			/**
			 * Return the item object for the sidebar entry of a calendar type
			 * @returns {Object}
			 */
			calTypeEntry() {
				return (calType) => {
					console.log('calType:', calType)
					return {
						text: calType.title,
                        title: calType.title
					}
				}
			},
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