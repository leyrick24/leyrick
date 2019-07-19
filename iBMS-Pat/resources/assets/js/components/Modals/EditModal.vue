
<template>
	<!-- cat,show are props of vue contain the data from other component -->
	<!-- gateway display -->
	<!-- check if the category is gateway -->
	<div v-if="cat == 'gateway'">
		<!-- modal -->
		<div class="modal" :class="show">
			<!-- call close function on line 254 -->
			<div class="modal-background" @click="close"></div>
			<!-- modal dialog -->
			<div class="modal-dialog modal-dialog-centered" role="document">
				<!-- modal content -->
				<div class="modal-content">
					<!-- modal header -->
					<div class="modal-header">
						<h5 class="modal-title">Update Gateway</h5>
						<!-- call close function on line 254 -->
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<!-- modal header end-->

					<!-- modal body -->
					<div class="modal-body">
						<div class="form-group">
					    	<label class="label">Floor</label>
				            <div class="">
								<!-- call getRooms function on line 242 every change-->
				                <select class="custom-select" v-model="modalData.FLOOR_ID" @change="getRooms()">
				                	<!-- loop the floors data and select initial display using selected -->
				                    <option v-for="floor,key in floors" :key="floor.FLOOR_ID" :value="floor.FLOOR_ID" :selected="floor.FLOOR_ID == modalData.FLOOR_ID">{{floor.FLOOR_NAME}}</option>
				                </select>
				            </div>
						</div>
						<div class="form-group">
					    	<label class="label">Room</label>
				            <div class="">
				                <select class="custom-select" v-model="modalData.ROOM_ID">
				                	<!-- loop the rooms data and select initial display using selected -->
				                    <option v-for="room,key in rooms" :key="room.ROOM_ID" :value="room.ROOM_ID" :selected="room.ROOM_ID == modalData.ROOM_ID">{{room.ROOM_NAME}}</option>
				                </select>
				            </div>
						</div>
						 <div class="form-group">
					    	<label class="label">Gateway Name</label>
				            <!-- when the input is enter call the addData funtion on line 270 -->
			  				<input class="form-control" :class="{'border border-danger':errors.GATEWAY_NAME}" type="text" v-model="modalData.GATEWAY_NAME" @keyup.enter="updateData()">
			  				<!-- display this when error occured -->
			  				<small v-if="errors.GATEWAY_NAME" class="text-danger">{{ errors.GATEWAY_NAME[0] }}</small>
						</div>
					</div>
					<!-- modal body end -->

					<!-- modal footer -->
					<div class="modal-footer">
						<!-- call addData function on line 270 -->
				        <!-- enable /disable the save button using gwUpdateDisabled function on line 318 -->
						<button class="btn btn-primary" @click="updateData(0)" :disabled="gwUpdateDisabled">
							<!-- display loading animation -->
						    <span class="pull-left" v-if="loading">
								<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
							</span>
							<!-- change text value when click -->
							<span> {{ btn_text }} </span>
						</button>
						<!-- call close function on line 254 -->
						<button type="button" class="btn btn-secondary"  @click="close">Close</button>
					</div>
					<!-- modal footer end -->
				</div>
				<!-- modal content end -->
			</div>
			<!-- modal dialog end -->
		</div>
		<!-- modal end-->
	</div>
	<div v-else-if="cat == 'modBusGateway'">
		<!-- modal -->
		<div class="modal" :class="show">
			<!-- call close function on line 254 -->
			<div class="modal-background" @click="close"></div>
			<!-- modal dialog -->
			<div class="modal-dialog modal-dialog-centered" role="document">
				<!-- modal content -->
				<div class="modal-content">
					<!-- modal header -->
					<div class="modal-header">
						<h5 class="modal-title">Update ModBus Gateway</h5>
						<!-- call close function on line 254 -->
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<!-- modal header end-->

					<!-- modal body -->
					<div class="modal-body">
						<div class="form-group">
					    	<label class="label">Floor</label>
				            <div class="">
								<!-- call getRooms function on line 242 every change-->
				                <select class="custom-select" v-model="modalData.FLOOR_ID" @change="getRooms()">
				                	<!-- loop the floors data and select initial display using selected -->
				                    <option v-for="floor,key in floors" :key="floor.FLOOR_ID" :value="floor.FLOOR_ID" :selected="floor.FLOOR_ID == modalData.FLOOR_ID">{{floor.FLOOR_NAME}}</option>
				                </select>
				            </div>
						</div>
						<div class="form-group">
					    	<label class="label">Gateway Serial No</label>
				            <!-- when the input is enter call the addData funtion on line 270 -->
			  				<input class="form-control" :class="{'border border-danger':errors.GATEWAY_SERIAL_NO}" type="text" v-model="modalData.GATEWAY_SERIAL_NO" @keyup.enter="updateData()">
			  				<!-- display this when error occured -->
			  				<small v-if="errors.GATEWAY_SERIAL_NO" class="text-danger">{{ errors.GATEWAY_SERIAL_NO[0] }}</small>
						</div>
						<div class="form-group">
					    	<label class="label">Gateway Name</label>
				            <!-- when the input is enter call the addData funtion on line 270 -->
			  				<input class="form-control" :class="{'border border-danger':errors.GATEWAY_NAME}" type="text" v-model="modalData.GATEWAY_NAME" @keyup.enter="updateData()">
			  				<!-- display this when error occured -->
			  				<small v-if="errors.GATEWAY_NAME" class="text-danger">{{ errors.GATEWAY_NAME[0] }}</small>
						</div>
						<div class="form-group">
					    	<label class="label">Gateway IP</label>
				            <!-- when the input is enter call the addData funtion on line 270 -->
			  				<input class="form-control" :class="{'border border-danger':errors.GATEWAY_IP}" type="text" v-model="modalData.GATEWAY_IP" @keyup.enter="updateData()">
			  				<!-- display this when error occured -->
			  				<small v-if="errors.GATEWAY_IP" class="text-danger">{{ errors.GATEWAY_IP[0] }}</small>
						</div>
					</div>
					<!-- modal body end -->

					<!-- modal footer -->
					<div class="modal-footer">
						<!-- call addData function on line 270 -->
				        <!-- enable /disable the save button using gwUpdateDisabled function on line 318 -->
						<button class="btn btn-primary" @click="updateData()" :disabled="modgwUpdateDisabled">
							<!-- display loading animation -->
						    <span class="pull-left" v-if="loading">
								<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
							</span>
							<!-- change text value when click -->
							<span> {{ btn_text }} </span>
						</button>
						<!-- call close function on line 254 -->
						<button type="button" class="btn btn-secondary"  @click="close">Close</button>
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
			<!-- call close function on line 254 -->
			<div class="modal-background" @click="close"></div>
			<!-- modal dialog -->
			<div class="modal-dialog modal-dialog-centered" role="document">
				<!-- modal content -->
				<div class="modal-content">
					<!-- modal header -->
					<div class="modal-header">
						<h5 class="modal-title">Update Device</h5>
						<!-- call close function on line 254 -->
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<!-- modal header end -->

					<!-- modal body -->
					<div class="modal-body">
						<div class="form-group">
					    	<label class="label">Floor</label>
				            <div class="">
				                <select class="custom-select" v-model="modalData.FLOOR_ID" disabled>
				                	<!-- loop the floors data and select initial display using selected -->
				                    <option v-for="floor,key in floors" :key="floor.FLOOR_ID" :value="floor.FLOOR_ID" :selected="floor.FLOOR_ID == modalData.FLOOR_ID">{{floor.FLOOR_NAME}}</option>
				                </select>
				            </div>
						</div>
						<div class="form-group">
					    	<label class="label">Room</label>
				            <div class="">
				                <select class="custom-select" v-model="modalData.ROOM_ID" disabled>
				                	<!-- loop the rooms data and select initial display using selected -->
				                    <option v-for="room,key in rooms" :key="room.ROOM_ID" :value="room.ROOM_ID" :selected="room.ROOM_ID == modalData.ROOM_ID">{{room.ROOM_NAME}}</option>
				                </select>
				            </div>
						</div>
						<div class="form-group">
					    	<label class="label">Device Type</label>
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
				            </div>
						</div>

						<!-- display when switch gang is 1 -->
						<div v-if="(modalData.DEVICE_TYPE == 'embedded_switch_1' || modalData.DEVICE_TYPE == 'wall_switch_1')">
							<div class="form-group">
						    	<label class="label">One Gang Name <small>(optional)</small></label>
				  				<input class="form-control" :class="{'border border-danger':errors['DATA.0.device_name']}" type="text" v-model="modalData.DATA[0].device_name">
				  				<!-- display this when error occured -->
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
						    	<label class="label">Two Gang Name <small>(optional)</small></label>
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
						<!-- call updateData function on line 270 -->
				        <!-- enable /disable the save button using devUpdateDisabled function on line 326 -->
						<button class="btn btn-primary" @click="updateData()" :disabled="devUpdateDisabled">
							<!-- display loading animation -->
						    <span class="pull-left" v-if="loading">
								<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
							</span>
							<!-- change text value when click -->
							<span> {{ btn_text }} </span>
						</button>
						<!-- call close function on line 254 -->
						<button type="button" class="btn btn-secondary"  @click="close">Close</button>
					</div>
					<!-- modal footer end -->
				</div>
				<!-- modal content end -->
			</div>
			<!-- modal dialog end -->
		</div>
	</div>
		<!-- modal end -->
	<div v-else-if="cat == 'modBusMeter'">
		<!-- modal -->
		<div class="modal" :class="show">
			<!-- call close function on line 254 -->
			<div class="modal-background" @click="close"></div>
			<!-- modal dialog -->
			<div class="modal-dialog modal-dialog-centered" role="document">
				<!-- modal content -->
				<div class="modal-content">
					<!-- modal header -->
					<div class="modal-header">
						<h5 class="modal-title">Update Meter Device</h5>
						<!-- call close function on line 254 -->
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<!-- modal header end -->

					<!-- modal body -->
					<div class="modal-body">
						<div class="form-group">
					    	<label class="label">Floor</label>
				            <div class="">
				                <select class="custom-select" v-model="modalData.FLOOR_ID" disabled>
				                	<!-- loop the floors data and select initial display using selected -->
				                    <option v-for="floor,key in floors" :key="floor.FLOOR_ID" :value="floor.FLOOR_ID" :selected="floor.FLOOR_ID == modalData.FLOOR_ID">{{floor.FLOOR_NAME}}</option>
				                </select>
				            </div>
						</div>
						<div class="form-group">
					    	<label class="label">Room</label>
				            <div class="">
				                <select class="custom-select" v-model="modalData.ROOM_ID" disabled>
				                	<!-- loop the rooms data and select initial display using selected -->
				                    <option v-for="room,key in rooms" :key="room.ROOM_ID" :value="room.ROOM_ID" :selected="room.ROOM_ID == modalData.ROOM_ID">{{room.ROOM_NAME}}</option>
				                </select>
				            </div>
						</div>
						<div class="form-group">
					    	<label class="label">Gateway Name</label>
			  				<input class="form-control" type="text" v-model="modalData.gateway.GATEWAY_NAME" readonly>
						</div>

						<div class="form-group">
					    	<label class="label">Gateway IP</label>
			  				<input class="form-control" type="text" v-model="modalData.gateway.GATEWAY_IP" readonly>
						</div>
						<div class="form-group">
					    	<label class="label">Serial No</label>
			  				<input class="form-control" :class="{'border border-danger':errors.SERIAL_NO}" type="text" v-model="modalData.SERIAL_NO">
				  				<!-- display this when error occured -->
			  				<small v-if="errors.SERIAL_NO" class="text-danger">{{ errors.SERIAL_NO[0] }}</small>
						</div><div class="form-group">
					    	<label class="label">Slave ID</label>
			  				<input class="form-control" :class="{'border border-danger':errors.SLAVE_ID}" type="text" v-model="modalData.SLAVE_ID">
				  				<!-- display this when error occured -->
			  				<small v-if="errors.SLAVE_ID" class="text-danger">{{ errors.SLAVE_ID[0] }}</small>
						</div>
					</div>
					<!-- modal body end -->

					<!-- modal footer -->
					<div class="modal-footer">
						<!-- call updateData function on line 270 -->
				        <!-- enable /disable the save button using devUpdateDisabled function on line 326 -->
						<button class="btn btn-primary" @click="updateData()" :disabled="modUpdateDisabled">
							<!-- display loading animation -->
						    <span class="pull-left" v-if="loading">
								<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
							</span>
							<!-- change text value when click -->
							<span> {{ btn_text }} </span>
						</button>
						<!-- call close function on line 254 -->
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
		props: ['cat','show', 'currentPage' , 'modalData'],
		created (){
			//call getFloors function
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
				btn_text: 'Update'
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
		            this.getRooms();
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
                    //this.modalData.ROOM_ID = null;
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
				this.btn_text = 'Update',
				this.$emit('loaddata', this.currentPage),
				this.$emit('close')
			},
			//Function Name: errs
			//Function Description: error function
			//PARAM: error = response
			errs(error){
				this.errors = error.response.data.errors
				this.loading = false,
				this.btn_text = 'Update'
			},
			//Function Name: updateData
			//Function Description: update device or gateway function
			//KEY: 0 = gateway, 1 = device
			updateData(){
				this.loading = true;
				this.btn_text = 'Updating';
				switch(this.cat){
					case "gateway":
						axios({
						  url: 'gateways/update',
						  method: 'post',
						  data: {
						  	'KEY': this.cat,
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
						.catch((error) => console.log(error));

					break;
					case "modBusGateway":
						axios({
						  url: 'gateways/update',
						  method: 'post',
						  data: {
						  	'KEY': this.cat,
						  	'FLOOR_ID':this.modalData.FLOOR_ID,
						  	'GATEWAY_ID':this.modalData.GATEWAY_ID,
						  	'GATEWAY_SERIAL_NO':this.modalData.GATEWAY_SERIAL_NO,
						  	'GATEWAY_NAME':this.modalData.GATEWAY_NAME,
						  	'GATEWAY_IP':this.modalData.GATEWAY_IP
						  	}
						})
						.then((response)=>
							setTimeout(() => {
							  	this.close();
							}, 1500)
						)
						.catch((error) => console.log(error));
					break;
					case "device":
						axios({
						  url: 'devices/update',
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
					break;
					case "modBusMeter":
						axios({
						  url: 'meters/update',
						  method: 'post',
						  data: {
						  	'METER_ID':this.modalData.METER_ID,
						  	'SERIAL_NO':this.modalData.SERIAL_NO,
						  	'SLAVE_ID':this.modalData.SLAVE_ID
						  	}
						})
						.then((response)=>
							setTimeout(() => {
							   this.close();
							}, 1500)
						)
						.catch((error) => this.errs(error));
					break;
				}
			},
		},
		computed:{
			//disable gateway button
		    gwUpdateDisabled(){
		    	if(this.modalData.GATEWAY_NAME == null || this.modalData.GATEWAY_NAME == "" || this.modalData.ROOM_ID == null){
					return true;
		    	}else{
		    		return false;
		    	}
		    },
		    modgwUpdateDisabled(){
		    	if(this.modalData.GATEWAY_NAME == null || this.modalData.GATEWAY_NAME == "" || this.modalData.FLOOR_ID == null || this.modalData.GATEWAY_SERIAL_NO == null || this.modalData.GATEWAY_IP == null){
					return true;
		    	}else{
		    		return false;
		    	}
		    },
		    //disable update button
		    devUpdateDisabled(){
		    	if(this.modalData.DEVICE_NAME == null || this.modalData.DEVICE_NAME == ""){
					return true;
		    	}else{
		    		return false;
		    	}
		    },
		    //disable update button
		    modUpdateDisabled(){
		    	if(this.modalData.SERIAL_NO == null || this.modalData.SLAVE_ID == null || this.modalData.SERIAL_NO == "" || this.modalData.SLAVE_ID == ""){
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