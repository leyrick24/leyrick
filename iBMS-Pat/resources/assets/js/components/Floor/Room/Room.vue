
<!--  <System Name> iBMS
 <Program Name> Room.vue

 <Create> 2018.9.27 TP  Harvey
 <Update> 2018.9.28 TP  Harvey Table View
 	      2018.10.26 TP Harvey Bug Fix
 	      2018.11.05 TP Harvey Add Functions for Name Edit, Add functions for Device Mapping

 -->

<template>
	<div class="row">
		<div class="col-md-12 overflow-y-hidden" :class="rooms.length > 24 ? 'h-362':'h-400'">
			<b-table
						:items="rooms"
						:fields="table.fields"
						:current-page="table.currentPage"
						:per-page="table.perPage"
						:filter="roomSearch"
						@row-clicked="chooseRoom">
				<template slot="button" slot-scope="row">
					<a class="pointer room-action-btn" @click="openEditRoomModal(row.item)" data-toggle="popover" data-container="body"data-content="Edit" data-placement="right">
						<i class="fa fa-edit fa-lg" aria-hidden="true"></i>
					</a>
					<!-- <a class="pointer room-action-btn" @click="openDeviceMapping(row.item)" title="Plot">
						<i class="fa fa-map-marker fa-lg" aria-hidden="true"></i>
					</a> -->
					<a class="pointer room-action-btn" @click="openAddRoomGatewayModal(row.item)" data-toggle="popover" data-container="body"data-content="Add Gateway" data-placement="right">
						<i class="fa fa-download fa-lg" aria-hidden="true"></i>
					</a>
					<a class="pointer floor-action-btn" @click="deleteRoom(row.item)" data-toggle="popover" data-container="body" data-content="Delete" data-placement="right">
						<i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
					</a>
				</template>

				<!-- For Showing Details of gateway -->
				<template slot="show_details" slot-scope="row">
					<button @click.stop="row.toggleDetails" class="btn btn-sm background-orange">{{row.item.gateways.length}} {{$t('floor.gateway')}}</button>
				</template>
				<template slot="row-details" slot-scope="row">
					<b-table 	:items="row.item.gateways"
								:fields="table.gatewayDetailsFields">
					</b-table>
				</template>
				<!-- End -->
			</b-table>
			<!-- <div v-else class="mt-5 ml-5">
				<h1>{{$t('floor.noRooms')}}</h1>
			</div> -->
		</div>
		<div class="col-md-12">
			<div class="d-flex justify-content-center" :class="rooms.length > 24 ? '':'custom-pagination-black'">
				<b-pagination :total-rows="table.totalRows" :per-page="table.perPage" v-model="table.currentPage" />
			</div>
		</div>
		<div class="col-md-12">
			<div class="float-right add-floor-btn">
				<div @click="openModal('addRoomModal')" class="btn btn-sm background-orange"> <i class="fa fa-plus-square-o fa-lg" aria-hidden="true"></i> {{$t('floor.addRoom')}}</div>
			</div>
		</div>
		<div v-if="chosenModal=='editRoomModal'">
			<EditRoomModal :roomData="chosenRoom" @closeModal="closeModal" :rooms="rooms"></EditRoomModal>
		</div>
		<div v-if="chosenModal=='addRoomGatewayModal'">
			<AddRoomGatewayModal :roomDataProps="chosenRoom" :modalModeProps="'gatewayMode'" @closeModal="closeModal"></AddRoomGatewayModal>
		</div>
		<div v-if="chosenModal=='addRoomModal'">
			<AddRoomModal :floorID="floorID" @roomAdded="closeModal" @closeModal="closeModal"></AddRoomModal>
		</div>
	</div>
