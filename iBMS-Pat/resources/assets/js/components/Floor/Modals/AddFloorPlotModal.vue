
<!--  <System Name> iBMS
 <Program Name> AddFloorPlotModal.vue

 <Create> 2018.10.31 TP Harvey
 <Update> 2018.11.05 TP Harvey Plotting and add Functions

 -->

<template>
	<div class="modal d-block">
		<div class="modal-background" @click="closeModal"></div>
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content" style="margin-top:-18px">
				<div class="modal-header">
					{{$t('floor.savePlot')}}
				</div>
				<div class="modal-body">
					<div class="form-group">
						<!-- Select Room -->
						<label for="selRoom">{{$t('floor.selectRoom')}}:</label>
						<select class="form-control" id="selRoom" v-model="roomSelected">
							<option disabled value=null>{{$t('floor.pleaseSelect')}}</option>
							<option v-for="room,key in rooms" :value="key">{{room['ROOM_NAME']}}</option>
						</select>
					</div>
					<div class="row">
						<div class="col-md-12">
							<!-- Show plot preview -->
							<div class="floor-save-plot">
								<svg class="svg" preserveAspectRatio="none" viewBox="0 0 100 100">
								<path :d="coordinates" vector-effect="non-scaling-stroke" class="hilight hilight-plotted"></path>
								</svg>
							</div>
						</div>

					</div>
				</div>
				<div class="modal-footer">
					<!-- Buttons -->
					<button class="btn btn-success" @click="btnSave">{{$t('user.save')}}</button>
					<button class="btn btn-secondary" @click="closeModal">{{$t('user.cancel')}}</button>
				</div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default{
		props:{
			floorData:'',
			coordinates:''
		},
		data(){
			return{
				rooms:'',				//Chosen room
				roomSelected:null		//Int key number of chosen room
			}
		},
		created(){
			this.getRooms();
		},
		methods:{
			//Function Name: getRooms
			//Function Description: Get Floor from Database
			//Param: floorData['FLOOR_ID']
			getRooms(){
				axios.post("floors/"+ this.floorData['FLOOR_ID'] + "/rooms" )
				.then(response => {
					this.rooms = response.data;
				});
			},
			//Function Name: btnSave
			//Function Description: Save the Room plot to database
			btnSave(){
				if(this.roomSelected != null){
					var floorID = this.floorData['FLOOR_ID'];
					var floorMapData = '';
					var roomSelectedName = this.rooms[this.roomSelected]['ROOM_MAP_DATA']['ROOM_MAP']; //Selected Room from Database;

					axios.post('floors/' + floorID)
					.then(response=>{
						if(response.data){
							floorMapData = response.data['FLOOR_MAP_DATA'];
							for(var i in floorMapData['roomMap']){

								if(floorMapData['roomMap'][i]['roomMap'] == roomSelectedName){
									floorMapData['roomMap'][i]['coor'] = this.coordinates;

									//Update to databse
									axios.post('floors/update',
										{'FLOOR_MAP_DATA':floorMapData,
										'FLOOR_ID':floorID})
									.then(response => {
										this.$swal({
											type:'success',
											title:'Successfully updated',
											showConfirmButton:false,
											timer:1500
										}).then(()=>
											this.closeModal()
										);
									});
								}
							}
						}else{
							this.$swal({
								type:'error',
								title:"Can't find floor data.",
								showConfirmButton:false,
								timer:1500
							});
						}

					});


				}else{
					this.$swal({
						type:'error',
						title:'Please select Floor',
						showConfirmButton:false,
						timer:1500
					});
				}
			},
			//Function Name: closeModal
			//Function Description: Close Modal
			closeModal(){
				this.$emit('close');
			}
		}
	};
</script>