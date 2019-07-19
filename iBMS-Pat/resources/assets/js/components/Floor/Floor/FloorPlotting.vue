<!--  <System Name> iBMS
 <Program Name> FloorPlotting.vue

 <Create> 2018.10.31 TP Harvey
 <Update> 2018.10.31 TP Harvey Plotting
 		  2018.11.12 TP Harvey Button

 -->

<template>
	<div class="col-md-12">
		<div class="row">
			<div class="col-md-12 mb-3">
				<div class=" my-1">
					{{$t('floor.floorPlot')}}
				</div>
				<div class="card">
					<div class="card-body">
						<div class="row">
							<div class="col-md-12">
								<svg class="svg" preserveAspectRatio="none" @click="getPointPositionPercent()" viewBox="0 0 100 100">
									<path :d="coordinates" vector-effect="non-scaling-stroke" class="hilight hilight-plotted"></path>
								</svg>
								<img v-if="floor" :src="floor['FLOOR_MAP_DATA']['floorImage']" class="floor-image unselectable">
							</div>
							<div class="col-md-12">
								<div class="float-right">
									<button type="button" class="btn btn-sm btn-warning border border-dark" @click="clearCoordinates">{{$t('floor.clear')}}</button>
									<button type="button" class="btn btn-sm btn-warning border border-dark" @click="openModal('setPlotModal')">{{$t('floor.setPlot')}}</button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div v-if="modal=='setPlotModal'">
					<AddFloorPlotModal :floorData="floorData" :coordinates="coordinates" @close='closeModal'></AddFloorPlotModal>
				</div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
import AddFloorPlotModal from "../Modals/AddFloorPlotModal.vue";
	export default{
		components:{
			AddFloorPlotModal
		},
		props:{
			floorData:'',
		},
		data(){
			return{
				floor:null,
				coordinates:'',
				modal:'',

				roomPlotter:{					//For Room Plotting
					coor : '',					//for Map Coordinates
					mouseX:'',					//mouse X coordinates
					mouseY:'',					//mouse Y coordinates
					html:''						//for output
				},
			}
		},
		created(){
			this.getFloor();
		},
		methods:{
			//Function Name: getFloor
			//Function Description: Get Floor from database
			getFloor(){
				axios.post('floors/'+this.floorData['FLOOR_ID'])
				.then(response=>{
					this.floor = response.data;
				});
			},
			//Function name: getPointPositionPercent
			//Function Description: Plot to map
			//Param: e
			getPointPositionPercent(e){
				//Initialize variable
            	let mX = this.roomPlotter.mouseX;
            	let mY = this.roomPlotter.mouseY;
            	let coor = this.roomPlotter.coor;
            	let html = this.roomPlotter.html;

            	//Get mouse coordinates
            	let pointWidth = event.clientX - $(".svg").offset().left;
            	let pointHeight = event.clientY - $(".svg").offset().top + $(window)['scrollTop']();

            	//computing the percentage of X and Y base on image
				mX = parseFloat((pointWidth / $(".svg").width()) * 100).toFixed(3);
				mY = parseFloat((pointHeight / $(".svg").height()) * 100).toFixed(3);

				//for first command (collecting coordinates every click)
				if(this.roomPlotter.coor==''){
					coor = coor + "M" + mX + ' ' + mY;
				}else{
					coor = coor + ' L' + mX + ' ' + mY;
				}
				html = coor + 'Z';
                this.roomPlotter.coor = coor;
                this.coordinates = html;
			},
			//Function Name: clearCoordinates
			//Function Description: clears coordinates
			clearCoordinates(){
				this.coordinates='';
				this.roomPlotter.coor='';
			},
			//Function Name: openModal
			//Function Description: Open Set Plot Modal
			//Param: data (modal)
			openModal(data){
				if(this.coordinates!=''){
					this.modal = data;
				}else{
					this.$swal({
						type:'error',
						title:'Coordinate is empty',
						showConfirmButton:false,
						timer:1500
					});
				}
			},
			//Function Name: closeModal
			//Function Description: Closes modals
			closeModal(){
				this.modal='';
				this.coordinates='';
				this.roomPlotter.coor='';
			}
		},
		watch:{
			floorData:function(){
				this.getFloor();
			}
		}
	};
</script>