<template>
    <div id="content" class="app-elb-cal-types-events">
        <AppNavigation>

            <div class="nav-title-field">
                <h4>{{ t('elb_cal_types', 'Assigned calendar types') }}</h4>
            </div>

            <AppNavigationNew
                    :text="t('elbcaltypes', 'Create event')"
                    :disabled="!(currentCalTypeLinkID > 0)"
                    button-id="new-caltype-event-button"
                    button-class="icon-add"
                    :title="t('elbcaltypes', 'Create and assigne calendar event with reminders for selected calendar type')"
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
    },
    beforeMount() {
    	axios.get(OC.generateUrl('/apps/elb_cal_types/getassignedcalendartypes')).then((result) => {
			this.assignedCalendarTypes = result.data
			console.log('assigned cal types: ', this.assignedCalendarTypes)
		})
	},
    methods: {
		openCalType(calType) {
            this.currentCalTypeLinkID = calType.link_id
		},
		newCalendarTypeEvent() {
			alert('implement event create form')
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
        /*border-radius: var(--border-radius-pill);*/
        font-weight: bold;
    }
</style>