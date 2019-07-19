<template>
	<div>
		<div class="wrapper hardware-img-bg">
			<div class="container-fluid">
				<div class="row bg-white">
					<div class="col-md-6 my-2">
						<h6 class="my-1">
							{{ $t('devop.devop') }} {{ viewMode }} {{ $t('devop.view') }}
						</h6>
					</div>
					<div class="col-md-6 my-2">
						<div class="my-1">
							<clock :locale="$i18n.locale" size="small"></clock>
						</div>
					</div>
					<div class="col-md-12">
						<div class="card">
							<div class="card-header d-flex justify-content-between" style="background-color:rgba(178,178,176,1);">
								<div class="d-flex align-items-center">
									<button type="button" id="dropDownMenuFloor" class="btn btn-light btn-sm mr-2 border border-secondary" data-toggle='dropdown' aria-haspopup="true" aria-expanded="false">
										<i class="fa fa-bars"></i>
									</button>
									<!-- Dropdown Menu -->
									<div class="dropdown-menu" aria-labelledby="dropDownMenuFloor">
										<a class="dropdown-item pointer" href="#" v-for="floor,key in floors" @click="chooseFloor(key)">{{floor['FLOOR_NAME']}}</a>
									</div>
									<div>
										{{currentFloor['FLOOR_NAME']}}{{'/' + currentRoom['ROOM_NAME']}}
									</div>
								</div>
								<div class="d-flex">
									<!-- Search Room -->
									<div class="d-flex">
										<div class="input-group border border-secondary rounded p-0" :class="viewMode=='Map' ? 'col-sm-10 grow shrink' : 'col-sm-12 grow'">
											<input type="text" class="form-control form-control-sm" v-model="searchRoom" :placeholder="$t('devop.searchroom')">
											<div class="input-group-append">
												<span class="input-group-text"><i class="fa fa-search"></i></span>
											</div>
										</div>
										<!--Zoom Button-->
										<div v-if="viewMode=='Map'" class="ml-2" :class="viewMode=='Map' ? 'fade-in' : ''">
											<button type="button" @click="zoomMapToggle()" :class="zoomToggle==true ? 'device-operation-view-button':''" class="btn btn-sm btn-light border border-secondary"><i class="fa fa-search-plus fa-xs"></i></button>
										</div>
									</div>
									<div class="ml-2">
										<!--View Devices Mode Button (Map View, Table View, Tree View)-->
										<div class="btn-group btn-group-sm border border-secondary rounded">
											<button @click="changeView('Map')" type="button" class="btn btn-light" :class="viewMode=='Map' ? 'device-operation-view-button':''">
												<i class="fa fa-map-o fa-xs"></i>
											</button>
											<button @click="changeView('Table')" type="button" class="btn btn-light" :class="viewMode=='Table' ? 'device-operation-view-button':''">
												<i class="fa fa-table fa-xs"></i>
											</button>
											<button @click="changeView('Tree')" type="button" class="btn btn-light" :class="viewMode=='Tree' ? 'device-operation-view-button':''">
												<i class="fa fa-sitemap fa-xs"></i>
											</button>
										</div>
									</div>
								</div>
							</div>
							<!--Floor Map-->
							<div  v-if="currentFloor" class="card-body border border-dark h-362" :class="viewMode=='Tree' ? 'tree-view-bg':''">
								<div class="row" v-if="viewMode=='Map'">
									<MapView :currentDevices="currentDevices" :currentFloor="currentFloor" :searchRoom="searchRoom" :zoomToggle="zoomToggle" @chooseRoom="chooseRoom" @chooseDevice="chooseDevice"></MapView>
								</div>
								<div class="row" v-if="viewMode=='Table'">
									<TableView :currentDevices="currentDevices" :currentFloor="currentFloor" :searchRoom="searchRoom" @chooseRoom="chooseRoom" @chooseDevice="chooseDevice"></TableView>
								</div>
								<div class="row" v-if="viewMode=='Tree'">
									<TreeView :currentFloor="currentFloor" :searchRoom="searchRoom" @chooseRoom="chooseRoom"></TreeView>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<!-- Devices Carousel-->
					<div class="col-md-12 pl-0 pr-0">
						<div class="card">
							<div class="card-header d-flex justify-content-between bg-dark-nav text-white">
								<div class="d-flex align-items-center">
									<div>
										{{currentRoom['ROOM_NAME']}} {{$t('devop.devices')}}
									</div>
									<!-- Search Devices -->
									<div class="ml-2" data-toggle="tooltip" data-placement="top" title="Control all Devices">
										<input @change='commandAll()' v-model="statusAll" class="tgl tgl-flat" id="switchAll" type="checkbox"/>
					    				<label class="tgl-btn mb-0" for="switchAll"></label>
									</div>
								</div>
								<div class="">
									<div class="input-group border border-secondary rounded">
										<input type="text" class="form-control form-control-sm" v-model="searchDevice" :placeholder="$t('devop.searchdev')">
										<div class="input-group-append">
											<span class="input-group-text"><i class="fa fa-search"></i></span>
										</div>
									</div>
								</div>
							</div>
							<DeviceCarousel :searchDevice="searchDevice" :currentDevices="currentDevices" :currentRoom="currentRoom" @chooseDevice="chooseDevice"></DeviceCarousel>
						</div>
					</div>
					<!-- Devices -->
					<div class="col-md-12 pl-0 pr-0">
						<div class="card device-operation-bottom">
							<div class="card-body bg-transparent" :class="chosenDevice ? '':'h-180'">
								<div v-if="chosenDevice" class="row">
									<!--Device Controller-->
									<div class="col-md-6">
										<div class="card my-2 ml-2 border-dark bg-transparent device-operation-card">
											<div class="card-header">
												<span class="font-weight-bold">{{chosenDevice['DEVICE_NAME']}}</span>
												<div class="pull-right">{{$t('devop.lastupdate')}} {{$d(new Date(chosenDevice['UPDATED_AT']), 'long', $i18n.locale)}}</div>
											</div>
											<div class="card-body" :class="chosenDevice.ONLINE_FLAG == 0 ? 'disableDiv':''">
												<DeviceCard :device="chosenDevice"></DeviceCard>
											</div>
										</div>
									</div>
									<!-- Device Graph-->
									<div class="col-md-6">
										<div class="card my-2 mr-2 border-dark bg-transparent device-operation-card">
											<div class="card-body">
												<DeviceGraph :device="chosenDevice"></DeviceGraph>
											</div>
										</div>
									</div>
								</div>

								<div v-else class="text-center text-white">
									<span class="align-middle">{{$t('devop.notavailable')}}</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	    <Footer></Footer>
	</div>
