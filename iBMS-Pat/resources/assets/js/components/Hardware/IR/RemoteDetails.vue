
<template>
    <!-- v-if="show" -->
    <div v-if="show" class="col-sm-6">
        <div class="col-sm-12 mt-5">
            <div class="hardware-right-pane">
                <div class="box-inside-right-pane">
                    <div class="hardware-action-position">
                        <a v-if="irData.category == 'updateIrList'" class="pointer" @click="del(irData)">
                            <span>
                                <i aria-hidden="true" class="text-white fa fa-trash-o"></i>
                            </span>
                        </a>
                    </div>
                    <img :src="sourceImage" class="hardware-img-right-pane mt-4" @error="imgUrlAlt">
                </div>
                <div class="hardware-right-pane-details">
                    <div class="text-dark font-weight-bold h5">
                        <div class="divider-line">REMOTE</div>
                    </div>
                    <div class="text-dark line-height-1">Appliance: {{ irData.TARGET_DEVICE }}</div>
                    <div class="text-dark line-height-1">Brand: {{ irData.TARGET_BRAND }}</div>
                    <div v-if="irData.category == 'updateIrList'" class="text-dark line-height-1">IR Learning: {{ irData.LEARNING_NAME }}</div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        //declare components
        components: { },
        props: [],
        created() {
            this.$bus.$on('selectedApplianceData', data => {
                this.showPanel(data);
            });
        },
        data() {
            return {
                irData: {},
                editMode: false,
                show: false,
            }
        },
        methods: {
            //Function Name: showPanel
            //Function Description: show ir remote details
            //Param: data (device)
            showPanel(data){
                if(data.category == 'addIrList'){
                    this.show = true;
                    this.irData = Object.assign({}, data);
                }else if(data.category == 'updateIrList'){
                    this.show = true;
                    this.irData = Object.assign({}, data);
                }else{
                    this.show = false;
                }
            },
            //Function Name: del
            //Function Description: displays delete modal
            //Param: data
            del(data){
                this.openModal(data);
            },
            //Function Name: openModal
            //Function Description: opens specific modal
            //Param: data
            openModal(data){
                    this.$swal({
                        title:"Delete IR Learning Record",
                        text:"Are you sure?",
                        type:"warning",
                        showCancelButton:true,
                        confirmButtonColor:"#3085d6",
                        cancelButtonColor:"#d33",
                        confirmButtonText:"Yes"
                    }).then((result) =>{
                        if(result.value){
                            axios.post('learning-list/delete',{
                                LEARNING_LIST: this.irData
                            }).then(response => {
                                this.$swal({
                                    title:'Deleted',
                                    text:"IR Record has been deleted.",
                                    type:'success',
                                    timer:1500,
                                    showConfirmButton:false
                                });
                                this.$bus.$emit('changeTabUpdate');
                                this.show = false;
                            });
                        }
                    });
            },
            //Function Name: imgUrlAlt
            //Functoin Description: changes device image to not found on 404 error
            //Param: event (error)
            imgUrlAlt(event) {
                event.target.src = "img/image_not_found.png"
            },
        },
        beforeDestroy() {
            this.$bus.$off('selectedApplianceData');
        },
        computed:{
            disableSave(){
                if(this.irData.GATEWAY_NAME == '' || this.irData.GATEWAY_NAME == null){
                    return true;
                }
            },
            sourceImage(){
                var image_source = '';
                if(this.irData.TARGET_DEVICE == 'Aircon'){
                    image_source = 'img/aircon.png';
                }else if(this.irData.TARGET_DEVICE == 'TV'){
                    image_source = 'img/TV.png';
                }
                return image_source;
            }
        },
        watch: {
        },
    };
</script>