
<!--  <System Name> iBMS
 <Program Name> EditFloorPlotModal.vue

 <Create> 2018.10.31 TP Harvey
 <Update> 2018.10.31 TP Harvey Floor Edit Name
 		  2018.11.15 Add Comments

 -->
<template>
	<div class="modal d-block">
		<div class="modal-background" @click="cancelModal()"></div>
		<div class="modal-dialog modal-sm modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header text-black">
					{{$t('floor.editFloor')}}
				</div>
				<div class="modal-body">
					<input type="text" class="form-control" v-model="editFloorInput">
				</div>
				<div class="modal-footer">
					<button class="btn btn-primary" @click="btnSave">{{$t('user.save')}}</button>
					<button class="btn btn-secondary" @click="cancelModal()">{{$t('user.cancel')}}</button>
				</div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default{
		props:{
			floorData:'',
			floors:''
		},
		data(){
			return{
				editFloorInput:''
			}
		},
		created(){
			this.editFloorInput = this.floorData['FLOOR_NAME'];
		},
		methods:{
			//Function Name: btnSave
			//Function Description: Edit name of Floor and checks if name exists before save
			btnSave(){
				if(this.editFloorInput != ''){

					for(var i in this.floors){
						console.log(this.editFloorInput + "..." +  this.floors[i]['FLOOR_NAME']);
						if(this.editFloorInput == this.floors[i]['FLOOR_NAME']){
							let message = '';
							message = this.$i18n.messages[this.$i18n.locale].modalText.floorExist;
							this.$swal({
							type:"error",
							title: message,
							showConfirmButton:false,
							timer:1500
							});
							return;
						}
					}
					//ajax for update script
					//param: FLOOR_ID, FLOOR_NAME
					axios.post('floors/update',
					{"FLOOR_ID":this.floorData['FLOOR_ID'],"FLOOR_NAME":this.editFloorInput})
					.then(response=>{
						console.log(response.data);
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
					//display error message at the top
					this.$toast.error("Please input Floor Name","Error",{position:"topCenter"});
				}
			},
			//Function Name: closeModal
			//Function Description: Close Modal
			closeModal(){
				this.$emit('closeModal');
			},
			//Function Name: cancelModal
			//Function Description: cancel floor name edit modal
			cancelModal(){
				this.$emit('cancelModal');
			}
		}
	};
</script>