<template>
	<div class="row align-items-center">
		<div class='col-md-1'>
		</div>
		<div class='col-md-11'>
			<div class='row padcenter'>
				<div class='col-md-4'><label>{{device['DATA'][0]['device_name']}}</label></div>
				<div class='col-md-8'>
					<!-- @change trigger toggleOnOff -->
				    <input @change='toggleOnOff(1)' v-model="switchStatus[0]" class="tgl tgl-ios" :id="'embedded_switch_1' + device['key']" type="checkbox"/>
				    <label  class="tgl-btn" :for="'embedded_switch_1' + device['key']"></label>
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

				switchStatus:{0:false},					//Device default status
				device_type:'embedded_switch_1'			//Device Type

			}
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
			//Function Description: Change displayed image to indicate door lock status
			//Param: data (device)
			readDeviceData(data){
					for (var i in data['DATA'])
					{
						if(data['DATA'][i]['status']==1){

							this.switchStatus[i]=true;

						}else if(data['DATA'][i]['status']==0){

							this.switchStatus[i]=false;

						}
					}
			},
			//Function Name: toggleOnOff
			//Function Description: Toggle switch and command device
			//Param: num
			toggleOnOff(num){
				var value='';
				if(this.switchStatus[num-1]==true)
				{
					value = 1;
				}else{
					value = 0;
				}
				this.commandDevice('status_' + num, value);
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