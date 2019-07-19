<template>
	<!-- cat,show are props of vue contain the data from other component -->
	<!-- gateway display -->
	<!-- check if the category is gateway -->
	<div v-if="cat == 'gateway'">
		<!-- modal -->
		<div class="modal" :class="show">
			<!-- call close function on line 259 -->
			<div class="modal-background" @click="close"></div>
			<!-- modal dialog -->
			<div class="modal-dialog modal-dialog-centered" role="document">
				<!-- modal content -->
				<div class="modal-content">
					<!-- modal header -->
					<div class="modal-header">
						<h5 class="modal-title text-dark">Add Gateway</h5>
						<!-- call close function on line 259 -->
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<!-- modal header end-->

					<!-- modal body -->
					<div class="modal-body">
						<div class="form-group">
					    	<label class="label text-dark">Floor</label>
				            <div class="">
								<!-- call getRooms function on line 247 every change-->
				                <select class="custom-select" v-model="modalData.FLOOR_ID" @change="getRooms()">
				                	<!-- loop the floors data -->
				                    <option v-for="floor,key in floors" :key="floor.FLOOR_ID" :value="floor.FLOOR_ID">{{floor.FLOOR_NAME}}</option>
				                </select>
				            </div>
						</div>
						<div class="form-group">
					    	<label class="label text-dark">Room</label>
				            <div class="">
				            	<!-- enable /disable the room select using roomDisabled function on line 324 -->
				                <select class="custom-select" v-model="modalData.ROOM_ID" :disabled="roomDisabled">
				                	<!-- loop the rooms data -->
				                    <option v-for="room,key in rooms" :key="room.ROOM_ID" :value="room.ROOM_ID">{{room.ROOM_NAME}}</option>
				                </select>
				            </div>
						</div>
						 <div class="form-group">
					    	<label class="label text-dark">Gateway Name</label>
				            <!-- enable /disable the gateway name input using nameDisabled function on line 332 -->
				            <!-- when the input is enter call the addData funtion on line 341 -->
			  				<input class="form-control" :class="{'border border-danger':errors.GATEWAY_NAME}" type="text" v-model="modalData.GATEWAY_NAME" @keyup.enter="addData(0)" :disabled="nameDisabled">
			  				<!-- display this when error occured -->
			  				<small v-if="errors.GATEWAY_NAME" class="text-danger">{{ errors.GATEWAY_NAME[0] }}</small>
						</div>
						 <div class="form-group">
					    	<label class="label text-dark">Gateway IP</label>
			  				<input class="form-control" type="text" v-model="modalData.GATEWAY_IP" readonly>
						</div>
					</div>
					<!-- modal body end -->

					<!-- modal footer -->
					<div class="modal-footer">
						<!-- call addData function on line 275 -->
				        <!-- enable /disable the save button using gwSaveDisabled function on line 341 -->
						<button class="btn btn-primary" @click="addData(0)" :disabled="gwSaveDisabled">
							<!-- display loading animation -->
						    <span class="pull-left" v-if="loading">
								<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
							</span>
							<!-- change text value when click -->
							<span> {{ btn_text }} </span>
						</button>
						<button type="button" class="btn btn-secondary" @click="close">Close</button>
					</div>
					<!-- modal footer end -->
				</div>
				<!-- modal content end -->
			</div>
			<!-- modal dialog end -->
		</div>
		<!-- modal end-->
	</div>
	<!-- gateway display end -->

	<!-- device display -->
	<!-- check if the category is device -->
	<div v-else-if="cat == 'device'">
		<!-- modal -->
		<div class="modal" :class="show">
			<!-- call close function on line 259 -->
			<div class="modal-background" @click="close"></div>
			<!-- modal dialog -->
			<div class="modal-dialog modal-dialog-centered" role="document">
				<!-- modal content -->
				<div class="modal-content">
					<!-- modal header -->
					<div class="modal-header">
						<h5 class="modal-title text-dark">Add Device</h5>
						<!-- call close function on line 259 -->
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<!-- modal header end -->

					<!-- modal body -->
					<div class="modal-body text-dark">
						<div class="form-group">
					    	<label class="label">Floor</label>
				            <div class="">
				                <select class="custom-select" v-model="modalData.FLOOR_ID" disabled>
				                	<!-- loop the floors data -->
				                    <option v-for="floor,key in floors" :key="floor.FLOOR_ID" :value="floor.FLOOR_ID" :selected="floor.FLOOR_ID == modalData.FLOOR_ID">{{floor.FLOOR_NAME}}</option>
				                </select>
				            </div>
						</div>
						<div class="form-group">
					    	<label class="label">Room</label>
				            <div class="">
				                <select class="custom-select" v-model="modalData.ROOM_ID" disabled>
				                	<!-- loop the rooms data -->
				                    <option v-for="room,key in rooms" :key="room.ROOM_ID" :value="room.ROOM_ID" :selected="room.ROOM_ID == modalData.ROOM_ID">{{room.ROOM_NAME}}</option>
				                </select>
				            </div>
						</div>
						 <div class="form-group">
					    	<label class="label">Device Type</label>
					    	<!-- display device type -->
			  				<input class="form-control" type="text" v-model="modalData.DEVICE_TYPE" readonly>
						</div>

						<div class="form-group">
					    	<label class="label">Device Name</label>
			  				<input class="form-control" :class="{'border border-danger':errors.DEVICE_NAME}" type="text" v-model="modalData.DEVICE_NAME">
			  				<!-- display this when error occured -->
			  				<small v-if="errors.DEVICE_NAME" class="text-danger">{{ errors.DEVICE_NAME[0] }}</small>
						</div>
						<div class="form-group">
					    	<label class="label">Device Category</label>
				            <div class="">
				                <select class="custom-select" v-model="modalData.DEVICE_CATEGORY">
				                	<!-- loop the rooms data -->
				                    <option v-for="type,key in deviceType" :key="type.ID" :value="type.ID" :selected="type.ID == modalData.DEVICE_CATEGORY">{{type.NAME}}</option>
				                </select>
			  					<small v-if="errors.DEVICE_CATEGORY" class="text-danger">{{ errors.DEVICE_CATEGORY[0] }}</small>
				            </div>
						</div>
						<!-- display when switch gang is 1 -->
						<div v-if="(modalData.DEVICE_TYPE == 'embedded_switch_1' || modalData.DEVICE_TYPE == 'wall_switch_1')">
							<div class="form-group">
						    	<label class="label">One Gang Name <small>(optional)</small></label>
				  				<input class="form-control" :class="{'border border-danger':errors['DATA.0.device_name']}" type="text" v-model="modalData.DATA[0].device_name">
				  				<small v-if="errors['DATA.0.device_name']" class="text-danger">{{ error_text }}</small>
							</div>
						</div>

						<!-- display when switch gang is 2 -->
						<div v-if="(modalData.DEVICE_TYPE == 'embedded_switch_2' || modalData.DEVICE_TYPE == 'wall_switch_2')">
							<div class="form-group">
						    	<label class="label">One Gang Name <small>(optional)</small></label>
				  				<input class="form-control" :class="{'border border-danger':errors['DATA.0.device_name']}" type="text" v-model="modalData.DATA[0].device_name">
				  				<!-- display this when error occured -->
				  				<small v-if="errors['DATA.0.device_name']" class="text-danger">{{ error_text }}</small>
							</div>
							<div class="form-group">
						    	<label class="label">Two Gang Name </label>
				  				<input class="form-control" :class="{'border border-danger':errors['DATA.1.device_name']}" type="text" v-model="modalData.DATA[1].device_name">
				  				<!-- display this when error occured -->
				  				<small v-if="errors['DATA.1.device_name']" class="text-danger">{{ error_text }}</small>
							</div>
						</div>

						<!-- display when switch gang is 3 -->
						<div v-if="(modalData.DEVICE_TYPE == 'embedded_switch_3' || modalData.DEVICE_TYPE == 'wall_switch_3')">
							<div class="form-group">
						    	<label class="label">One Gang Name <small>(optional)</small></label>
				  				<input class="form-control" :class="{'border border-danger':errors['DATA.0.device_name']}" type="text" v-model="modalData.DATA[0].device_name">
				  				<!-- display this when error occured -->
				  				<small v-if="errors['DATA.0.device_name']" class="text-danger">{{ error_text }}</small>
							</div>
							<div class="form-group">
						    	<label class="label">Two Gang Name <small>(optional)</small></label>
				  				<input class="form-control" :class="{'border border-danger':errors['DATA.1.device_name']}" type="text" v-model="modalData.DATA[1].device_name">
				  				<!-- display this when error occured -->
				  				<small v-if="errors['DATA.1.device_name']" class="text-danger">{{ error_text }}</small>
							</div>
							<div class="form-group">
						    	<label class="label">Three Gang Name <small>(optional)</small></label>
				  				<input class="form-control" :class="{'border border-danger':errors['DATA.2.device_name']}" type="text" v-model="modalData.DATA[2].device_name">
				  				<!-- display this when error occured -->
				  				<small v-if="errors['DATA.2.device_name']" class="text-danger">{{ error_text }}</small>
							</div>
						</div>
					</div>
					<!-- modal body end -->

					<!-- modal footer -->
					<div class="modal-footer">
						<!-- call addData function on line 275 -->
				        <!-- enable /disable the save button using devSaveDisabled function on line 348 -->
						<button class="btn btn-primary" @click="addData(1)" :disabled="devSaveDisabled">
							<!-- display loading animation -->
						    <span class="pull-left" v-if="loading">
								<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
							</span>
							<!-- change text value when click -->
							<span> {{ btn_text }} </span>
						</button>
						<button type="button" class="btn btn-secondary"  @click="close">Close</button>
					</div>
					<!-- modal footer end -->
				</div>
				<!-- modal content end -->
			</div>
			<!-- modal dialog end -->
		</div>
		<!-- modal end -->
	</div>
	<!-- device display end-->
