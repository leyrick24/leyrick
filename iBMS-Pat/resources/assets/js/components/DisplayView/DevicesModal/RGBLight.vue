<template>
	<!-- start row -->
	<div class="row align-items-center">
		<div class='col-md-3'>
			<img :src="status == true ? '/img/devices/WC_Light_Warm.png' : '/img/devices/WC_Light_Off.png' " class='device-icon'>
			<div class="d-flex justify-content-center">
				<input  class="tgl tgl-ios" @change="toggleOnOff" v-model="status" :id="'rgbLightOnOff' + device['key']" type="checkbox"/>
				<label  class="tgl-btn " :for="'rgbLightOnOff' + device['key']"></label>
			</div>
		</div>
		<div class='col-md-2' :class='disableDiv'>
			<ul class="nav flex-column nav-pills nav-pills-icons mb-3" id="pills-tab" role="tablist" style="width:110px;">
			  <li class="nav-item" @click="toggleTab('warmLightTab')">
			    <a class="nav-link text-center" :class="tab.warmLightTab=='' ? 'active show' : ''"  id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">
			    	<i class="fa fa-lightbulb-o fa-lg" aria-hidden="true"></i><br>
			    	Warm<br>Light
				</a>
			  </li>
			  <li class="nav-item" @click="toggleTab('colorLightTab')">
			    <a class="nav-link text-center" :class="tab.colorLightTab=='' ? 'active show' : ''" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">
			    	<i class="fa fa-paint-brush fa-lg" aria-hidden="true"></i><br>
			    	Color<br>Light
				</a>
			  </li>
			</ul>
		</div>
		<div class='col-md-7' :class='disableDiv'>
			<div class="row" :class="tab.warmLightTab">
				<div class='col-md-4 '><label>Brightness</label></div>
				<div class='col-md-8'>
					<input @change='slideTemperature' v-model='brightness' class='range_slider' type="range" min="0" max="255" value="10" />
					<br>
				</div>
			</div>
			<div class="row" :class="tab.colorLightTab">
				<div class='col-md-12 d-flex justify-content-center'>
					<div @mouseup="updateColorValue()">
						<materialpicker v-model="colors"/><br>
					</div>
				</div>
				<div class='col-md-4'><label>Color Brightness</label></div>
				<div class='col-md-8'>
					<input @change='slideColorBrightness' v-model='colorBrightness' class='range_slider' type="range" min="0" max="255" value="10" />
				</div>
			</div>
		</div>
	</div><!-- end row -->
</template>
<script type="text/javascript">
	import materialpicker from 'vue-color/src/components/Chrome.vue';
	export default{
		components:{
			materialpicker
		},
		props:{
			device:'',										//Device data from Room.vue > DeviceList.vue > Current Vue
		},
		data(){
			return{
				mode:'',									//Warm or Color
				status: true,								//Device default status
				brightness:0,								//brightness
				rgb_color:0,								//color for default color
				colorBrightness:0,							//color brighntess

				disableDiv:'',								//Disable the div element if light is off
				tab:{										//View warm Light tab or color Light tab
					warmLightTab:'',
				    colorLightTab:'d-none',
				},
			    colors: { r: 200, g: 0.66, b: 0.30 },		//Component varable for color picker
			    device_type:'rgb_light'						//Device type

			}
		},
		created(){
			this.getDataFromDatabase();
			//Lisening on event updateDeviceData Event from Room.vue
			self = this;
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
			//Function Description: Modify device data to be readable
			//Param: data (device)
			readDeviceData(data){

				//get all data
				self.mode = data['DATA']['mode'];
				self.statusData = data['DATA']['status'];
				self.brightness = data['DATA']['brightness'];
				self.rgb_color = data['DATA']['color'];
				self.colorBrightness = data['DATA']['color_brightness'];
				//Normal or Colored
				if(self.mode == "normal"){
					this.tab.warmLightTab='';
					this.tab.colorLightTab='d-none';

				}else if(self.mode == "color"){
					this.tab.warmLightTab='d-none';
					this.tab.colorLightTab='';
				}

				//Light Status
				if(self.statusData == 1){
					self.status = true;
					self.disableDiv='';
				}else if(self.statusData == 0){
					self.status = false;
					self.disableDiv='disableDiv';
				}
			},
			//Function Name: toggleTab
			//Function Description: Toggle View Mode, Light or Warm
			//Param: data
			toggleTab(data){
				if(data=="warmLightTab"){

					this.tab.warmLightTab='';
					this.tab.colorLightTab='d-none';

					this.commandDevice('mode','normal');

				}else if(data=="colorLightTab"){

					this.tab.warmLightTab='d-none';
					this.tab.colorLightTab='';

					this.commandDevice('mode','color');

				}
			},
			//Function Name: updateColorValue
			//Function Description: send command to update color
			updateColorValue(){
				this.commandDevice('color',"#ffffff");
			},
			//Function Name: toggleOnOff
			//Function Description: Toggle switch and send command to device
			//Param: num
			toggleOnOff(){
				var value = 0;
				if(this.status == true){
					value = 1;
					this.disableDiv='';

				}else if(this.status == false){
					value = 0;
					this.disableDiv='disableDiv';
				}
				this.commandDevice('status',value);

			},
			//Function Name: updateColorValue
			//Function Description: send command to update temperature
			slideTemperature(){
				this.commandDevice('brightness',this.brightness);
			},
			//Function Name: updateColorValue
			//Function Description: send command to update color brightness
			slideColorBrightness(){
				this.commandDevice('color_brightness',this.colorBrightness);
			},
			//Function Name: commandDevice
			//Function Description: Send to Room.vue to command and Save to database
			//Param: command (status), value
			commandDevice(command,value){
				//Initialize varables
				var gateway_id = this.device['GATEWAY_ID'];
				var device_id = this.device['DEVICE_ID'];
				var device_type = this.device['DEVICE_TYPE'];

				console.log("asd");
				this.$bus.$emit('commandDevice',{'GATEWAY_ID':gateway_id,
												'DEVICE_ID':device_id,
												'DEVICE_TYPE':device_type,
												'command':command,
												'value':value});
			}
		}
	};
</script>