<template>
	<ManageCalendarTypes />
</template>

<script>
import ManageCalendarTypes from './components/ManageCalendarTypes'
import ManageCalendarTypesEvents from './components/ManageCalendarTypesEvents'

export default {
	name: 'App',
	components: {
		ManageCalendarTypes,
		ManageCalendarTypesEvents,
	},
}
</script>
