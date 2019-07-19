<template>
	<div class="col-md-12">
		<div class="row">
			<div class="col-md-12">
				<!-- Header Button Floor -->
				<div class="d-flex justify-content-center">
					<div>
						<button class="btn btn-lg tree-view-custom-btn-header">{{currentFloor['FLOOR_NAME']}}</button>
						<div class="d-flex justify-content-center">
							<div class="tree-view-header-line" v-if="renderedRoom.length > 0"></div>
						</div>
					</div>
				</div>
				<!-- Rooms -->
			</div>
			<div class="col-md-12 tree-view-carousel">
				<carousel :perPage='1' :mouse-drag='false' :navigationEnabled="true">
					<slide v-for="slidePage,slideKey in Math.ceil(renderedRoom.length/11)" :key="slideKey">
						<div class="row">
							<!-- Header Button Line -->
							<div class="col-md-12">
								<div class="d-flex justify-content-center position-relative">
									<div class="tree-view-header-div-line"></div>
								</div>
							</div>
							<div v-for="room,key in renderedRoom" v-if="showRoom(key,slideKey)"  class="col-md-2" :class="checkCounter(key)=='indent' ? 'tree-view-margin' :''">
								<div class="d-flex justify-content-center">
									<div style="height:15px;"></div>
									<!-- Vertical Lign -->
									<div :class="verticalLineChecker(key,slideKey)"></div>
								</div>
								<div class="d-flex justify-content-center">
									<div @click="chooseRoom(room)" class="btn tree-view-custom-btn d-flex justify-content-center align-items-center">{{room['ROOM_NAME']}}</div>
								</div>
							</div>
						</div>
					</slide>
				</carousel>
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
				rooms:'',
				renderedRoom:'',
			}
		},
		created(){
			this.getFloorRooms();
		},
		mounted(){
		},
		watch:{
			//Watch for changes of Current Floor
			currentFloor:function(){
				this.getFloorRooms();
			},
			searchRoom:function(){
				this.triggerSearch();
			},

		},
		computed:{

		},
		methods:{
			//Function Name: chooseRoom
			//Function Description: Choose Room
			//Param: room
			chooseRoom(room){
				this.$emit('chooseRoom',room);
			},
			//Function Name: checkCounter
			//Function Description: Check number of rooms for row indentions
			//Param: key (index for rooms)
			checkCounter(key){
				if((key-6)%11==0){
					return 'indent';
				}
			},
			//Function Name: showRoom
			//Function Description: show Room for specific pages
			//Param: key (index in rooms), slideKey (slide index key)
			showRoom(key,slideKey){
				if((key >= (11 * slideKey)) && (key <(11 * slideKey + 11))){
					return true;
				}else{
					return false;
				}
			},
			//Function Name: triggerSearch
			//Function Description: Trigger search function
			triggerSearch(){
				if(this.searchRoom != ""){
					var details = this.rooms;
					var search = this.searchRoom;

					this.renderedRoom = details.filter(function(obj){return obj.ROOM_NAME.toLowerCase().includes(search.toLowerCase());});
				}else{
					this.renderedRoom = this.rooms;
				}
				this.$forceUpdate();
			},
			//Function Name: verticalLineChecker
			//Function Description: Check Vertical Line adjustments depends on row
			//Param: key (index for rooms), slideKey (index for Carousel item)
			verticalLineChecker(key,slideKey){
				if(key >= (11 * slideKey + 6) && key <= (11 * slideKey + 10)){
					return 'tree-view-line-second-row';
				}else{
					return 'tree-view-line-first-row';
				}
			},
			//Function Name: getFloorRooms
			//Function Description: Get Rooms By Floor
			//Param: currentFloor['FLOOR_ID']
			getFloorRooms(){
				axios.post('floors/' + this.currentFloor['FLOOR_ID'] + '/rooms')
				.then(response =>{
					this.rooms = response.data;
					this.triggerSearch();
				});

			},
		}
	};
</script>