<template>
	<!-- openmodal are props of vue contain the data from other component -->
	<!-- modal -->
	<div class="modal" :class="show">
		<!-- call close function on line 48 -->
		<div class="modal-background" @click="close"></div>
		<!-- modal dialog -->
		<div class="modal-dialog modal-dialog-centered" role="document">
			<!-- modal content -->
			<div class="modal-content scan-bg">
				<!-- modal body -->
				<div class="modal-body">
					<div v-if="showSpinner" class="m-3 text-center">
						<div class="spinner">
							<spinner :status="spinner.status" :color="spinner.color" :size="spinner.size" :depth="spinner.depth" :rotation="spinner.rotation" :speed="spinner.speed"></spinner>
						</div>
						<h5 class="text-orange mt-4"> {{$t('gateway.scan')}} .......</h5>
						<div class="text-center mt-3">
							<a class="btn btn-secondary col-sm-4 text-white" @click="close">{{$t('user.cancel')}}</a>
						</div>
					</div>
					<div v-else class="display-4 text-center">
						<div class="text-center">
							<i class="text-orange fa fa-check-circle fa-3x fa-fw"></i>
						</div>
						<h5 class="text-orange mt-4"> {{$t('gateway.scanComplete')}}</h5>
						<div class="text-center mt-3">
							<a class="btn btn-secondary col-sm-4 text-white" @click="close">{{$t('user.save')}}</a>
						</div>
					</div>
				</div>
				<!-- modal body end -->
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
		props: ['show', 'category', 'currentPage'],
		created(){
			this.getScan();
            $("body").addClass("modal-open");
		},
		data(){
			return{
				showSpinner: true,
				spinner: {
                    size: 150,
                    status: true,
                    // color: '#4fc08d',
                    color: '#fd9500',
                    depth: 15,
                    rotation: true,
                    speed: 0.7,
                }
			}
		},
		methods: {
			//Function Name: getScan
			//Function Description: scan for gateway or device
			//Param: category
			getScan(){
				var url = '';
				if(this.category == 'device'){
					url = 'devices/scan';
				}else{
					url = 'gateways/scan';
				}
				axios.get(url)
		        .then(response => {
		            let data = response.data;
		            for(let i = 0; i <= 50; i++){
		                setTimeout(() => {
		                   	this.showSpinner = false;
		                }, 10*i)
		            }
					this.$emit('loaddata', this.currentPage);

		        })
		        .catch(errors => {
		            console.log(errors);
		        });
			},
			//Function Name: close
			//Function Description: close modal function
			close(){
				this.$emit('loaddata', this.currentPage);
				this.$emit('close');
			},
		},
		beforeDestroy() {
            $("body").removeClass("modal-open");
		}
	}
</script>