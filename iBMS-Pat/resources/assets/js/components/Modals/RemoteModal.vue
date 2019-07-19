
<template>
	<!-- modal -->
	<div class="modal" :class="openmodal">
			<!-- call close function on line 190 -->
		<div class="modal-background" @click="close"></div>
		<!-- modal dialog -->
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<!-- modal content -->
			<div class="modal-content">
				<!-- modal header -->
				<div class="modal-header">
					<!-- display Device Name -->
					<h5 class="modal-title">Remote Controller - {{ list.DEVICE_NAME }}</h5>
					<!-- call close function on line 190 -->
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<!-- modal body -->
				<div class="modal-body">
					<div class="form-group">
				    	<label class="label">Select Controller</label>
			            <div class="">
			            	<!-- call chooseRemote function on line 212-->
			            	<!-- enable/disable select using remoteTypeDisabled on line 274 -->
			                <select class="custom-select" v-model="info.REMOTE_TYPE" @change="chooseRemote()" :disabled="remoteTypeDisabled">
			                    <option value="TV">Television</option>
			                    <option value="AC">Air Condition</option>
			                </select>
			            </div>
					</div>
					<div class="form-group">
				    	<label class="label">Controller Name</label>
		  				<input class="form-control" :class="{'border border-danger':errors['info.CONTROLLER_NAME']}" type="text" v-model="info.CONTROLLER_NAME">
			  			<!-- display this when error occured -->
		  				<small v-if="errors['info.CONTROLLER_NAME']" class="text-danger">{{ errors['info.CONTROLLER_NAME'][0] }}</small>
					</div>
					<!-- TV display -->
					<!-- check remote type -->
					<div v-if="info.REMOTE_TYPE == 'TV'">
					</div>
					<!-- TV display end -->

					<!-- AC display -->
					<div v-else-if="info.REMOTE_TYPE == 'AC'">
						<div class="form-group">
							<div class="">
								<!-- leaning mode -->
								<div class="d-flex align-items-center">
									<!-- call learn_mode function on line 217-->
									<a href="#" class="" data-toggle="tooltip" data-placement="right" title="Learn new command from Remote" @click="learn_mode()">
										<i class="fa fa-info-circle fa-2x fa-fw" :class="{'text-danger':learning}"></i>
									</a>
									<!-- change text value when click -->
									<div v-if="!learning">Learn</div>
									<div v-else>Learning Mode</div>
								</div>
								<!-- learning mode end -->

								<!-- buttons -->
								<div class="m-4">
									<div class="row">
									     <div class="col-md-12">
									          <div class="text-center">
												<div class="">Power</div>
												<!-- call learn function on line 231-->
												<!-- enable/disabled button using btnDisabled on line 258-->
												<button class="btn border border-secondary bg-light" v-model="info.AIRCON_POWER" @click="learn('power')" :disabled="btnDisabled">
													<!-- change the background using the power_learn in every button -->
													<i class="fa fa-power-off fa-5x fa-fw text-secondary" :class="power_learn"></i>
												</button>
									          </div>
									     </div>
									 </div>
									<div class="row mt-3">
									     <div class="col-md-12">
									          <div class="text-center">
												<h6>Temperature</h6>
												<!-- call learn function on line 231-->
												<!-- enable/disabled button using btnDisabled on line 258-->
												<button class="btn border border-secondary bg-light" v-model="info.AIRCON_TEMP_UP" @click="learn('temp_up')" :disabled="btnDisabled">
													<!-- change the background using the power_learn in every button -->
													<i class="fa fa-chevron-up fa-5x fa-fw text-secondary" :class="temp_up_learn"></i>
												</button>
												<!-- call learn function on line 231-->
												<!-- enable/disabled button using btnDisabled on line 258-->
												<button class="btn border border-secondary bg-light" v-model="info.AIRCON_TEMP_DOWN" @click="learn('temp_down')" :disabled="btnDisabled">
													<!-- change the background using the power_learn in every button -->
													<i class="fa fa-chevron-down fa-5x fa-fw text-secondary" :class="temp_down_learn"></i>
												</button>
									          </div>
									     </div>
									 </div>
								</div>
								<!-- buttons end -->
							</div>
						</div>
					</div>
					<!-- AC display end -->
				</div>
				<!-- modal footer -->
				<div class="modal-footer">
					<!-- call saveController funtion on line 241 -->
					<!-- enable/disabled button using saveBtnDisabled on line 278 -->
					<button class="btn btn-primary" @click="saveController" :disabled="saveBtnDisabled">
						<!-- display loading animation -->
					    <span class="pull-left" v-if="loading">
							<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
						</span>
						<!-- change text value when click -->
						<span> {{ btn_text }} </span>
					</button>
					<!-- select function depends on the text value -->
					<button type="button" class="btn btn-secondary" @click="btn == 'Cancel' ? learn_mode() : close()">{{ btn }}</button>
				</div>
				<!-- modal footer end -->
			</div>
			<!-- modal content end -->
		</div>
		<!-- modal dialog end -->
	</div>
	<!-- modal end -->
