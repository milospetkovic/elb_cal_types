<template>
	<div id="content" class="app-elb-cal-types-events">
		<AppNavigation>
			<div class="nav-title-field">
				<h4>{{ t('elbcaltypes', 'Assigned calendar types') }}</h4>
			</div>

			<template v-if="allowsuperadminswitch">
				<SwitchViewButton v-on:perform-switch="changeView" />
			</template>

			<AppNavigationNew
				:text="t('elbcaltypes', 'Create event')"
				:disabled="disableCreateNewEventButton"
				button-id="new-caltype-event-button"
				button-class="icon-add"
				:title="t('elbcaltypes', 'Create and assign calendar event with reminders to users')"
				@click="newCalendarTypeEvent" />

			<ul>
				<template v-for="calType in assignedCalendarTypes">
					<AppNavigationItem :key="calType.id" :item="calTypeEntry(calType)" icon="icon-user" />
				</template>
			</ul>
		</AppNavigation>

		<AppContent>
			<div v-if="currentCalTypeLinkID">
				<h2>{{ t('elbcaltypes', 'Manage events for selected calendar type') }}: {{ calTypeEntryItemName(assignedCalendarTypes[currentCalTypeLinkID]) }}</h2>
			</div>
			<div v-else>
				<h2>{{ t('elbcaltypes', 'Please choose calendar type from the left menu to manage events') }}</h2>
			</div>

			<div v-if="visibleCreateNewEventForm">
				<hr class="elb-hr-sep">
				<h3 class="font-bold">
					{{ t('elbcaltypes', 'Form for creating event for selected calendar type') }}
				</h3>
				<div class="table-responsive">
					<table class="table elb-create-cal-event-table">
						<thead>
							<tr>
								<th class="col1">
									{{ t('elbcaltypes', 'Event attributes') }}
								</th>
								<th class="col2">
									{{ t('elbcaltypes', 'Values') }}
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="col1">
									{{ t('elbcaltypes', 'Event name') }}
								</td>
								<td class="col2">
									<input v-model="eventTitle" name="eventname" type="text">
								</td>
							</tr>

							<tr>
								<td class="col1">
									{{ t('elbcaltypes', 'Event description') }}
								</td>
								<td class="col2">
									<textarea v-model="eventDescription" name="eventdescription" />
								</td>
							</tr>

							<tr>
								<td class="col1">
									{{ t('elbcaltypes', 'Event date and time') }}
								</td>
								<td class="col2">
									<template>
										<span>
											<DatetimePicker
												v-model="eventDateTime"
												type="datetime"
												:default-value="new Date()"
												:clearable="true"
												:format="'DD.MM.YYYY HH:mm'"
												:show-second="false"
												:time-select-options="{minutes: [0,5,10,15,20,25,30,35,40,45,50,55]}"
												:lang="'en'"
												:first-day-of-week="1"
												:not-before="new Date()" />
										</span>
									</template>
								</td>
							</tr>

							<tr>
								<td class="col1">
									{{ t('elbcaltypes', 'Event end date and time (optional)') }}
								</td>
								<td class="col2">
									<template>
										<span>
											<DatetimePicker
												v-model="eventEndDateTime"
												type="datetime"
												:default-value="new Date()"
												:clearable="true"
												:format="'DD.MM.YYYY HH:mm'"
												:show-second="false"
												:time-select-options="{minutes: [0,5,10,15,20,25,30,35,40,45,50,55]}"
												:lang="'en'"
												:first-day-of-week="1"
												:not-before="new Date()" />
										</span>
									</template>
								</td>
							</tr>

							<tr>
								<td class="col1">
									{{ t('elbcaltypes', 'Reminders for event') }}
								</td>
								<td class="col2">
									<template>
										<div class="wrapper">
											<Multiselect v-model="preselectedCalReminders"
												track-by="id"
												:options="optionsForCalReminders"
												:multiple="true"
												:tag-width="200"
												:close-on-select="false"
												label="name" />
										</div>
									</template>
								</td>
							</tr>
							<tr>
								<td class="col1">
									{{ t('elbcaltypes', 'Assign users for event') }}
								</td>
								<td class="col2">
									<template>
										<div class="wrapper">
											<Multiselect v-model="usersForEvent"
												track-by="userID"
												:options="availableUsersForCalType"
												:multiple="true"
												:tag-width="200"
												:close-on-select="false"
												:custom-label="showUserWithGroups" />
										</div>
									</template>
								</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td />
								<td>
									<button class="button primary" @click="saveNewEvent">
										{{ t('elbcaltypes', 'Save') }}
									</button>

									<button @click="cancelCreateNewEvent">
										{{ t('elbcaltypes', 'Cancel') }}
									</button>
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
				<hr class="elb-hr-sep">
			</div>
			<div v-if="calTypeEvents">
				<h3 class="font-bold">
					{{ t('elbcaltypes', 'Created events for selected calendar type') }}
				</h3>

				<div class="table-responsive">
					<table class="table elb-events-table">
						<thead>
							<tr>
								<th>{{ t('elbcaltypes', 'ID') }}</th>
								<th>{{ t('elbcaltypes', 'Event title') }}</th>
								<th>{{ t('elbcaltypes', 'Event description') }}</th>
								<th>{{ t('elbcaltypes', 'Event datetime') }}</th>
								<th>{{ t('elbcaltypes', 'Event end datetime') }}</th>
								<th>{{ t('elbcaltypes', 'Event assigned users') }}</th>
								<th>{{ t('elbcaltypes', 'Event reminders') }}</th>
								<th>{{ t('elbcaltypes', 'Event executed') }}</th>
								<th>{{ t('elbcaltypes', 'Event actions') }}</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="calEvent in calTypeEvents" :key="calEvent.link_id">
								<td>{{ calEvent.link_id }}.</td>
								<td>{{ calEvent.event_title }}</td>
								<td>{{ calEvent.event_description }}</td>
								<td>{{ calEvent.event_datetime }}</td>
								<td>{{ calEvent.event_end_datetime }}</td>
								<td>
									<div v-for="eventAssignedUser in calEvent.event_assigned_users" :key="eventAssignedUser" class="item-as-box">
										{{ eventAssignedUser }}
									</div>
								</td>
								<td>
									<div v-for="eventReminder in calEvent.event_assigned_reminders" :key="eventReminder.def_reminder_title" class="item-as-box">
										{{ eventReminder.def_reminder_title_translated }}
									</div>
								</td>
								<td>
									<template v-if="calEvent.event_executed">
										<span :title="calEvent.event_executed">{{ t('elbcaltypes', 'Yes') }}</span>
									</template>
									<template v-else>
										<span>{{ t('elbcaltypes', 'No') }}</span>
									</template>
								</td>
								<td>
									<input v-if="isEventNonExecuted(calEvent)"
										type="button"
										class="primary pull-left"
										:value="t('elbcaltypes', 'Execute event')"
										@click="saveCalendarEventForUsers(calEvent.link_id)">
									<button v-if="isEventNonExecuted(calEvent)" class="icon-delete pull-right" @click="deleteCalendarTypeEvent(calEvent.link_id)" />
								</td>
							</tr>
						</tbody>
					</table>
				</div>
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
	DatetimePicker,
	Multiselect,
} from 'nextcloud-vue'

