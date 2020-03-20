<template>
    <div id="content" class="app-elb-cal-types">

        <AppNavigation>
            <AppNavigationNew v-if="permissionToManageCalendarTypes"
                              :text="t('elbcaltypes', 'New calendar type')"
                              :disabled="false"
                              button-id="new-caltype-button"
                              button-class="icon-add"
                              @click="newCalendarType" />

            <ul v-if="permissionToManageCalendarTypes">
                <AppNavigationItem v-for="calType in calTypes" :key="calType.id" :item="calTypeEntry(calType)" :icon="'icon-user'" />
            </ul>

            <AppNavigationSettings>
                Example settings
            </AppNavigationSettings>
        </AppNavigation>

        <AppContent>

            <h2 class="pull-left" v-if="currentCalTypeID === -1">
                {{ t('elbcaltypes', 'Create a new calendar type') }}
            </h2>

            <h2 class="pull-left" v-else-if="currentCalTypeID > 0">
                {{ t('elbcaltypes', 'Update calendar type') }}
            </h2>

            <button v-show="showToggleSidebarButton" @click="toggleSidebar" class="pull-right">
                {{ t('elbcaltypes', 'Toggle sidebar') }}
            </button>

            <div v-if="currentCalType">
                <input ref="title"
                       :placeholder="t('elbcaltypes', 'Name for calendar type')"
                       v-model="currentCalType.title"
                       type="text"
                       :disabled="updating">
                <textarea ref="content" v-model="currentCalType.description" :disabled="updating" rows="15" :placeholder="t('elbcaltypes', 'Description for calendar type')"/>
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
                    :title="t('elbcaltypes', 'Manage reminders for calendar type')"
                    :subtitle="getTitleOfCurrentCalType"
                    @close="toggleSidebar">

            <AppSidebarTab id="avail-reminders" :name="t('elbcaltypes', 'Available reminders')" icon="icon-edit">

                <div v-if="defaultCalReminders.length">
                    <p>
                        {{ t('elbcaltypes', 'Select reminder to assign it to the selected calendar type') }}
                    </p>
                    <ul class="reminders-for-cal-type">
                        <li v-for="defCal in defaultCalReminders">
                            <input :id="'link-checkbox'+ defCal.id"
                                   name="link-checkbox[]"
                                   class="checkbox link-checkbox"
                                   type="checkbox">
                            <label :for="'link-checkbox'+ defCal.id" class="link-checkbox-label">{{ t('elbcaltypes', defCal.title) }}</label>
                        </li>
                    </ul>
                </div>
                <div v-else>
                    {{ t('elbcaltypes', 'No default reminders available to assign it to the selected calendar type') }}
                </div>

            </AppSidebarTab>

            <AppSidebarTab id="assigned-reminders" :name="t('elbcaltypes', 'Assigned reminders')" icon="icon-edit" >
                Assigned reminders
            </AppSidebarTab>

        </AppSidebar>

    </div>
</template>

<script>
import {
    AppContent,
    AppNavigation,
    AppNavigationItem,
    AppNavigationNew,
    AppNavigationSettings,
    Multiselect,
	AppSidebar,
	AppSidebarTab,
    ActionCheckbox,
} from 'nextcloud-vue'

import axios from '@nextcloud/axios'

export default {
    name: 'App',
    components: {
        AppContent,
        AppNavigation,
        AppNavigationItem,
        AppNavigationNew,
        AppNavigationSettings,
        Multiselect,
		AppSidebar,
		AppSidebarTab,
		ActionCheckbox
    },
    data: function() {
        return {
            calTypes: [],
            currentCalTypeID: null,
            loading: true,
			updating: false,
			isAdminUser: false,
            defaultCalReminders: [],
			selectedOpenSidebar: false
        }
    },
	beforeMount() {
        // Perform ajax call to check up if current logged in user belongs to the super admin user group
		axios.post(OC.generateUrl('/apps/elb_cal_types/isusersuperadmin')).then((result) => {
			this.isAdminUser = result.data.isSuperAdmin
		}),
		// Perform ajax call to fetch default reminders
		axios.post(OC.generateUrl('/apps/elb_cal_types/getallreminders')).then((result) => {
			console.log('Result get all reminders: ', result);
			this.defaultCalReminders = result.data
		})
	},
    computed: {
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
         * Check up if managing calendar types is allowed
         * @returns {Boolean}
         */
    	permissionToManageCalendarTypes() {
            return (!this.loading && this.isAdminUser)
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
        /**
         * Returns true if a calendar type is selected and its title is not empty
         * @returns {Boolean}
         */
        savePossible() {
            return this.currentCalType && this.currentCalType.title !== ''
        },
    },
    /**
     * Fetch list of calendar types when the component is loaded
     */
    async mounted() {
        try {
        	//if (this.isAdminUser) {
				const response = await axios.get(OC.generateUrl('/apps/elb_cal_types/caltypes'))
				this.calTypes = response.data
			//}
        } catch (e) {
            console.error(e)
            //OCP.Toast.error(t('elbcaltypes', 'Could not fetch calendar types'))
        }
        this.loading = false
    },
    methods: {
		/**
		 * Create a new calendar type and focus the calendar type content field automatically
		 * @param {Object} calType calType object
		 */
		openCalType(calType) {
			if (this.updating) {
				return
			}
			this.currentCalTypeID = calType.id
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
		newCalendarType(e) {
			if (this.currentCalTypeID !== -1) {
				let alreadyStartedCreating = this.calTypes.findIndex((calType) => calType.id == -1)
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
				OCP.Toast.error(t('elb_cal_types', 'Could not create a new calendar type'))
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
				OCP.Toast.error(t('elb_cal_types', 'Could not update the calendar type'))
			}
			this.updating = false
		},
		/**
		 * Delete a calendar type, remove it from the frontend and show a hint
		 * @param {Object} calType calType object
		 */
		async deleteCalType(calType) {
			if (confirm(t('elb_cal_types', 'Really delete' + '?'))) {
				try {
					await axios.delete(OC.generateUrl(`/apps/elb_cal_types/caltypes/${calType.id}`))
					this.calTypes.splice(this.calTypes.indexOf(calType), 1)
					if (this.currentCalTypeID === calType.id) {
						this.currentCalTypeID = null
					}
					OCP.Toast.success(t('elb_cal_types', 'Calendar type has been deleted'))
				} catch (e) {
					console.error(e)
					OCP.Toast.error(t('elb_cal_types', 'Could not delete the calendar type'))
				}
			} else {
				return false;
            }
		},
		/**
         * Set property to show/hide sidebar
		 */
		toggleSidebar() {
			this.selectedOpenSidebar = !this.selectedOpenSidebar
        }
	},
}
</script>
<style scoped>
    ul.reminders-for-cal-type {
        padding: 10px 4px;
    }
    ul.reminders-for-cal-type li {
        padding: 3px 0;
    }
    #app-content > div {
        width: 100%;
        height: 100%;
        padding: 20px;
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
</style>
