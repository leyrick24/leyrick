<!--
    <Update> 2019.31.1  Jethro Changed table format to bootstrap vue table for uniform and removed uneccessary
-->
<template>
	<!-- card-body -->
    <div class="py-2" :class="tableHeight">
        <div class="input-group col-sm-6 float-right my-2">
            <input  v-model="tableData.search" @input="getData()" type="text" class="form-control" placeholder="Search">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="fa fa-search" aria-hidden="true"></i>
                </span>
            </div>
        </div>
        <!-- table -->
        <div class="table-responsive">
            <b-table  selectable
                      ref="deviceTable"
                      select-mode="single"
                      :items="devices"
                      :fields="table.fields"
                      :current-page="table.currentPage"
                      :per-page="table.perPage"
                      :filter="tableData.search"
                      @row-clicked="showGatewayDetails">
                <template v-if="tableData.flag == 0 || tableData.flag == 4" slot="gateway" slot-scope="row">
                    {{row.item.gateway.GATEWAY_NAME}}
                </template>
                <template v-if="tableData.flag == 0 || tableData.flag == 4" slot="devtype" slot-scope="row">
                    {{row.item.DEVICE_TYPE}}
                </template>
                <template v-if="tableData.flag == 0 || tableData.flag == 4" slot="serialno" slot-scope="row">
                    {{row.item.DEVICE_SERIAL_NO}}
                </template>
                <template v-if="tableData.flag == 0 || tableData.flag == 4" slot="action" slot-scope="row">
                    <a v-if="tableData.flag == 0" class="btn" @click="openModal('addModal',row.item)">
                        <span class="">
                            <i class="text-primary fa fa-plus-circle fa-lg" aria-hidden="true"></i>
                        </span>
                    </a>
                    <a v-if="tableData.flag == 0" class="btn" @click="block(row.item.DEVICE_ID)">
                        <span class="">
                            <i class="text-danger fa fa-ban fa-lg" aria-hidden="true"></i>
                        </span>
                    </a>
                    <a v-if="tableData.flag == 4" class="btn" @click="unblock(row.item.DEVICE_ID)">
                        <span class="">
                            <i class="text-primary fa fa-check-circle fa-lg" aria-hidden="true"></i>
                        </span>
                    </a>
                    <a class="btn" @click="del(row.item.DEVICE_ID)">
                        <span class="">
                            <i class="text-danger fa fa-trash-o fa-lg" aria-hidden="true"></i>
                        </span>
                    </a>
                </template>
            </b-table>
            <div v-if="devices.length == 0" class="d-flex justify-content-center">
                <strong>{{$t('binding.noDevice')}}</strong>
            </div>
            <div class="position-relative mt-5 pt-3">
            	<div class="category_position">
	                <div class="d-flex">
	                    <!-- scan button -->
	                    <!-- check if the flag/category if 0 = unregistered -->
	                    <div class="mr-2" v-if="tableData.flag == 0">
	                        <!-- call scan function on line 381 -->
	                        <button class="btn btn-primary" @click="scan">
	                            <!-- change the text value of button when click -->
	                            <span>
	                                {{$t('gateway.rescan')}}
	                            </span>
	                        </button>
	                    </div>
	                    <!-- scan button end-->

	                    <!-- gateway status condition -->
	                    <div :class="[{ 'col-sm-12': tableData.flag === 1 }]">
	                        <!-- call getData function on line when category is change -->
	                        <select class="custom-select custom-select-gateway" v-model="tableData.flag" @change="changeCategory()">
	                            <option value="1">{{$t('gateway.gatewayCat.regist')}}</option>
	                            <option value="0">{{$t('gateway.gatewayCat.unregist')}}</option>
	                            <option value="4">{{$t('gateway.gatewayCat.blocked')}}</option>
	                        </select>
	                    </div>
	                    <!-- gateway status condition end-->
	                </div>
	            </div>
            </div>
        </div>
        <!-- table end -->
        <div class="col-md-12 pagination-class">
            <div class="d-flex justify-content-center" :class="devices.length > 24 ? '':'custom-pagination-white'">
                <b-pagination :total-rows="table.totalRows" :per-page="table.perPage" v-model="table.currentPage" />
            </div>
        </div>
        <!-- add modal component -->
        <div v-if="chooseModal == 'addModal'">
            <AddModal :show="activeModal" :modalData="modalData" :cat="category" :currentPage="table.currentPage" @loaddata="getData($event)" @close="closeModal"></AddModal>
        </div>
        <!-- complete scan modal component -->
        <div v-else-if="chooseModal == 'completeScanModal'">
            <CompleteScanModal :show="activeModal" :category="category" :currentPage="table.currentPage" @loaddata="getData($event)" @close="closeModal"></CompleteScanModal>
        </div>
    </div>
	<!-- card-body end -->
