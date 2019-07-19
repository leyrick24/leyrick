<template>
	<div class="row">
		<div class='col-md-6'>
			<img :src="statusImage" class='device-icon center-block'>
		</div>
		<div class='col-md-6'>
			<h2 class="text-center center-all" >{{status}} ppm</h2>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default{
		components:{

		},
		props:{
			device:'',													//Device data from Room.vue > DeviceList.vue > Current Vue
		},
		data(){
			return{
				status:'',												//Device Status
				statusImage:'',											//Device Image
				device_type:'light_detector',								//Device Type
				statusImages:{0:'/img/devices/light_detector.png',			//Device Status Images
							  1:'/img/devices/light_detector.png',
							  2:'/img/devices/light_detector.png'}
			}
		},
		beforeDestroy(){
			this.$bus.off('updateDeviceData-' + this.device['DEVICE_ID']);
		},
		created(){
			this.getDataFromDatabase();
			//Lisening on event updateDeviceData Event from Room.vue
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
			//Function Description: Change displayed image to indicate light detector status
			//Param: data (device)
			readDeviceData(data){
				this.status = data['DATA']['status'];

				if( this.status <  100){

					this.statusImage = this.statusImages[0];

				}else if(this.status  >=  100 && this.status < 200){

					this.statusImage = this.statusImages[1];

				}else if (this.status >=  200)
				{
					this.statusImage = this.statusImages[2];
				}
				else{
					console.log("Can't read "+ this.device_type +" Data");
				}
			}
		}
	};
</script>