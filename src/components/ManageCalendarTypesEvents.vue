<template>
    <div id="content2" class="app-elb-cal-types-events">
        <AppNavigation>
            <h4>{{ t('elb_cal_types', 'Choose assigned calendar types') }}</h4>
            <ul>
            </ul>
        </AppNavigation>
    </div>
</template>

<script>
import {
    AppNavigation,
} from 'nextcloud-vue'

import axios from '@nextcloud/axios'

export default {
    name: 'ManageCalendarTypesEvents',
    components: {
        AppNavigation,
    },
    data() {
    	return {
    		assignedCalendarTypes: []
        }
    },
    beforeMount() {
		//console.log('poziva ?')
    	axios.get(OC.generateUrl('/apps/elb_cal_types/getassignedcalendartypes')).then((result) => {
			this.assignedCalendarTypes = result.data
			console.log('assigned cal types: ', this.assignedCalendarTypes)
		})
	}
}
</script>
