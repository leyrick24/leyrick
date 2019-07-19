<template>
	<!-- cat,show are props of vue contain the data from other component -->
	<!-- gateway display -->
	<!-- check if the category is gateway -->
	<div v-if="cat == 'modBusGateway'">
		<!-- modal -->
		<div class="modal" :class="show">
			<!-- call close function on line 126 -->
			<div class="modal-background" @click="close"></div>
			<!-- modal dialog -->
			<div class="modal-dialog modal-dialog-centered" role="document">
				<!-- modal content -->
				<div class="modal-content">
					<!-- modal header -->
					<div class="modal-header">
						<h5 class="modal-title">Activate ModBus Gateway</h5>
						<!-- call close function on line 126 -->
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<!-- modal header end-->

					<!-- modal body -->
					<div class="modal-body">
						<section class="modal-card-body">
							Are you sure want to activate <b>{{modalData.GATEWAY_NAME}}</b>?
						</section>
					</div>
					<!-- modal body end -->

					<!-- modal footer -->
					<div class="modal-footer">
						<!-- call undelData function on line 134 -->
						<button class="btn btn-primary" @click="undelData()">
							<!-- display loading animation -->
						    <span class="pull-left" v-if="loading">
								<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
							</span>
							<!-- change text value when click -->
							<span v-if="undelvalue"> Process </span>
							<span v-else="undelvalue"> Processing </span>
						</button>
						<!-- call close function on line 126 -->
						<button type="button" class="btn btn-secondary" @click="close">Cancel</button>
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
	<div v-else-if="cat == 'modBusMeter'">
		<!-- modal -->
		<div class="modal" :class="show">
			<!-- call close function on line 126 -->
			<div class="modal-background" @click="close"></div>
			<!-- modal dialog -->
			<div class="modal-dialog modal-dialog-centered" role="document">
				<!-- modal content -->
				<div class="modal-content">
					<!-- modal header -->
					<div class="modal-header">
						<h5 class="modal-title">Activate Modbus Meter</h5>
						<!-- call close function on line 126 -->
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<!-- modal header end -->

					<!-- modal body -->
					<div class="modal-body">
						<section class="modal-card-body">
							Are you sure want to activate <b>{{modalData.SERIAL_NO}}</b>?
						</section>
					</div>
					<!-- modal body end -->

					<!-- modal footer -->
					<div class="modal-footer">
						<!-- call undelData function on line 134 -->
						<button class="btn btn-primary" @click="undelData()">
							<!-- display loading animation -->
						    <span class="pull-left" v-if="loading">
								<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
							</span>
							<!-- change text value when click -->
							<span v-if="undelvalue"> Process </span>
							<span v-else="undelvalue"> Processing </span>
						</button>
						<!-- call close function on line 126 -->
						<button type="button" class="btn btn-secondary" @click="close">Cancel</button>
					</div>
					<!-- modal footer end -->
				</div>
				<!-- modal content end -->
			</div>
			<!-- modal dialog end -->
		</div>
		<!-- modal end-->
	</div>
	<!-- device display end-->
</template>

<script>
	export default{
		//get the attributes from other components
		props: ['cat','show', 'currentPage', 'modalData'],
		data(){
			return{
				errors:{},
				loading:false,
				undelvalue:1,
			}
		},
		created(){
            $("body").addClass("modal-open");
		},
		methods: {
			//Funciton Name: close
			//Function Description: close modal function
			close(){
				this.$emit('loaddata', this.currentPage),
				this.$emit('close');
				this.loading = false;
				this.undelvalue = 1;
			},
			//Function name: undelData
			//Function Description: undelete function
			//KEY: 0 = gateway, 1 = device
			undelData(){
				this.loading = true;
				this.undelvalue = 0;
				switch(this.cat){
					case "modBusGateway":
						axios({
						  url: 'gateways/undelete',
						  method: 'post',
						  data: {
						  	'GATEWAY_ID':this.modalData.GATEWAY_ID,
						  	}
						})
						.then((response)=>
							setTimeout(() => {
								this.close();
							}, 1500)
						)
						.catch((error) => console.log(error));
					break;
					case "modBusMeter":
						axios({
						  url: 'meters/unDelete',
						  method: 'post',
						  data: {
						  	'METER_ID':this.modalData.METER_ID,
						  	}
						})
						.then((response)=>
							setTimeout(() => {
								this.close();
							}, 1500)
						)
						.catch((error) => console.log(error));
					break;
				}
			}
		},
		beforeDestroy() {
            $("body").removeClass("modal-open");
		}
	}
</script>