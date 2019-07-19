<template>
	<div class="row">
		<div class='col-md-3'>
		</div>
		<div class='col-md-6'>
			<img :src="status==true ? '/img/devices/smoke_detector_on.png' : '/img/devices/smoke_detector_off.png'" class='device-icon center-block'>
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
			device:'',											//Device data from Room.vue > DeviceList.vue > Current Vue
		},
		data(){
			return{
				status:false,									//Device status
				device_type:'smoke_detector',					//Device type

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
			//Function Description: Modify data to indicate device status
			//Param: data (device)
			readDeviceData(data){
				var status = data['DATA']['status'];
				if(status ==  1){

					this.status = true;
					let self = this;
					setTimeout(function(){
						self.status = false;
					},3000);

				}else if(status  ==  0){

				}else{
					console.log("Can't read "+ this.device_type +" Data");
				}
			}
		}
	};
</script>