<template>

    <div v-if="showProfile" class="col-md-6" id="userProfile">

        <div class="p-top41 h-100">
            <div class="profile-container mb-20px">
                <div class="col-md-12 d-flex justify-content-between py-2 h-53px">
                    <div class="fs-20">{{$t('user.profile')}}</div>
                    <div>
                        <transition name="slide-fade">
                            <div @click="editProfile(); isEditingProfile = !isEditingProfile" v-if="!isEditingProfile" v-show="showEditButton==true" class="fs-20" style="color: white;">
                                <i class="btn fa fa-cog"></i>
                            </div>
                        </transition>
                        <transition name="slide-fade">
                            <div v-if="isEditingProfile">
                                <button @click="saveUserProfile()"  type="button" class="btn btn-warning">{{$t('user.save')}}</button>
                                <button @click="isEditingProfile = false; cancelEdit();" type="button" class="btn btn-secondary">{{$t('user.cancel')}}</button>
                            </div>
                        </transition>
                    </div>
                </div>
                <div class="card mx-4 mb-4 mt-c2 bg-card border border-white">
                    <div class="card-body position-relative">
                        <div class="profile-logo position-absolute">
                            <div v-if="editable" class="">
                                <label for="file">
                                    <img v-if="previewLogo" :src="previewLogo" :alt="user.USERNAME" class="clickable"/>
                                    <img v-else :src="user.USER_LOGO != null ? user.USER_LOGO : '/img/users/Go-Tensei-Inc(1000).png'" :alt="user.USERNAME" class="clickable"/>
                                </label>
                                <input class="d-none" id="file" type="file" accept="image/*" ref="file" @change="checkImageFile"/>
                            </div>
                            <img v-else :src="user.USER_LOGO != null ? user.USER_LOGO : '/img/users/Go-Tensei-Inc(1000).png'" :alt="user.USERNAME"/>
                        </div>

                        <!-- USERNAME -->
                        <div class="mt-c35 ">
                            <transition name="fade">
                                <div v-if="editable" class="text-center">
                                    <input v-model="user.USERNAME" class="form-control w-50 mx-auto mt-c4 py-1" placeholder="Username">
                                    <span class="text-danger">{{ errors.username }}</span>
                                </div>
                                <h3 v-else class="text-center mt-c4">{{user.USERNAME}}</h3>
                            </transition>
                        </div>

                        <!-- EMAIL -->
                        <div>
                            <transition name="fade">
                                <div v-if="editable" class="text-center">
                                    <input v-model="user.EMAIL" class="form-control mt-1 w-50 mx-auto py-1" placeholder="Email">
                                    <span class="text-danger">{{ errors.email }}</span>
                                </div>
                                <h5 v-else class="text-center">{{user.EMAIL}}</h5>
                            </transition>
                        </div>

                        <!-- Change Password Button -->
                        <div class="text-center mt-2">
                            <transition name="fade">
                                <div v-if="editable" @click="modalChangePassword" class="btn background-orange">{{$t('user.changepass')}}</div>
                            </transition>
                        </div>

                    </div>
                </div>
            </div>

            <div class="designation-container border border-dark">
                <div class="col-md-12 d-flex justify-content-between py-2 h-53px">
                    <div class="fs-20">{{$t('user.designation')}}</div>
                    <div class="">
                        <transition name="slide-fade">
                            <div @click="isDisabled = !isDisabled; isEditingDesignation = !isEditingDesignation" v-if="!isEditingDesignation" v-show="showEditButton==true" class="fs-20" style="color: white;">
                                <i class="btn fa fa-cog"></i>
                            </div>
                        </transition>
                        <transition name="slide-fade">
                            <div v-if="isEditingDesignation">
                            <button @click="saveUserDesignation" type="button" class="btn btn-warning">{{$t('user.save')}}</button>
                            <button @click="isDisabled = !isDisabled; isEditingDesignation = false; removeErrors();" type="button" class="btn btn-secondary">{{$t('user.cancel')}}</button>
                            </div>
                        </transition>
                    </div>
                </div>
                <div class="card-body mx-4 mb-4 p-2" style="background-color: white;">
                    <div class="text-dark border-bottom pb-1">{{$t('user.userLocation')}}</div>
                    <!-- All Floors -->
                    <div class="pt-2" v-if="usertype == '1'" v-show="user.USER_TYPE == '1'">
						<select v-model="floorSelected" class="form-control" :disabled="!isEditingDesignation">
							<option :value="floors">{{$t('user.all')}}</option>
						</select>
						<span v-if="isEditingDesignation" class="text-danger">{{ errors.floors }}</span>
					</div>
                    <!-- Multiselect -->
                    <div class="pt-2" v-if="usertype == '1'" v-show="user.USER_TYPE == '2' || user.USER_TYPE == '3'">
                        <multiselect  v-model="floorSelected" :options="floors" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Pick Floor" label="FLOOR_NAME" track-by="FLOOR_NAME" :preselect-first="false" :disabled="isDisabled"></multiselect>
                        <span v-if="isEditingDesignation" class="text-danger">{{ errors.floors }}</span>
                    </div>
                    <!-- Select 1 Floor -->
                    <!-- <div class="pt-2" v-if="usertype == '1' || usertype == '2'" v-show="user.USER_TYPE == '3'">
						<select v-model="floorSelected" class="form-control" :disabled="isDisabled">
							<option v-for="floor in floors" :value="[floor]">
								{{floor['FLOOR_NAME']}}
							</option>
						</select>
                        <span v-if="isEditingDesignation" class="text-danger">{{ errors.floors }}</span>
					</div> -->
                </div>

                <div class="card-body mx-4 mb-4 p-2" style="background-color: orange;">
                    <div class="text-dark border-bottom pb-1">{{$t('user.userModules')}}</div>
                    <!-- Module Input -->
                    <div class="pt-2" v-if="usertype == '1' || usertype == '2'">
                        <multiselect  v-model="moduleSelected" :options="modules" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Pick Module" label="MODULE_NAME" track-by="MODULE_NAME" :preselect-first="false" :disabled="isDisabled"></multiselect>
                    </div>
                </div>
            </div>

        </div>
    </div>

