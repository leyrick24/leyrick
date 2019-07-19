<template>
	<!-- cat,show are props of vue contain the data from other component -->
	<!-- gateway display -->
	<!-- modal -->
	<div class="modal" :class="show">
		<!-- call close function on line 259 -->
		<div class="modal-background" @click="close"></div>
		<!-- modal dialog -->
		<div class="modal-dialog modal-dialog-centered" role="document">
			<!-- modal content -->
			<div class="modal-content">
				<!-- modal header -->
				<div class="modal-header text-dark">
					<h5 class="modal-title">Add Gateway</h5>
					<!-- call close function on line 259 -->
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<!-- modal header end-->

				<!-- modal body -->
				<div class="modal-body text-dark">
					<div class="form-group">
				    	<label class="label">Manufacturer</label>
			            <div class="">
			                <select class="custom-select" v-model="modalData.MANUFACTURER_ID">
			                	<!-- loop the rooms data -->
			                    <option v-for="manufacturer,key in manufacturers" :key="manufacturer.MANUFACTURER_ID" :value="manufacturer.MANUFACTURER_ID">{{manufacturer.MANUFACTURER_NAME}}</option>
			                </select>
			            </div>
					</div>
					<div class="form-group">
				    	<label class="label">Floor</label>
			            <div class="">
							<!-- call getRooms function on line 247 every change-->
			                <select class="custom-select" v-model="modalData.FLOOR_ID" :disabled="floorDisabled">
			                	<!-- loop the floors data -->
			                    <option v-for="floor,key in floors" :key="floor.FLOOR_ID" :value="floor.FLOOR_ID">{{floor.FLOOR_NAME}}</option>
			                </select>
			            </div>
					</div>
					<div class="form-group">
				    	<label class="label">Gateway Serial No</label>
		  				<input class="form-control" :class="{'border border-danger':errors.GATEWAY_SERIAL_NO}" type="text" v-model="modalData.GATEWAY_SERIAL_NO" :disabled="serialDisabled">
		  				<!-- display this when error occured -->
		  				<small v-if="errors.GATEWAY_SERIAL_NO" class="text-danger">{{ errors.GATEWAY_SERIAL_NO[0] }}</small>
					</div>
					<div class="form-group">
				    	<label class="label">Gateway Name</label>
			            <!-- enable /disable the gateway name input using nameDisabled function on line 332 -->
			            <!-- when the input is enter call the addData funtion on line 341 -->
		  				<input class="form-control" :class="{'border border-danger':errors.GATEWAY_NAME}" type="text" v-model="modalData.GATEWAY_NAME" :disabled="nameDisabled">
		  				<!-- display this when error occured -->
		  				<small v-if="errors.GATEWAY_NAME" class="text-danger">{{ errors.GATEWAY_NAME[0] }}</small>
					</div>
					<div class="form-group">
				    	<label class="label">Gateway IP</label>
		  				<input class="form-control" :class="{'border border-danger':errors.GATEWAY_IP}" type="text" v-model="modalData.GATEWAY_IP" @keyup.enter="addData()" :disabled="ipDisabled">
		  				<!-- display this when error occured -->
		  				<small v-if="errors.GATEWAY_IP" class="text-danger">{{ errors.GATEWAY_IP[0] }}</small>
					</div>
				</div>
				<!-- modal body end -->

				<!-- modal footer -->
				<div class="modal-footer">
					<!-- call addData function on line 275 -->
			        <!-- enable /disable the save button using gwSaveDisabled function on line 341 -->
					<button class="btn btn-primary" @click="addData()" :disabled="gwSaveDisabled">
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
</template>

<script>
	export default{
		//get the attributes from other components
		props: ['cat','show', 'currentPage'],
		created (){
        	this.getManufacturer();
        	this.getFloors();
            $("body").addClass("modal-open");
		},
		data(){
			return{
                // where data variables is declare and initialize]
               	manufacturers: {},
				floors:{},
				modalData:{},
				errors:{},
				loading:false,
				btn_text: 'Save',
				error_text: 'This field is required',
			}
		},
		methods: {
			//Function Name: getManufacturer
			//Function Description: get manufacturer function
			getManufacturer(){
				//ajax call
				axios.get('manufacturer')
	            .then(response => {
	            	this.manufacturers = response.data;
	            })
	            .catch(errors => {
	                console.log(errors);
	            });
			},
			//Function Name: getFloors
			//Function Description: get floors
			getFloors(){
				//ajax call
				axios.get('floors')
	            .then(response => {
	            	this.floors = response.data;
	            })
	            .catch(errors => {
	                console.log(errors);
	            });
			},
			//Function Name: close
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
			//PARAM = KEY: 0 = gateway, 1 = device
			addData(){
				this.loading = true;
				this.btn_text = 'Saving';
				//ajax call
				//PARAM: FLOOR_ID,ROOM_ID,GATEWAY_ID,GATEWAY_NAME
				axios({
				  url: 'gateways/registerModBus',
				  method: 'post',
				  data: {
				  	'MANUFACTURER_ID':this.modalData.MANUFACTURER_ID,
				  	'FLOOR_ID':this.modalData.FLOOR_ID,
				  	'GATEWAY_SERIAL_NO':this.modalData.GATEWAY_SERIAL_NO,
				  	'GATEWAY_ID':this.modalData.GATEWAY_ID,
				  	'GATEWAY_NAME':this.modalData.GATEWAY_NAME,
				  	'GATEWAY_IP':this.modalData.GATEWAY_IP
				  	}
				})
				.then((response)=>
					setTimeout(() => {
					   this.close();
					}, 1500)
				)
				.catch((error) => this.errs(error));


			}
		},
		computed:{
			//disable room select
			floorDisabled(){
		    	if(this.modalData.MANUFACTURER_ID == null){
					return true;
		    	}else{
		    		return false;
		    	}
		    },
			//disable serial field
		    serialDisabled(){
		    	if(this.modalData.FLOOR_ID == null){
					return true;
		    	}else{
		    		return false;
		    	}
		    },
			//disable name field
		    nameDisabled(){
		    	if(this.modalData.GATEWAY_SERIAL_NO == null){
					return true;
		    	}else{
		    		return false;
		    	}
		    },
		    ipDisabled(){
		    	if(this.modalData.GATEWAY_NAME == null){
					return true;
		    	}else{
		    		return false;
		    	}
		    },

			//disable gateway button
		    gwSaveDisabled(){
		    	if(this.modalData.GATEWAY_IP == null || this.modalData.GATEWAY_IP == ""){
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