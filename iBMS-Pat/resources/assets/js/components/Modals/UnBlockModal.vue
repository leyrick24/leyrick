<template>
	<!-- cat,show are props of vue contain the data from other component -->
	<!-- gateway display -->
	<!-- check if the category is gateway -->
	<div v-if="cat == 'gateway'">
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
						<h5 class="modal-title">Unblock Gateway</h5>
						<!-- call close function on line 126 -->
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<!-- modal header end-->

					<!-- modal body -->
					<div class="modal-body">
						<section class="modal-card-body">
							Are you sure want to unblock <b>{{modalData.GATEWAY_NAME}}</b>?
						</section>
					</div>
					<!-- modal body end -->

					<!-- modal footer -->
					<div class="modal-footer">
						<!-- call unblockData function on line 134 -->
						<button class="btn btn-primary" @click="unblockData()">
							<!-- display loading animation -->
						    <span class="pull-left" v-if="loading">
								<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
							</span>
							<!-- change text value when click -->
							<span v-if="unblockvalue"> Process </span>
							<span v-else="unblockvalue"> Processing </span>
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
	<div v-else-if="cat == 'device'">
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
						<h5 class="modal-title">Unblock Device</h5>
						<!-- call close function on line 126 -->
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<!-- modal header end -->

					<!-- modal body -->
					<div class="modal-body">
						<section class="modal-card-body">
							Are you sure want to unblock <b>{{modalData.DEVICE_NAME}}</b>?
						</section>
					</div>
					<!-- modal body end -->

					<!-- modal footer -->
					<div class="modal-footer">
						<!-- call unblockData function on line 134 -->
						<button class="btn btn-primary" @click="unblockData()">
							<!-- display loading animation -->
						    <span class="pull-left" v-if="loading">
								<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
							</span>
							<!-- change text value when click -->
							<span v-if="unblockvalue"> Process </span>
							<span v-else="unblockvalue"> Processing </span>
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
		props: ['cat', 'show', 'currentPage', 'modalData'],
		data(){
			return{
				errors:{},
				loading:false,
				unblockvalue:1,
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
				this.unblockvalue = 1;
			},
			//Function Name: unblockData
			//Function Description: unblock function
			//KEY: 0 = gateway, 1 = device
			unblockData(){
				this.loading = true;
				this.unblockvalue = 0;
				switch(this.cat){
					case "gateway":
						axios({
						  url: 'gateways/unblock',
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
					case "device":
						axios({
						  url: 'devices/unblock',
						  method: 'post',
						  data: {
						  	'DEVICE_ID':this.modalData.DEVICE_ID,
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