<template>
	<!-- modal -->
	<div class="modal" :class="openmodal">
		<!-- call close function on line 72 -->
		<div class="modal-background" @click="close"></div>
		<!-- modal dialog -->
		<div class="modal-dialog modal-dialog-centered" role="document">
			<!-- modal content -->
			<div class="modal-content">
				<!-- modal header -->
				<div class="modal-header">
					<h5 class="modal-title">Delete Bind</h5>
					<!-- call close function on line 72 -->
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<!-- modal body -->
				<div class="modal-body">
					<section class="modal-card-body">
						Are you sure want to remove bind of this <b> {{ list2.target_device.DEVICE_NAME }} </b> to <b> {{ list.DEVICE_NAME }} </b>?
					</section>
				</div>
				<!-- modal body end -->

				<!-- modal footer -->
				<div class="modal-footer">
					<!-- call deleteBinding function on line 79 -->
					<button class="btn btn-danger" @click="deleteBinding">
						<!-- display loading animation -->
					    <span class="pull-left" v-if="loading">
							<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
						</span>
						<!-- change text value when click -->
						<span v-if="delvalue"> Delete </span>
						<span v-else="delvalue"> Deleting </span>
					</button>
					<!-- call close function on line 72 -->
					<button type="button" class="btn btn-secondary" @click="close">Cancel</button>
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
		props: {
			openmodal: String
		},
		data(){
			return{
                // where data variables is declare and initialize
				list:{},
				list2:{
					target_device:{
						DEVICE_NAME: '',
					},
				},
				errors:{},
				loading:false,
				delvalue:1,
			}
		},
		created(){
            $("body").addClass("modal-open");
		},
		methods: {
			//Function Name: close
			//Function Description: close modal function
			close(){
				this.$emit('loaddata', '');
				this.$emit('close');
				this.loading = false;
				this.delvalue = 1;
			},
			//Function Name: deleteBinding
			//Function Description: delete binding function
			deleteBinding(){
				this.loading = true;
				this.delvalue = 0;
				//ajax call
				//PARAM: BINDING_ID
				axios({
				  url: 'bindings/delete',
				  method: 'post',
				  data: {
				  	'BINDING_ID':this.list2.BINDING_ID,
				  	}
				})
				.then((response)=>
					setTimeout(() => {
					  	this.close();
					}, 1500)
				)
				.catch((error) => console.log(error));
			}
		},
		beforeDestroy() {
            $("body").removeClass("modal-open");
		}
	}
</script>