<template>
	<div class="row align-items-center">
		<div class='col-md-5'>
			<img :src="status == true ? '/img/devices/water_valve_open.png' : '/img/devices/water_valve_close.png' " class='device-icon'>
		</div>
		<div class='col-md-7'>
			<div class="row">
				<div class='col-md-12'>
				    	<div class='row align-items-center'>
							<div class="col-md-2">
								<div class="pull-right">{{$t('close')}}</div>
							</div>
							<div class="col-md-2">
								<div class="row">
									<input @change='toggle' v-model='status' class="tgl tgl-flat" :id="'device' + device['key']" type='checkbox'>
									<label class='tgl-btn' data-tg-off="UNLOCK" data-tg-on="LOCK" :for="'device' + device['key']"></label>
								</div>
							</div>
							 <div class="col-md-3">
								{{$t('open')}}
							</div>
						</div>
				    <br>
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
			device:'',								//Device data from Room.vue > DeviceList.vue > Current Vue
		},
		data(){
			return{
				status: false,						//Device default status
				device_type:'water_valve'			//Device Type
			}
		},
		created(){
			this.getDataFromDatabase();
			//Lisening on event updateDeviceData Event from Room.vues
			this.$bus.$on('updateDeviceData-' + this.device['DEVICE_ID'],data=>{
				if(data['DEVICE_TYPE'] == this.device_type && data['DEVICE_ID'] == this.device['DEVICE_ID']){
					console.log(data);
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
			//Function Description: Modify data and image to indicate device status
			//Param: data (device)
			readDeviceData(data){
				var status = data['DATA']['status'];
				if(status ==  '01'){

					this.status = true;

				}else if(status  ==  '00'){

					this.status = false;

				}else{
					console.log("Can't read "+ this.device_type +" Data");
				}
			},
			//Function Name: toggle
			//Function Description: Toggles and commands device
			//Param: num
			toggle(){
				if(this.status == true)
				{
					this.commandDevice('status',1);
				}else{
					this.commandDevice('status',0);
				}

			},
			//Function Name: commandDevice
			//Function Description: Send to Room.vue to command and Save to database
			//Param: command (status), value
			commandDevice(command,value){
				//Initialize varables
				var gateway_id = this.device['GATEWAY_ID'];
				var device_id = this.device['DEVICE_ID'];
				var device_type = this.device['DEVICE_TYPE'];

				this.$bus.$emit('commandDevice',{'GATEWAY_ID':gateway_id,
												'DEVICE_ID':device_id,
												'DEVICE_TYPE':device_type,
												'command':command,
												'value':value});
			}
		}
	};
</script>