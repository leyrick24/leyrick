<template>
	<div class="modal d-block">
		<div class="modal-background" @click="closeModal()"></div>
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					Baguhin Ako
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label class="label">Username</label>
						<input v-model="form_input.username" type="text" class="form-control" readonly="">
					</div>
					<div class="form-group">
						<label class="label">New Password</label>
						<input v-model="form_input.password" type="password" class="form-control" v-on:keyup.enter="savePassword()">
						<span class="text-danger">{{ errors.password }}</span>
					</div>
					<div class="form-group">
						<label class="label">Confirm Password</label>
						<input v-model="form_input.confirm_password" type="password" class="form-control" @input="checkPassword()" v-on:keyup.enter="savePassword()">
						<span class="text-danger">{{ errors.confirm_password }}</span>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-primary" @click="savePassword()">Save</button>
					<button class="btn btn-secondary" @click="closeModal()">Cancel</button>
				</div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default{
		props:['modalData','loggedUser'],
		data(){
			return{
				form_input:{
					username:this.modalData.USERNAME,
					password:'',
					confirm_password:''
				},
				errors:{
					password: '',
					confirm_password: '',
				},
			}
		},
		created(){
            $("body").addClass("modal-open");
		},
		methods:{
			//Function Name: savePassword
			//Function Description: saves password
			//Param: form_input
			savePassword(){
				this.errors.password = "";
				this.errors.confirm_password = "";
				// Password Check
				if(this.form_input.password == ""){
					this.errors.password = "Password is Required!";
				}else if(!/^[a-zA-Z0-9].{7,}$/.test(this.form_input.password)){
					this.errors.password = "Password must contain at least 8 characters";
				}else if(!/\d/.test(this.form_input.password)){
					this.errors.password = "Password must contain at least one digit";
				}else if(!/(?=.*[A-Z])/.test(this.form_input.password)){
					this.errors.password = "Password must contain at least one upper case";
				}else if(!/(?=.*[a-z])/.test(this.form_input.password)){
					this.errors.password = "Password must contain at least one lower case";
				}else if(this.form_input.confirm_password != this.form_input.password){
					this.errors.confirm_password = "Password doesn't match!";
				}

				// System Logs
                if(this.errors.password != "" || this.errors.confirm_password != ""){
                    axios({
                        url: 'logs/addSystemLogs',
                        method: 'post',
                        data: {
                            'ERROR_MESSAGE':[this.errors.password,this.errors.confirm_password],
                            'USERNAME':this.loggedUser.USERNAME,
                        }
                    })
                    .then((response)=>
						console.log('saved')
                    )
                    .catch((error) => console.log(error));
                }
				if(this.errors.password == "" && this.errors.confirm_password == ""){
					axios({
					  url: 'users/change-password-user',
					  method: 'post',
					  data: {
					  	'USERID':this.modalData.USER_ID,
					  	'PASSWORD':this.form_input.password,
					  	}
					})
					.then(response=>{
						if (response.data == 'failed') {
							this.form_input.password="testing of error";
							this.$swal({
								position: 'center',
								type: 'error',
								title: 'chubachuchu',
								showConfirmButton: false,
								timer: 2000
				});

						}else{
							this.success();

						}
					})
					.catch((error) => this.errs(error));
				}
			},
			//Function Name: success
			//Function Description: display success alert
			success(){
				this.closeModal();
                this.$bus.$emit('getActiveUsers');
				this.$swal({
					position: 'center',
					type: 'success',
					title: 'Successfully Change Password',
					showConfirmButton: false,
					timer: 2000
				});
			},
			//Function Name: checkPassword
			//Function Description: checks if password matches with first entry
			//Param: form_input.password
			checkPassword(){
				if(this.form_input.password == this.form_input.confirm_password){
					this.errors.confirm_password = "";
					return true;
				}else{
					this.errors.confirm_password = "Password doesn't match";
					return false;
				}
			},
			//Function Name: closeModal
			//Function Description: closes modal
			closeModal(){
				this.$emit("closeModal");
			}
		},
		beforeDestroy() {
            $("body").removeClass("modal-open");
		}
	};
</script>