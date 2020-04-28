<template>
	<div v-if="issuperadminuser">
		<div id="content" class="app-elb-cal-types">
			<AppNavigation>
				<div class="nav-title-field">
					<h4>{{ t('elbcaltypes', 'Manage calendar types') }}</h4>
				</div>

				<template v-if="allowsuperadminswitch">
					<SwitchViewButton @:perform-switch="changeView" />
				</template>

				<AppNavigationNew
					:text="t('elbcaltypes', 'New calendar type')"
					:disabled="false"
					button-id="new-caltype-button"
					button-class="icon-add"
					@click="newCalendarType" />

				<ul>
					<template v-for="calType in calTypes">
						<AppNavigationItem :key="calType.id" :item="calTypeEntry(calType)" icon="icon-user">
							<AppNavigationCounter>10</AppNavigationCounter>
						</AppNavigationItem>
					</template>
				</ul>
			</AppNavigation>

			<AppContent>
				<h2 v-if="currentCalTypeID === -1" class="pull-left">
					{{ t('elbcaltypes', 'Create a new calendar type') }}
				</h2>

				<h2 v-else-if="currentCalTypeID > 0" class="pull-left">
					{{ t('elbcaltypes', 'Update calendar type') }}
				</h2>

				<button v-show="showToggleSidebarButton" class="pull-right" @click="toggleSidebar">
					{{ t('elbcaltypes', 'Toggle sidebar') }}
				</button>

				<div v-if="currentCalType">
					<input ref="title"
						v-model="currentCalType.title"
						:placeholder="t('elbcaltypes', 'Name for calendar type')"
						type="text"
						:disabled="updating">
					<textarea ref="content"
						v-model="currentCalType.description"
						:disabled="updating"
						rows="15"
						:placeholder="t('elbcaltypes', 'Description for calendar type')" />
					<input type="button"
						class="primary"
						:value="t('elbcaltypes', 'Save')"
						:disabled="updating || !savePossible"
						@click="saveCalType">
				</div>
				<div v-else-if="!calTypes.length" class="emptycontent">
					<div class="icon-file" />
					<h2>{{ t('elbcaltypes', 'Create a new calendar type to get started') }}</h2>
				</div>

				<div v-else class="emptycontent">
					<div class="icon-file" />
					<h2>{{ t('elbcaltypes', 'Create a new calendar type or update existing') }}</h2>
				</div>
			</AppContent>

			<AppSidebar v-show="showSidebar"
				:title="getTitleOfCurrentCalType"
				:subtitle="t('elbcaltypes', 'Assign reminders and group folders for calendar type')"
				@close="toggleSidebar">
				<AppSidebarTab id="assigned-reminders" :name="t('elbcaltypes', 'Assigned reminders')" icon="icon-edit">
					{{ t('elbcaltypes', 'Assigned reminders for the selected calendar type') }}

					<hr>

					<div v-if="assignedRemindersForCalTypeID">
						<ul class="assigned-reminders-for-cal-type">
							<li v-for="calTypeReminder in listAssignedRemindersForCalTypeID" :key="calTypeReminder.link_id">
								{{ calTypeReminder.cal_def_reminder_title_trans }}<button class="icon-delete pull-right" @click="removeReminderForCalendarType(calTypeReminder.link_id)" />
							</li>
						</ul>
					</div>
					<div v-else>
						<p class="text-warning">
							{{ t('elbcaltypes', 'The selected calendar type doesn\'t have assigned reminder') }}
						</p>
					</div>
				</AppSidebarTab>

				<AppSidebarTab id="avail-reminders" :name="t('elbcaltypes', 'Available reminders')" icon="icon-edit">
					<div v-if="defaultCalReminders.length">
						<p>
							{{ t('elbcaltypes', 'Select reminder to assign it to the selected calendar type') }}
						</p>

						<hr>

						<ul class="reminders-for-cal-type">
							<li v-for="defCal in defaultCalReminders" :key="defCal.id">
								<input :id="'link-checkbox'+ defCal.id"
									v-model="modelDefaultCalReminder"
									name="link-checkbox[]"
									:disabled="checkIfDefCalReminderIsAssignedToCalTypeID(defCal.id)"
									class="checkbox link-checkbox"
									:value="defCal.id"
									type="checkbox">
								<label :for="'link-checkbox'+ defCal.id" class="link-checkbox-label">{{ t('elbcaltypes', defCal.title) }}</label>
							</li>
						</ul>

						<div class="app-sidebar-tab__buttons">
							<button class="primary" @click="saveRemindersForCalendarType">
								{{ t('elbcaltypes', 'Assign') }}
							</button>
						</div>
					</div>
					<div v-else>
						<hr>
						{{ t('elbcaltypes', 'No default reminders available to assign it to the selected calendar type') }}
					</div>
				</AppSidebarTab>

				<AppSidebarTab id="assigned-group-folders" :name="t('elbcaltypes', 'Group folders')" icon="icon-user">
					<p>
						{{ t('elbcaltypes', 'Assign group folder(s) to the selected calendar type') }}
					</p>

					<hr>

					<ul v-if="groupFolders.length" class="group-folders-for-cal-type">
						<li v-for="gf in groupFolders" :key="gf.folder_id">
							<input :id="'gf-checkbox'+ gf.folder_id"
								v-model="modelGroupFolder"
								name="gf-checkbox[]"
								:disabled="checkIfGroupFolderIsAssignedToCalTypeID(gf.folder_id)"
								class="checkbox gf-checkbox"
								:value="gf.folder_id"
								type="checkbox">
							<label :for="'gf-checkbox'+ gf.folder_id" class="gf-checkbox-label">{{ gf.mount_point }}</label>

							<button v-if="checkIfGroupFolderIsAssignedToCalTypeID(gf.folder_id)" class="icon-delete pull-right" @click="removeGroupFolderForCalendarType(gf.folder_id)" />
						</li>
					</ul>

					<div class="app-sidebar-tab__buttons">
						<button class="primary" @click="saveGroupFoldersForCalendarType">
							{{ t('elbcaltypes', 'Assign') }}
						</button>
					</div>
				</AppSidebarTab>
			</AppSidebar>
		</div>
	</div>
