<template>
	<transition name="fade">
		<div class="modal d-block">
			<div class="modal-background" @click="closeModal()"></div>
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header text-dark">
						{{$t('user.add')}}
					</div>
					<div class="add-user modal-body">
						<!-- User Type Select -->
						<div class="form-group">
							<label class="label text-dark">{{$t('user.usertype')}}</label>
							<div class="">
								<select class="custom-select" v-model="form_input.usertype" @change="onChange()">
									<!-- loop the rooms data -->
									<option value="null" disabled selected>---</option>
									<option value="1" v-if="usertype==1">{{$t('user.type.gtiAdmin')}}</option>
									<option value="2">{{$t('user.type.genAdmin')}}</option>
									<option value="3">{{$t('user.type.nurse')}}</option>
								</select>
							</div>
							<span class="text-danger">{{ errors.userType }}</span>
						</div>
						<!-- Username Input -->
						<div class="form-group">
							<label class="label text-dark">{{$t('user.username')}}</label>
							<input v-model="form_input.username" type="text" class="form-control">
							<span class="text-danger">{{ errors.username }}</span>
						</div>
						<!-- User Logo Input -->
						<div class="form-group">
							<label class="label text-dark">{{$t('user.userlogo')}}</label>
							<label for="file" class="form-control"><input type="file" class="custom-image-input" accept="image/*" @change="checkImageFile" id="file" ref="file">{{fileName}}</label>
							<span class="text-danger">{{ errors.fileName }}</span>
						</div>
						<!-- Password Input -->
						<div class="form-group">
							<label class="label text-dark">{{$t('user.password')}}</label>
							<input v-model="form_input.password" type="password" class="form-control">
							<span class="text-danger">{{ errors.password }}</span>
						</div>
						<!-- Confirm Password Input -->
						<div class="form-group">
							<label class="label text-dark">{{$t('user.confirmpass')}}</label>
							<input v-model="form_input.confirm_password" type="password" class="form-control" @input="checkPassword()">
							<span class="text-danger">{{ errors.confirm_password }}</span>
						</div>
						<!-- Email Input -->
						<div v-if="usertype == 1" class="form-group" v-show="form_input.usertype == '1'">
							<label class="label text-dark">{{$t('user.email')}}</label>
							<input v-model="form_input.email" type="email" class="form-control">
							<span class="text-danger">{{ errors.email }}</span>
						</div>
						<!-- location Input -->
						<div class="form-group" v-show="form_input.usertype == '1'">
							<label class="label text-dark">{{$t('user.location')}}</label>
							<select v-model="floorSelected" class="form-control">
								<option :value="floors" selected>{{$t('user.all')}}</option>
							</select>
							<span class="text-danger">{{ errors.floors }}</span>
						</div>
						<div class="form-group" v-show="form_input.usertype == '2' || form_input.usertype == '3'">
							<label class="label text-dark">{{$t('user.location')}}</label>
							<multiselect  v-model="floorSelected" :options="floors" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Pick Floor" label="FLOOR_NAME" track-by="FLOOR_NAME" :preselect-first="false"></multiselect>
							<span class="text-danger">{{ errors.floors }}</span>
						</div>
						<!-- <div class="form-group" v-show="form_input.usertype == '3'">
							<label class="label text-dark">{{$t('user.location')}}</label>
							<select v-model="floorSelected" class="form-control">
								<option v-for="floor in floors" :value="[floor]">
									{{floor['FLOOR_NAME']}}
								</option>
							</select>
							<span class="text-danger">{{ errors.floors }}</span>
						</div> -->
						<!-- Module Input -->
						<div class="form-group">
							<label class="label text-dark">{{$t('user.module')}}</label>
							<multiselect  v-model="moduleSelected" :options="modules" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" :placeholder="$t('user.pickmod')" label="MODULE_NAME" track-by="MODULE_NAME" :preselect-first="false"></multiselect>
							<span class="text-danger">{{ errors.modules }}</span>
						</div>
					</div>
					<div class="modal-footer">
						<!-- Save Button -->
						<button class="btn btn-primary" @click="btnSave()">{{$t('user.save')}}</button>
						<button class="btn btn-secondary" @click="closeModal()">{{$t('user.cancel')}}</button>
					</div>
				</div>
			</div>
		</div>
	</transition>
