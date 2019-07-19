<template>
	<div class="row align-items-center">
		<div class='col-md-12'>
			<div class="row">
				<div class="col-md-6">
					<div class='text-center'><h3>{{$t('devop.temperature')}}</h3></div>
					<div class='row align-items-center'>
						<div class='col-md-4 text-center'>
							<img src="img/devices/thermometer.png" class="">
						</div>
						<div class='col-md-6 text-center'><h4>{{temp}}</h4></div>
					</div>
				</div>
				<div class="col-md-6">
					<div class='text-center'><h3>{{$t('devop.hum')}}</h3></div>
					<div class='row align-items-center'>
						<div class='col-md-4 text-center'>
							<img src="img/devices/humidity.png" class="">
						</div>
						<div class='col-md-6 text-center'><h4>{{hum}}</h4></div>
					</div>
				</div>
			</div>
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
				temp:'',								//Temperature
				hum:'',									//Humidity
				device_type:'temp_hum'					//Device type
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
			//Function Description: Modify data to be readable by user
			//Param: data (device)
			readDeviceData(data){
				this.temp = data['DATA']['temp'];
				this.hum = data['DATA']['hum'];
			}
		}
	};
</script>