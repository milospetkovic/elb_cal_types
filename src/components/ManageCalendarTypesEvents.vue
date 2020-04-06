<template>
    <div id="content" class="app-elb-cal-types-events">
        <AppNavigation>

            <div class="nav-title-field">
                <h4>{{ t('elb_cal_types', 'Assigned calendar types') }}</h4>
            </div>

            <AppNavigationNew
                    :text="t('elbcaltypes', 'Create event')"
                    :disabled="disableCreateNewEventButton"
                    button-id="new-caltype-event-button"
                    button-class="icon-add"
                    :title="t('elbcaltypes', 'Create and assign calendar event with reminders to users')"
                    @click="newCalendarTypeEvent" />

            <ul>
                <template v-for="calType in assignedCalendarTypes">
                    <AppNavigationItem :key="calType.id" :item="calTypeEntry(calType)" icon="icon-user">
                    </AppNavigationItem>
                </template>
            </ul>

        </AppNavigation>

        <AppContent>

            <div v-if="currentCalTypeLinkID">
                <h2>{{ t('elb_cal_types', 'Manage events for selected calendar type') }}: {{ assignedCalendarTypes[currentCalTypeLinkID]['cal_type_title'] }}</h2>
            </div>
            <div v-else>
                <h2>{{ t('elb_cal_types', 'Please choose assigned calendar type from the left menu to manage events') }}</h2>
            </div>

        </AppContent>
    </div>
</template>

<script>
import {
    AppNavigation,
    AppNavigationNew,
    AppNavigationItem,
    AppContent,
} from 'nextcloud-vue'

import axios from '@nextcloud/axios'

export default {
    name: 'ManageCalendarTypesEvents',
    components: {
        AppNavigation,
		AppNavigationNew,
		AppNavigationItem,
		AppContent,
    },
    data() {
    	return {
    		assignedCalendarTypes: [],
			currentCalTypeLinkID: null,
            visibleCreateNewEventForm: false,
        }
    },
    computed: {
		/**
		 * Return the item object for the sidebar entry of a calendar type
		 * @returns {Object}
		 */
		calTypeEntry() {
			return (calType) => {
				return {
					text: calType.cal_type_title,
					action: () => this.openCalType(calType),
					classes: this.currentCalTypeLinkID === calType.link_id ? 'active' : '',
				}
			}
		},
		disableCreateNewEventButton() {
			if (!this.currentCalTypeLinkID || this.visibleCreateNewEventForm) {
				return true
            }
			return false
        },
    },
    beforeMount() {
    	axios.get(OC.generateUrl('/apps/elb_cal_types/getassignedcalendartypes')).then((result) => {
			this.assignedCalendarTypes = result.data
			console.log('assigned cal types: ', this.assignedCalendarTypes)
		})
	},
    methods: {
		openCalType(calType) {
			this.visibleCreateNewEventForm = false
            this.currentCalTypeLinkID = calType.link_id
		},
		newCalendarTypeEvent() {
			this.visibleCreateNewEventForm = true
			//alert('implement event create form')
        }
    }
}
</script>
<style scoped>
    #app-content {
        padding: 20px;
    }
    .nav-title-field {
        display: inline-block;
        padding: 10px 10px 10px 20px;
        background-color: #EDEDED;
        border: 1px solid #DBDBDB;
        margin: 10px 10px;
        font-weight: bold;
    }
</style>