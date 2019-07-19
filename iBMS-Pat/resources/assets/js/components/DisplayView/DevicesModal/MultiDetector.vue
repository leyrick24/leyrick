<template>
	<div class="row align-items-center">
		<div class='col-md-4'>
			<div class="row">
				<div class='col-md-12'>
					<div class="row">
						<div class='mx-auto'><h5>Temperature</h5></div>
					</div>
					<div class='row align-items-center'>
						<div class='col-md-4 text-center'>
							<img src="img/devices/thermometer.png" class="device-icon">
						</div>
						<div class='col-md-6 text-right'><h5>{{temp}}</h5></div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class='col-md-12'>
					<div class="row">
						<div class='mx-auto'><h5>Humidity</h5></div>
					</div>
					<div class='row align-items-center'>
						<div class='col-md-4 text-center'>
							<img src="img/devices/humidity.png" class="device-icon">
						</div>
						<div class='col-md-6 text-right'><h5>{{hum}}</h5></div>
					</div>
				</div>
			</div>
		</div>
		<div class='col-md-4'>
			<img :src="status_motion==true ? '/img/devices/motion_alarm_on.png' : '/img/devices/motion_alarm_off.png'" class='device-icon'>
		</div>
		<div class='col-md-4'>
			<img src="/img/devices/co2_level_1.png" class='device-icon'>
			<h2 class="text-center" style="margin-top:-40px">{{status_light}}</h2>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default{
		components:{

		},
		props:{
			device:''									//Device data from Room.vue > DeviceList.vue > Current Vue
		},
		data(){
			return{
				temp:'1',								//Temperature
				hum:'1',									//Humidity
				status_motion:'1',
				status_light:'1',
				device_type:'multi_detector'					//Device type

			}
		},
		created(){
			this.getDataFromDatabase();
			//Send to Room.vue to command and Save to database
			this.$bus.$on('updateDeviceData-' + this.device['DEVICE_ID'],data=>{
				if(data['DEVICE_TYPE'] == this.device_type && data['DEVICE_ID'] == this.device['DEVICE_ID']){
					this.readDeviceData(data);


				}
			});
		},
		beforeDestroy(){
			this.$bus.off('updateDeviceData-' + this.device['DEVICE_ID']);
		},
		watch:{
			device:function(){
				this.readDeviceData(this.device);
			}
		},
		methods:{
			//Function Name: getDataFromDatabase
			//Function Description: Get current device data from database
			//Param: device['DEVICE_ID']
			getDataFromDatabase(){
				axios.post('devices/' + this.device['DEVICE_ID'])
				.then(response=>{
					this.readDeviceData(response.data);

				});
			},
			//Function Name: readDeviceData
			//Function Description: Modify data to be readable
			//Param: data (device)
			readDeviceData(data){

				this.temp = data['DATA']['temp'];
				this.hum = data['DATA']['hum'];
				this.status_motion = data['DATA']['status_motion'];
				this.status_light = data['DATA']['status_light'];

			}
		}
	};
</script>