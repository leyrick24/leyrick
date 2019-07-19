
<!--  <System Name> iBMS
 <Program Name> DeviceMapping.vue

 <Create> 2018.11.05 TP Harvey
 <Update> 2018.11.05 TP Harvey Device Mapping
 		  2018.11.06 TP Harvey Adding Functions
 		  2018.11.07 TP Harvey Device Mapping

 -->
 <template>
	<div class="col-md-12">
		<div class="row">
			<div class="col-md-4 mb-3">
				<div class=" my-1">{{floorData['FLOOR_NAME']}} / {{roomData['ROOM_NAME']}}</div>
				<div class="card">
					<div class="card-body h-388">
						<div class="row unselectable">
							<div class="col-md-12 device-mapping-svg">
								<!-- Room Map -->
								<svg class="svg-circle cursor-crosshair" preserverAspectRatio="none" @click="getPointPositionPercent()" viewBox="0 0 100 100">
									<a v-for="coor in deviceCoor"
									   v-on:mouseover="deviceHoverOn(coor['DEVICE_KEY'])"
									   v-on:mouseleave="deviceHoverOff(coor['DEVICE_KEY'])"
									   data-toggle="popover"
									   data-container="body"
									   :data-content="coor['DEVICE_NAME']"
									   data-placement="right">
										<circle :cx="coor['coor']['cx']" :cy="coor['coor']['cy']" r="4" vector-effect="non-scaling-stroke" class="hilight hilight-danger"/>
									</a>
								</svg>
								<!-- End Room Map -->
								<img class="svg-image" v-if="roomImage!=''" :src="roomImage">
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- Devices -->
			<div class="col-md-8 mb-3">
				<div class=" my-1">{{$t('floor.plotDevice')}}</div>
				<div class="card">
					<div class="card-body overflow-y-hidden h-388">
						<div class="row font-size-12">
							<!-- Devices -->
							<div v-for="device,key in devices" class="col-md-3 device-card-col-paddings">
								<div v-if="device['PLOTTED']==true" class="h-57 card text-white bg-info my-1 mx-1" :class="device['HOVER']==true ? 'device-plot-card-hover':''">
									<div class="card-body p-3 overflow-hidden">
										<div class="row">
											<div class="col-md-9">
												{{device['DEVICE_NAME']}}
											</div>
											<div class="col-md-3">
												<i @click="deletePlottedDevice(device['DEVICE_MAP_NAME'])" class="fa fa-times fa-lg pointer"></i>
											</div>
										</div>
									</div>
								</div>
								<div v-else class="h-57 card bg-light my-1 mx-1">
									<div class="card-body ">
										{{device['DEVICE_NAME']}}
									</div>
								</div>
							</div>
							<!-- End Devices -->
						</div>
					</div>
				</div>
			</div>
			<!--Device end Tag -->
		</div>
		<div v-if="chosenModal=='addDevicePlotModal'">
			<AddDevicePlotModal :roomMapName="roomMapName" :coordinates="coordinates" :devices="devices" @close="closeModal"></AddDevicePlotModal>
		</div>
	</div>
 </template>
 <script type="text/javascript">
 	import AddDevicePlotModal from "../Modals/AddDevicePlotModal.vue";
 	export default{
 		components:{
 			AddDevicePlotModal
 		},
 		props:{
 			roomData:'',
 			floorData:''
 		},
 		data(){
 			return{
 				rooms:'',
 				roomKey:'',
 				chosenRoom:'',
 				roomImage:'',
 				roomMapName:'',
 				deviceCoor:'',
 				devices:'',
 				chosenModal:'',
 				floorID:'',					//Trigger true of hovered

 				coordinates:{
	 				cx:0,
	 				cy:0
 				},

 				roomPlotter:{					//For Room Plotting
					coor : '',					//for Map Coordinates
					mouseX:'',					//mouse X coordinates
					mouseY:'',					//mouse Y coordinates
					html:''						//for output
				},
 			}
 		},
 		created(){
 			//Check if roomData is not empty
 			if(this.roomData !== ""){
 				this.chosenRoom = this.roomData;
 				this.getRooms(this.chosenRoom['FLOOR_ID']);
 			}else{

 			}

 		},
 		watch:{
 			roomData:function(){
 				this.chosenRoom = this.roomData;
 				this.getRooms(this.chosenRoom['FLOOR_ID']);
 			}
 		},
 		methods:{
 			//Function Name: getRooms
 			//Function Description: Get Rooms
 			//Param: id (floor_id)
			getRooms(id){
				axios.post("floors/" + id + "/rooms?include=floor")
				.then(response => {
					this.rooms = response.data;
					this.getRoomImage();
				});
			},
			//Function Name: deviceHoverOn
			//Function Description: highlight on device hover
			//Param: key (index in device)
			deviceHoverOn(key){
				this.devices[key]['HOVER'] = true;
				//I use this because the Class Hover cant trigger if we dont change any data
				this.devices[key]['DEVICE_NAME'] = this.devices[key]['DEVICE_NAME'] + ' ';
			},
			//Function Name: deviceHoverOff
			//Function Description: transparent on device hover off
			//Param: key (index in device)
			deviceHoverOff(key){
				this.devices[key]['HOVER'] = false;
				//I use this because the Class Hover cant trigger if we dont change any data
				this.devices[key]['DEVICE_NAME'] = this.devices[key]['DEVICE_NAME'] + ' ';
			},
			//Function Name: deletePlottedDevice
			//Function Description: Delete a specific device coordinates on Floor DB
			//Param: data (device coordinate name)
 			deletePlottedDevice(data){
 				this.$swal({
 					title:'Are you sure?',
 					text:'Delete Device Plot',
 					type:'warning',
 					showCancelButton:true,
 					cancelButtonColor:'#d3   3',
 					confirmButtonColor:'#3085d6',
 					confirmButtonText:'Delete'
 				}).then((result)=>{
					if(result.value){
						axios.get('floors/' + this.floorID)
						.then(response =>{
							//Floor Map Data of DB
							let floorMapData = response.data['FLOOR_MAP_DATA'];
							var newdata = null;
							//Match all  Database Room Map Name to
							//current room map
							for(var i in floorMapData['roomMap']){
								//Match DB Map Name and Current Map Name
								if(floorMapData['roomMap'][i]['roomMap'] == this.chosenRoom['ROOM_MAP_DATA']['ROOM_MAP']){
									// Loop all device Coordinate
									for(var j in floorMapData['roomMap'][i]['deviceCoor']){
										// Match Device Coordinate name of database to the
										// current Device coordinate name to be deleted .
										if(floorMapData['roomMap'][i]['deviceCoor'][j]['name'] == data){

											//Remove the Device Coordinate
											floorMapData['roomMap'][i]['deviceCoor'].splice(j,1);

											axios.post('floors/update',
	 										{'FLOOR_ID':this.floorID,'FLOOR_MAP_DATA':floorMapData})
	 										.then(response =>{
	 											this.getRoomImage();
											});
											break;
										}
									}
								}
							}
						});
						this.$swal({
							title:'Deleted',
							text:'Device plot has been deleted',
							type:'success',
							showConfirmButton:false,
							timer:1000
						});
 					}
 				});
 			},
 			//Function Name: getRoomImage
 			//Function Description: This will generate an Image of the Floor from Database,
 			// Will also get other data from database to be process
 			//Param: floor_id
		 	getRoomImage(){
 				//Room Map Name
 				let roomMapName = this.chosenRoom['ROOM_MAP_DATA']['ROOM_MAP'];
 				this.roomMapName = this.chosenRoom['ROOM_MAP_DATA']['ROOM_MAP'];
 				let roomID = this.chosenRoom['ROOM_ID'];

 				let floorID = this.chosenRoom['floor']['FLOOR_ID'];
 				this.floorID = this.chosenRoom['floor']['FLOOR_ID'];
 				//Collections of Room Map from Database
 				axios.get('floors/' + floorID)
 				.then(response =>{
 				let roomMaps = response.data['FLOOR_MAP_DATA']['roomMap'];

 				//Matching Current Rooms to Rooms in Database
				for(var i in roomMaps){
				 	if(roomMaps[i]['roomMap'] == roomMapName){
				 		this.roomImage = roomMaps[i]['roomImage'];
				 		this.deviceCoor = roomMaps[i]['deviceCoor'];

				 		//Input new data for hover
				 		for(var i in this.deviceCoor){
				 			this.deviceCoor[i]['hover'] = false;
				 		}
				 		break;
				 	}
				}
				});
 				this.getDevices();
 			},
 			//Function Name: Get Devices
 			//Function Description: Get Devices from database
 			//Param: chosenRoom["ROOM_ID"]
 			getDevices(){
 				axios.get('rooms/' + this. chosenRoom['ROOM_ID'] + '/devices')
 				.then(response =>{
 					this.devices = response.data;
 					//Input new data for hover
 					for(var i in this.devices){
 						this.devices[i]['HOVER'] = false;
 					}

 					this.matchDeviceCoordinates();
 				});
 			},
 			//Function Name: matchDeviceCoordinates
 			//Function Description: Match Device Coordinates to Devices
 			//Please edit this next
 			matchDeviceCoordinates(){

 				for(var i in this.deviceCoor){
 					for(var j in this.devices){

 						if(this.devices[j]['DEVICE_MAP_NAME'] == this.deviceCoor[i]['name']){
 							this.deviceCoor[i]['DEVICE_NAME'] = this.devices[j]['DEVICE_NAME'];
 							this.deviceCoor[i]['DEVICE_KEY'] = j;
 							this.devices[j]['PLOTTED'] = true;
 							break;
 						}
 					}
 				}
 			},
 			//Function Name: getPointPositionPercent
 			//Function Description: Compute the percentage of X and Y based on the image
 			getPointPositionPercent(){
				//Initialize variable
            	let mX = this.roomPlotter.mouseX;
            	let mY = this.roomPlotter.mouseY;
            	let coor = this.roomPlotter.coor;
            	let html = this.roomPlotter.html;

            	//Get mouse coordinates
            	let pointWidth = event.clientX - $(".svg-circle").offset().left;
            	let pointHeight = event.clientY - $(".svg-circle").offset().top + $(window)['scrollTop']();

            	//computing the percentage of X and Y base on image
				mX = parseFloat((pointWidth / $(".svg-circle").width()) * 100).toFixed(3);
				mY = parseFloat((pointHeight / $(".svg-circle").height()) * 100).toFixed(3);

				//for first command (collecting coordinates every click)
				this.coordinates['cx'] = mX;
				this.coordinates['cy'] = mY;

				this.openModal('addDevicePlotModal');
 			},
 			//Function Name: openModal
 			//Function Descriptionn: Open Specific modal
 			//Param: data (modal)
 			openModal(data){
 				this.chosenModal = data;
 			},
 			//Function Name: closeModal
 			//Function Description: Close Modal
 			closeModal(){

 				this.getRoomImage();
 				this.chosenModal = '';

 			},
 			//Function Name: btnBack
 			//Function Description:Back button
 			btnBack(){
 				this.$emit('close','roomTable');
 			}
 		}
 	};
 </script>