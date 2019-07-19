<template>
	<div class="col-md-12">
		<div class="row">
			<!-- Floor Map -->
			<div id="zoomMap" class="col-md-12">
				<svg class="svg" preserveAspectRatio="none" viewBox="0 0 100 100">
					<path v-b-tooltip.hover
						v-if="true"
						:title="room['ROOM_NAME']"
						:d="room['coor']"
						:class="hilight_key==key ? 'hilight-selected' : (room['SEARCH_ROOM']=='on') ? 'hilight-selected' : ''"
						@dblclick="chooseRoom(room,'dblclick',key)"
						@click="chooseRoom(room,'click',key)"
						v-for="room,key in currentFloor['FLOOR_MAP_DATA']['roomMap']"
						class="hilight hilight-connected"
						vector-effect="non-scaling-stroke"/>
				</svg>
				<img id="FloorMap" class="svg-image unselectable" :src="currentFloor['FLOOR_MAP_DATA']['floorImage']">
			</div>
		</div>
		<div v-if="displayMode=='room'" id="RoomMap" class="d-flex justify-content-center position-absolute" style="top:0px;height:100%;width:100%">
			<div class="row position-absolute left-250">
				<div class="col-md-12">
					<i class="fa fa-times-circle-o fa-2x pointer" @click="closeRoom()" style="margin-right:20px;">

					</i>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 device-mapping-svg">
					<!-- Map of Devices-->
					<svg class="svg-circle" preserverAspectRatio="none" viewBox="0 0 100 100">
						<a v-for="coor in selectedRoom['deviceCoor']"
						   data-toggle="popover"
						   data-container="body"
						   :data-content="coor['DEVICE_NAME']"
						   data-placement="right">
							<circle :cx="coor['coor']['cx']"
									:cy="coor['coor']['cy']"
									r="4"
									vector-effect="non-scaling-stroke"
									class="hilight hilight-danger pointer"
									@click="chooseDevice(coor['deviceFunctionKey'])"/>
						</a>
					</svg>
					<img  class="device-operation-device-svg unselectable" v-if="selectedRoom['roomImage']!=''" :src="selectedRoom['roomImage']">
					<!-- End Room Map -->
				</div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default{
		props:{
			currentFloor:'',
			currentDevices:'',
			zoomToggle:'',
			searchRoom:''
		},
		data(){
			return{
				selectedRoom:'',
				displayMode:'floor',
				hilight_key:'0'
			}
		},
		created(){

		},
		mounted(){

		},
		watch:{
			searchRoom:function(){
				this.$forceUpdate();
			},
			//Watch for changes of zoom Toggle
			zoomToggle:function(){
				if(this.zoomToggle==true){
					$('#zoomMap').zoom({magnify:0.8});
				}else{
					$('#zoomMap').trigger('zoom.destroy');
				}
			},
			//Watch for changes of Current Floor
			currentFloor:function(){
				$('#FloorMap').fadeTo(500,1);
				this.displayMode='';
				this.hilight_key=0;
			}
		},
		methods:{
			//Function Name: closeRoom
			//Function Description: Exit Room View
			closeRoom(){
				 $('#FloorMap').fadeTo(500,1);
				 this.displayMode='';
			},
			//Function Name: chooseRoom
			//Function Description: Choose a specific room
			//Param: room (room), event (click), hilight_key(index in currentFloor.room)
 			chooseRoom(room,event,hilight_key){
 				if(event == 'dblclick'){
 					this.selectedRoom = room;
 					$('#FloorMap').fadeTo(500,0.05);
 					$('#RoomMap').fadeTo(500,1);
 					this.displayMode='room';
 					this.$emit('displayMode', this.displayMode);
 				}
 				this.hilight_key = hilight_key;
 				this.$emit('chooseRoom',room['roomFunctionKey']);
 			},
 			//Function Name: chooseDevice
			//Function Description: Click on Device and emit data
			//Param: key (device)
			chooseDevice(key){
				this.$emit('chooseDevice',key);
			}
		}
	};
</script>
