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
					<AppNavigationItem :key="calType.id" :item="calTypeEntry(calType)" icon="icon-user" />
				</template>
			</ul>
		</AppNavigation>

		<AppContent>
			<div v-if="currentCalTypeLinkID">
				<h2>{{ t('elb_cal_types', 'Manage events for selected calendar type') }}: {{ assignedCalendarTypes[currentCalTypeLinkID]['cal_type_title'] }}</h2>
			</div>
			<div v-else>
				<h2>{{ t('elb_cal_types', 'Please choose calendar type from the left menu to manage events') }}</h2>
			</div>

			<div v-if="visibleCreateNewEventForm">
				<div class="table-responsive">
					<table class="table" width="1000">
						<thead>
							<tr>
								<th>{{ t('elb_cal_types', 'Event attributes') }}</th>
								<th>{{ t('elb_cal_types', 'Values') }}</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="text-right">
                                    {{ t('elb_cal_types', 'Event name') }}
                                </td>
								<td>
                                    <input name="eventname" v-model="eventTitle" type="text" >
                                </td>
							</tr>

							<tr>
								<td class="text-right">
                                    {{ t('elb_cal_types', 'Event description') }}
                                </td>
								<td>
									<textarea name="eventdescription" />
								</td>
							</tr>

							<tr>
								<td class="text-right">
                                    {{ t('elb_cal_types', 'Event date and time') }}
                                </td>
								<td>
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
                                                :not-before="new Date()"/>
                                        </span>
                                    </template>
                                </td>
							</tr>

							<tr>
								<td class="text-right">
                                    {{ t('elb_cal_types', 'Reminders for event') }}
                                </td>
								<td>
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
								<td class="text-right">
                                    {{ t('elb_cal_types', 'Assign users for event') }}
                                </td>
								<td></td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td />
								<td>
									<button class="button primary" @click="saveNewEvent">
										{{ t('elb_cal_types', 'Save') }}
									</button>

									<button @click="cancelCreateNewEvent">
										{{ t('elb_cal_types', 'Cancel') }}
									</button>
								</td>
							</tr>
						</tfoot>
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

export default {
	name: 'ManageCalendarTypesEvents',
	components: {
		AppNavigation,
		AppNavigationNew,
		AppNavigationItem,
		AppContent,
		DatetimePicker,
		Multiselect,
	},
	data() {
		return {
			assignedCalendarTypes: [],
			currentCalTypeLinkID: null,
			visibleCreateNewEventForm: false,
            eventTitle: null,
            eventDateTime: null,
			defaultCalReminders: [],
			eventReminders: null,
            defAssignedRemindersForCalTypes: [],
			preselectedCalReminders: null,
			optionsForCalReminders: []
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
		// perform ajax call to fetch assigned calendar types for group folder which the logged in user belongs to
		axios.get(OC.generateUrl('/apps/elb_cal_types/getassignedcalendartypes')).then((result) => {
			this.assignedCalendarTypes = result.data
			let calTypesIds = []
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
					console.log('defAssignedRemindersForCalTypes: ', this.defAssignedRemindersForCalTypes)
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
            this.populatePreselectedCalReminders()
            this.populateTitleForCreateEventForm()
		},
		populateTitleForCreateEventForm() {
            this.eventTitle = this.assignedCalendarTypes[this.currentCalTypeLinkID]['cal_type_title']
        },
		newCalendarTypeEvent() {
			this.visibleCreateNewEventForm = true
		},
		cancelCreateNewEvent() {
			if (confirm(t('elb_cal_types', 'Really cancel') + '?')) {
				this.visibleCreateNewEventForm = false
			}
		},
		async saveNewEvent() {
			alert('save event')
			const data = {
				caltypeid: this.currentCalTypeID,
                eventname: this.eventname,
				reminders: this.preselectedCalReminders,
			}

			console.log('data for save event: ', data)

			try {
				res = await axios.post(OC.generateUrl('/apps/elb_cal_types/saveacalendartypeevent'), data)

                console.log('response for save event: ', res)
			} catch (e) {
				console.error(e)
				OCP.Toast.error(t('elb_cal_types', 'Could not save event for calendar type'))
			}
		},
		populatePreselectedCalReminders() {
			this.preselectedCalReminders = null
            let currentCalTypeID = this.assignedCalendarTypes[this.currentCalTypeLinkID]['cal_type_id']
			if (this.defAssignedRemindersForCalTypes !== undefined) {
				if (this.defAssignedRemindersForCalTypes[currentCalTypeID] !== undefined) {
					const ret = []
					Object.keys(this.defAssignedRemindersForCalTypes[currentCalTypeID]).forEach(key => {
						console.log('ulazi')
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
		// customLabel (option) {
		// 	return `${option.library} - ${option.language}`
		// },
		// onSelect (option) {
		// 	console.log("Added");
		// 	option.checked = true;
		// 	console.log(option.library + "  Clicked!! " + option.checked);
		// },
		// onRemove (option) {
		// 	console.log("Removed");
		// 	option.checked = false;
		// 	console.log(option.library + "  Removed!! " + option.checked);
		// },
		// multiSelectLoad(option) {
		// 	console.log('load called!!')
        // },
		// toggleSelected(value, id) {
		// 	//alert('Selected option for reminders for event ' + value.name)
		// }
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
.checkbox-label {
    display: block;
}
.test {
    position: absolute;
    right: 1vw;
}
</style>