</template>

<script>
	export default{
		//get the attributes from other components
		props: ['openmodal', 'currentPage'],
		created (){
            $("body").addClass("modal-open");
		},
		data(){
			return{
                // where data variables is declare and initialize
				list:{},
				info:{
					AIRCON_POWER: false,
					AIRCON_TEMP_UP: false,
					AIRCON_TEMP_DOWN: false,
					REMOTE_TYPE: null,
				},
				errors:{},
				loading:false,
				btn_text: 'Save',
				btn: 'Close',
				learning: false,
			}
		},
		methods: {
			//Funciton Name: close
			//Function Description: close modal function
			close(){
				this.errors = {},
				this.loading = false,
				this.learning = false;
				this.info.AIRCON_POWER = false;
				this.info.AIRCON_TEMP_UP = false;
				this.info.AIRCON_TEMP_DOWN = false;
				this.info.CONTROLLER_NAME = null;
				this.btn = 'Close';
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
			//Function name: chooseRemote
			//Function Description: remote type function
			chooseRemote(){
				this.learning = false;
				this.info.REMOTE_TYPE;
			},
			//Function name: learn_mode
			//Functino Description: learning mode function
			learn_mode(){
				if(this.learning){
					this.learning = false;
					this.info.AIRCON_POWER = false;
					this.info.AIRCON_TEMP_UP = false;
					this.info.AIRCON_TEMP_DOWN = false;
					this.btn = 'Close';
				}else{
					this.learning = true;
					this.btn = 'Cancel';
				}
			},
			//Function Name: learn
			//Function Description: learn data per buttons
			//PARAM: data
			learn(data){
				var command,value;
				if(data == 'power'){
					command = 'air_power_on_learn';
					value = 204;
				}else if(data == 'temp_up'){
					command = 'air_temp_up_learn';
					value = 205;
				}else if(data == 'temp_down'){
					command = 'air_temp_down_learn';
					value = 206;
				}
				axios.post('instructions/send',
				{
					'GATEWAY_ID' : this.list.gateway.GATEWAY_ID,
					'DEVICE_ID' : this.list.DEVICE_ID,
					'COMMAND': command,
					'VALUE' : value
				}).then(response=>{
					console.log(response.data);
				});
			},
			//Function Name: saveController
			//Function Description: save remote function
			saveController(){
				this.loading = true;
				this.btn_text = 'Saving';
				//ajax call
				//PARAM: all of data
				axios.post('/saveRemote', this.$data)
				.then((response)=>
					setTimeout(() => {
					   this.close();
					}, 1500)
				)
				.catch((error) => this.errs(error));
			},
		},
 		mounted(){

 			//Listen to incoming device update
			Echo.channel('test').listen('.irEvent', (e) => {
				console.log("learned");
				console.log(e.data);
			});
		},
		computed:{
			//disable learning
			btnDisabled(){
		        return this.learning ? false : true;
			},
			//disable AC power
			power_learn() {
		        return this.info.AIRCON_POWER  ? 'text-success' : 'text-secondary';
		    },
			//disable AC temp up
			temp_up_learn() {
		        return this.info.AIRCON_TEMP_UP  ? 'text-success' : 'text-secondary';
		    },
			//disable AC temp down
			temp_down_learn() {
		        return this.info.AIRCON_TEMP_DOWN  ? 'text-success' : 'text-secondary';
		    },
			//disable remote type select
		    remoteTypeDisabled(){
		    	return this.learning ? true : false;
		    },
			//disable savge button
		    saveBtnDisabled(){
		    	if(this.info.CONTROLLER_NAME == "" || this.info.CONTROLLER_NAME == null || this.learning == false){
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