</template>
<script type="text/javascript">
	import DeviceCard from './DevicesModal/DeviceList.vue';
	import MapView from './MapView/MapView.vue';
	import TableView from './TableView/TableView.vue';
	import TreeView from './TreeView/TreeView.vue';
	import DeviceCarousel from './DeviceCarousel/DeviceCarousel.vue';
	import DeviceGraph from './DeviceGraph/DeviceGraph.vue';


	export default{
		components:{DeviceCard,MapView,TableView,TreeView,DeviceCarousel,DeviceGraph},
		props:{
			userid:''
		},
		data(){
			return{
				sample:'',
				params:'',
				floors:'',				//List of Floors
				rooms:'',				//List of Rooms
				currentFloor:'',		//Current Floor Selected
				currentRoom:'',			//Current Room Selected
				currentDevices:'',		//List of Devices based on Rooms
				chosenDevice:'',		//Specific Device that is chosen
				viewMode:'Map',			//View Mode of Device Operation (map,table,tree)
				zoomToggle:false,
				searchDevice:'',
				searchRoom:'',
				statusAll:''
			}
		},
		created(){
			//Trigger when receive a signal from 'commandDevice' bus emit
			this.$bus.$on('commandDevice',data => {
				this.commandDevice(data['GATEWAY_ID'],
									data['DEVICE_ID'],
									data['DEVICE_TYPE'],
									data['command'],
									data['value'],
									data['addlValue']);
			});
			//Get Floor List
			this.getFloors();
		},
		watch:{
			searchRoom:function(){
				//For Floor Map Searching
				if(this.searchRoom != ""){
					for(var i in this.currentFloor['FLOOR_MAP_DATA']['roomMap']){
						if(this.currentFloor['FLOOR_MAP_DATA']['roomMap'][i]['ROOM_NAME'] != undefined){
							if(this.currentFloor['FLOOR_MAP_DATA']['roomMap'][i]['ROOM_NAME'].toLowerCase().indexOf(this.searchRoom.toLowerCase()) >= 0){
								this.currentFloor['FLOOR_MAP_DATA']['roomMap'][i]['SEARCH_ROOM'] = "on";
							}else{
								this.currentFloor['FLOOR_MAP_DATA']['roomMap'][i]['SEARCH_ROOM'] = "";
							}
						}
					}
				}else{
					for(var i in this.currentFloor['FLOOR_MAP_DATA']['roomMap']){
						if(this.currentFloor['FLOOR_MAP_DATA']['roomMap'][i]['ROOM_NAME'] != undefined){
							this.currentFloor['FLOOR_MAP_DATA']['roomMap'][i]['SEARCH_ROOM'] = "";
						}
					}
				}
				//




			},
			//For Searching Devices
			searchDevice:function(){
				if(this.searchDevice != ""){
					for(var i in this.currentDevices){
						if(this.currentDevices[i]['DEVICE_NAME'].toLowerCase().indexOf(this.searchDevice.toLowerCase()) >= 0){
							this.currentDevices[i]['hide'] = false;
						}else{
							this.currentDevices[i]['hide'] = true;
						}
					}
				}else{
					for(var i in this.currentDevices){
						this.currentDevices[i]['hide'] = false;
					}
				}
			},
			//Refresh Search status whenever device change
			currentDevices:function(){
				for(var i in this.currentDevices){
					this.currentDevices[i]['hide'] = false;
				}
			},
		},
		mounted(){
			//Add this to all child components for multilingual
			this.$i18n.locale = this.$parent.locale;
			//Listen to incoming device update
			Echo.channel('test').listen('.deviceCommandEvent', (e) => {
				console.log(e.data);
				let eventData = e.data;
				let device_id = e.data.DEVICE_ID;

				for(var i in this.currentDevices){
					if(this.currentDevices[i]['DEVICE_ID'] == e.data['DEVICE_ID']){
						this.currentDevices[i]['DATA'] = e.data['DATA'];
					}
				}

				this.$bus.$emit('updateDeviceData-' + device_id ,eventData);
			})
		},
		methods:{
			//Function Name: commandAll
			//Function Description: Command all devices in selected room
			//Param: gateawy_id, device_id, device_type, statusAll
			commandAll(){


				let command 	='';
				let value 		='';
				let gateway_id 	='';
				let device_id 	='';
				let device_type ='';
				let extVal 		='';

				if(this.statusAll==true){
					value = 1;
					this.$toast.success("All device turned on.","Device Command",{position:'topCenter'});
				}else{
					value = 0;
					this.$toast.warning("All device turned off.","Device Command",{position:'topCenter'});
				}
				for(var i in this.currentDevices){

					switch(this.currentDevices[i]['DEVICE_TYPE']){
						case 'wall_switch_1':
						case 'wall_switch_2':
						case 'wall_switch_3':
							command = 'status';
							gateway_id = this.currentDevices[i]['GATEWAY_ID'];
							device_id = this.currentDevices[i]['DEVICE_ID'];
							device_type = this.currentDevices[i]['DEVICE_TYPE'];
							extVal = '';
							this.commandDevice(gateway_id,device_id,device_type,command,value,extVal);
							break;
						case 'embedded_switch_1':
						case 'embedded_switch_2':
						case 'embedded_switch_3':
							command = 'status';
							gateway_id = this.currentDevices[i]['GATEWAY_ID'];
							device_id = this.currentDevices[i]['DEVICE_ID'];
							device_type = this.currentDevices[i]['DEVICE_TYPE'];
							extVal = '';
							this.commandDevice(gateway_id,device_id,device_type,command,value,extVal);
							break;
						case 'gas_valve':
							command = 'status';
							gateway_id = this.currentDevices[i]['GATEWAY_ID'];
							device_id = this.currentDevices[i]['DEVICE_ID'];
							device_type = this.currentDevices[i]['DEVICE_TYPE'];
							extVal = '';
							this.commandDevice(gateway_id,device_id,device_type,command,value,extVal);
							break;

					}

				}
			},
			//Function Name: zoomMapToggle
			//Function Description: Toggles zoom function in map view
			//Used By: Map View
			zoomMapToggle(){
				this.zoomToggle = !this.zoomToggle;
			},
			//Function Name: modifyFloorMapData
			//Function Description: Modify Floor Map Data to work with the Map View
			//Used By: Map View
			modifyFloorMapData(){

				//Process only for Floor Map
					for(var i in this.rooms){
						for(var j in this.currentFloor['FLOOR_MAP_DATA']['roomMap']){
							if(this.rooms[i]['ROOM_MAP_DATA']['ROOM_MAP'] == this.currentFloor['FLOOR_MAP_DATA']['roomMap'][j]['roomMap'])
							{
								//Use for selecting room Key when use chooseRoom Function
								this.currentFloor['FLOOR_MAP_DATA']['roomMap'][j]['roomFunctionKey'] = i;
								this.currentFloor['FLOOR_MAP_DATA']['roomMap'][j]['ROOM_NAME'] = this.rooms[i]['ROOM_NAME'];
								this.currentFloor['FLOOR_MAP_DATA']['roomMap'][j]['SEARCH_ROOM'] = '';

								for(var k in this.currentFloor['FLOOR_MAP_DATA']['roomMap'][j]['deviceCoor']){
									let device_coordinates_name = this.currentFloor['FLOOR_MAP_DATA']['roomMap'][j]['deviceCoor'][k]['name'];
									for(var l in this.currentDevices){
										if(device_coordinates_name == this.currentDevices[l]['DEVICE_MAP_NAME']){
											this.currentFloor['FLOOR_MAP_DATA']['roomMap'][j]['deviceCoor'][k]['deviceFunctionKey'] = l;
											this.currentFloor['FLOOR_MAP_DATA']['roomMap'][j]['deviceCoor'][k]['DEVICE_NAME'] = this.currentDevices[l]['DEVICE_NAME'];
											break;
										}
									}
								}
								break;
							}
						}
					}
			},
			//Function Name: getFloors
			//Function Description: Retrieve floors where user is authenticated to operate and set first entry as default data
			//Params: userid
			getFloors(){
				axios.post('floors/getAuthFloor',{USERID:this.userid})
				.then(response=>{
					if(response.data.length > 0){
						this.floors = response.data;							//Get all floors
						this.rooms = response.data[0]['rooms'];					//Get all rooms
						this.currentFloor = response.data[0];					//Get default current floor
						this.currentRoom = response.data[0]['rooms'][0];		//Get default current room
						this.getRoomDevices(this.currentRoom['ROOM_ID']);
					}
				});
			},
			//Function Name: getRoomDevices
			//Function Description: Retrieve selected room devices and set first entry as default data
			//Params: id (currentRoom.ROOM_ID)
			getRoomDevices(id){
				axios.get('rooms/' + id + '/meters')
				.then(response=>{
					if(response.data.length > 0){

						//Simple animation for device carousel when clicked
						$('.device-carousel').animate({width:'150px',height:'100px'},100);
						this.currentDevices = response.data;
						this.chosenDevice = response.data[0];
					}else{
						this.currentDevices = [];
						this.chosenDevice = '';
					}
					this.modifyFloorMapData();
				}).then(response=>{
					this.chooseDevice(0);
				});
			},
			//Function Name: chooseFloor
			//Function Description: Selects floor based on user input
			//Params: key (index in floors)
			chooseFloor(key){
				this.currentFloor = this.floors[key];
				axios.get('floors/' + this.currentFloor['FLOOR_ID'] + "/rooms").
				then(response=>{
					if(response.data.length > 0){
						this.rooms = response.data;					//Get all rooms
						this.currentRoom = response.data[0];
						this.getRoomDevices(this.currentRoom['ROOM_ID']);
					}
				});
			},
			//Function Name: chooseRoom
			//Function Description: Choose a Current Room
			//Param:Key(Floor View) or Data(Table View & Tree View)
			chooseRoom(data){
				if(data === undefined){
					this.chosenDevice = '';
					this.currentDevices = '';
				}
				//Data is from Map View
				else if(this.rooms[data]){
					this.currentRoom = this.rooms[data];
					this.getRoomDevices(this.currentRoom['ROOM_ID']);
				}
				//Data is from Table View
				else if(data['ROOM_ID']){
					this.currentRoom = data;
					this.getRoomDevices(this.currentRoom['ROOM_ID']);
				}

			},
			//Function Name: chooseDevice
			//Function Description: Choose Single Device
			//Param: key(index in currentDevices)
			chooseDevice(key){
				if(this.currentDevices[key]){
					//Simple animation for device carousel when clicked
					$('.device-carousel').animate({width:'150px',height:'100px'},100);
					$('#device-' + key).clearQueue();
					$('#device-' + key).animate({width:'170px',height:'120px'},200);

					this.chosenDevice = this.currentDevices[key];
					if (this.chosenDevice.DEVICE_TYPE != 'electric_meter') {
						this.$bus.$emit('updateDeviceData-' + this.chosenDevice['DEVICE_ID'] ,this.chosenDevice['DATA']);
					}
				}
			},
			//Function Name: changeView
			//Function Description: Changes view mode on user input
			//Param: mode (Map View/Table View/Tree View)
			changeView(mode){
				this.viewMode = mode;
			},
			//Function Name: commandDevice
			//Function Description: Commands selected device
			//Params:
			//gateway_id  : Gateway ID
			//device_id   : Device ID
			//device_type : device Type
			//command     : Command
			//value       : Value
			commandDevice(gateway_id,device_id,device_type,command,value,extVal){
				//Initialize varables
				var gatewayID = gateway_id;
				var deviceID = device_id;
				var extValue = extVal;

				//this.retrieveDeviceData(device_id,device_type,'send');
				console.log(command + " : " + value);

				//Temporarily disable incoming refresh notification from this device.
				// this.pauseDeviceRefreshId = deviceID;
				// var self = this;
				// clearTimeout(this.deviceTimeout);
				// this.deviceTimeout = setTimeout(function(){
				// 	self.$set(self,'pauseDeviceRefreshId','');
				// 	self.$forceUpdate();
				// },3000);

				//Instruct device
				axios.post('instructions/send',
				{'GATEWAY_ID' : gatewayID,
				 'DEVICE_ID' : deviceID,
				 'COMMAND': command,
				 'VALUE' : value,
				 'addlValue' : extValue,
				 'TYPE' : "Manual"})
				.then(response=>{
					console.log(response.data);
				});

			},
		}
	};
</script>