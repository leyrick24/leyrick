<!--  <System Name> iBMS
 <Program Name> Floor.vue

 <Create> 2018.10.31 TP Harvey
 <Update> 2018.11.5 TP Harvey Table View
 	      2018.11.12 TP Harvey AddRoomGateway Modal
 	      2018.12.05 TP Harvey AddRoomGateway Modal

 -->

<template>
	<div class="row">
		<!-- Check if Floor Table View or Floor Plotting View -->
		<div class="col-md-12" :class="floors.length > 24 ? 'h-362':'h-400'">
			<b-table  :items="floors"
						:fields="table.fields"
						:current-page="table.currentPage"
						:per-page="table.perPage"
						:filter="floorSearch"
						@row-clicked="chooseFloor">

				<template slot="button" slot-scope="row">
					<a class="pointer floor-action-btn" v-on:click="editFloor(row.item); openModal('editFloorModal');">
						<i class="fa fa-edit fa-lg" aria-hidden="true"></i>
					</a>
					</a>
					<a class="pointer floor-action-btn" @click="deleteFloor(row.item)" data-toggle="popover" data-container="body" data-content="Delete" data-placement="right">
						<i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
					</a>
				</template>
				<template slot="floor_id" slot-scope="row">
					{{row.item['FLOOR_ID']}}
				</template>
				<template slot="floor_name" slot-scope="row">
					{{row.item['FLOOR_NAME']}}
				</template>
			</b-table>
		</div>
		<div class="col-md-12">
			<div class="d-flex justify-content-center" :class="floors.length > 24 ? '':'custom-pagination-black'">
				<b-pagination :total-rows="table.totalRows" :per-page="table.perPage" v-model="table.currentPage" />
			</div>
		</div>
		<div class="col-md-12">
			<div class="float-right add-floor-btn">
				<div @click="openModal('addFloorModal')" class="btn btn-sm background-orange"> <i class="fa fa-plus-square-o fa-lg" aria-hidden="true"></i> {{$t('floor.addFloor')}}</div>
			</div>
		</div>
		<div v-if="chosenModal=='addFloorModal'">
			<AddFloorModal @closeModal="closeModal" @openFloorPlotting="openFloorPlotting" :floors="floors"></AddFloorModal>
		</div>
		<div v-else-if="chosenModal=='editFloorModal'">
			<EditFloorModal :floorData="chosenFloor" @closeModal="closeModal" @cancelModal="cancelModal" :floors="floors"></EditFloorModal>
		</div>
	</div>
</template>
<script type="text/javascript">
	import AddFloorModal from "../Modals/AddFloorModal.vue";
	import EditFloorModal from "../Modals/EditFloorModal.vue";
	export default{

		components:{AddFloorModal,EditFloorModal},
		props:{
			floorSearch:''
		},
		data(){
			return{
				chosenModal:'',
				table:{
					currentPage:1,
					perPage:6,
					totalRows:0,
					pageOptions:[5,10,15],
					fields:[
					{key:'floor_id',label:"Floor ID"},
					{key:'floor_name',label:"Floor Name"},
					{key:'button',label:"Action"}
					]
				},
				chosenFloor:'',
				floors:[]
			}
		},
		created(){
			this.getFloors()
		},
		mounted(){
			// this.changeTableLabel();
			//for table header multilingual support
			let fields = this.$children[0].fields;
			let head = this.$i18n.messages[this.$i18n.locale].floor.tableLabels;
			console.log(this.$i18n.locale);
			for(var i in fields){
				Object.keys(head).forEach(function(mess){
					if (fields[i].key == mess) {
						fields[i].label = head[mess];
					}
				});
			}
			this.$children[0].fields = fields;
		},
		methods:{
			//Function Name: getFloors
			//Function Description: Get Floors from database
			getFloors(){
				axios.post("floors")
				.then(response => {
					if(response.data.length > 0){
						this.floors = response.data;
						this.table.totalRows =  response.data.length;

						//pick the default floor
						this.chosenFloor = response.data[0];
						//Open in Room Table, Choose the first floor data
						this.$emit('chooseFloor',response.data[0]);
					}
				});
			},
			//Function Name: changeTableLabe
			//Function Description: Change table labels based on language settings
			//Param: $i18n.locale
			// changeTableLabel(){

			// },
			//Function name: editFloor
			//Function Description: Edit Floor on action buttons
			//Param: data (floor)
			editFloor(data){
				this.chosenFloor = data;
			},
			//Function Name: chooseFloor
			//Function Description: Choose Floor open Room
			//Param: data (floor), key (index in floor)
			chooseFloor(data,key){
				//Clear the Selected hilight
				for(var i in this.floors){
					this.$set(this.floors[i],"_rowVariant","");
				}
				//Hilight selected
				this.$set(data,"_rowVariant","white");


				this.chosenFloor = data;
				this.$emit('chooseFloor',data);
			},
			//Function Name: deleteFloor
			//Function Description: Delete Floors and Rooms from Database
			//Param: data (floor)
			deleteFloor(data)
			{
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
						axios.post('floors/delete',{FLOOR_ID:data['FLOOR_ID']})
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
							this.getFloors();
						});


					}

				});
			},
			//Function Name: openFloorPlotting
			//Function Description: Switch to Floor Plotting Mode
			//Param: data (floor)
			openFloorPlotting(data){
				//New
				this.chosenModal = '';
				this.chosenFloor = data;
				this.getFloors();
				this.$emit('openFloorPlotting',data);

			},
			//Function Name: openModal
			//Function Description: Open Modal
			//Param: data (editFloorModal, addFloorModal)
			openModal(data){
				this.chosenModal = data;
			},
			//Function Name: closeModal
			//Function Description: Close Modal
			closeModal(){
				this.getFloors();
				this.chosenModal = '';
			},
			//Function Name: cancelModal
			//Function Description: Close Modal
			cancelModal(){
				this.chosenModal = '';
			}
		},
	};
</script>