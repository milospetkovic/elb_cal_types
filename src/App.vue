<template>
    <div id="content" class="app-elb-cal-types">

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
                       :placeholder="t('elbcaltypes', 'Name for calendar type')"
                       v-model="currentCalType.title"
                       type="text"
                       :disabled="updating">
                <textarea ref="content" v-model="currentCalType.description" :disabled="updating" rows="15" :placeholder="t('elbcaltypes', 'Description for calendar type')"/>
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

    </div>
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
            calTypes: [],
            currentCalTypeID: null,
            loading: true,
			updating: false,
        }
    },
    computed: {
        /**
         * Return the currently selected calendar type
         * @returns {Object|null}
         */
        currentCalType() {
            if (this.currentCalTypeID === null) {
                return null
            }
            return this.calTypes.find((calTypes) => calTypes.id === this.currentCalTypeID)
        },
        /**
         * Return the item object for the sidebar entry of a calendar type
         * @returns {Object}
         */
        calTypeEntry() {
            return (calType) => {
                return {
                    text: calType.title,
                    title: calType.title
                }
            }
        },
        /**
         * Returns true if a calendar type is selected and its title is not empty
         * @returns {Boolean}
         */
        savePossible() {
            return this.currentCalType && this.currentCalType.title !== ''
        },
    },
    /**
     * Fetch list of calendar types when the component is loaded
     */
    async mounted() {
        try {
            const response = await axios.get(OC.generateUrl('/apps/elb_cal_types/caltypes'))
            this.calTypes = response.data
        } catch (e) {
            console.error(e)
            OCP.Toast.error(t('elbcaltypes', 'Could not fetch calendar types'))
        }
        this.loading = false
    },
    methods: {
		/**
		 * Create a new calendar type and add focus to the title field automatically
		 * The calendar type is not yet saved, therefore an id of -1 is used until it
		 * has been persisted in the backend
		 */
        newCalendarType(e) {
            if (this.currentCalTypeID !== -1) {
                this.currentCalTypeID = -1
                this.calTypes.push({
                    id: -1,
                    title: '',
                    description: '',
                })
                this.$nextTick(() => {
                    this.$refs.title.focus()
                })
            }
        },
		/**
		 * Action triggered when clicking the save button
		 * create a new calendar type or update existing
		 */
		saveCalType() {
			if (this.currentCalTypeID === -1) {
				this.createCalType(this.currentCalType)
			}/* else {
				this.updateCalType(this.currentCalType)
			}*/
		},
		/**
		 * Create a new calendar type by sending the information to the server
		 * @param {Object} calType Calendar type object
		 */
		async createCalType(calType) {
			this.updating = true
			try {
				const response = await axios.post(OC.generateUrl(`/apps/elb_cal_types/caltypes`), calType)
				const index = this.calTypes.findIndex((match) => match.id === this.currentCalTypeID)
				this.$set(this.calTypes, index, response.data)
				this.currentCalTypeID = response.data.id
			} catch (e) {
				console.error(e)
				OCP.Toast.error(t('elb_cal_types', 'Could not create a new calendar type'))
			}
			this.updating = false
		},
    },
}
</script>
<style scoped>
    #app-content > div {
        width: 100%;
        height: 100%;
        padding: 20px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    input[type="text"] {
        width: 100%;
    }
    textarea {
        width: 100%;
    }
</style>