</template>
<script type="text/javascript">
	import EditRoomModal from "../Modals/EditRoomModal.vue";
	import AddRoomGatewayModal from "../Modals/AddRoomGatewayModal.vue";
	import AddRoomModal from "../Modals/AddRoomModal.vue";

	export default{

		components:{EditRoomModal,AddRoomGatewayModal, AddRoomModal},
		props:{
			floorID:"",
			roomSearch:"",
			locale:''
		},
		data(){
			return{
				table:{
					currentPage:1,
					perPage:6,
					totalRows:0,
					filter:null,
					pageOptions:[5,10,15],
					fields:[
					{key:'ROOM_ID',label:"Room ID"},
					{key:'ROOM_NAME',label:"Room Name"},
					{key:'show_details',label:'Gateways'},
					{key:'button',label:"Action",'class':'min-w-150'}
					],
					gatewayDetailsFields:[
						{key:"GATEWAY_ID",label:"Gateway ID",class:'text-black'},
						{key:"GATEWAY_IP",label:"Gateway IP",class:'text-black'},
						{key:"GATEWAY_NAME",label:"Gateway Name",class:'text-black'}
					],
				},
				rooms:[],
				chosenModal:'',
				chosenRoom:''
			}
		},
		created(){
			this.getFloorRooms();
		},
		methods:{
			//Function Name: getFloorRooms
			//Function Description: Get Rooms based on Floor
			//Param: floorID
			getFloorRooms(){
				if(this.floorID != ""){
					axios.post("floors/" + this.floorID + "/rooms?include=floor>gateways")
					.then(response => {
						if(response.data.length > 0){
							this.rooms = response.data;
							this.table.totalRows = response.data.length;
						}else{
							this.rooms = [];
						}
					});
				}
			},
			//Function Name: changeTableLabe
			//Function Description: Change table labels based on language settings
			//Param: $i18n.locale
			changeTableLabel(){
				//for table header multilingual support
				let fields = this.table.fields;
				let gateway = this.table.gatewayDetailsFields;
				let head = this.$t('floor.tableLabels');
				console.log(head);
				// for(var i in fields){
				// 	Object.keys(head).forEach(function(mess){
				// 		if (fields[i].key == mess) {
				// 			// console.log(head[mess]);
				// 			fields[i].label = head[mess];
				// 		}
				// 	});
				// }
				// this.$children[0].fields = fields;
				// console.log(this.$children[0]);
			},
			//Function Name: openEditRoomModal
			//Function Description: Open Edit Modal
			//Param: data (room)
			openEditRoomModal(data){
				this.chosenRoom = data;
				this.chosenModal = 'editRoomModal';
			},
			//Function Name: openAddRoomGatewayModal
			//Function Description: Open Add Room Gateway Modal
			//Param: data (room)
			openAddRoomGatewayModal(data){
				this.chosenRoom = data;
				this.chosenModal = 'addRoomGatewayModal';
			},
			//Function Name: chooseRoom
			//Function Description: choose room and highlight on floor map
			//Param: data (room), key (index in rooms)
			chooseRoom(data,key){
				//Clear the Selected hilight
				for(var i in this.rooms){
					this.$set(this.rooms[i],"_rowVariant","");
				}
				//Hilight selected
				this.$set(data,"_rowVariant","white");
				this.chosenRoom = data;
				this.$emit('openDeviceMapping',data);
			},
			//Function Name: closeModal
			//Function Description: Close Modal
			closeModal(){
				this.getFloorRooms();
				this.chosenModal = "";
			},
			//Function Name: openModal
			//Function Description: Open Modal
			//Param: data
			openModal(data){
				this.chosenModal = data;
			},
			//Function Name: deleteRoom
			//Function Description: Delete room
			//Param: data (room)
			deleteRoom(data){
				this.$swal({
					title:"Delete",
					text:"Are you sure?",
					type:"warning",
					showCancelButton:true,
					confirmButtonColor:"#3085d6",
					cancelButtonColor:"#d33",
					confirmButtonText:"Yes"
				}).then((result) =>{
					if(result.value){
						//Remove floor from database
						axios.post('rooms/delete',{ROOM_ID:data['ROOM_ID']})
						.then(response => {

							console.log(response.data);


							if(response.data == 'success'){
								this.$swal({
								title:'Deleted',
								text:"Floor has been deleted.",
								type:'success',
								timer:1500,
								showConfirmButton:false
								});
							}else{
								this.$swal(
								'Error',
								"Floor can't delete.",
								'error'
								);
							}
							this.getFloorRooms();
						});


					}

				});
			}
		},
		watch:{
			floorID:function(){
				this.getFloorRooms(this.floorID);
				this.chosenModal = "";
			},
			locale:function(){
				this.changeTableLabel();
				console.log(this.locale);
			}
		}

	};
</script>