</template>

<script>
//Modals
import UpdateUserModal from './Modals/UpdateUserModal.vue';
import UserChangePasswordModal from './Modals/UserChangePasswordModal.vue';

export default {
    components: {UpdateUserModal,UserChangePasswordModal},
    props:['usertype'],
    data() {
        return {
            //Users from Database
            users: '',
            user: '',
            cachedUser: '',
            editable: false,
            isEditingProfile: false,
            isEditingDesignation: false,
            floors:[],
            floorSelected: [],
            modules: [],
            moduleSelected: [],
            isDisabled: true,
            showProfile: false,
            showEditButton: '',
            previewLogo:'',
            file:'',
            fileName:'',
            errors:{
                username: '',
                email: '',
                floors: '',
            },
        }
    },
    created(){
        this.$bus.$on('getUsers', data => {
            this.users = data;
        });
        this.getUser();
    },
    methods: {
        //Function Name: getUser
        //Function Description: Get user data
        getUser(){
            this.$bus.$on('getActiveUser', data => {
                this.showEditButton = true;
                this.showProfile = true;
                this.getSelectedUser(data);
                this.editable = false;
                this.isEditingProfile = false;
                this.isEditingDesignation = false;
                this.isDisabled = true;
            });
            this.getFloors();
            this.getModules();
            this.$bus.$on('hideActiveUser', data => {
                this.showProfile = data;
            });
        },
        //Function Name: stopListen
        //Function Description: stop listening for emit getActiveUser
        stopListen(){
            this.$bus.$off('getActiveUser');
        },
        //Function Name: editProfile
        //Function Description: enable editting of profile
        editProfile(){
            $("#profileButton").fadeIn(500);
            this.editable = true;
            this.cachedUser = Object.assign({}, this.user);
        },
        //Function Name: cancelEdit
        //Function Description: cancel profile editting
        cancelEdit(){
            this.user = Object.assign({}, this.cachedUser);
            this.previewLogo = '';
            this.file = '';
            this.fileName = '';
            this.editable = false;
            // this.getUser();
        },
        //Function Name: removeErrors
        //Function Description: reset error messages
        removeErrors(){
            this.errors.username = "";
            this.errors.email = "";
            this.errors.floors = "";
        },
        //Function Name: getSelectedUser
        //Function Description: get selected user data
        //Param: data (user)
        getSelectedUser(data){
            if(data.FIRST_NAME==null){
                data.FIRST_NAME='FirstName';
            }
            if(data.LAST_NAME==null){
                data.LAST_NAME='LastName';
            }
            if(data.EMAIL==null){
                data.EMAIL='WalaEmail@email.com';
            }
            this.user = Object.assign({}, data);
            this.floorSelect();
            this.moduleSelect();

        },
        //Function Name: saveUserProfile
        //Function Description: saves user data after checking for errors
        saveUserProfile(){
            this.errors.username = "";
            this.errors.email = "";
            this.errors.fileName = '';
            let hasFile = undefined;
            let hasModule = undefined;
            // If nothing change do nothing
            if(this.user.USERNAME == this.cachedUser.USERNAME && this.user.EMAIL == this.cachedUser.EMAIL && this.file == ''){
                this.isEditingProfile = false;
                this.cancelEdit();
            }else{
                // Username Check
                if(this.user.USERNAME == ""){
                    this.errors.username = "Field Required!";
                }else if(this.user.USERNAME.length < 6){
                    this.errors.username = "Minimum Character Must Be 6!";
                }else if(this.user.USERNAME != this.cachedUser.USERNAME){
                    for(var i = 0; i < this.users.length; i++) {
                        if (this.users[i].USERNAME == this.user.USERNAME) {
                            this.errors.username = "The username already exists";
                            break;
                        }
                    }
                }
                // Email Check
                if(this.user.EMAIL){
                    if(!/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(this.user.EMAIL)){
                        this.errors.email = "Invalid Email";
                    }
                }
                // Image File Check
				if(this.fileName == 'Invalid file type'){
					this.$toast.error("Invalid File type.","Error",{position:"topCenter"});
					this.errors.fileName = "File type is invalid!";
				}

                // Image File Size Check
                if(!this.checkFileSize(this.file['size'])){
                    this.$toast.error("Minimum of 100kb is allowed for User Logo","Error",{position:"topCenter"});
                    this.errors.fileName = "Minimum of 100kb File is allowed";
                }

                this.file == "" ? hasFile = 0 : hasFile = 1;

                if(this.errors.username == "" && this.errors.email == "" && this.errors.fileName == ""){

                    let formData = new FormData();
                    formData.append('file', this.file);
                    formData.append('fileName', this.fileName);
                    formData.append('USERID', this.user.USER_ID);
                    formData.append('USERNAME', this.user.USERNAME);
                    formData.append('USEREMAIL', this.user.EMAIL);
                    formData.append('hasFile', hasFile);

                    let config = {
                        headers:{
                            'Config-Type':'multipart/form-data'
                        }
                    };
                    axios.post('users/update-user-profile',formData,config)
                    .then((response)=>
                        this.success(),
                    )
                    .catch((error) => console.log(error));
                }
            }
        },
        //Function Name: saveUserDesignation
        //Function Description: Saves updated user designations
        //Param: event
        saveUserDesignation(event){
            $('.btn').attr('disabled', 'disabled');
            this.errors.floors = "";
            // Floor Check
            if(this.user.USER_TYPE == 1){
                if(this.floorSelected.length != this.floors.length){
                    this.errors.floors = "Field Required!";
                    $('.btn').removeAttr('disabled');
                }
            }
            if(this.floorSelected.length == 0){
                this.errors.floors = "Field Required!";
                $('.btn').removeAttr('disabled');

            }

            if(this.errors.floors == ""){
                this.errors.floors = "";
                axios({
                    url: 'users/update-user-designation',
                    method: 'post',
                    data: {
                    'USERID':this.user.USER_ID,
                    'FLOORS':this.floorSelected,
                    'MODULES':this.moduleSelected,
                    }
                })
                .then((response)=>
                    this.success(),
                )
                .catch((error) => console.log(error));
            }
        },
        //Function Name: success
        //Function Description: displays a success message upon completion save
        success(){
            // this.closeModal();
            $('.btn').removeAttr('disabled');
            this.$bus.$emit('getActiveUsers');
            this.editable = false;
            this.isEditingProfile = false;
            this.isDisabled = true;
            this.isEditingDesignation = false;
            this.showProfile = false;
            this.previewLogo = '';
            this.file = '';
            this.fileName = '';
            this.$swal({
                position: 'center',
                type: 'success',
                title: 'Successfully Update',
                showConfirmButton: false,
                timer: 2000
            });
        },
        //Function Name: isImageValid
        //Function Description: Checks if image is a valid type
        //Param: fileInfo (file type)
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
        //Function name: checkImageFile
        //Function Description: Checking Image File Upload
		checkImageFile(){
			//Get File Info to be upload
			let fileInfo = String(event.target.files[0].type).split("/");
			console.log(fileInfo);

			//Checking file type of image
			if(this.isImageValid(fileInfo)){
				const file 						= event.target.files[0];
                this.previewLogo 				= URL.createObjectURL(file);
				this.file					 	= event.target.files[0];
				this.fileName				 	= event.target.files[0].name;
			}else{
				this.fileName = "Invalid file type";
			}

		},
		//Function Name: checkFileSize
        //Function Description: File Checker (minimum of 100KB)
        //Param: size (file size)
		checkFileSize(size){
			if((size/1000 <= 100)){
				return false;
			}else{
				return true;
			}
		},
        //Function Name: getFloors
        //Function Description: Get all floors
        getFloors(){
            axios.post('floors')
            .then(response=>{
                if(response.data.length > 0){
                    this.floors = response.data;
                }
            });
        },
        //Function Name: floorSelect
        //Function Description: add selected floor to user designation
        floorSelect(){
            this.floorSelected = [];
            var assignedFloor = this.user.auth_user_floor;
            var floorList = this.floors;
            for(var i = 0; i <= assignedFloor.length - 1; i++){
                for(var j = 0; j <= floorList.length -1; j++){
                    if(assignedFloor[i].FLOOR_ID == floorList[j].FLOOR_ID){
                        this.floorSelected.push(this.floors[j]);
                    }
                }
            }
        },
        //Function Name: getModules
        //Function Description: get available modules
        getModules(){
            axios.post('modules')
            .then(response=>{
                if(response.data.length > 0){
                    // Exclude the Dashboard and Device Operation Modules
                    response.data.splice(0,2);
                    this.modules = response.data;
                }
            });
        },
        //Function Name: moduleSelect
        //Function Description: add selected module to user auth
        moduleSelect(){
            this.moduleSelected = [];
            var assignedModule = this.user.auth_modules;
            var moduleList = this.modules;
            for(var i = 0; i <= assignedModule.length - 1; i++){
                for(var j = 0; j <= moduleList.length -1; j++){
                    if(assignedModule[i].MODULE_ID == moduleList[j].MODULE_ID){
                        this.moduleSelected.push(this.modules[j]);
                    }
                }
            }
        },
        //Function Name: modalChangePassword
        //Function Description: opens change password modal
        modalChangePassword(){
            this.$emit('changePasswordModal',this.user);
        },
    }
};
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>