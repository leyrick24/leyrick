<!--  <System Name> iBMS
 <Program Name> DeviceMapping.vue

 <Create> 2018.11.05 TP Harvey
 <Update> 2018.11.05 TP Harvey
 		  2018.11.06 TP Harvey
 		  2018.11.07 TP Harvey Adding Plotting Modal
 		  2018.11.08 TP Harvey Optimizing Adding Device Plot
 -->
<template>
	<div class="modal d-block">
		<div class="modal-background" @click="closeModal()"></div>
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					{{$t('floor.devicePlot')}}
				</div>
				<div v-if="devices!=''" class="modal-body">
					<div class="form-group">
						<div class="card bg-light">
							<div v-if="deviceSelected!==''" class="card-body">
								{{devices[deviceSelected]['DEVICE_NAME']}}
							</div>
							<div v-else class="card-body">
								{{$t('floor.selectDevice')}}
							</div>
						</div>
					</div>
					<div class="form-group">
						<ul class="list-group border-top border-bottom device-plot-list-group-scroll">
							<li v-for="device,key in devices"
								@click="selectDevice(key)"
								:class="device['PLOTTED']==true ? 'bg-info text-white' : '' "
								class="list-group-item list-group-item-action pointer">
								{{device['DEVICE_NAME']}}
							</li>
						</ul>
						<div id="plottedNote" class="font-italic" style="display:none;color:red">{{$t('floor.deviceNote')}}</div>
					</div>
				</div>
				<div class="modal-body" v-else>
					{{$t('floor.noDevice')}}
				</div>
				<div class="modal-footer">
					<button class="btn btn-primary" @click="btnSave">{{$t('user.save')}}</button>
					<button class="btn btn-secondary" @click="closeModal()">{{$t('user.cancel')}}</button>
				</div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default{
		props:{
			coordinates:'',
		 	devices:'',
		 	roomMapName:'',
		 	floor:''
		},
		data(){
			return{
		 		deviceSelected:""
			}
		},
		created(){

		},
		methods:{
			//Function Name: select Device
			//Function Description: Choose Device to register the coordinates
			//Param: key (index in devices)
			selectDevice(key){
				this.deviceSelected = key;

				$('#plottedNote').clearQueue();
				if(this.devices[key]['PLOTTED']){
					$("#plottedNote").slideDown();
				}else{
					$("#plottedNote").slideUp();
				}
			},
			//Function Name: btnSave
			//Function Description: save device plot
			btnSave(){
				//1. Updates the current Device Map Name for the new Device Plot in Device Table
				//2.
				if(this.deviceSelected !== ""){
					//Device ID
					let deviceID = this.devices[this.deviceSelected]['DEVICE_ID'];
					//Device Map Name Old
					let deviceMapNameOld = this.devices[this.deviceSelected]['DEVICE_MAP_NAME'];
					//Floor ID
					let floorID = this.devices[this.deviceSelected]['FLOOR_ID']
					//Create random key for matching room name and
					//room name in loor data.
					let randomName = (Math.random() + 1).toString(36).substr(2,10);

					axios.post('devices/update',
						{'DEVICE_ID': deviceID,'DEVICE_MAP_NAME':randomName}
						).then(response => {

						}).catch(error=>{
							console.log(error);
						});

					//Get Floor Data to be save on DB
					axios.post('floors/' + floorID)
					.then(response => {
						let floorMapData = response.data['FLOOR_MAP_DATA'];
						for(var i in floorMapData['roomMap']){
							//Compare roomMap from Room and Room Map from Floor Database
							if(floorMapData['roomMap'][i]['roomMap'] == this.roomMapName){

								//Remove the current device coordinates to avoid duplication of coordinates
								for(var j in floorMapData['roomMap'][i]['deviceCoor']){
									if(deviceMapNameOld == floorMapData['roomMap'][i]['deviceCoor'][j]['name']){
										floorMapData['roomMap'][i]['deviceCoor'].splice(j,1);
										console.log('Coordinates deleted!');
										break;
									}
								}

								//Create new Device Coordinates to be save on database
								let newDeviceCoor= {"coor":
										{"cx":this.coordinates['cx'],
										 "cy":this.coordinates['cy']
										},
										"name":randomName
									};



								if(!floorMapData['roomMap'][i]['deviceCoor']){
									floorMapData['roomMap'][i]['deviceCoor'] = [];
								}

								floorMapData['roomMap'][i]['deviceCoor'].push(newDeviceCoor);

								//Update FLOOR MAP DATA to add new Device Coordinates
								axios.post('floors/update',
								{'FLOOR_ID':floorID,'FLOOR_MAP_DATA':floorMapData})
								.then(response => {
									this.$swal({
										type:'success',
										text:'Successfully Added!',
										showConfirmButton:false,
										timer:1000
									}).then(()=>
										this.closeModal()
									);
								});
								break;
							}
						}
					});

				}else{
					this.$swal({
						type:'error',
						text:'Please select device',
						showConfirmButton:false,
						timer:1000
					});
				}
			},
			//Function Name: closeModal
			//Function Description: Closes modal
			closeModal(){
				this.$emit('close');
			}
		}

	};

</script>