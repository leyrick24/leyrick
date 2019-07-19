<template>
	<div class="row align-items-center">
		<div class='col-md-3'>
			<img :src="status == true ? '/img/devices/WC_Light_Warm.png' : '/img/devices/WC_Light_Off.png' " class='device-icon'>
		</div>
		<div class='col-md-9'>
			<div class="row">
				<div class='col-md-10'><label>On/Off</label></div>
				<div class='col-md-2'>
				    <input @change='toggleOnOff' v-model="status" class="tgl tgl-ios" :id="'wcLightOnOff' + device['DEVICE_ID']" type="checkbox"/>
				    <label  class="tgl-btn pull-right" :for="'wcLightOnOff' + device['DEVICE_ID']"></label>
				    <br>
			    </div>
			</div>
			<div class="row" :class='disableDiv'>
				<div class='col-md-4'><label>Brightness</label></div>
				<div class='col-md-8'>
					<input @change='slideBrightness' v-model='brightness' class='range_slider' type="range" min="0" max="255" value="10" />
					<br>
				</div>
			</div>
			<div class="row" :class='disableDiv'>
				<div class='col-md-4'><label>Color Temperature </label></div>
				<div class='col-md-8'>
					<input @change='slideTemperature' v-model='tempColor' class='range_slider' type="range" min="0" max="510" value="10" />
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
			device:'',									//Device data from Room.vue > DeviceList.vue > Current Vue
		},
		data(){
			return{
				status: false,							//Device current status
				brightness:0,							//brightness
				tempColor:0,							//Teperature color
				disableDiv:'disableDiv',				//Disable Configurations if light status is off
				device_type:'temp_light'				//Device Type

			}
		},
		created(){
			this.getDataFromDatabase();
			//Lisening on event updateDeviceData Event from Room.vues
			self = this
			this.$bus.$on('updateDeviceData-' + this.device['DEVICE_ID'] ,data=>{
				if(data['DEVICE_TYPE'] == this.device_type && data['DEVICE_ID'] == self.device['DEVICE_ID']){
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
				self.statusData = data['DATA']['status'];
				self.brightness = data['DATA']['brightness'];
				self.tempColor = data['DATA']['temp_color'];


				if(self.statusData == 1){
					self.status = true;
					self.disableDiv='';

				}else if(self.statusData == 0){
					self.status = false;
					self.disableDiv='disableDiv'
				}
			},
			//Function Name: toggleOnOff
			//Function Description: Toggles switch and commands device
			//Param: num
			toggleOnOff(){
				if(this.status == true)
				{
					this.commandDevice('status',1);
					this.disableDiv='';
				}else{
					this.commandDevice('status',0);
					this.disableDiv='disableDiv';
				}

			},
			//Function Name: sliderBrightness
			//Function Description: send command to update brightness
			//Param: brightness
			slideBrightness(){
				this.commandDevice('brightness',this.brightness);
			},
			//Function Name: slideTemperature
			//Function Description: send command to update light temperature
			//Param: tempColor
			slideTemperature(){
				this.commandDevice('temp_color',this.tempColor);
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