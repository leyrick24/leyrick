<template>
	<div class="card">
		<div class="card-body device-operation-carousel-bg carousel-custom">
			<carousel :perPage="$isMobile.isMobile==true ? 2 : 6 " >
				<slide v-for="device,key in devices" :key="key" mouseDrag="true">
					<div @click="showData(key, {'DEVICE_ID': device.DEVICE_ID, 'DEVICE_TYPE': device.DEVICE_TYPE, 'DEVICE_NAME': device.DEVICE_NAME})" :id="'dbdevice'+key" class="db-device mx-auto text-center" :title="device.ONLINE_FLAG == 0 ? 'Device is Offline':''" v-b-tooltip.hover>
						<img :src="device.DEVICE_IMG" onerror="javascript:this.src='/img/image_not_found.png'" class="db-device-img" :alt="device.DEVICE_TYPE" :class="device.ONLINE_FLAG == 0 ? 'grayscale':''">

					</div>
					<p class="text-center">{{deviceTypes[key]}}</p>
				</slide>
			</carousel>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default{
		props:{
			floor:'',
			room:'',
		},
		data(){
			return{
				devices:[],
				defaultData: '',
				deviceTypes:'',
			}
		},
		mounted(){
			this.getUniqueDevices();
			//show default data if device is not empty
			if (this.devices.length > 0) {
				this.showData(0, {'DEVICE_ID': this.devices[0].DEVICE_ID, 'DEVICE_TYPE': this.devices[0].DEVICE_TYPE, 'DEVICE_NAME': this.devices[0].DEVICE_NAME});
			}
		},
		methods:{
			//Function Name: getUniqueDevices
			//Function Description: Retrieve all devices filtered based on room or floor and select first device as default
			//Params: selectedRoom, selectedFloor from Dashboard
			getUniqueDevices(){
				let suffix = '';
				if (this.floor != '') {
					if (this.room != '') {
						suffix = '?FLOOR_ID='+this.floor+'&ROOM_ID='+this.room;
						this.devices = [];
					}else{
						suffix = '?FLOOR_ID='+this.floor;
						this.devices = [];
					}
				}
				axios.get('getUniqueDevices'+suffix)
				.then(response=>{
					if (response.data.length > 0) {
						response.data.map((val,key) => {
						val.DEVICE_IMG = '/img/device_carousel/'+val.DEVICE_TYPE+'.png';
						this.devices.push(val);
						});
						this.defaultData = response.data[0];
						this.deviceTypes = [...new Set(this.devices.map(x => x.DEVICE_TYPE.toUpperCase().replace(/_/g, ' ')))];
						this.showData(0, this.defaultData);
					}else{
						this.$bus.$emit('activeDeviceData', {});
						this.$emit('changeTitle', 'No Device');
					}
				})
				.then(resposne=>{
					$('#dbdevice0').animate({width:'160px',height:'110px'},200);
				});
			},
			//Function Name: showData
			//Function Description: Retrieves processed data for selected device, emits title change for dashboard and shows animation for selected device on carousel
			//Params: index (index on carousel), value (device)
			showData(index, value){
				//emits modified device type string for chart title
				this.$emit('changeTitle',value.DEVICE_TYPE.toUpperCase().replace(/_/g, ' '));
				//get device processed data and emit for chart
				//param: DEVICE_ID, DEVICE_TYPE, FLOOR_ID, ROOM_ID
				axios.post('getProcessedData',{'DEVICE_ID': value.DEVICE_ID,'DEVICE_TYPE': value.DEVICE_TYPE,'FLOOR_ID':this.floor,'ROOM_ID':this.room})
				.then(response=>{
            		this.$bus.$emit('activeDeviceData', response.data);
				});
				// Animation onClick
				$('.db-device').animate({width:'150px',height:'100px'},100);
				$('#'+event.currentTarget.id).clearQueue();
				$('#'+event.currentTarget.id).animate({width:'160px',height:'120px'},200);
			},
		},
		watch:{
			//get devices on floor change
			floor:function(){
				this.getUniqueDevices();
			},
			//get devices on room change
			room:function(){
				this.getUniqueDevices();
			}
		}
	};
</script>