</template>

<script>
	export default{
		//get the attributes from other components
		props: ['cat','show', 'currentPage', 'modalData'],
		created (){
			//call floor and room functions
        	this.getFloors();
            $("body").addClass("modal-open");
		},
		data(){
			return{
                // where data variables is declare and initialize
				floors:{},
				rooms:{},
				deviceType:[{"ID":0,"NAME":"Device"},{"ID":1,"NAME":"Sensor"}],
				errors:{},
				loading:false,
				btn_text: 'Save',
				error_text: 'This field is required',
			}
		},
		methods: {
			//Function Name: getFloors
			//Function Description: get floor function
			getFloors(){
				//ajax call
				axios.get('floors')
	            .then(response => {
	            	this.floors = response.data;
	            	if(this.cat == 'device'){
		            	this.getRooms();
	            	}
	            })
	            .catch(errors => {
	                console.log(errors);
	            });
			},
			//Funciton Name: getRooms
			//Function Description: get room function
			//Param: FLOOR_ID
			getRooms(){
				//ajax call
				axios.post('floors/'+ this.modalData.FLOOR_ID +'/rooms')
                .then(response => {
					this.rooms = response.data;
                })
                .catch(errors => {
                    console.log(errors);
                });
			},
			//Funciton Name: close
			//Function Description: close modal function
			close(){
				this.errors = {},
				this.loading = false,
				this.btn_text = 'Save',
				this.$emit('loaddata', this.currentPage),
				this.$emit('close')
			},
			//Function Name: errs
			//Function Description: error function
			//PARAM: error = response
			errs(error){
				this.errors = error.response.data.errors
				this.loading = false,
				this.btn_text = 'Save'
			},
			//Function Name: addData
			//Function Description: add device or gateway function
			//PARAM: key = KEY: 0 = gateway, 1 = device
			addData(key){
				this.loading = true;
				this.btn_text = 'Saving';
				if(key == 0){
					//ajax call
					//PARAM: FLOOR_ID,ROOM_ID,GATEWAY_ID,GATEWAY_NAME
					axios({
					  url: 'gateways/register',
					  method: 'post',
					  data: {
					  	'FLOOR_ID':this.modalData.FLOOR_ID,
					  	'ROOM_ID':this.modalData.ROOM_ID,
					  	'GATEWAY_ID':this.modalData.GATEWAY_ID,
					  	'GATEWAY_NAME':this.modalData.GATEWAY_NAME
					  	}
					})
					.then((response)=>
						setTimeout(() => {
						   this.close();
						}, 1500)
					)
					.catch((error) => this.errs(error));

				}else if(key == 1){
					//ajax call
					//PARAM: FLOOR_ID,ROOM_ID,DEVICE_ID,DEVICE_NAME,DEVICE_DATA
					axios({
					  url: 'devices/register',
					  method: 'post',
					  data: {
					  	'FLOOR_ID':this.modalData.FLOOR_ID,
					  	'ROOM_ID':this.modalData.ROOM_ID,
					  	'GATEWAY_ID':this.modalData.GATEWAY_ID,
					  	'DEVICE_ID':this.modalData.DEVICE_ID,
					  	'DEVICE_NAME':this.modalData.DEVICE_NAME,
					  	'DEVICE_CATEGORY':this.modalData.DEVICE_CATEGORY,
					  	'DEVICE_DATA':this.modalData.DATA
					  	}
					})
					.then((response)=>
						setTimeout(() => {
						   this.close();
						}, 1500)
					)
					.catch((error) => this.errs(error));
				}
			}
		},
		computed:{
			//disable room select
			roomDisabled(){
		    	if(this.modalData.FLOOR_ID == null){
					return true;
		    	}else{
		    		return false;
		    	}
		    },
			//disable name field
		    nameDisabled(){
		    	if(this.modalData.ROOM_ID == null){
					return true;
		    	}else{
		    		return false;
		    	}
		    },

			//disable gateway button
		    gwSaveDisabled(){
		    	if(this.modalData.GATEWAY_NAME == null || this.modalData.GATEWAY_NAME == ""){
					return true;
		    	}else{
		    		return false;
		    	}
		    },
			//disable device button
		    devSaveDisabled(){
		    	if(this.modalData.DEVICE_NAME == null || this.modalData.DEVICE_NAME == ""){
					return true;
		    	}else{
		    		return false;
		    	}
		    },
		},
		beforeDestroy() {
            $("body").removeClass("modal-open");
		}
	}
</script>