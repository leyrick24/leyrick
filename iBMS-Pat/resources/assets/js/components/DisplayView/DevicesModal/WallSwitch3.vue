<template>
	<div class="row align-items-center pad-50">
		<div class='col-md-1'>
		</div>
		<div class='col-md-9'>
			<div class='row'>
				<div class='col-md-4'><label>{{device['DATA'][0]['device_name']}}</label></div>
				<div class='col-md-8'>
				    <input @change='toggleOnOff(1)' v-model="switchStatus[0]" class="tgl tgl-ios" :id="'wall_switch_1' + device['key']" type="checkbox"/>
				    <label  class="tgl-btn" :for="'wall_switch_1' + device['key']"></label>
			    </div>
				<div class='col-md-4'><label>{{device['DATA'][1]['device_name']}}</label></div>
				<div class='col-md-8'>
				    <input @change='toggleOnOff(2)' v-model="switchStatus[1]" class="tgl tgl-ios" :id="'wall_switch_2' + device['key']" type="checkbox"/>
				    <label  class="tgl-btn" :for="'wall_switch_2' + device['key']"></label>
			    </div>
				<div class='col-md-4'><label>{{device['DATA'][2]['device_name']}}</label></div>
				<div class='col-md-8'>
				    <input @change='toggleOnOff(3)' v-model="switchStatus[2]" class="tgl tgl-ios" :id="'wall_switch_3' + device['key']" type="checkbox"/>
				    <label  class="tgl-btn" :for="'wall_switch_3' + device['key']"></label>
			    </div>
			</div>
		</div>
		<div class='col-md-2'>
		    <input @change="toggleOnOffAll('all')" v-model="switchStatusAll" class="tgl tgl-ios" :id="'wall_switch_3_all' + device['key']" type="checkbox"/>
		    <label  class="tgl-btn" :for="'wall_switch_3_all' + device['key']"></label>
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

				switchStatus:{  0:false,				//Device default status
								1:false,
								2:false},
				switchStatusAll:false,
				device_type:'wall_switch_3'				//Device Type

			}
		},
		created(){
			this.getDataFromDatabase();
			//Lisening on event updateDeviceData Event from Room.vues
			this.$bus.$on('updateDeviceData-' + this.device['DEVICE_ID'],data=>{
				if(data['DEVICE_TYPE'] == this.device_type && data['DEVICE_ID'] == this.device['DEVICE_ID']){

					this.readDeviceData(data);

				}
				this.checkAllButton();
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
			//Function Description: Change switch status
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
			//Function Name: toggleOnOffAll
			//Function Description: Toggle switch all and command device
			toggleOnOffAll(){
					var value=0;
					if(this.switchStatusAll==true){
						value=1;
					}else{
						value=0;
					}
					this.commandDevice('status', value);
					//Toggle Button
					for(var i in this.switchStatus){
						this.switchStatus[i]=this.switchStatusAll;
					}
			},
			//Function Name: checkAllButton
			//Function Description: Toggle switch all based on device swtiches
			//Param: switchStatus
			checkAllButton(){
				for (var i in this.switchStatus)
					if(!this.switchStatus[i])
					{
						this.switchStatusAll=false;
						return false;
					}

				this.switchStatusAll=true;
				return;
			},
			//Function Name: toggleOnOff
			//Function Description: Toggles individual switch and commands device
			//Param: num
			toggleOnOff(num){
				//for disabling binding
				console.log(this.device);
				if(this.device.BINDING_STATUS == true){
					this.$swal({
						title: 'This Device has Binding!',
						text: "Do you want to Disable?",
						type: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: 'Yes, Disable!'
						}).then((result) => {
						if (result.value) {
							axios.post('bindings/disable',{
	                            BINDING_ID: this.device.device_bindings,
							  	TYPE: 1,
	                        }).then(response => {
	                            if(response.data == 'success'){
	                                this.$swal({
	                                    title:'Binding',
	                                    text: "Binding has been disable.",
	                                    type:'success',
	                                    timer:1500,
	                                    showConfirmButton:false
	                                });
	                            }else{
	                                this.$swal(
	                                    'Error',
	                                    "Binding can't disable.",
	                                    'error'
	                                );
	                            }
                        	});
						}
					});
				}else{
					var value='';
					this.checkAllButton();
					if(this.switchStatus[num-1]==true)
					{
						value = 1;
					}else{
						value = 0;
					}
					this.commandDevice('status_' + num, value);
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
		},
		computed: {
			//process the binding status and add binding status in device
			processBinding(){
				var data = this.device;
				var bindings = this.device.device_bindings;
				if(bindings.length > 0){
					for(var z in bindings){
						if(bindings[z].BINDING_STATUS == 1){
							data['BINDING_STATUS'] = true;
							break;
						}else{
							data['BINDING_STATUS'] = false;
							break;
						}
					}
				}else{
					data['BINDING_STATUS'] = false;
				}
				return data;
			}
		}
	};
</script>