<template>
	<div class="modal d-block">
		<div class="modal-background" @click="closeModal()"></div>
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					Update User
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label class="label">Username</label>
						<input v-model="form_input.username" v-show="form_input.edit == false" @dblclick="form_input.edit = true" class="form-control" readonly>
						<input v-show="form_input.edit == true" v-model="form_input.username" v-on:blur="form_input.edit=false; $emit('update')" keyup.enter="form_input.edit=false; $emit('update')" type="text" class="form-control">
					</div>

					<!-- location Input -->
					<div class="form-group" v-if="user_type == '1'" v-show="form_input.usertype == '1'">
						<label class="label">Location</label>
						<select v-model="floorSelected" class="form-control">
							<option :value="floors">All</option>
						</select>
						<span class="text-danger">{{ errors.floors }}</span>
					</div>
					<div class="form-group" v-if="user_type == '1'" v-show="form_input.usertype == '2'">
						<label class="label">Location</label>
						<multiselect  v-model="floorSelected" :options="floors" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Pick Floor" label="FLOOR_NAME" track-by="FLOOR_NAME" :preselect-first="false"></multiselect>
						<span class="text-danger">{{ errors.floors }}</span>
					</div>
					<div class="form-group" v-if="user_type == '1'" v-show="form_input.usertype == '3'">
						<label class="label">Location</label>
						<select v-model="floorSelected" class="form-control">
							<option v-for="floor in floors" :value="[floor]">
								{{floor['FLOOR_NAME']}}
							</option>
						</select>
						<span class="text-danger">{{ errors.floors }}</span>
					</div>

					<!-- Module Input -->
					<div class="form-group" v-if="user_type == '1'">
						<label class="label">Module</label>
						<multiselect  v-model="moduleSelected" :options="modules" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Pick Module" label="MODULE_NAME" track-by="MODULE_NAME" :preselect-first="false"></multiselect>
						<span class="text-danger">{{ errors.modules }}</span>
					</div>

				</div>
				<div class="modal-footer">
					<button class="btn btn-primary" @click="saveUser()">Save</button>
					<button class="btn btn-secondary" @click="closeModal()">Cancel</button>
				</div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default{
		props:['modalData', 'user_type'],
		data(){
			return{
				form_input:{
					username:this.modalData.USERNAME,
					editedUsername: null,
					edit:false,
					usertype: this.modalData.USER_TYPE,
				},
				errors:{
					floor:'',
				},
				floors:[],
				floorSelected: [],
				modules: [],
				moduleSelected: [],
			}
		},
		created(){
			if(this.user_type=='1'){
				this.getFloors();
				this.getModules();
			}
            $("body").addClass("modal-open");
		},
		methods:{
			//Function Name: getFloors
			//Function Description: Get floors
			getFloors(){
				axios.post('floors')
				.then(response=>{
					if(response.data.length > 0){
						this.floors = response.data;
						this.floorSelect();
					}
				});
			},
			//Function Name: getModules
			//Function Description: get modules
			getModules(){
				axios.post('modules')
				.then(response=>{
					if(response.data.length > 0){
						// Exclude the Dashboard and Device Operation Modules
						response.data.splice(0,2);
						this.modules = response.data;
						this.moduleSelect();
					}
				});
			},
			//Function Name: saveUser
			//Function Description: Save user
			saveUser(){
				if(this.floorSelected.length != 0){
					console.log(this.form_input.username),
					axios({
					  url: 'users/update-user',
					  method: 'post',
					  data: {
						'USERNAME':this.form_input.username,
					  	'USERID':this.modalData.USER_ID,
					  	'FLOORS':this.floorSelected,
					  	'MODULES':this.moduleSelected,
					  	}
					})
					.then((response)=>
						this.success(),
					)
					.catch((error) => this.errs(error));
				}else{
					errors.floor = "Field Require!";
				}
			},
			//Function Name: success
			//Function Description: display alert after success and close modal
			success(){
				this.closeModal();
                this.$bus.$emit('getActiveUsers');
				this.$swal({
					position: 'center',
					type: 'success',
					title: 'Successfully Update',
					showConfirmButton: false,
					timer: 2000
				});
			},
			//Function Name: floorSelect
			//Function Description: select floors
			floorSelect(){
				var assignedFloor = this.modalData.auth_user_floor;
				var floorList = this.floors;
				for(var i = 0; i <= assignedFloor.length - 1; i++){
					for(var j = 0; j <= floorList.length -1; j++){
						if(assignedFloor[i].FLOOR_ID == floorList[j].FLOOR_ID){
							this.floorSelected.push(this.floors[j]);
						}
					}
				}
			},
			//Function Name: moduleSelect
			//Function Description: select modules
			moduleSelect(){
				var assignedModule = this.modalData.auth_modules;
				var moduleList = this.modules;
				for(var i = 0; i <= assignedModule.length - 1; i++){
					for(var j = 0; j <= moduleList.length -1; j++){
						if(assignedModule[i].MODULE_ID == moduleList[j].MODULE_ID){
							this.moduleSelected.push(this.modules[j]);
						}
					}
				}
			},
			//Function Name: closeModal
			//Function Description: close modal
			closeModal(){
				this.$emit("closeModal");
			},
			//Function Name: editUsername
			//Function Description: sets updated username
			//Param: user (user input)
			editUsername(user){
				this.editedUsername = user;
			}
		},
		mounted() {
		},
		beforeDestroy() {
            $("body").removeClass("modal-open");
		}
	};
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>