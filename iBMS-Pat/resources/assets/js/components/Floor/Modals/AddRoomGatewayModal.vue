<!--  <System Name> iBMS
 <Program Name> AddRoomGatewayModal.vue

 <Create> 2018.11.08 TP Harvey
 <Update> 2018.11.08 TP Harvey Plotting
 		  2018.11.05 TP Harvey Adding Data and Bug Fixing
 		  2018.11.07 TP Harvey Fix Adding Gateway

 -->
<template>
	<div class="modal d-block">
		<div class="modal-background" @click="closeModal()"></div>
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header text-black">
					{{$t('floor.addGateway')}}
				</div>
				<div v-if="gateways.length !== 0" class="modal-body text-black">
					<b-table hover 	:items="gateways"
									:fields="table.fields"
									:per-page="table.perPage"
									:current-page="table.currentPage">
							<template slot="button" slot-scope="row">
								<a class="pointer room-action-button" @click="addGateway(row.item)">
									<i class="text-success fa fa-plus-circle fa-lg" aria-hidden="true"></i>
								</a>
							</template>
					</b-table>
					<div class="custom-pagination-black d-flex justify-content-center">
						<b-pagination :total-rows="table.totalRows"
									  :per-page="table.perPage"
									  v-model="table.currentPage">

						</b-pagination>
					</div>
				</div>
				<div v-else class="text-black modal-body">
					{{$t('floor.noGateway')}}
				</div>
				<div class="modal-footer">
					<!-- Button -->
					<button class="btn btn-secondary" @click="closeModal()">{{$t('close')}}</button>
				</div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default{
		props:{
			roomDataProps:'',
			modalModeProps:'',
			floorData:''
		},
		data(){
			return{

				gateways:[],
				table:{
					currentPage:1,
					totalRows:0,
					perPage:5,
					fields:[
						{key:'GATEWAY_ID',label:'ID'},
						{key:'GATEWAY_IP',label:'Gateway IP'},
						{key:'button',label:'Add',class:'text-center'}
					]
				},
				tableRooms:{
					currentPage:1,
					totalRows:0,
					perPage:5,
					fields:[
						{key:'ROOM_ID',label:'ID'},
						{key:'ROOM_NAME',label:'Room Name'},
						{key:'button',label:'Add Gateway'}
					]
				},
				modalMode:'',
				rooms:''
			}
		},
		created(){

			//Input to roomData from Room.vue
			this.roomData = this.roomDataProps;
			this.getGateways();
		},
		methods:{
			//Function Name: addGateway
			//Function Description: Register Gateway on which room you choose
			addGateway(data){
				this.$swal({
					title:"Add Gateway",
					text:"Add Gateway to " + this.roomData['ROOM_NAME'],
					input:'text',
					inputPlaceholder: 'Gateway Name',
					type:"question",
					showCancelButton:true,
					cancelButtonColor:"#3085d6",
					confirmButtonColor:"#39bd57",
					confirmButtonText:'&nbsp;&nbsp; Ok &nbsp;&nbsp;',
					inputValidator:(value) =>{
						return !value && 'Please input Gateway Name';
					},
					preConfirm:(gateway_name)=>{

					}
				}).then((result)=>{

					if(result.value){
						axios.post('gateways/update',
						{	KEY:'gateway',
							GATEWAY_ID:data['GATEWAY_ID'],
							GATEWAY_NAME:result.value,
							FLOOR_ID:this.roomData['FLOOR_ID'],
							ROOM_ID:this.roomData['ROOM_ID'],
							REG_FLAG:1
						})
						.then(response =>{
							if(response.data){
								this.getGateways();
								this.$swal({
								title:"Successfully added",
								type:"success",
								showConfirmButton:false,
								timer:1000
								});
							}else{
								this.$swal({
								title:"Error Adding Gateway",
								type:"error",
								showConfirmButton:false,
								timer:1000
								});
							}
						});
					}
				});
			},
			//Function Name: getGateways
			//Function Description: Get gateways
			getGateways(){
				axios.get('gateways/unregistered')
				.then(response => {
					this.gateways = response.data;
					this.table.totalRows = response.data.length;
				});
			},
			//Function Name: closeModal
			//Function Description: Close Modal
			closeModal(){
				this.$emit("closeModal");
			},
		}
	};
</script>