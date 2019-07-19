<template>
	<div class="row">
		<div class='col-md-6'>
			<img :src="statusImage" class='device-icon center-block'>
		</div>
		<div class='col-md-6 pt-3'>
			<h2 class="text-center">{{$t('devop.daily')}}: {{daily}} KwH</h2>
			<h2 class="text-center">{{$t('devop.monthly')}}: {{montly}} KwH</h2>
			<h2 class="text-center">{{$t('devop.total')}}: {{total}} KwH</h2>
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
				daily:'',												//Meter Daily Consumption
				montly:'',												//Meter Monthly Consumption
				total:'',												//Meter Total Consumption
				statusImage:'/img/devices/electric_meter.png',	//Meter Image
				device_type:'electric_meter',							//Device Type
			}
		},
		created(){
			this.getDataFromDatabase();
		},
		watch:{
			device:function(){
				this.getDataFromDatabase();
			}
		},
		methods:{
			//Function Name: getDataFromDatabase
			//Function Description: Get current device data from database
			getDataFromDatabase(){
				axios.post('modbus-data/meterdata', this.device)
				.then(response=>{
					this.readDeviceData(response.data[0]);
				});
			},
			//Function Name: readDeviceData
			//Function Description: Modify data to show
			readDeviceData(data){
				this.daily = data['DAILY_CONSUMPTION_KWH'];
				this.montly = data['CURRENT_MONTH_KWH'];
				this.total = data['TOTAL_CONSUMPTION_KWH'];
			}
		}
	};
</script>