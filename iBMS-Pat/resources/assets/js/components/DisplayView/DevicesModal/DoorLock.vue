<template>
	<div class="row">
		<div class='col-md-6'>
			<img :src="status == false ? '/img/devices/door_lock_lock.png' : '/img/devices/door_lock_unlock.png' " class='device-icon'>
		</div>
		<div class='col-md-6'>
			<div class="col-sm-12">
				<div class='col-md-11'>
					<div class="text-center keypad-bg">
						<div class="col-sm-12">
							<input type="password" v-model="Total" class="col-sm-12 my-2" disabled="">
							<div class="d-flex justify-content-center">
								<button @click="btnNumber('1')" class="btn border border-secondary keypad-btn keypad-num-padding">1</button>
								<button @click="btnNumber('2')" class="btn border border-secondary keypad-btn keypad-num-padding">2</button>
								<button @click="btnNumber('3')" class="btn border border-secondary keypad-btn keypad-num-padding">3</button>
							</div>
							<div class="d-flex justify-content-center">
								<button @click="btnNumber('4')" class="btn border border-secondary keypad-btn keypad-num-padding">4</button>
								<button @click="btnNumber('5')" class="btn border border-secondary keypad-btn keypad-num-padding">5</button>
								<button @click="btnNumber('6')" class="btn border border-secondary keypad-btn keypad-num-padding">6</button>
							</div>
							<div class="d-flex justify-content-center">
								<button @click="btnNumber('7')" class="btn border border-secondary keypad-btn keypad-num-padding">7</button>
								<button @click="btnNumber('8')" class="btn border border-secondary keypad-btn keypad-num-padding">8</button>
								<button @click="btnNumber('9')" class="btn border border-secondary keypad-btn keypad-num-padding">9</button>
							</div>
							<div class="d-flex justify-content-center pb-1">
								<button @click="enterPass(btnNumber(Total))" class="btn border border-secondary keypad-btn">
									<i class="fa fa-check-circle"></i>
								</button>
								<button @click="btnNumber('0')" class="btn border border-secondary keypad-btn keypad-num-padding">0</button>
								<button @click="eraseNum()" class="btn border border-secondary keypad-btn">
									<i class="fa fa-times"></i>
								</button>
							</div>

						</div>
					</div>
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
			device:'',					//Device data from Room.vue > DeviceList.vue > Current Vue
		},
		data(){
			return{
				Total: '',								//Device Password
				status: false,							//Device default status
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
					var status = data['DATA']['status_lock'];
					if(status ==  '0808'){
						this.status = true;
						//Set Time out for automatic lock
						var that = this;
						setTimeout(function(){
							this.status = false;
					    },7000);
					}else if(status  ==  0){
						this.status = false;
					}else{
						console.log("Can't read "+ this.device_type +" Data");
					}
			},
			//Function Name: enterPass
			//Function Description: Input Password for the device
			//Param: Total
			enterPass(Total){
					this.commandDevice('status',1,this.Total);
					this.Total = '';
			},
			//Function Name: eraseNum
			//Function Description: Erase number from password input
			eraseNum(){
				this.Total = this.Total.slice(0, -1);
			},
			//Function Name: btnNumber
			//Function Description: add number to password input
			//Param: newNum (button number)
			btnNumber(newNum){
				this.Total = this.Total += newNum;
			},
			//Function Name: commandDevice
			//Function Description: Send to Room.vue to command and Save to database
			//Param: command (status), value (1), pass (password input)
			commandDevice(command,value,pass){
				//Initialize varables
				var gateway_id = this.device['GATEWAY_ID'];
				var device_id = this.device['DEVICE_ID'];
				var device_type = this.device['DEVICE_TYPE'];

				this.$bus.$emit('commandDevice',{'GATEWAY_ID':gateway_id,
												'DEVICE_ID':device_id,
												'DEVICE_TYPE':device_type,
												'command':command,
												'value':value,
												'addlValue':pass});
			}
		},
		computed:{
		}
	};
</script>