<template>
    <div id="content2" class="app-elb-cal-types-events">
        <AppNavigation>

            <h4>{{ t('elb_cal_types', 'Choose assigned calendar types') }}</h4>


            <ul>
                <template v-for="calType in assignedCalendarTypes">
                    <AppNavigationItem :key="calType.id" :item="calTypeEntry(calType)" icon="icon-user">
                    </AppNavigationItem>
                </template>
            </ul>

        </AppNavigation>
    </div>
</template>

<script>
import {
    AppNavigation,
    AppNavigationItem,
} from 'nextcloud-vue'

import axios from '@nextcloud/axios'

export default {
    name: 'ManageCalendarTypesEvents',
    components: {
        AppNavigation,
		AppNavigationItem,
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
					classes: this.currentCalTypeLinkID === calType.id ? 'active' : '',
					utils: {
						actions: [
							{
								icon: calType.id === -1 ? 'icon-close' : 'icon-delete',
								text: calType.id === -1 ? t('elb_cal_types', 'Cancel calendar type creation') : t('elb_cal_types', 'Delete calendar type'),
								action: () => {
									if (calType.id === -1) {
										this.cancelNewCalType(calType)
									} else {
										this.deleteCalType(calType)
									}
								},
							},
						],
					},
				}
			}
		},
    },
    beforeMount() {
    	axios.get(OC.generateUrl('/apps/elb_cal_types/getassignedcalendartypes')).then((result) => {
			this.assignedCalendarTypes = result.data
			console.log('assigned cal types: ', this.assignedCalendarTypes)
		})
	}
}
</script>
