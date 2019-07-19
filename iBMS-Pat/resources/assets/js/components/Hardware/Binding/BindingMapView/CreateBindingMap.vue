<template>
	<div class="col-md-12">
		<div class="row">
			<!-- Floor Map -->
			<div id="zoomMap" class="col-md-12">
				<svg class="svg" preserveAspectRatio="none" viewBox="0 0 100 100">
					<path v-b-tooltip.hover
						:title="room['ROOM_NAME']"
						:d="room['coor']"
						:class="room['roomMap']==roomMapName ? 'hilight-selected' : ''"
						v-for="room,key in currentFloor['FLOOR_MAP_DATA']['roomMap']"
						class="hilight hilight-bind"
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
		},
		data(){
			return{
				selectedRoom:'',
				displayMode:'floor',
				hilight_key:'0',
				roomMapName:''
			}
		},
		created(){

		},
		mounted(){

		},
		watch:{
			//Watch for changes of Current Floor
			currentFloor:function(){
				$('#FloorMap').fadeTo(500,1);
				this.displayMode='';
				this.hilight_key=0;
			},
			currentRoom:function(){
				if(this.currentRoom != ''){
					this.roomMapName = this.currentRoom['ROOM_MAP_DATA']['ROOM_MAP'];
				}else{
					this.roomMapName = '';
				}
			}
		},
		methods:{
			//Function Name: clickRoom
			//Function Description: highlights selected room
			//Param: room (room)
			clickRoom(room){
				axios.post("/floors/" + this.currentFloor['FLOOR_ID'] + "/rooms")
				.then(response => {
					if(response.data.length > 0){
						for(var i in response.data){
							// console.log(response.data[i].ROOM_MAP_DATA.ROOM_MAP);
							if(room['roomMap'] == response.data[i].ROOM_MAP_DATA.ROOM_MAP){
								// console.log("match");
								this.$emit('selectRoom',response.data[i]);
							}
						}
					}


				});
			},
		}
	};
</script>
