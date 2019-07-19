
<template>
    <div v-if="show" class="col-sm-6">
        <div class="col-sm-12 mt-5">
            <div class="hardware-right-pane">
                <div class="box-inside-right-pane">
                    <div class="hardware-action-position">
                        <a v-if="gatewayData.REG_FLAG == 1" class="pointer" @click="edit()">
                            <span>
                                <i aria-hidden="true" class="text-white fa fa-edit"></i>
                            </span>
                        </a>
                        <a v-if="gatewayData.REG_FLAG == 9" class="pointer" @click="active()">
                            <span>
                                <i aria-hidden="true" class="text-white fa fa-check"></i>
                            </span>
                        </a>
                        <a v-if="gatewayData.REG_FLAG == 1" class="pointer" @click="del(gatewayData.MANUFACTURER_ID)">
                            <span>
                                <i aria-hidden="true" class="text-white fa fa-trash-o"></i>
                            </span>
                        </a>
                    </div>
                    <img :src="sourceImage" class="hardware-img-right-pane mt-2" @error="imgUrlAlt">
                </div>
                <div class="hardware-right-pane-details">
                    <div class="text-dark font-weight-bold h5">
                        <input type="text" v-model="gatewayData.GATEWAY_NAME" :disabled="!editMode" :class="editMode ? 'form-control col-sm-6 fade-in' : 'view'" placeholder="Gateway Name">
                        <div><small v-if="errors.GATEWAY_NAME" class="text-danger">{{ errors.GATEWAY_NAME[0] }}</small></div>
                        <div class="divider-line"></div>
                    </div>
                    <div class="text-dark line-height-1">{{$t('gateway.gatewayIp')}}: {{ gatewayData.GATEWAY_IP }}</div>
                    <div class="text-dark line-height-1">{{$t('gateway.devSerialNo')}}: {{ gatewayData.GATEWAY_SERIAL_NO }}</div>
                    <div class="text-dark line-height-1">{{$t('gateway.onlineStat')}}:
                        <span v-if="gatewayData.ONLINE_FLAG == 1"> {{$t('online')}} </span>
                        <span v-else>{{$t('offline')}}</span>
                    </div>
                    <div v-if="editMode" class="py-3 fade-in">
                        <button @click="save()" class="btn btn-primary" :disabled="disableSave">{{$t('user.save')}}</button>
                        <button @click="cancelEdit()" class="btn btn-danger">{{$t('user.cancel')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        created() {
            //watch for selectedData emit
            this.$bus.$on('selectedData', data => {
                this.showPanel(data);
            });
        },
        data() {
            return {
                gatewayData: {},
                editMode: false,
                show: false,
                errors:{},
            }
        },
        methods: {
            //Function Name: errs
            //Function Description: stores error data
            //Param: error
            errs(error){
                this.errors = error.response.data.errors;
            },
            //Function Name: showPanel
            //Function Description: show gateway details
            //Param: data (gateway)
            showPanel(data){
                if(data.REG_FLAG == 1 || data.MANUFACTURER_ID == 2){
                    this.show = true;
                    this.gatewayData = Object.assign({}, data);
                }else{
                    this.show = false;
                }
            },
            //Function Name: active
            //Function Description: activate modbus emeter
            active(){
                this.$swal({
                    title:"Active ModBus Gateway",
                    text:"Are you sure?",
                    type:"warning",
                    showCancelButton:true,
                    confirmButtonColor:"#3085d6",
                    cancelButtonColor:"#d33",
                    confirmButtonText:"Yes"
                }).then((result) =>{
                    if(result.value){
                        axios.post('gateways/undelete',{
                            GATEWAY_ID: this.gatewayData.GATEWAY_ID
                        }).then(response => {
                            if(response.data == 'success'){
                                this.$swal({
                                    title:'Actived ModBus Gateway',
                                    text:"ModBus Gateway has been deleted.",
                                    type:'success',
                                    timer:1500,
                                    showConfirmButton:false
                                });
                            }else{
                                this.$swal(
                                    'Error',
                                    "ModBus Gateway can't delete.",
                                    'error'
                                );
                            }
                            this.$bus.$emit('getData', response.data);
                            this.show = false;
                        });
                    }
                });

            },
            //Function Name: edit
            //Function Description: enable editting of gateway
            edit(){
                this.cachedData = Object.assign({}, this.gatewayData);
                this.editMode = true;
            },
            //Function Name: del
            //Function Description: opens modal to delete gateway
            del(data){
                this.openModal(0,data);
            },
            //Function Name: save
            //Function Description: updates gateway details
            save(){
                var cat = this.gatewayData.MANUFACTURER_ID;
                if(cat == 2){
                    cat = 'modBusGateway';
                }else if(cat == 1){
                    cat = 'gateway';
                }
                axios.post('gateways/update', {
                    KEY: cat,
                    FLOOR_ID:this.gatewayData.FLOOR_ID,
                    ROOM_ID:this.gatewayData.ROOM_ID,
                    GATEWAY_ID:this.gatewayData.GATEWAY_ID,
                    GATEWAY_NAME:this.gatewayData.GATEWAY_NAME
                }).then(response=>{
                    this.$bus.$emit('getData', response.data);
                    this.openModal(1);
                    this.editMode = false;
                    this.errors = {};
                }).catch((error) => this.errs(error));
            },
            //Function Name: cancelEdit
            //Function Description: cancel edit mode
            cancelEdit(){
                this.gatewayData = Object.assign({}, this.cachedData);
                this.editMode =  false;
                var details = '';
            },
            //Function Name: openModal
            //Function Description: opens specific modals and alerts
            //Param: data
            openModal(data,cat){
                if(data == 1){
                    this.$swal({
                        position: 'center',
                        type: 'success',
                        title: 'Gateway Successfully Updated',
                        showConfirmButton: false,
                        timer: 1200
                    });
                }else{
                    if(cat == 2){
                        cat = 'modBusGateway';
                    }else if(cat == 1){
                        cat = 'gateway';
                    }
                    this.editMode = false;
                    this.$swal({
                        title:"Delete Gateway",
                        text:"Are you sure?",
                        type:"warning",
                        showCancelButton:true,
                        confirmButtonColor:"#3085d6",
                        cancelButtonColor:"#d33",
                        confirmButtonText:"Yes"
                    }).then((result) =>{
                        if(result.value){
                            axios.post('gateways/delete',{
                                KEY: cat,
                                GATEWAY_ID: this.gatewayData.GATEWAY_ID
                            }).then(response => {
                                if(response.data == 'success'){
                                    this.$swal({
                                        title:'Deleted',
                                        text:"Gateway has been deleted.",
                                        type:'success',
                                        timer:1500,
                                        showConfirmButton:false
                                    });
                                }else if (response.data == 'gateway'){
                                    this.$swal({
                                        title:"Unable to contact gateway.",
                                        text:"Continue with deletion?",
                                        type:"warning",
                                        showCancelButton:true,
                                        confirmButtonColor:"#3085d6",
                                        cancelButtonColor:"#d33",
                                        confirmButtonText:"Yes"
                                    }).then((result)=>{
                                        axios.post('gateways/delete',{
                                            FORCE: true,
                                            KEY: cat,
                                            GATEWAY_ID: this.gatewayData.GATEWAY_ID
                                        })
                                        .then(response=>{
                                            if (response.data == 'success') {
                                                this.$swal({
                                                    title:'Deleted',
                                                    text:"Gateway has been deleted.",
                                                    type:'success',
                                                    timer:1500,
                                                    showConfirmButton:false
                                                });
                                            }
                                            else{
                                                this.$swal(
                                                    'Error',
                                                    "Gateway can't delete.",
                                                    'error'
                                                );
                                            }
                                            this.$bus.$emit('getData', response.data);
                                            this.show = false;
                                        })
                                    });
                                }else{
                                    this.$swal(
                                        'Error',
                                        "Gateway can't delete.",
                                        'error'
                                    );
                                }
                                this.$bus.$emit('getData', response.data);
                                this.show = false;
                            });
                        }
                    });
                }
            },
            //Function Name: imgUrAlt
            //Function Description: change image target to not found on error
            //Param: event (404)
            imgUrlAlt(event) {
                event.target.src = "img/image_not_found.png"
            },
        },
        //stops watching for selectData emit on close
        beforeDestroy() {
            this.$bus.$off('selectedData');
        },
        computed:{
            //return true to disable save button when parameters are empty
            disableSave(){
                if(this.gatewayData.GATEWAY_NAME == '' || this.gatewayData.GATEWAY_NAME == null){
                    return true;
                }
            },
            //changes source image according to category
            sourceImage(){
                var image_source = '';
                if(this.gatewayData.category == 'systemGateway'){
                    image_source = 'img/gateway-image.png';
                }else{
                    image_source = 'img/modBus_gateway.png';
                }
                return image_source;
            }
        },
        watch: {
        },
    };
</script>