</template>

<script>
import {
	AppContent,
	AppNavigation,
	AppNavigationItem,
	AppNavigationNew,
	AppSidebar,
	AppSidebarTab,
	AppNavigationCounter,
} from 'nextcloud-vue'

import axios from '@nextcloud/axios'
import SwitchViewButton from './SwitchViewButton'

export default {
	name: 'ManageCalendarTypes',
	components: {
		AppContent,
		AppNavigation,
		AppNavigationItem,
		AppNavigationNew,
		AppSidebar,
		AppSidebarTab,
		AppNavigationCounter,
		SwitchViewButton,
	},
	props: {
		issuperadminuser: {
			type: Boolean,
			default: false,
		},
		allowsuperadminswitch: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			calTypes: [],
			currentCalTypeID: null,
			loading: true,
			updating: false,
			defaultCalReminders: [],
			selectedOpenSidebar: true,
			assignedReminders: [],
			modelDefaultCalReminder: [],
			assignedRemForCalTypes: [],
			modelGroupFolder: [],
			groupFolders: [],
			assignedGroupFoldersForCalTypes: [],
		}
	},
	computed: {
		/**
		 * Sort assigned reminder for the selected calendar type by reminder timeline
		 * @returns {Object}
		 */
		listAssignedRemindersForCalTypeID() {
			const ret = {}
			Object.keys(this.assignedRemForCalTypes[this.currentCalTypeID]).forEach(key => {
				const calRemObjID = this.assignedRemForCalTypes[this.currentCalTypeID][key]['minutes_before_event']
				const calRemObj = this.assignedRemForCalTypes[this.currentCalTypeID][key]
				ret[calRemObjID] = { 'cal_def_reminder_title_trans': calRemObj.cal_def_reminder_title_trans, 'link_id': calRemObj.link_id }
			})
			return ret
		},
		/**
		 * Check up if selected calendar type has at least one assigned reminder
		 * @returns {Integer}
		 */
		assignedRemindersForCalTypeID() {
			if (this.assignedRemForCalTypes[this.currentCalTypeID] !== undefined) {
				return this.assignedRemForCalTypes[this.currentCalTypeID]
			}
			return false
		},
		/**
		 * Count assigned reminders for the selected calendar type
		 * @returns {Integer}
		 */
		countAssignedRemindersForCalTypeID() {
			if (this.assignedRemForCalTypes[this.currentCalTypeID] !== undefined && this.currentCalTypeID > 0) {
				return Object.keys(this.assignedRemForCalTypes[this.currentCalTypeID]).length
			}
			return 0
		},
		/**
		 * Check up if sidebar should be visible
		 * @returns {Boolean}
		 */
		showSidebar() {
			return (this.selectedOpenSidebar && this.currentCalTypeID > 0)
		},
		/**
		 * Show toggle button for sidebar if calendar type is selected/created
		 * @returns {Boolean}
		 */
		showToggleSidebarButton() {
			return (this.currentCalTypeID > 0)
		},
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
		 * Get title of currently selected calendar type (needed for sidebar)
		 * @returns {string}
		 */
		getTitleOfCurrentCalType() {
			if (this.currentCalType) {
				return this.currentCalType.title
			}
			return ''
		},
		/**
		 * Return the item object for the sidebar entry of a calendar type
		 * @returns {Object}
		 */
		calTypeEntry() {
			return (calType) => {
				return {
					text: calType.title,
					action: () => this.openCalType(calType),
					classes: this.currentCalTypeID === calType.id ? 'active' : '',
					utils: {
						actions: [
							{
								icon: calType.id === -1 ? 'icon-close' : 'icon-delete',
								text: calType.id === -1 ? t('elbcaltypes', 'Cancel calendar type creation') : t('elbcaltypes', 'Delete calendar type'),
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
					bullet: '#0082c9',
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
	beforeMount() {
		// Perform ajax call to fetch default reminders
		axios.post(OC.generateUrl('/apps/elb_cal_types/getdefaultreminders')).then((result) => {
			this.defaultCalReminders = result.data
		})
		// fetch assigned reminders for calendar types
		this.fetchAssignedReminders()
		// Perform ajax call to fetch all group folders
		axios.get(OC.generateUrl('/apps/elb_cal_types/getallgroupfolders')).then((result) => {
			this.groupFolders = result.data
		})
		// fetch assigned group folders for calendar types
		this.fetchAssignedGroupFolders()
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
		}
		this.loading = false
	},
	methods: {
		/**
		 * Check up if default reminder id is already assigned to the selected calendar type id
		 * @param {int} defRemID Id of default reminder
		 * @returns {boolean}
		 */
		checkIfDefCalReminderIsAssignedToCalTypeID(defRemID) {

			let response = false

			if (this.assignedRemForCalTypes && this.currentCalTypeID > 0) {

				if (this.assignedRemForCalTypes[this.currentCalTypeID] !== undefined) {

					Object.keys(this.assignedRemForCalTypes[this.currentCalTypeID]).forEach(key => {
						const calRemObj = this.assignedRemForCalTypes[this.currentCalTypeID][key]
						if (parseInt(calRemObj.cal_def_reminder_id) === defRemID) {
							response = true
							return true
						}
					})
				}
			}
			return response
		},
		/**
		 * Check up if group folder id is already assigned to the selected calendar type id
		 * @param {int} gfID Id of group folder
		 * @returns {boolean}
		 */
		checkIfGroupFolderIsAssignedToCalTypeID(gfID) {

			let response = false

			if (this.assignedGroupFoldersForCalTypes && this.currentCalTypeID > 0) {

				if (this.assignedGroupFoldersForCalTypes[this.currentCalTypeID] !== undefined) {

					Object.keys(this.assignedGroupFoldersForCalTypes[this.currentCalTypeID]).forEach(key => {
						const gfObj = this.assignedGroupFoldersForCalTypes[this.currentCalTypeID][key]

						if (gfObj.gf_id === gfID) {
							response = true
							return true
						}
					})
				}
			}
			return response
		},
		/**
		 * Uncheck all checked checkboxes for available reminders
		 */
		uncheckSelectedAvailableReminders() {
			this.modelDefaultCalReminder = []
		},
		/**
		 * Uncheck all checked checkboxes for group folders
		 */
		uncheckSelectedGroupFolders() {
			this.modelGroupFolder = []
		},
		/**
		 * Perform ajax call to fetch assigned reminders to calendar types
		 */
		fetchAssignedReminders() {
			axios.post(OC.generateUrl('/apps/elb_cal_types/getassignedreminders')).then((result) => {
				this.assignedRemForCalTypes = result.data
			})
		},
		/**
		 * Perform ajax call to fetch assigned group folders to calendar types
		 */
		fetchAssignedGroupFolders() {
			axios.post(OC.generateUrl('/apps/elb_cal_types/getassignedgroupfolders')).then((result) => {
				this.assignedGroupFoldersForCalTypes = result.data
			})
		},
		/**
		 * Create a new calendar type and focus the calendar type content field automatically
		 * @param {Object} calType calType object
		 */
		openCalType(calType) {
			if (this.updating) {
				return
			}
			this.currentCalTypeID = calType.id
			this.uncheckSelectedAvailableReminders()
			this.uncheckSelectedGroupFolders()
			this.checkAssignedRemindersForCalTypeID()
			this.$nextTick(() => {
				this.$refs.content.focus()
			})
		},
		/**
		 * Abort creating a new calendary type
		 */
		cancelNewCalType() {
			this.calTypes.splice(this.calTypes.findIndex((calType) => calType.id === -1), 1)
			this.currentCalTypeID = null
		},
		/**
		 * Create a new calendar type and add focus to the title field automatically
		 * The calendar type is not yet saved, therefore an id of -1 is used until it
		 * has been persisted in the backend
		 */
		newCalendarType() {
			if (this.currentCalTypeID !== -1) {
				const alreadyStartedCreating = this.calTypes.findIndex((calType) => calType.id === -1)
				if (alreadyStartedCreating === -1) {
					this.currentCalTypeID = -1
					this.calTypes.push({
						id: -1,
						title: '',
						description: '',
					})
					this.$nextTick(() => {
						this.$refs.title.focus()
					})
				} else {
					this.currentCalTypeID = -1
					this.$refs.title.focus()
				}
			}
		},
		/**
		 * Action triggered when clicking the save button
		 * create a new calendar type or update existing
		 */
		saveCalType() {
			if (this.currentCalTypeID === -1) {
				this.createCalType(this.currentCalType)
			} else {
				this.updateCalType(this.currentCalType)
			}
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
				OCP.Toast.error(t('elbcaltypes', 'Could not create a new calendar type'))
			}
			this.updating = false
		},
		/**
		 * Update a existing calendar type on the server
		 * @param {Object} calType calType object
		 */
		async updateCalType(calType) {
			this.updating = true
			try {
				await axios.put(OC.generateUrl(`/apps/elb_cal_types/caltypes/${calType.id}`), calType)
			} catch (e) {
				console.error(e)
				OCP.Toast.error(t('elbcaltypes', 'Could not update the calendar type'))
			}
			this.updating = false
		},
		/**
		 * Delete a calendar type, remove it from the frontend and show a hint
		 * @param {Object} calType calType object
		 */
		async deleteCalType(calType) {
			if (confirm(t('elbcaltypes', 'Really delete') + '?')) {
				try {
					await axios.delete(OC.generateUrl(`/apps/elb_cal_types/caltypes/${calType.id}`))
					this.calTypes.splice(this.calTypes.indexOf(calType), 1)
					if (this.currentCalTypeID === calType.id) {
						this.currentCalTypeID = null
					}
					OCP.Toast.success(t('elbcaltypes', 'Calendar type has been deleted'))
				} catch (e) {
					console.error(e)
					OCP.Toast.error(t('elbcaltypes', 'Could not delete the calendar type'))
				}
			} else {
				return false
			}
		},
		/**
		 * Set property to show/hide sidebar
		 */
		toggleSidebar() {
			this.selectedOpenSidebar = !this.selectedOpenSidebar
		},
		/**
		 * Assign selected default reminder(s) to the selected calendar type
		 */
		async saveRemindersForCalendarType() {

			if (this.modelDefaultCalReminder.length) {

				const data = {
					caltypeid: this.currentCalTypeID,
					reminders: this.modelDefaultCalReminder,
				}

				try {
					await axios.post(OC.generateUrl('/apps/elb_cal_types/assigndefreminderstocaltype'), data)
					this.fetchAssignedReminders()
					this.uncheckSelectedAvailableReminders()
				} catch (e) {
					console.error(e)
					OCP.Toast.error(t('elbcaltypes', 'Could not assign reminder(s)'))
				}

			} else {
				alert(t('elbcaltypes', 'Please select at least one reminder'))
			}
		},
		/**
		 * Delete assigned reminder for a calendar type by it's link id
		 * @param {int} id Id of calendar type
		 * @returns {Promise<void>}
		 */
		async removeReminderForCalendarType(id) {

			const data = {
				caltypeid: this.currentCalTypeID,
				caltyperemid: id,
			}

			try {
				await axios.post(OC.generateUrl('/apps/elb_cal_types/removereminderforcaltype'), data)
				this.$delete(this.assignedRemForCalTypes[this.currentCalTypeID], id)
			} catch (e) {
				console.error(e)
				OCP.Toast.error(t('elbcaltypes', 'Could not remove reminder for calendar type'))
			}
		},
		/**
		 * Assign selected group folder(s) to the selected calendar type
		 */
		async saveGroupFoldersForCalendarType() {

			if (this.modelGroupFolder.length) {

				const data = {
					caltypeid: this.currentCalTypeID,
					groupfolders: this.modelGroupFolder,
				}

				try {
					await axios.post(OC.generateUrl('/apps/elb_cal_types/assigngroupfolderstocaltype'), data)
					this.fetchAssignedGroupFolders()
					this.uncheckSelectedGroupFolders()
				} catch (e) {
					console.error(e)
					OCP.Toast.error(t('elbcaltypes', 'Could not assign group folder(s)'))
				}

			} else {
				alert(t('elbcaltypes', 'Please select at least one group folder'))
			}
		},
		/**
		 * Delete assigned group folder for the calendar type
		 * @param {int} gfID ID of group folder
		 * @returns {Promise<void>}
		 */
		async removeGroupFolderForCalendarType(gfID) {

			let linkID = false

			Object.keys(this.assignedGroupFoldersForCalTypes[this.currentCalTypeID]).forEach(key => {
				const gfObj = this.assignedGroupFoldersForCalTypes[this.currentCalTypeID][key]

				if (gfObj.gf_id === gfID) {
					linkID = key
					return true
				}
			})

			const data = {
				caltypeid: this.currentCalTypeID,
				linkid: linkID,
			}

			try {
				await axios.post(OC.generateUrl('/apps/elb_cal_types/removegroupfolderforcaltype'), data)
				this.$delete(this.assignedGroupFoldersForCalTypes[this.currentCalTypeID], linkID)
			} catch (e) {
				console.error(e)
				OCP.Toast.error(t('elbcaltypes', 'Could not remove assigned group folder for calendar type'))
			}
		},
		/**
		 * Check up if selected calendar type has at least one assigned reminder
		 * @returns {Integer}
		 */
		checkAssignedRemindersForCalTypeID() {
			if (this.assignedRemForCalTypes[this.currentCalTypeID] !== undefined) {
				return this.assignedRemForCalTypes[this.currentCalTypeID]
			}
			return false
		},
		changeView() {
			this.$emit('perform-switch')
		},
	},
}
</script>
<style scoped>
.nav-title-field {
	display: inline-block;
	padding: 10px 10px 10px 20px;
	background-color: #EDEDED;
	border: 1px solid #DBDBDB;
	margin: 10px 10px;
	font-weight: bold;
}
ul.reminders-for-cal-type,
ul.group-folders-for-cal-type {
	padding: 10px 4px;
}
ul.assigned-reminders-for-cal-type li {
	padding: 6px 0;
}
ul.assigned-reminders-for-cal-type button {
	background-color: transparent;
	opacity: 1;
	border: none;
}
ul.group-folders-for-cal-type button.icon-delete {
	background-color: transparent;
	opacity: 1;
	border: none;
}
ul.reminders-for-cal-type li,
ul.group-folders-for-cal-type li {
	padding: 3px 0;
}
#app-content {
	padding: 20px;
}
#app-content > div {
	width: 100%;
	height: 100%;
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
.app-sidebar-tab__buttons button {
	width: 100%;
}
#content #app-sidebar .app-sidebar-header > .app-sidebar__close.icon-close {
	top: 10px;
}
#app-sidebar hr {
	opacity: 0.1;
}
</style>
