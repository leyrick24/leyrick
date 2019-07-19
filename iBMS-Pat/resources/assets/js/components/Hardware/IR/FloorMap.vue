<template>
	<div class="">
		<div class="row">
			<!-- Floor Map -->
			<div id="zoomMap" class="col-md-12">
				<svg class="svg" preserveAspectRatio="none" viewBox="0 0 100 100">
					<path v-b-tooltip.hover
						v-if="true"
						:title="room['ROOM_NAME']"
						:d="room['coor']"
						:class="hilight_key==key ? 'hilight-selected' : (room['SEARCH_ROOM']=='on') ? 'hilight-selected' : ''"
						v-for="room,key in currentFloor['FLOOR_MAP_DATA']['roomMap']"
						class="hilight ir-highlight"
						vector-effect="non-scaling-stroke"/>
				</svg>
				<img id="FloorMap" class="svg-image unselectable" :src="currentFloor['FLOOR_MAP_DATA']['floorImage']">
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default{
		props:{
			currentFloor:'',
			currentRoom:'',
			zoomToggle:'',
			searchRoom:''
		},
		data(){
			return{
				selectedRoom:'',
				displayMode:'floor',
				hilight_key:'0',
				rooms:''
			}
		},
		created(){
			this.getRooms(this.currentFloor.FLOOR_ID);
		},
		mounted(){

		},
		watch:{
			searchRoom:function(){
				this.$forceUpdate();
			},
			currentRoom:function(){
				if (this.currentRoom != null) {
 					this.chooseRoom('','click',this.currentRoom.ROOM_ID-this.rooms[0].ROOM_ID);
				}
			},
			//Watch for changes of Current Floor
			currentFloor:function(){
				this.getRooms(this.currentFloor.FLOOR_ID);
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
			//Function name: chooseRoom
			//Function Description: Choose a specific room
			//Param: room (room), event (click), hilight_key (index in currentFloor.room)
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
 			//Function Name: getRooms
 			//Function Description: Get Rooms
 			//Param: floor_id
			getRooms(id){
				axios.post("floors/" + id + "/rooms?include=floor")
				.then(response => {
					this.rooms = response.data;
				});
			},
			//Function Name: chooseDevice
			//Function Description: Click on Device and emit
			//Param: key (device)
			chooseDevice(key){
				this.$emit('chooseDevice',key);
			}
		}
	};
</script>
