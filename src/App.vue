<template>
    <div>
        <div v-if="permissionToManageCalendarTypes">
            <ManageCalendarTypes :isSuperAdminUser="isSuperAdminUser" />
        </div>
        <div v-if="!permissionToManageCalendarTypes">
            <ManageCalendarTypesEvents />
        </div>
    </div>
</template>

<script>
import ManageCalendarTypes from './components/ManageCalendarTypes'
import ManageCalendarTypesEvents from './components/ManageCalendarTypesEvents'
import axios from "@nextcloud/axios";

export default {
	name: 'App',
	components: {
		ManageCalendarTypes,
		ManageCalendarTypesEvents,
	},
	data() {
        return {
			isSuperAdminUser: false
		}
	},
    computed: {
		/**
		 * Check up if managing calendar types is allowed
		 * @returns {Boolean}
		 */
		permissionToManageCalendarTypes() {
			return (this.isSuperAdminUser)
		},
    },
	beforeMount() {
		// Perform ajax call to check up if current logged in user belongs to the super admin user group
		axios.post(OC.generateUrl('/apps/elb_cal_types/isusersuperadmin')).then((result) => {
			this.isSuperAdminUser = result.data.isSuperAdmin
		})
	},
}
</script>
