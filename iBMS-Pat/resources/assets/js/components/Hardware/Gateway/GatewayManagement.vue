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
                      ref="gatewayTable"
                      select-mode="single"
                      :items="gateways"
                      :fields="table.fields"
                      :current-page="table.currentPage"
                      :per-page="table.perPage"
                      :filter="tableData.search"
                      @row-clicked="showGatewayDetails">
                <template v-if="tableData.flag == 0 || tableData.flag == 4" slot="ip" slot-scope="row">
                    {{row.item.GATEWAY_IP}}
                </template>
                <template v-if="tableData.flag == 0 || tableData.flag == 4" slot="serial_no" slot-scope="row">
                    {{row.item.GATEWAY_SERIAL_NO}}
                </template>
                <template v-if="tableData.flag == 0 || tableData.flag == 4" slot="action" slot-scope="row">
                    <a v-if="tableData.flag == 0" class="btn" @click="openModal('addModal',row.item)">
                        <span class="">
                            <i class="text-primary fa fa-plus-circle fa-lg" aria-hidden="true"></i>
                        </span>
                    </a>
                    <a v-if="tableData.flag == 0" class="btn" @click="block(row.item.GATEWAY_ID)">
                        <span class="">
                            <i class="text-danger fa fa-ban fa-lg" aria-hidden="true"></i>
                        </span>
                    </a>
                    <a v-if="tableData.flag == 4" class="btn" @click="unblock(row.item.GATEWAY_ID)">
                        <span class="">
                            <i class="text-primary fa fa-check-circle fa-lg" aria-hidden="true"></i>
                        </span>
                    </a>
                    <a class="btn" @click="del(row.item.GATEWAY_ID)">
                        <span class="">
                            <i class="text-danger fa fa-trash-o fa-lg" aria-hidden="true"></i>
                        </span>
                    </a>
                </template>
            </b-table>
            <div v-if="gateways.length == 0" class="d-flex justify-content-center">
                <strong>{{$t('binding.noDevice')}}</strong>
            </div>
            <div class="category_position">
                <div class="d-flex">
                    <!-- scan button -->
                    <!-- check if the flag/category if 0 = unregistered -->
                    <div class="mr-2" v-if="tableData.flag == 0">
                        <!-- call scan function on line 381 -->
                        <button class="btn btn-primary" @click="scan">
                            <!-- display loading animation -->
                            <span class="pull-left pr-1" v-if="scan_loader">
                                <i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
                            </span>
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
                        <select class="custom-select" v-model="tableData.flag" @change="changeCategory()">
                            <option value="1">{{$t('gateway.gatewayCat.regist')}}</option>
                            <option value="0">{{$t('gateway.gatewayCat.unregist')}}</option>
                            <option value="4">{{$t('gateway.gatewayCat.blocked')}}</option>
                        </select>
                    </div>
                    <!-- gateway status condition end-->
                </div>
            </div>
        </div>
        <!-- table end -->

        <!-- pagination component -->
        <!-- call getData function when prev, next and number is click -->
        <div class="col-md-12 pagination-class">
            <div class="d-flex justify-content-center" v-if="gateways.length > 10" :class="gateways.length > 24 ? '':'custom-pagination-white'">
                <b-pagination :total-rows="table.totalRows" :per-page="table.perPage" v-model="table.currentPage" />
            </div>
        </div>
        <!-- add modal component -->
        <div v-if="chooseModal == 'addModal'">
            <AddModal :show="activeModal" :modalData="modalData" :cat="category" :currentPage="table.currentPage" @loaddata="getData($event)" @close="closeModal"></AddModal>
        </div>
        <div v-else-if="chooseModal == 'completeScanModal'">
            <CompleteScanModal :show="activeModal" :currentPage="table.currentPage" @loaddata="getData($event)" @close="closeModal"></CompleteScanModal>
        </div>
    </div>
    <!-- card-body-end -->