import axios from '@nextcloud/axios'
import SwitchViewButton from './SwitchViewButton'

export default {
	name: 'ManageCalendarTypesEvents',
	components: {
		AppNavigation,
		AppNavigationNew,
		AppNavigationItem,
		AppContent,
		DatetimePicker,
		Multiselect,
		SwitchViewButton,
	},
	props: {
		allowsuperadminswitch: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			assignedCalendarTypes: [],
			currentCalTypeID: null,
			currentCalTypeLinkID: null,
			currentCalTypeGroupFolderID: null,
			visibleCreateNewEventForm: false,
			eventTitle: null,
			eventDescription: null,
			eventDateTime: null,
			eventEndDateTime: null,
			defaultCalReminders: [],
			eventReminders: null,
			usersForEvent: null,
			allUsersPerGroupFolders: null,
			availableUsersForCalType: null,
			defAssignedRemindersForCalTypes: [],
			preselectedCalReminders: null,
			optionsForCalReminders: [],
			nrOfGroupFolders: 0,
			calTypeEvents: null,
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
					text: this.calTypeEntryItemName(calType),
					action: () => this.openCalType(calType),
					classes: this.currentCalTypeLinkID === calType.link_id ? 'active' : '',
					bullet: '#0082c9',
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
		// perform ajax call to fetch assigned calendar types for group folder which the logged in user belongs to
		axios.get(OC.generateUrl('/apps/elb_cal_types/getassignedcalendartypes')).then((result) => {
			this.assignedCalendarTypes = result.data.assigned_calendars
			if (result.data.users_per_group_folder !== undefined) {
				this.allUsersPerGroupFolders = result.data.users_per_group_folder
			}

			this.nrOfGroupFolders = result.data.nr_of_group_folders
			const calTypesIds = []
			if (this.assignedCalendarTypes) {
				Object.keys(this.assignedCalendarTypes).forEach(key => {
					const obj = this.assignedCalendarTypes[key]
					calTypesIds.push(obj.cal_type_id)
				})
			}
			if (calTypesIds.length) {
				const data = {
					calTypesIds: calTypesIds,
				}
				// perform ajax call to fetch assigned reminders for calendar types
				axios.post(OC.generateUrl('/apps/elb_cal_types/getassignedremindersforcaltypesids'), data).then((result) => {
					this.defAssignedRemindersForCalTypes = result.data
				})
			}
		})
		// perform ajax call to fetch default reminders
		axios.post(OC.generateUrl('/apps/elb_cal_types/getdefaultreminders')).then((result) => {
			this.defaultCalReminders = result.data
			this.populateOptionsForCalReminders()
		})
	},
	methods: {
		openCalType(calType) {
			this.visibleCreateNewEventForm = false
			this.currentCalTypeLinkID = calType.link_id
			this.currentCalTypeID = calType.cal_type_id
			this.currentCalTypeGroupFolderID = calType.group_folder_id
			this.populatePreselectedCalReminders()
			this.populateEventTitleForCreateEventForm()
			this.populateEventDescriptionForCreateEventForm()
			this.populateAvailableUsers()
			this.getCalTypeEvents()
		},
		calTypeEntryItemName(calType) {
			if (this.nrOfGroupFolders > 1) {
				return calType.cal_type_title + ' (' + calType.group_folder_name + ')'
			}
			return calType.cal_type_title
		},
		populateEventTitleForCreateEventForm() {
			this.eventTitle = this.assignedCalendarTypes[this.currentCalTypeLinkID]['cal_type_title']
		},
		populateEventDescriptionForCreateEventForm() {
			this.eventDescription = this.assignedCalendarTypes[this.currentCalTypeLinkID]['cal_type_description']
		},
		populateAvailableUsers() {
			this.availableUsersForCalType = null
			const ret = []
			Object.keys(this.allUsersPerGroupFolders).forEach(key => {
				const obj = this.allUsersPerGroupFolders[key]
				Object.keys(obj).forEach(key2 => {
					const obj2 = obj[key2]
					if (key2 === this.currentCalTypeGroupFolderID) {
						Object.keys(obj2).forEach(key3 => {
							const valObj3 = obj2[key3]
							ret.push({ 'userID': key3, 'userGroups': valObj3 })
						})
					}
				})
			})
			if (ret.length) {
				this.availableUsersForCalType = ret
			}
		},
		newCalendarTypeEvent() {
			this.visibleCreateNewEventForm = true
		},
		cancelCreateNewEvent() {
			if (confirm(t('elbcaltypes', 'Really cancel') + '?')) {
				this.visibleCreateNewEventForm = false
			}
		},
		async saveNewEvent() {
			let res = null
			const data = {
				caltypeid: this.currentCalTypeID,
				caltypelinkid: this.currentCalTypeLinkID,
				eventname: this.eventTitle,
				eventdesc: this.eventDescription,
				eventdatetime: (this.eventDateTime !== null) ? this.eventDateTime : null,
				eventenddatetime: (this.eventEndDateTime !== null) ? this.eventEndDateTime : null,
				timezoneoffset: (this.eventDateTime !== null) ? this.eventDateTime.getTimezoneOffset() : null,
				reminders: this.preselectedCalReminders,
				assignedusers: this.usersForEvent,
			}
			try {
				res = await axios.post(OC.generateUrl('/apps/elb_cal_types/saveacalendartypeevent'), data)
				if (res.data.error) {
					OC.dialogs.alert(
						t('elbcaltypes', res.data.error_msg),
						t('elbcaltypes', 'Error')
					)
				} else {
					this.getCalTypeEvents()
					OC.dialogs.info(
						t('elbcaltypes', 'Calendar event has been saved. Please press \'Execute event\' button to save calendar event for each assigned user.'),
						t('elbcaltypes', 'Success')
					)
				}
			} catch (e) {
				console.error(e)
				OCP.Toast.error(t('elbcaltypes', 'Could not save event for calendar type'))
			}
		},
		populatePreselectedCalReminders() {
			this.preselectedCalReminders = null
			const currentCalTypeID = this.assignedCalendarTypes[this.currentCalTypeLinkID]['cal_type_id']
			if (this.defAssignedRemindersForCalTypes !== undefined) {
				if (this.defAssignedRemindersForCalTypes[currentCalTypeID] !== undefined) {
					const ret = []
					Object.keys(this.defAssignedRemindersForCalTypes[currentCalTypeID]).forEach(key => {
						const assCalTypeRem = this.defAssignedRemindersForCalTypes[currentCalTypeID][key]
						ret.push({ 'id': parseInt(assCalTypeRem.cal_def_reminder_id), 'name': assCalTypeRem.cal_def_reminder_title_trans })
					})
					this.preselectedCalReminders = ret
				}
			}
		},
		populateOptionsForCalReminders() {
			const ret = []
			Object.keys(this.defaultCalReminders).forEach(key => {
				const defCalRem = this.defaultCalReminders[key]
				ret[key] = { 'id': defCalRem.id, 'name': defCalRem.title }
			})
			this.optionsForCalReminders = ret
		},
		showUserWithGroups({ userID, userGroups }) {
			return `${userID} - ${userGroups}`
		},
		getCalTypeEvents() {
			this.calTypeEvents = null
			const data = {
				caltypeid: this.currentCalTypeID,
				caltypelinkid: this.currentCalTypeLinkID,
			}
			try {
				axios.post(OC.generateUrl('/apps/elb_cal_types/getcalendartypeevents'), data).then((result) => {
					this.calTypeEvents = result.data
					if (this.calTypeEvents) { // show latest events on the top
						const obj = result.data
						let arr = Object.keys(obj).map(function(key) {
							return obj[key]
						})
						arr = arr.reverse()
						this.calTypeEvents = arr
					}
				})
			} catch (e) {
				console.error(e)
				OCP.Toast.error(t('elbcaltypes', 'Could not get events for calendar type'))
			}
		},
		saveCalendarEventForUsers(id) {
			const data = {
				caltypeeventid: id,
			}
			try {
				axios.post(OC.generateUrl('/apps/elb_cal_types/saveacalendareventforusers'), data).then((result) => {
					if (!result.data) {
						OC.dialogs.alert(t('elbcaltypes', 'Could not save calendar event for users for calendar type event'), t('elbcaltypes', 'Error'))
					} else {
						this.getCalTypeEvents()
						OC.dialogs.info(t('elbcaltypes', 'Calendar event has been saved for assigned users'), t('elbcaltypes', 'Success'))
					}
				})
			} catch (e) {
				console.error(e)
				OC.dialogs.alert(t('elbcaltypes', 'Could not save calendar event for users for calendar type event'), t('elbcaltypes', 'Error'))
			}
		},
		deleteCalendarTypeEvent(id) {
			if (confirm(t('elbcaltypes', 'Really delete') + '?')) {
				const data = {
					linkid: id,
				}
				try {
					axios.post(OC.generateUrl('/apps/elb_cal_types/deletecaltypeevent'), data)
					this.getCalTypeEvents()
				} catch (e) {
					console.error(e)
					OCP.Toast.error(t('elbcaltypes', 'Could not delete event for calendar type'))
				}
			}
		},
		isEventNonExecuted(calEvent) {
			if (calEvent.event_executed) {
				return false
			}
			return true
		},
		convertObjectToArray(obj) {
			return Object.keys(obj).map(function(key) {
				return [Number(key), obj[key]]
			})
		},
		changeView() {
			this.$emit('perform-switch')
		},
	},
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
td.text-right {
	text-align: right;
}
/*.checkbox-label {
	display: block;
}*/
.elb-events-table .icon-delete {
   min-width: 35px;
   min-height: 35px;
   background-color: transparent;
}
.elb-events-table .icon-delete:hover {
	border-color: #f00;
}
.item-as-box {
	background-color: #ededed;
	border: 1px solid #dbdbdb;
	border-radius: 2px;
	box-sizing: border-box;
	margin: 2px;
	padding: 3px;
}
.elb-hr-sep {
	border-bottom: 1px solid #dbdbdb;
	border-top: 0;
	margin: 15px 0;
}
.font-bold {
	font-weight: 700;
}
.elb-create-cal-event-table {
	width: 100%;
}
.elb-create-cal-event-table thead th {
	padding: 5px;
	font-weight: bold;
	background-color: #0082c9;
	color: #fff;
	border-right: 1px solid #fff;
	border-bottom: 1px solid #dbdbdb;
}
.elb-create-cal-event-table .col1 {
	width: 15%;
	text-align: right;
}
.elb-create-cal-event-table .col2 {
	width: 85%;
}
.elb-create-cal-event-table td.col1 {
	vertical-align: top;
	padding: 8px 5px;
	font-weight: 500;
}
.elb-create-cal-event-table td.col2 {
	padding: 3px;
}
input[type="text"] {
	width: 100%;
}
textarea,
div.multiselect {
	width: 100%;
}
</style>
