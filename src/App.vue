<template>
	<div>
		<div v-if="permissionToManageCalendarTypes">
			<ManageCalendarTypes
					:isSuperAdminUser='isSuperAdminUser'
					:allowSuperAdminSwitch="permissionToSwitchBetweenCalendarTypesAndEvents"
					v-on:perform-switch="performSwitch" />
		</div>
		<div v-if="permissionToManageCalendarTypesEvents">
			<ManageCalendarTypesEvents
					:allowSuperAdminSwitch="permissionToSwitchBetweenCalendarTypesAndEvents"
					v-on:perform-switch="performSwitch" />
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
			administratorForCalTypesView: false,
		}
	},
	computed: {
		/**
		 * Check up if managing calendar types is allowed
		 * @returns {Boolean}
		 */
		permissionToManageCalendarTypes() {
			return (this.isSuperAdminUser && this.administratorForCalTypesView)
		},
		permissionToManageCalendarTypesEvents() {
			return (this.isGroupFolderAdminUser && !this.administratorForCalTypesView)
		},
		userWithoutAccessPermission() {
			return (!this.isSuperAdminUser && !this.isGroupFolderAdminUser)
		},
		permissionToSwitchBetweenCalendarTypesAndEvents() {
			return (this.isSuperAdminUser && this.isGroupFolderAdminUser)
		},
	},
	beforeMount() {
		// Perform ajax call to check up if current logged in user belongs to the super admin user group
		axios.post(OC.generateUrl('/apps/elb_cal_types/isusersuperadmin')).then((result) => {
			this.isSuperAdminUser = result.data.isSuperAdmin
			this.administratorForCalTypesView = result.data.isSuperAdmin
		})
		// perform ajax call to check up if current logged in user is administrator of group folder
		axios.post(OC.generateUrl('/apps/elb_cal_types/isuseradminforgroupfolder')).then((result) => {
			this.isGroupFolderAdminUser = result.data.isGroupFolderAdmin
		})
	},
	methods: {
		performSwitch() {
			this.administratorForCalTypesView = !this.administratorForCalTypesView
		}
	}
}
</script>
