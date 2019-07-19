<!--  <System Name> iBMS
 <Program Name> AddFloorModal.vue

 <Create> 2018.10.31 TP Harvey
 <Update> 2018.10.31 TP Harvey Plotting
 		  2018.11.05 TP Harvey Adding Data and Bug Fixing

 -->
<template>
	<div class="modal d-block">
		<div class="modal-background" @click="closeModal()"></div>
		<div class="modal-dialog modal-dialog-centered w-600" role="document">
			<div class="modal-content text-black">
				<div class="modal-header">
					{{$t('floor.addFloor')}}<i v-if="addingStep==2" @click="back()" class="fa fa-arrow-left fa-lg add-floor-back-btn pointer"></i>
				</div>
				<div class="modal-body" id="addFloorModal">

					<!-- Step 1 -->
					<div v-if="addingStep==1" class="form-group">
						<label for="floorName">{{$t('floor.floorName')}}:</label>
						<!-- Floor Name -->
						<input type="text" class="form-control" v-model="floor['floorName']" id="floorName">
						<label for="uploadFloorImage"> {{$t('floor.uploadFloor')}}:</label>

						<div class="custom-file">
							<!-- Floor Image File -->
							<input type="file" class="custom-file-input" @change="checkFloorFile($event)" name="uploadFloorImage" required>
							<label class="custom-file-label" for="uploadFloorImage">{{floor['floorImageName']}}</label>
						</div>
						<img v-if="previewURL" class="upload-floor-img-size" :src="previewURL">
					</div>

					<!-- Step 2 -->
					<div v-if="addingStep==2">
						<div class="row">
							<div class="col-md-6">{{$t('floor.roomName')}}</div>
						</div>
						<div class="newrow room-list-group-scroll">
							<!-- Loop Room List -->
							<div v-for="room,key in roomList" class="col-md-12 room-list-padding">
								<div class="row">
									<div class="col-md-6">
										<!-- Room Name -->
										<input type="text" class="form-control" v-model="roomList[key]['roomName']" :placeholder=" $t('floor.room') + ' ' + (key+1)">
									</div>
									<div class="col-md-4">
										<div class="custom-file">
											<!-- Room Image File -->
											<input type="file" class="custom-file-input" @change="checkRoomFile($event,key)" required>
											<label class="custom-file-label custom-file-sm">{{roomList[key]['roomImageName']}}</label>
										</div>
									</div>
									<div class="col-md-2">
										<button class="btn btn-default ml-17" @click="removeRoom(key)"><i class="fa fa-times fa-lg"></i></button>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<!-- Add Remove of Rooms + - -->
							<div class="col-md-12">
								<div class="float-right add-remove-room-list">
									<button @click="addRoomList()" class="btn btn-default">
									<i class="fa fa-plus fa-lg pointer"></i>
									</button>
									&emsp;
									<button @click="removeRoomList()" class="btn btn-default">
									<i class="fa fa-minus fa-lg pointer"></i>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<!-- Buttons -->
					<button v-if="addingStep==1" class="btn btn-primary" @click="btnProceed()">{{$t('floor.proceed')}}</button>
					<button v-else-if="addingStep==2" class="btn btn-primary" @click="btnSave()">{{$t('user.save')}}</button>
					<button class="btn btn-secondary" @click="closeModal()">{{$t('user.cancel')}}</button>
				</div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default{
		props:{
			floors:''
		},
		data(){
			return{
				previewURL:null,

				floor:{'floorName':'','floorImageFile':null,'floorImageName':'Upload Image...'},

				roomList:[{'roomName':null,'roomImageFile':null,'roomImageName':'Upload Image...'},
							{'roomName':null,'roomImageFile':null,'roomImageName':'Upload Image...'},
							{'roomName':null,'roomImageFile':null,'roomImageName':'Upload Image...'},
							{'roomName':null,'roomImageFile':null,'roomImageName':'Upload Image...'},
							{'roomName':null,'roomImageFile':null,'roomImageName':'Upload Image...'},
							{'roomName':null,'roomImageFile':null,'roomImageName':'Upload Image...'}],				//Default Room List

				addingStep:1
			}
		},
		created(){

		},
		methods:{
			//Function Name: isImageValid
			//Function Description: Checking file format iv valid or not
			//Param: fileInfo
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
			//Function Name: checkRoomFile
			//Function Description: Checking Room Upload
			//Param: event, key
			checkRoomFile(event,key){

				//Get File Info to be upload
				let fileInfo = String(event.target.files[0].type).split("/");

				//Checking file type
				if(this.isImageValid(fileInfo)){

					console.log(key);

					this.roomList[key]['roomImageFile'] = event.target.files[0];
					this.roomList[key]['roomImageName'] = event.target.files[0].name;

				}else{
					this.roomList[key]['roomImageFile'] = null;
					this.roomList[key]['roomImageName'] = 'Invalid file type';
				}

			},
			//Function Name: checkFloorFile
			//Function Description: Checking Floor Upload
			//Param: file
			checkFloorFile(){

				//Get File Info to be upload
				let fileInfo = String(event.target.files[0].type).split("/");

				//Checking file type of image
				if(this.isImageValid(fileInfo)){

					const file 						= event.target.files[0];
					this.previewURL 				= URL.createObjectURL(file);
					this.floor['floorImageFile'] 	= event.target.files[0];
					this.floor['floorImageName'] 	= event.target.files[0].name;

				}else{
					this.floor['floorImageName'] = "Invalid file type";
					this.previewURL = null;
				}

			},
			//Function Name: btnProceed
			//Function Description: Proceed to Step 2
			//Param: addingStep
			btnProceed(){
				//Check Floor Information if valid before proceeding to step 2
				if(this.floor['floorName'] != '' &&
					this.validAlphaNumeric(this.floor['floorName']) &&
					this.floor['floorImageFile'] != null &&
					this.floor['floorImageName'] != 'Invalid file type' ){

					//Check if Room Name Existed
					for(var i in this.floors){
						if(this.floors[i]['FLOOR_NAME'].toUpperCase() == this.floor['floorName'].toUpperCase()){
							this.$toast.error("Floor Name already exist","Error",{position:"topCenter"});
							return;
						}
					}

					//Check File Image Size
					if(!this.checkFileSize(this.floor['floorImageFile']['size'])){
						console.log(this.floor['floorImageFile']['size']);
						this.$toast.error("Maximum of 2MB File is allowed.","Error",{position:"topCenter"});
						return;
					}

					self = this;
					$("#addFloorModal").slideUp(200,function(){
						self.addingStep = 2;
					});
					setTimeout(function(){$("#addFloorModal").slideDown(200)},300);


				}else{
					//Invalid
					this.$toast.error("Floor name or image uploaded is invalid","Error",{position:"topCenter"});
				}

			},
			//Function Name: validAlphaNumeric
			//Function Desciption: Check if valid string
			//Param: data (string)
			validAlphaNumeric(data){
				if(data.match(/^[a-zA-Z0-9\s]+$/)!==null){
					return true;
				}else{
					return false;
				}
			},
			//Function Name: chedkFileSize
			//Function Description: File Checker (Maximum of 2 MB)
			//Param: size (file size)
			checkFileSize(size){
				if(((size/1000)/1000 <= 2)){
					return true;
				}else{
					return false;
				}
			},
			//Function Name: back
			//Function Description: Back to Step 1
			back(){

				self = this;
				$("#addFloorModal").slideUp(200,function(){
					self.addingStep = 1;
				});
				setTimeout(function(){$("#addFloorModal").slideDown(200)},300);

			},
			//Function Name: addRoomList
			//Function Description: Increase room list by 1
			addRoomList(){
				this.roomList.push({'roomImageFile':null,'roomImageName':'Upload Image...'});
			},
			//Function Name: removeRoomList
			//Function Description: Decrease room list by 1
			removeRoomList(){
				if(this.roomList.length > 1){
					this.roomList.pop();
				}else{
					this.$toast.error("Rooms cannot be empty","Error",{position:"topCenter"});
				}
			},
			//Function Name: removeRoom
			//Function Description: Remove specific room in room list
			removeRoom(key){
				if (this.roomList.length > 1) {
					this.roomList.splice(key,1);
				}else{
					this.$toast.error("Rooms cannot be empty","Error",{position:"topCenter"});
				}
			},
			//Function name: btnSave
			//Function Description: Generate JSON to be save in Database
			btnSave(){


					//Check all rooms list if valid before upload
					for(var i in this.roomList){
						if(this.roomList[i]['roomImageFile']==null){
							this.$toast.error("Image in Room " + (parseInt(i) + 1) + " is invalid or empty","Error",{position:"topCenter"});
							return;
						}
						// if(this.checkFileSize(this.roomList[i]['roomImageFile']['size'])){
						// 	this.$toast.error("Image size in Room " + (parseInt(i) + 1) + " exceeded. Maximum Image size is 2MB","Error",{position:"topCenter"});
						// 	return;
						// }
						if(this.roomList[i]['roomName'] == null || this.roomList[i]['roomName'] == ''){
							this.$toast.error("Name in Room " + (parseInt(i) + 1) + " is empty","Error",{position:"topCenter"});
							return;
						}
						if(!this.validAlphaNumeric(this.roomList[i]['roomName'])){
							this.$toast.error("Name in Room " + (parseInt(i) + 1) + " is invalid, use only Alphanumeric","Error",{position:"topCenter"});
							return;
						}
						//console.log(this.roomList[i]['roomImageName']);
					}

					//Create a formdata to be uploaded to laravel files
					let formData = new FormData();
					formData.append('floorName',this.floor['floorName']);
					formData.append('floorImageFile',this.floor['floorImageFile']);
					formData.append('floorImageName',this.floor['floorImageName']);
					for(var i in this.roomList){
						formData.append('roomName' + (parseInt(i) + 1),this.roomList[i]['roomName']);
						formData.append('roomImageFile' + (parseInt(i) + 1),this.roomList[i]['roomImageFile']);
						formData.append('roomImageName' + (parseInt(i) + 1),this.roomList[i]['roomImageName']);
					}

					let config = {
						headers:{
							'Config-Type':'multipart/form-data'
						}
					};
					//Upload to database and image
					axios.post('api/imageUpload',formData,config)
					.then(response=>{
						if(typeof response.data === 'object'){
							this.$swal({
								type:'success',
								title:'Successfully created',
								showConfirmButton:false,
								timer:1500
							}).then(() =>
								this.openFloorPlotting(response.data)
							);
						}else if(response.data == 'duplicate'){
							//Image file is duplicated
							this.$swal({
								type:'error',
								title:'Duplicate image file found',
								showConfirmButton:false,
								timer:1000
							});
						}else{
							this.$swal({
								type:'error',
								title:'Error adding floor',
								showConfirmButton:false,
								timer:1000
							});
						}
					});

			},
			//Function Name: closeModal
			//Function Description: Close Modal
			closeModal(){
				this.$emit("closeModal");
			},
			//Function Name: openFloorPlotting
			//Function Description: opens floor plotting
			openFloorPlotting(data){
				this.$emit("openFloorPlotting" , data);
			}
		}
	};
</script>