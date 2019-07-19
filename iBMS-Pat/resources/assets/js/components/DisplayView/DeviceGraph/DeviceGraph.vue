<!-- Edited: December 7, 2018 By: Jethro -->
<!-- Updated: March 26, 2019 By: Jethro-->
<template>
		<div v-if="device['DEVICE_TYPE'] != 'ir_remote'">
			<deviceStatusChart :device="device"></deviceStatusChart>
		</div>
		<div v-else-if="device['DEVICE_TYPE'] == 'ir_remote'">
			<RemoteLog :device="device"></RemoteLog>
		</div>
		<div v-else>
			{{$t('devop.nostatus')}}
		</div>
</template>
<script type="text/javascript">
	import deviceStatusChart from './DeviceStatusChart.vue';
	import RemoteLog from './RemoteLog.vue';
	export default{
		components: {
			deviceStatusChart,RemoteLog
		},
		props:{
			//from DeviceOperation.vue
			device:'',
		},
	}
</script>