</template>
<script type="text/javascript">
	export default{
		props:['usertype', 'modalData', 'loggedUser'],
		data(){
			return{
				users: this.modalData,
				form_input:{
					usertype: '0',
					username: '',
					password: '',
					confirm_password: '',
					email: '',
					user_logo: '',
					// floor_selected: 0
				},
				floors:[],
				modules:[],
				errors:{
					userType: '',
					username: '',
					fileName: '',
					password: '',
					confirm_password: '',
					email: '',
					floors: '',
					fileName: '',
					modules: ''
				},
				floorSelected: [],
				moduleSelected: [],
				file:'',
				fileName:'',
			}
		},
		created(){
			//Get Floor Data from Database
			axios.post('floors')
				.then(response=>{
					if(response.data.length > 0){
						this.floors = response.data;
					}
				});
			axios.post('modules')
				.then(response=>{
					if(response.data.length > 0){
						// Exclude the Dashboard and Device Operation Modules
						response.data.splice(0,2);
						this.modules = response.data;
						this.setDafaultModules();
					}
				});
            $("body").addClass("modal-open");
		},
		methods:{
			//Function Name: btnSave
			//Function Description: checks if form input is valid and saves user
			//Param: form_input
			btnSave(){
				this.errors.username = "";
				this.errors.password = "";
				this.errors.confirm_password = "";
				this.errors.email = "";
				this.errors.fileName = "";
				this.errors.floors = "";
				this.errors.modules = "";
				let hasFile = undefined;
				let hasModule = undefined;
				//UserType Check
				if(this.form_input.usertype == null || this.form_input.usertype == "0"){
					this.errors.userType = "User Type is Required!";
				}
				// Username Check
				if(this.form_input.username == ""){
					this.errors.username = "Username is Required!";
				}else if(this.form_input.username.length < 6){
					this.errors.username = "Minimum Character Must Be 6!";
				}else{
					for(var i = 0; i < this.users.length; i++) {
						if (this.users[i].USERNAME == this.form_input.username) {
							this.errors.username = "The username already exists";
							break;
						}
					}
				}
				// Password Check
				if(this.form_input.password == ""){
					this.errors.password = "Password is Required!";
				}else if(!/^[a-zA-Z0-9]{8,}$/.test(this.form_input.password)){
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
				// Email Check
				if(this.form_input.email){
					if(!/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(this.form_input.email)){
						this.errors.email = "Invalid Email";
					}
				}
				// Floor Check
				if(this.floorSelected.length == 0){
					this.errors.floors = "Location is Required!";
				}

				// Module Check
				if(this.moduleSelected.length == 0){
					this.errors.modules = "Module is Required!";
				}

				// Image File Check
				if(this.fileName == 'Invalid file type'){
					this.errors.fileName = "File type is invalid!";
				}else if(this.file != '' && !this.checkFileSize(this.file['size'])){
					this.errors.fileName = "Maximum of 2MB File is allowed";
				}else if(this.file != '' && !this.checkMinFileSize(this.file['size'])){
					this.errors.fileName = "Minimum of 50KB File is allowed";
				}

				this.file == "" ? hasFile = 0 : hasFile = 1;
				this.moduleSelected.length > 0 ? hasModule = 1 : hasModule = 0;

                // System Logs
                if(this.errors.userType != "" || this.errors.username != "" || this.errors.password != "" || this.errors.confirm_password != "" || this.errors.floors != "" || this.errors.email != "" || this.errors.fileName != ""){
                    axios({
                        url: 'logs/addSystemLogs',
                        method: 'post',
                        data: {
                            'ERROR_MESSAGE':[this.errors.username,this.errors.password,this.errors.confirm_password,this.errors.floors,this.errors.email,this.errors.fileName],
                            'USERNAME':this.loggedUser.USERNAME,
                        }
                    })
                    .then((response)=>
						console.log('saved')
                    )
                    .catch((error) => console.log(error));
                }
				if(this.errors.username == "" && this.errors.password == "" && this.errors.confirm_password == "" && this.errors.floors == "" && this.errors.email == "" && this.errors.fileName == ""  && this.errors.modules == ""){
					let floorids = [];
					let moduleids = [];
					let formData = new FormData();
					formData.append('file', this.file);
					formData.append('fileName', this.fileName);
					formData.append('hasFile', hasFile);
					formData.append('USERNAME', this.form_input.username);
					formData.append('USER_TYPE', this.form_input.usertype);
					formData.append('PASSWORD', this.form_input.password);
					formData.append('EMAIL', this.form_input.email);

					this.floorSelected.map(function(value, key) {
						floorids.push(value.FLOOR_ID);
					});
					this.moduleSelected.map(function(value, key) {
						moduleids.push(value.MODULE_ID);
					});
					formData.append('floorids', floorids);
					formData.append('moduleids', moduleids);
					formData.append('hasModule', hasModule);
					let config = {
						headers:{
							'Config-Type':'multipart/form-data'
						}
					};
					axios.post('users/new-user',formData,config)

					.then((response)=>
						console.log('Success!'),
						this.success(),
					)
					.catch((error) => this.errs(error));
				}
			},
			//Function Name: success
			//Function Description: displays success alert
			success(){
				this.closeModal();
                // this.$bus.$on('getActiveUsers');
                this.$bus.$emit('getActiveUsers');
				this.$swal({
					position: 'center',
					type: 'success',
					title: 'Successfully Saved',
					showConfirmButton: false,
					timer: 2000
				});
			},
			//Function Name: isImageValid
			//Function Description: checks if file type selected is valid
			//Param: fileInfo (file info)
			isImageValid(fileInfo){
				if(fileInfo[0] == "image" && (fileInfo[1] == "jpeg" ||
											  fileInfo[1] == "jpg"  ||
											  fileInfo[1] == "gif"  ||
											  fileInfo[1] == "png")){
					return true;
				}else{
					return false;
				}
			},
			//Function Name: checkImageFile
			//Function Description: Checking Image File Upload
			//Param: target.file
			checkImageFile(){
				//Get File Info to be upload
				let fileInfo = String(event.target.files[0].type).split("/");
				console.log(fileInfo);

				//Checking file type of image
				if(this.isImageValid(fileInfo)){
					this.file					 	= event.target.files[0];
					this.fileName				 	= event.target.files[0].name;
				}else{
					this.fileName = "Invalid file type";
				}

			},
			//Function Name: checkFileSize
			//Function Description: File Checker (Maximum of 2 MB)
			//Param: size (file size)
			checkFileSize(size){
				if(((size/1000)/1000 <= 2)){
					return true;
				}else{
					return false;
				}
			},
			//Function Name: checkFileSize
			//Function Description: File Checker (Minimum of 50 kb)
			//Param: size (file size)
			checkMinFileSize(size){
				if (size/1000 <= 50) {
					return false;
				}else{
					return true;
				}
			},
			//Function Name: closeModal
			//Function Description: closes modal
			closeModal(){
				this.$emit("closeModal");
			},
			//Function Name: checkPassword
			//Function Description: checks if password matches previous password entry
			//Param: form_input (password)
			checkPassword(){
				if(this.form_input.password == this.form_input.confirm_password){
					this.errors.confirm_password = "";
					return true;
				}else{
					this.errors.confirm_password = "Password doesn't match";
					return false;
				}
			},
			//Function Name: setDafaultModules
			//Functin Description: Set Default List of Modules based on user type
			//Param: form_input (usertype)
			setDafaultModules(){

				//Input default modules
				var setOfModules = [];
				if(this.form_input.usertype == 1){
					setOfModules = ['User Management','Gateway Management','Device Management','Binding Management','IR Management','Floor Management','Logs'];
				}else if(this.form_input.usertype  == 2){
					setOfModules = ['User Management','Binding Management','IR Management','Logs'];
				}else if(this.form_input.usertype  == 3){
					setOfModules = ['Binding Management','IR Management','Logs'];
				}
				for(var i in this.modules){
					if(setOfModules.indexOf(this.modules[i]['MODULE_NAME']) >= 0){
							this.moduleSelected.push(this.modules[i]);
					}
				}
			},
			//Function Name: onChange
			//Function Description: changes form on change of user type
			//Param: form_input (user type)
			onChange() {
				this.form_input.username = '';
				this.form_input.password = '';
				this.form_input.confirm_password = '';
				this.form_input.email = '';
				this.errors.username = '';
				this.errors.password = '';
				this.errors.confirm_password = '';
				this.errors.floors = '';
				this.errors.modules = '';
				this.floorSelected = [];
				this.moduleSelected = [];

				this.setDafaultModules();
			},

		},
		beforeDestroy() {
            $("body").removeClass("modal-open");
		}
	}
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>