</template>
<script>
    // imported components
    import AddModal from '../../Modals/AddModal.vue';
    import CompleteScanModal from '../../Modals/CompleteScanModal.vue';

    export default {
        //declare components
        components: { AddModal, CompleteScanModal},
        props: [],
        created() {
            //call getData function
            this.getData();
            this.$bus.$on('getData' , data =>{
                this.getData();
            });
            this.$bus.$on('changeTab' , data =>{
                this.changeCategory();
            });
        },
        data() {
            return {
                // where data variables is declare and initialize
                table:{
                    fields:[
                        {key:'GATEWAY_ID', label:'ID'},
                        {key:'floor.FLOOR_NAME', label:'FLOOR NAME'},
                        {key:'room.ROOM_NAME', label:"ROOM NAME"},
                        {key:'GATEWAY_NAME', label:"GATEWAY NAME"},
                    ],
                    currentPage:1,
                    totalRows:0,
                    perPage:10,
                },
                tableData: {
                    search: '',
                    flag: 1
                },
                gateways: [],
                category: '',
                activeModal: '',
                chooseModal: '',
                modalData: '',
                scan_loader: false,
                progressbar: false,
                progress_value: 0,
                selectedRow: '',
                isUnregister: false,
            }
        },

        methods: {
            //Function Name: getData
            //Function Description: Get gateway data depending on REG_FLAG
            //Param: pages
            getData(pages) {
                //get gateway data depends of REG-FLAG
                //REG_FLAG: 1 = REGISTERED, 0 = UNREGISTERED, 4 = BLOCKED, 9 = DELETED
                let page_url;
                if(this.tableData.flag == 1){
                    page_url = 'gateways/registered?' || 'gateways/registered';
                    if (this.table.fields.length > 4) {
                        this.table.fields.splice(4, 3);
                    }
                    // this.$refs.gatewayTable.refresh();
                }else if(this.tableData.flag == 0){
                    page_url = 'gateways/unregistered?' || 'gateways/unregistered';
                    if (this.table.fields.length = 4) {
                        this.table.fields.push({key:'ip', label:'GATEWAY IP'},
                        {key:'serial_no', label:'GATEWAY SERIAL NO'},
                        {key:'action', label:'ACTION'});
                    }
                }else if(this.tableData.flag == 4){
                    page_url = 'gateways/blocked?' || 'gateways/blocked';
                    if (this.table.fields.length = 4) {
                        this.table.fields.push({key:'ip', label:'GATEWAY IP'},
                        {key:'serial_no', label:'GATEWAY SERIAL NO'},
                        {key:'action', label:'ACTION'});
                    }
                }
                //ajax call
                //PARAM: pageLength, include, sortBy, sortval, search, page
                axios.get(page_url, {
                    params:{
                        manufacturerID: 1,
                        include: "floor>room",
                        search: this.tableData.search
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
                        if (data[i].GATEWAY_NAME == null) {
                            data[i].GATEWAY_NAME = '-';
                        }
                    }
                    this.gateways = data;
                    this.table.totalRows = this.gateways.length;
                })
                .catch(errors => {
                    console.log (errors);
                });
                this.reload = false;
            },


            //Funciton Name: openModal
            //Function Description: opens specific modal function
            //Param: modal (modal name), item (gateway)
            openModal(modal,item){
                //dispaly modal
                this.chooseModal = modal;

                this.activeModal = 'd-block';
                // determine if the modal is gateway or device
                this.category = 'gateway';
                // set data
                this.modalData = item;
            },
            //Function Name: block
            //Function Description: blocks device
            //Param: id (gateway_id)
            block(id){
                this.$swal({
                    title:"Block Gateway",
                    text:"Are you sure?",
                    type:"warning",
                    showCancelButton:true,
                    confirmButtonColor:"#3085d6",
                    cancelButtonColor:"#d33",
                    confirmButtonText:"Yes"
                }).then((result) =>{
                    if(result.value){

                        axios.post('gateways/block',{
                            GATEWAY_ID: id
                        }).then(response => {
                            if(response.data == 'success'){
                                this.$swal({
                                    title:'Block',
                                    text:"Gateway has been Block.",
                                    type:'success',
                                    timer:1500,
                                    showConfirmButton:false
                                });
                            }else{
                                this.$swal(
                                    'Error',
                                    "Gateway can't Block.",
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
            //Param: id (gateway_id)
            unblock(id){
                this.$swal({
                        title:"Unblock Gateway",
                        text:"Are you sure?",
                        type:"warning",
                        showCancelButton:true,
                        confirmButtonColor:"#3085d6",
                        cancelButtonColor:"#d33",
                        confirmButtonText:"Yes"
                    }).then((result) =>{
                    if(result.value){

                        axios.post('gateways/unblock',{
                            GATEWAY_ID: id
                        }).then(response => {
                            if(response.data == 'success'){
                                this.$swal({
                                    title:'Unblock',
                                    text:"Gateway has been Unblock.",
                                    type:'success',
                                    timer:1500,
                                    showConfirmButton:false
                                });
                            }else{
                                this.$swal(
                                    'Error',
                                    "Gateway can't Unblock.",
                                    'error'
                                );
                            }
                            this.getData(this.table.currentPage);
                        });
                    }
                });
            },
            //Function Name: del
            //Function Description: deletes gateway
            //Param: id (gateway_id)
            del(id){
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
                            KEY: 'gateway',
                            GATEWAY_ID: id
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
                                        KEY: 'gateway',
                                        GATEWAY_ID: id
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
                            this.getData(this.table.currentPage);
                        });
                    }
                });
            },
            //Function Name: scan
            //Function Description: opens modal scan function
            scan(){
                this.openModal('completeScanModal','');
            },
            //Function Name: closeModal
            //Function Description: close modal function
            closeModal(){
                //clear the data
                this.category = this.modalData = this.activeModal = this.chooseModal = '';
            },
            //Function Name: showGatewayDetails
            //Function Description: shows gateway details
            //Param: details (gateway)
            showGatewayDetails(details){
                this.$set(details, "category", "systemGateway");
                this.$bus.$emit('selectedData', details);
                this.selectedRow = details.GATEWAY_ID;
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
                    if(i == 4 || i == 5 || i == 6){
                        column[i].display = style;
                    }
                }
                this.$emit('change-size', this.isUnregister);
                this.$bus.$emit('selectedData', details);
                this.selectedRow = '';
                this.getData();
            },
        },
        computed:{
            //table height change according to screen size
            tableHeight(){
                var tbHeight = '';
                if(!this.$isMobile.isMobile){
                    tbHeight = 'h-75vh';
                }else{
                    if(this.table.totalRows >= 10){
                        tbHeight = 'h-100';
                    }else{
                        tbHeight = 'h-75vh';
                    }
                }
                return tbHeight;
            }
        },
    };
</script>