</template>
<script>
	import AddModal from '../../Modals/AddModal.vue';
	import CompleteScanModal from '../../Modals/CompleteScanModal.vue';

	export default {
        //declare components
	    components: { AddModal, CompleteScanModal},
	    created() {
            //call getData function
	        this.getData();
            this.$bus.$on('getDeviceData' , data =>{
                this.getData();
            });
            this.$bus.$on('changeDeviceTab' , data =>{
                this.changeCategory();
            });
	    },
	    data() {
	        return {
                table:{
                    fields:[
                        {key:'DEVICE_ID', label:'ID'},
                        {key:'floor.FLOOR_NAME', label:'FLOOR NAME'},
                        {key:'room.ROOM_NAME', label:"ROOM NAME"},
                        {key:'DEVICE_NAME', label:"DEVICE NAME"}
                    ],
                    currentPage:1,
                    totalRows:0,
                    perPage:10,
                },
	            devices: [],
	            tableData: {
	                search: '',
	                flag: '1'
	            },
	            category: '',
                activeModal: '',
                chooseModal: '',
                modalData: '',
                selectedRow: '',
                isUnregister: false,
	        }
	    },

	    methods: {
            //Function Name: getData
            //Function Description: Get device data depending on REG_FLAG
            //Param: pages
	        getData(pages) {
                //get device data depends of REG-FLAG
                //REG_FLAG: 1 = REGISTERED, 0 = UNREGISTERED, 4 = BLOCKED, 9 = DELETED
	            let page_url;
	            if(this.tableData.flag == 1){
	                page_url = 'devices/registered';
                    this.table.perPage = 10;
                    if (this.table.fields.length > 4) {
                        this.table.fields.splice(3,4,{key:'DEVICE_NAME',label:'DEVICE NAME'});
                    }
	            }else if(this.tableData.flag == 0){
	                page_url = 'devices/unregistered';
                    this.table.perPage = 8;
                    if (this.table.fields.length = 4) {
                        this.table.fields.splice(3,1,{key:'gateway',label:'GATEWAY NAME'});
                        this.table.fields.push({key:'DEVICE_NAME',label:'DEVICE NAME'},{key:'devtype',label:'DEVICE TYPE'},{key:'serialno',label:"DEVICE SERIAL NO"},{key:'action',label:'ACTION'});
                    }
	            }else if(this.tableData.flag == 4){
	                page_url = 'devices/blocked';
                    this.table.perPage = 10;
                    if (this.table.fields.length = 4) {
                        this.table.fields.splice(3,1,{key:'gateway',label:'GATEWAY NAME'});
                        this.table.fields.push({key:'DEVICE_NAME',label:'DEVICE NAME'},{key:'devtype',label:'DEVICE TYPE'},{key:'serialno',label:"DEVICE SERIAL NO"},{key:'action',label:'ACTION'});
                    }
	            }
                //ajax call
                //PARAMS: pageLength,include,sortBy,sortVal,search,page
	            axios.get(page_url, {
	                params:{
	                    include: "floor>room>gateway",
	                    search: this.tableData.search,
	                }
	            })
	            .then(response => {
	                let data = response.data;
                    for(var i in data){
                        if (data[i].room == null) {
                            data[i].room = {};
                            data[i].room.ROOM_NAME = '-';
                        }
                        if (data[i].floor == null) {
                            data[i].floor = {};
                            data[i].floor.FLOOR_NAME = '-';
                        }
                        if (data[i].DEVICE_NAME == null) {
                            data[i].DEVICE_NAME = '-';
                        }
                    }
                    this.devices = data;
                    this.table.totalRows = this.devices.length;
	            })
	            .catch(errors => {
	                console.log(errors);
	            });
	        },
            //Funciton Name: openModal
            //Function Description: opens specific modal function
            //Param: modal (modal name), item (device)
            openModal(modal,item){
                //dispaly modal
                this.chooseModal = modal;

                this.activeModal = 'd-block';
                // determine if the modal is gateway or device
                this.category = 'device';
                // set data
                this.modalData = item;
            },
            //Function Name: block
            //Function Description: blocks device
            //Param: id (device_id)
            block(id){
                this.$swal({
                    title:"Block Device",
                    text:"Are you sure?",
                    type:"warning",
                    showCancelButton:true,
                    confirmButtonColor:"#3085d6",
                    cancelButtonColor:"#d33",
                    confirmButtonText:"Yes"
                }).then((result) =>{
                    if(result.value){

                        axios.post('devices/block',{
                            DEVICE_ID: id
                        }).then(response => {
                            if(response.data == 'success'){
                                this.$swal({
                                    title:'Block',
                                    text:"Device has been Block.",
                                    type:'success',
                                    timer:1500,
                                    showConfirmButton:false
                                });
                            }else{
                                this.$swal(
                                    'Error',
                                    "Device can't Block.",
                                    'error'
                                );
                            }
                            this.getData(this.table.currentPage);
                        });
                    }
                });
            },
            //Function Name: unblock
            //Function Description: unblocks device
            //Param: id (device_id)
            unblock(id){
                this.$swal({
                        title:"Unblock Device",
                        text:"Are you sure?",
                        type:"warning",
                        showCancelButton:true,
                        confirmButtonColor:"#3085d6",
                        cancelButtonColor:"#d33",
                        confirmButtonText:"Yes"
                    }).then((result) =>{
                    if(result.value){

                        axios.post('devices/unblock',{
                            DEVICE_ID: id
                        }).then(response => {
                            if(response.data == 'success'){
                                this.$swal({
                                    title:'Unblock',
                                    text:"Device has been Unblock.",
                                    type:'success',
                                    timer:1500,
                                    showConfirmButton:false
                                });
                            }else{
                                this.$swal(
                                    'Error',
                                    "Device can't Unblock.",
                                    'error'
                                );
                            }
                            this.getData(this.table.currentPage);
                        });
                    }
                });
            },
            //Function Name: del
            //Function Description: deletes device
            //Param: id (device_id)
            del(id){
                this.$swal({
                    title:"Delete Device",
                    text:"Are you sure?",
                    type:"warning",
                    showCancelButton:true,
                    confirmButtonColor:"#3085d6",
                    cancelButtonColor:"#d33",
                    confirmButtonText:"Yes"
                }).then((result) =>{
                    if(result.value){
                        axios.post('devices/delete',{
                            KEY: 'device',
                            DEVICE_ID: id
                        }).then(response => {
                            if(response.data == 'success'){
                                this.$swal({
                                    title:'Deleted',
                                    text:"Device has been deleted.",
                                    type:'success',
                                    timer:1500,
                                    showConfirmButton:false
                                });
                            }else{
                                this.$swal(
                                    'Error',
                                    "Device can't delete.",
                                    'error'
                                );
                            }
                            this.getData(this.table.currentPage);
                        });
                    }
                });
            },
            //Function Name: scan
	        //Function Description: opens modal scan function
	        scan(){
                this.openModal('completeScanModal','device');
	        },
            //Function Name: closeModal
	        //Function Description: close modal function
	        closeModal(){
                this.category = this.modalData = this.activeModal = this.chooseModal = '';
	        },
            //Function Name: showGatewayDetails
            //Function Description: shows device details
            //Param: details (device)
            showGatewayDetails(details){
                this.$set(details, "category", "device");
                this.$bus.$emit('selectedDeviceData', details);
                this.selectedRow = details.DEVICE_ID;
            },
            //Function Name: changeCategory
            //Function Description: change table size according to category
            //Param: table.flag
            changeCategory(){
                var details = '';
                var column = this.columns;
                var style = 'table-cell';

                if(this.tableData.flag == 0 || this.tableData.flag == 4){
                    this.isUnregister = true;
                    style = 'table-cell';
                }else{
                    this.isUnregister = false;
                    style = 'none';
                }
                for(var i in column){
                    if(i == 3 || i == 5 || i == 6 || i == 7){
                        column[i].display = style;
                    }
                }
                this.$emit('change-size', this.isUnregister);
                this.$bus.$emit('selectedDeviceData', details);
                this.selectedRow = '';
                this.getData();
            },
	    },
        computed:{
            //changes table height according to screen height and table length
            tableHeight(){
                var tbHeight = '';
                if(this.$screenHeight > 800){
                    if(!this.$isMobile.isMobile){
                        tbHeight = 'h-75vh';
                    }else{
                        if(this.table.totalRows >= 10){
                            tbHeight = 'h-100';
                        }else{
                            tbHeight = 'h-75vh';
                        }
                    }
                }else{
                    tbHeight = 'h-100';
                }
                return tbHeight;
            }
        },
	};
</script>