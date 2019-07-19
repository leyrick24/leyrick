<template>
	<div class="modal d-block">
		<div class="modal-background" @click="closeModal()"></div>
		<div class="modal-dialog modal-dialog-centered w-600" role="document">
			<div class="modal-content text-black">
				<div class="modal-header">
					{{$t('floor.addRoom')}}
				</div>
				<div class="modal-body" id="addFloorModal">

					<!-- Step 2 -->
					<div>
						<div class="row">
							<div class="col-md-6">{{$t('floor.roomName')}}</div>
						</div>
						<div class="newrow room-list-group-scroll">
							<!-- Loop Room List -->
							<div class="col-md-12 room-list-padding">
								<div class="row">
									<div class="col-md-6">
										<!-- Room Name -->
										<input type="text" class="form-control" v-model="roomData['roomName']" :placeholder="'Room Name'">
									</div>
									<div class="col-md-6">
										<div class="custom-file">
											<!-- Room Image File -->
											<input type="file" class="custom-file-input" @change="checkRoomFile($event)" required>
											<label class="custom-file-label custom-file-sm">{{roomData['roomImageName']}}</label>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-primary" @click="btnSave()">{{$t('user.save')}}</button>
					<button class="btn btn-secondary" @click="closeModal()">{{$t('user.cancel')}}</button>
				</div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default{
		props:{
			floorID:'',
		},
		data(){
			return{
                roomData:{
					floorId: this.floorID,
					roomImageFile:null,
					roomImageName:"Upload Image",
					roomName:null,
				}
			};
		},
		methods:{
			//Function Name: isImageValid
			//Function Description: Checking file format iv valid or not
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
			//Function Name: checkRoomFile
			//Function Description: Checking Room Upload
			//Param: event
			checkRoomFile(event){

				//Get File Info to be upload
				let fileInfo = String(event.target.files[0].type).split("/");

				//Checking file type
				if(this.isImageValid(fileInfo)){

					this.roomData['roomImageFile'] = event.target.files[0];
					this.roomData['roomImageName'] = event.target.files[0].name;
					console.log(this.roomData['roomImageFile']);

				}else{
					this.roomData['roomImageFile'] = null;
					this.roomData['roomImageName'] = 'Invalid file type';
				}

			},
			//Function Name: validAlphaNumeric
			//Function Description: Check if room name is valid
			//Param: data (room name)
			validAlphaNumeric(data){
				if(data.match(/^[a-zA-Z0-9\s]+$/)!==null){
					return true;
				}else{
					return false;
				}
			},
			//Function Name: btn Save
			//Function Description: Saves new room to db and updates floor json
			btnSave(){
				//Check all rooms list if valid before upload
				if(this.roomData['roomImageFile']==null){
					this.$toast.error("Image in Room is invalid or empty","Error",{position:"topCenter"});
					return;
				}
				if(this.roomData['roomName'] == null){
					this.$toast.error("Name is empty","Error",{position:"topCenter"});
					return;
				}
				if(!this.validAlphaNumeric(this.roomData['roomName'])){
					this.$toast.error("Name is invalid, use only Alphanumeric","Error",{position:"topCenter"});
					return;
				}

				let formData = new FormData();
				formData.append('floorId',this.roomData['floorId']);
				formData.append('roomName',this.roomData['roomName']);
				formData.append('roomImageFile',this.roomData['roomImageFile']);
				formData.append('roomImageName',this.roomData['roomImageName']);

				//Upload to database and image
				axios.post('api/addNewRoom',formData)
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
						this.$swal({
							type:'warning',
							title:'Duplicate room name!',
							showConfirmButton:false,
							timer:1500
						});
					}else{
						console.log(response.data);
					}
				});
			},
			//Function Name: closeModal
			//Function Description: Close Modal
			closeModal(){
				this.$emit("closeModal");
			},
			//Function name: openFloorPlotting
			//Function Description: opens floor plotting mode
			//Param: data (room)
			openFloorPlotting(data){
				this.$emit("roomAdded" , data);
			}
		}
	};
</script>