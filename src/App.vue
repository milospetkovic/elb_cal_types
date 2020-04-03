<template>
	<div>
		<div v-if="permissionToManageCalendarTypes">
			<ManageCalendarTypes :isSuperAdminUser='isSuperAdminUser' />
		</div>
		<div v-if="permissionToManageCalendarTypesEvents">
			<ManageCalendarTypesEvents />
		</div>
        <div v-if="userWithoutAccessPermission">
            <ForbiddenAccess />
        </div>
	</div>
</template>

<script>
import ManageCalendarTypes from './components/ManageCalendarTypes'
import ManageCalendarTypesEvents from './components/ManageCalendarTypesEvents'
import ForbiddenAccess from './components/ForbiddenAccess'
import axios from '@nextcloud/axios'

export default {
	name: 'App',
	components: {
		ManageCalendarTypes,
		ManageCalendarTypesEvents,
		ForbiddenAccess,
	},
	data() {
		return {
			isSuperAdminUser: false,
			isGroupFolderAdminUser: false,
		}
	},
	computed: {
		/**
		 * Check up if managing calendar types is allowed
		 * @returns {Boolean}
		 */
		permissionToManageCalendarTypes() {
			return (this.isSuperAdminUser && !this.isGroupFolderAdminUser)
		},
		permissionToManageCalendarTypesEvents() {
			return (this.isGroupFolderAdminUser)
		},
		userWithoutAccessPermission() {
			return (!this.isSuperAdminUser && !this.isGroupFolderAdminUser)
        }
	},
	beforeMount() {
		// Perform ajax call to check up if current logged in user belongs to the super admin user group
		axios.post(OC.generateUrl('/apps/elb_cal_types/isusersuperadmin')).then((result) => {
			this.isSuperAdminUser = result.data.isSuperAdmin
		})
		// perform ajax call to check up if current logged in user is administrator of group folder
		axios.post(OC.generateUrl('/apps/elb_cal_types/isuseradminforgroupfolder')).then((result) => {
			this.isGroupFolderAdminUser = result.data.isGroupFolderAdmin
		})
	},
}
</script>
