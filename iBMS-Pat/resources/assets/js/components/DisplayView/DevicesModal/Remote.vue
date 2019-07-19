<template>
	<!-- start row -->
	<div class="row">
		<!-- start col-md-3 -->
		<div class='col-md-5'>
			<div class="list-group remote-device-view-list">
				<a v-for="remote in device['DATA']" @click="chooseRemote(remote)" class="list-group-item list-group-item-action pointer">
					{{remote['type']}} ({{remote['brand']}})
				</a>
			</div>
		</div>

		<div class='col-md-7' v-if="remote['type'] =='AC'">
			<h4>{{remote['type']}} ({{remote['brand']}}) {{$t('devop.temp')}}: {{remote['temp_value']}}</h4>
			<div class='col-md-12'>
				<div class="row">
					<div class="col-md-12">
						<div class="text-center">
							<div>{{$t('devop.power')}}</div>
							<button @click="clickRemote('air_power')" class="btn border border-secondary bg-light" :class="remote_type['AC']['aircon_power'] == true ? '' : 'disableDiv'">
								<i class="fa fa-power-off fa-4x fa-fw text-secondary" :class="remote_type['AC']['status'] == true ? 'text-success':''"></i>
							</button>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="text-center">
							<div>{{$t('devop.temperature')}}</div>
							<button @click="clickRemote('air_temp_up')" class="btn border border-secondary bg-light" :class="">
								<i class="fa fa-chevron-up fa-4x fa-fw text-secondary"></i>
							</button>
							<button @click="clickRemote('air_temp_down')" class="btn border border-secondary bg-light" :class="">
								<i class="fa fa-chevron-down fa-4x fa-fw text-secondary"></i>
							</button>
						</div>
					</div>
				</div>
				<!-- <h4>{{remote['temp_value']}}</h4> -->
			</div>
		</div>

		<div class='col-md-7' v-if="remote['type'] =='TV'">
			<h4>{{remote['type']}} ({{remote['brand']}})</h4>
			<div class="row">
				<div class="col-md-12">
					<div class="text-center">
						<div> {{$t('devop.power')}}</div>
						<button @click="clickRemote('tv_power')" class="btn border border-secondary bg-light" :class="">
							<i class="fa fa-power-off fa-4x fa-fw text-secondary" :class=""></i>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div><!-- end row -->
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
				remote:'',								//choose current remote interface
				commandStatus: null,
				remote_type:{
						AC:{							//Device default status
							status:false,
							aircon_power: false,
							aircon_temp_down:false,
							aircon_temp_up:false
						},
						TV:{							//Device default status
							status:false,
							tv_power: false,
							tv_channel_up:false,
							tv_channel_down:false,
							tv_volume_up:false,
							tv_volume_down:false
						}
					},
				getDataOnce:false,
                irLearning:{
                    TARGET_BRAND: null,
                    TARGET_DEVICE: null,
                    LEARNING_NAME: null,
                    LEARNING_VALUE: null,
                    SOURCE_DEVICE_ID:null
                },
				device_type:'ir_remote'					//Device type
			}
		},
		beforeDestroy(){
			this.$bus.off('updateDeviceData-' + this.device['DEVICE_ID']);
		},
		created(){
			this.getDataFromDatabase();
			//Lisening on event updateDeviceData Event from Room.vue
			this.$bus.$on('updateDeviceData-' + this.device['DEVICE_ID'] ,data=>{
				if(data['DEVICE_TYPE'] == this.device_type && data['DEVICE_ID'] == this.device['DEVICE_ID']){
					this.readDeviceData(data);
				}

			});
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
			//Function Description: Modify data and images for latest entry
			//Param: data (device)
			readDeviceData(data){
				if(this.getDataOnce==false){
					if(data['DATA'].length > 0){
						this.remote = data['DATA'][0];
						this.chooseRemote(data['DATA'][0]);
					}
					this.getDataOnce = true;
				}
			},
			//Function Name: clickRemote
			//Function Description: command remote
			//Param: data
			clickRemote(data){

				if(data == 'air_power'){
					this.remote_type['AC']['status'] = !this.remote_type['AC']['status'];
					data = this.remote_type['AC']['status'] ? 'air_power_on' : 'air_power_off';
					if(data == 'air_power_on'){
						this.commandStatus = 'POWER_ON';
					}else if(data == 'air_power_off'){
						this.commandStatus = 'POWER_OFF';
					}

				}else if(data == 'air_temp_up'){
					var temp = new Number(this.remote['temp_value']);
					if(temp >= 16 && temp < 30){
						this.remote['temp_value'] = temp+1;
						this.commandStatus = 'TEMP_' + this.remote['temp_value'];
					}else{
						this.commandStatus = 'TEMP_' + this.remote['temp_value'];
					}

				}else if(data == 'air_temp_down'){
					var temp = new Number(this.remote['temp_value']);
					if(temp > 16 && temp <= 30){
						this.remote['temp_value'] = temp-1;
						this.commandStatus = 'TEMP_' + this.remote['temp_value'];
					}else{
						this.commandStatus = 'TEMP_' + this.remote['temp_value'];
					}
					// this.commandDevice(data,"203");

				}else if(data == 'tv_power'){
					// this.remote_type['TV']['status'] = !this.remote_type['TV']['status'];
					// data = this.remote_type['TV']['status'] ? 'air_power_on' : 'air_power_off';
						this.commandStatus = 'POWER';
				}
				var timer = '';
				clearTimeout(this.timer);
                this.timer = setTimeout(() => {
					this.getLearningList(this.commandStatus, this.device['DEVICE_ID']);
                }, 1000)

			},
			//Function Name: getLearningList
			//Function Description: Get IR Learning List
			//Param: act (commandStatus), devID (DEVICE_ID)
			getLearningList(act, devID){
                axios.get('learning-list')
                .then(response => {
                    this.irLists = response.data

                    // //loops irLists
                    for(var j in this.irLists){
                        if(this.irLists[j].SOURCE_DEVICE_ID == devID){
                            if(this.irLists[j].LEARNING_NAME == act && this.irLists[j].TARGET_BRAND == this.remote['brand']){
                            	console.log('COMMAND: ' + act + ' ' + this.irLists[j].LEARNING_VALUE);
                            	this.commandDevice('status',2,this.irLists[j].LEARNING_VALUE);
                            }
                        }else{

                        }
                    }
                })
                .catch(errors => {
                    console.log(errors);
                });
            },
            //Function Name: chooseRemote
			//Function Description: choose remote interface
			//Param: data
			chooseRemote(data){
				if(data['type'] == 'AC'){
					this.remote = data;
					this.remote_type['AC']['aircon_power'] = data['aircon_power'];
					this.remote_type['AC']['aircon_temp_up'] = data['aircon_temp_up'];
					this.remote_type['AC']['aircon_temp_down'] = data['aircon_temp_down'];

					if(data['status'] == 1){
						this.remote_type['AC']['status'] = true;
					}else{
						this.remote_type['AC']['status'] = false;
					}

				}else if(data['type'] == 'TV'){
					this.remote = data;
					this.remote_type['TV']['tv_power'] = data['tv_power'];
					// this.remote_type['TV']['tv_channel_up'] = data['tv_channel_up'];
					// this.remote_type['TV']['tv_channel_down'] = data['tv_channel_down'];
					// this.remote_type['TV']['tv_volume_up'] = data['tv_volume_up'];
					// this.remote_type['TV']['tv_volume_down'] = data['tv_volume_down'];

					if(data['status'] == 1){
						this.remote_type['TV']['tv_power'] = true;
					}else{
						this.remote_type['TV']['tv_power'] = false;
					}
				}
			},
			//Function Name: commandDevice
			//Function Description: Send to Room.vue to command and Save to database
			//Param: command (status), value, irValue
			commandDevice(command,value,irValue){
				//Initialize variables
				console.log(irValue);
				var gateway_id = this.device['GATEWAY_ID'];
				var device_id = this.device['DEVICE_ID'];
				var device_type = this.device['DEVICE_TYPE'];

				this.$bus.$emit('commandDevice',{'GATEWAY_ID':gateway_id,
												'DEVICE_ID':device_id,
												'DEVICE_TYPE':device_type,
												'command':command,
												'value':value,
												'addlValue':irValue});

			}
		}
	};
</script>