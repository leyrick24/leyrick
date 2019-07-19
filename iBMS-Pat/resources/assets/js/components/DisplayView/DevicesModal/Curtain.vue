<template>
	<div class="row align-items-center">
		<div class='col-md-4'>
		</div>
		<div class='col-md-4'>
			<img src="/img/devicecard/curtain_1.png" class="img-fluid invert">
			<br><br>
			<button @click="commandCurtain('100')" class="btn btn-success">{{$t('open')}}</button>&emsp;
			<button  @click="commandCurtain('0')" class="btn btn-primary">{{$t('close')}}</button>
		</div>
		<div class='col-md-4'>

		</div>
	</div>
</template>
<script type="text/javascript">
	export default{
		props:{
			device:'',									//Device data from Room.vue > DeviceList.vue > Current Vue
		},
		data(){
			return{

				sliderValue:50,							//Default Value
				device_type:'curtain_1'					//Device Type


			}
		},
		methods:{
			//Function Name: commandCurtain
			//Function Description: Change curtain display based on slider value and command device
			//Param: sliderValue (open = 100, close = 0)
			commandCurtain(sliderValue){
				this.sliderValue = 100 - sliderValue;
				var imgWidth = (sliderValue/100)*100;
				$('#curtainImage' + this.device['DEVICE_ID']).clearQueue();
				$('#curtainImage' + this.device['DEVICE_ID']).animate({'width':imgWidth+'%'});

				this.commandDevice('status',this.sliderValue);					//to be confirm

			},
			//Function Name: commandDevice
			//Function Description: Send to Room.vue to command and Save to database
			//Param: command (status), value (sliderValue)
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
			},
		}
	};
</script>