
<!--  <System Name> iBMS
 <Program Name> EditRoomPlotModal.vue

 <Create> 2018.10.31 TP Harvey
 <Update> 2018.11.05 TP Harvey Room Edit Name
 		  2018.11.15 Add Comments

 -->
<template>
	<div class="modal d-block">
		<div class="modal-background" @click="closeModal()"></div>
		<div class="modal-dialog modal-sm modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header text-black">
					{{$t('floor.editRoom')}}
				</div>
				<div class="modal-body">
					<input type="text" class="form-control" v-model="editRoomInput">
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
			roomData:'',
			rooms:''
		},
		data(){
			return{
				editRoomInput:''
			}
		},
		created(){
			this.editRoomInput = this.roomData['ROOM_NAME'];
		},
		methods:{
			//Function Name: btnSave
			//Function Description: Edit Room Name and checks if name exists already
			btnSave(){
				if(this.editRoomInput != ''){

					for(var i in this.rooms){
						if(this.editRoomInput ==  this.rooms[i]['ROOM_NAME']){
							let message = '';
							message = this.$i18n.messages[this.$i18n.locale].modalText.roomExist;
							this.$swal({
								type:"error",
								title:message,
								showConfirmButton:false,
								timer:1500
							});
							return;
						}
					}
					//call to update script
					//param: ROOM_ID, ROOM_NAME
					axios.post('rooms/update',
					{"ROOM_ID":this.roomData['ROOM_ID'],"ROOM_NAME":this.editRoomInput})
					.then(response=>{
						let message = '';
						message = this.$i18n.messages[this.$i18n.locale].modalText.editSuccess;
						this.$swal({
							type:"success",
							title: message,
							showConfirmButton:false,
							timer:1500
						}).then(() =>
							this.closeModal()
						);
					});
				}else{
					this.$toast.error("Please input Room Name","Error",{position:"topCenter"});
				}
			},
			//Function Name: closeModal
			//Function Description: Close Modal
			closeModal(){
				this.$emit('closeModal');
			}
		}
	};
</script>