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
					<table class="table">
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
								<td><input name="eventname" type="text" :value="assignedCalendarTypes[currentCalTypeLinkID]['cal_type_title']"></td>
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
                                    <DatetimePicker
                                            v-model="eventdatetime"
                                            type="datetime" />
                                </td>
							</tr>

							<tr>
								<td class="text-right">
                                    {{ t('elb_cal_types', 'Reminders for event') }}
                                </td>
								<td></td>
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
			// console.log('assigned cal types: ', this.assignedCalendarTypes)
		})
	},
	methods: {
		openCalType(calType) {
			this.visibleCreateNewEventForm = false
			this.currentCalTypeLinkID = calType.link_id
		},
		newCalendarTypeEvent() {
			this.visibleCreateNewEventForm = true
		},
		cancelCreateNewEvent() {
			if (confirm(t('elb_cal_types', 'Really cancel') + '?')) {
				this.visibleCreateNewEventForm = false
			}
		},
		saveNewEvent() {
			alert('save event')
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
</style>
