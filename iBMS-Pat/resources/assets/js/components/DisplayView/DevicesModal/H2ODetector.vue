<template>
	<div class="row">
		<div class='col-md-3'>
		</div>
		<div class='col-md-6'>
			<img :src="status==true ? '/img/devices/water_leak_on.png' : '/img/devices/water_leak_off.png'" class='device-icon center-block'>
		</div>
		<div class='col-md-3'>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default{
		components:{

		},
		props:{
			device:'',								//Device data from Room.vue > DeviceList.vue > Current Vue
		},
		data(){
			return{
				status:false,						//Device default status
				device_type:'h2o_detector',			//Device Type
			}
		},
		created(){
			this.getDataFromDatabase();
			//Lisening on event updateDeviceData Event from Room.vue
			this.$bus.$on('updateDeviceData-' + this.device['DEVICE_ID'],data=>{
				//&& data['DEVICE_ID'] == this.devices[data['KEY']]['DEVICE_ID']
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
			//Function Description: Change displayed image to indicate h2o detector status
			//Param: data (device)
			readDeviceData(data){
				var status = data['DATA']['status'];
				if(status ==  1){

					this.status = true;

				}else if(status  ==  0){

					this.status = false;

				}else{
					console.log("Can't read "+ this.device_type +" Data");
				}
			}


		}
	};
</script>