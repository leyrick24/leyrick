<template>
	<div class="col-md-12">
		<div class="row">
			<div v-if="renderData.length > 0" class="col-md-12 overflow-y-hidden h-300 scroll-design">
				<b-table :items="renderData"
						 :fields="table.fields"
						 :current-page="table.currentPage"
						 :per-page="table.perPage"
						 small
						 hover
						 @row-clicked="chooseRoom"
						 >

						<template slot="show_gateway_details" slot-scope="row">
							<button @click.stop="row.toggleDetails" @click="selectDetails('gateway')" class="btn btn-sm btn-primary">
								{{row.item.sys_reg_gateways.length}} {{$t('devop.gateways')}}
							</button>
						</template>

						<template slot="show_device_details" slot-scope="row">
							<button @click.stop="row.toggleDetails" @click="selectDetails('device')" class="btn btn-sm btn-primary">
								{{row.item.reg_devices.length}} {{$t('devop.devices')}}
							</button>
						</template>
						<template slot="row-details" slot-scope="row">
							<div v-if="detailsSelected == 'gateway'">
								<div v-if="row.item.sys_reg_gateways.length > 0">
									<b-table :items="row.item.sys_reg_gateways" :fields="table.gatewayDetailsFields"></b-table>
								</div>
								<div v-else class="text-center bg-warning py-3">
									{{$t('devop.nogateway')}}
								</div>
							</div>
							<div v-else>
								<div v-if="row.item.reg_devices.length > 0">
									<b-table :items="row.item.reg_devices" :fields="table.deviceDetailsFields"></b-table>
								</div>
								<div v-else class="text-center bg-warning py-3">
									{{$t('nodevice')}}
								</div>
							</div>
						</template>
				</b-table>
			</div>
			<div class="col-md-12">
				<div class="d-flex justify-content-center custom-pagination-black">
					<b-pagination :total-rows="table.totalRows" :per-page="table.perPage" v-model="table.currentPage">
					</b-pagination>
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
			currentFloor:'',
			searchRoom:''
		},
		data(){
			return{
				table:{
					currentPage:1,
					perPage:7,

					totalRows:0,
					fields:[
						{key:'floor.FLOOR_NAME',label:'Floor Name'},
						{key:'ROOM_NAME',label:'Room Name'},
						{key:'show_gateway_details',label:'Gateways'},
						{key:'show_device_details',label:'Devices'}
					],
					gatewayDetailsFields:[
						{key:'GATEWAY_NAME',label:'Gateway Name'},
						{key:'NEW_ONLINE_FLAG',label:'Status'},
					],
					deviceDetailsFields:[
						{key:'DEVICE_NAME',label:'Device Name'},
						{key:'DEVICE_TYPE',label:'Device Type'},
						{key:'DATA',label:'Device Status'},
					],
				},
				rooms:[],
				detailsSelected: '',
				choosenKey: 0,
			};
		},
		created(){
			this.getFloorRooms();
		},
		methods:{
			//Function Name: getFloorRooms
			//Function Description: Get Rooms By Floor
			//Param: currentFloor['FLOOR_ID']
			getFloorRooms(){
				axios.post('floors/' + this.currentFloor['FLOOR_ID'] + '/rooms?include=floor>regDevices>sysRegGateways')
				.then(response =>{
					this.rooms = response.data;
					this.chooseRoom(this.renderData[0], 0);
				});
			},
			//Function Name: chooseRoom
			//Function Description: Choose Room in Table and highlight selected room
			//Param: data (room), key (index in room)
			chooseRoom(data,key){
				for(var i in this.renderData){
					this.$set(this.renderData[i],"_rowVariant","");
				}
				this.$set(this.renderData[key],"_rowVariant","orange");
				this.$emit('chooseRoom',data);
				this.choosenKey = key;
			},
			//Function Name: selectDetails
			//Function Description: Shows Gateway/Device Details on click
			//Param: data (Gateway/Device)
			selectDetails(data){
				this.detailsSelected = data;
			},
			//Function Name: updateDeviceData
			//Function Descripotion: Update device data on command/update event
			//Param: data (new device data)
			updateDeviceData(data){
				var details = this.renderData;
				for(var x in details){
					var dev = details[x].reg_devices;
					for(var i in dev){
						if (dev[i]['DEVICE_ID'] == data['DEVICE_ID']) {
							if(data != null){
					            dev[i]['DATA'] = data['DATA'];
					            dev[i]['UPDATED_AT'] = data['UPDATED_AT'];
							}
			        	}
					}
				}
				this.$set(details[this.choosenKey],"_rowVariant","orange");
			},
		},
		computed:{
			renderData(){
				var details = this.rooms;
				var search = this.searchRoom;
				if(search != ''){
                    return data.filter(function(obj) {return obj.target_device.DEVICE_NAME.toLowerCase().includes(search.toLowerCase()) || obj.target_device.DEVICE_TYPE.toLowerCase().includes(search.toLowerCase()); });
				}else{
					for(var i in details){
						var data = details[i].reg_devices;
						for(var x in data){
							if(data[x].DEVICE_TYPE == 'temp_light' || data[x].DEVICE_TYPE == 'water_valve'){
								if(data[x].DATA["status"] == 0){
									data[x].DATA = 'OFF';
								}else{
									data[x].DATA = 'ON';
								}
							}
							if(data[x].DEVICE_TYPE == 'gas_valve'){
								if(data[x].DATA["status"] == 0){
									data[x].DATA = 'CLOSE';
								}else{
									data[x].DATA = 'OPEN';
								}
							}
							if(data[x].DEVICE_TYPE == 'panic_button'){
								if(data[x].DATA["status"] == 0){
									data[x].DATA = 'NORMAL';
								}else{
									data[x].DATA= 'ALARM';
								}
							}
							if(data[x].DEVICE_TYPE == 'curtain_1'){
								if(data[x].DATA["status"] >= 0 && data[x].DATA["status"] <= 10 ){
									data[x].DATA = 'CLOSE';
								}else{
									data[x].DATA = 'OPEN';
								}
							}
							if(data[x].DEVICE_TYPE == 'door_lock'){
								if(data[x].DATA.status_lock == 0){
									data[x].DATA = "LOCK";
								}else{
									data[x].DATA = "UNLOCK";
								}
							}
							if(data[x].DEVICE_TYPE == 'wall_switch_1' || data[x].DEVICE_TYPE == 'embedded_switch_1' || data[x].DEVICE_TYPE == 'wall_switch_2' || data[x].DEVICE_TYPE == 'embedded_switch_2' || data[x].DEVICE_TYPE == 'wall_switch_3' || data[x].DEVICE_TYPE == 'embedded_switch_3'){
								var arr = data[x].DATA;
								var stat;
								if(Array.isArray(arr)){
									for(var y in arr){
										if(arr[y].status == 1){
											stat = "ON";
											break;
										}else{
											stat = "OFF";
											break;
										}
									}
									data[x].DATA = stat;
								}
							}
							if(data[x].DEVICE_TYPE == 'ir_remote'){
								var arr = data[x].DATA;
								var tv_stat,ac_stat;
								if(Array.isArray(arr)){
									ac_stat = arr[0].status;
									tv_stat = arr[1].status;
									if(ac_stat == 0){
										ac_stat = "OFF";
									}else{
										ac_stat = "ON";
									}
									if(tv_stat == 0){
										tv_stat = "OFF";
									}else{
										tv_stat = "ON";
									}
									data[x].DATA = "AC: " + ac_stat + " TV: " + tv_stat;
								}
							}
							//sensors with 0 and 1 value only
							if(data[x].DATA["status"] == 0 && data[x].DEVICE_CATEGORY == 1){
								data[x].DATA = 'NORMAL';
							}else if(data[x].DATA["status"] == 1 && data[x].DEVICE_CATEGORY == 1){
								data[x].DATA = 'ALARM';
							}
							//Sensors
							if(data[x].DEVICE_TYPE == 'co2_detector'){
								if(data[x].DATA["status"] >= 0 && data[x].DATA["status"] <= 2500){
									data[x].DATA = 'GOOD';
								}else{
									data[x].DATA = 'BAD';
								}
							}
							if(data[x].DEVICE_TYPE == 'dust_detector'){
								if(data[x].DATA["status"] >= 0 && data[x].DATA["status"] <= 150){
									data[x].DATA = 'GOOD';
								}else{
									data[x].DATA = 'BAD';
								}
							}
							if(data[x].DEVICE_TYPE == 'temp_hum'){
								if (data[x].DATA["temp"] >= 0 && data[x].DATA["temp"] <= 23.0) {
									data[x].DATA = "COMFORT";
								}else if(data[x].DATA["temp"] >= 24.0 && data[x].DATA["temp"] <= 35.0){
									data[x].DATA = "DISCOMFORT";
								}
							}
							if(data[x].DEVICE_TYPE == 'light_detector'){
								if(data[x].DATA["status"] >= 0 && data[x].DATA["status"] <= 300){
									data[x].DATA = 'NIGHT';
								}else{
									data[x].DATA = 'DAY';
								}
							}
							if(data[x].DEVICE_TYPE == 'multi_detector'){
								data[x].DATA = 'N/A';
							}
						}

						var gatewayData = details[i].sys_reg_gateways;
						for(var z in gatewayData){
							var onlineFlag = gatewayData[z].ONLINE_FLAG;
							if(onlineFlag == 1){
								gatewayData[z].NEW_ONLINE_FLAG = 'ONLINE';
							}else{
								gatewayData[z].NEW_ONLINE_FLAG = 'OFFLINE';
							}
						}
					}
					return details;
				}
			},
		},
		watch:{
			currentFloor:function(){
				this.getFloorRooms();
			},
			searchRoom:function(){
				if(this.searchRoom == ''){
					var key = this.choosenKey;
					this.$set(this.renderData[key],"_rowVariant","orange");
				 	this.chooseRoom(this.renderData[key], key);
				}
				// this.$forceUpdate();
			},
		},
		mounted(){
 			//Listen to incoming device update
			Echo.channel('test').listen('.deviceCommandEvent', (e) => {
				let eventData = e.data;
				this.updateDeviceData(eventData);
			});
		},
	};
</script>