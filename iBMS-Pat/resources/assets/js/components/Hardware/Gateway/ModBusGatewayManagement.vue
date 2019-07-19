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
            <!-- datatable component -->
            <b-table  :items="gateways"
                      :fields="table.fields"
                      :current-page="table.currentPage"
                      :per-page="table.perPage"
                      :filter="tableData.search"
                      @row-clicked="showGatewayDetails">
            </b-table>
            <div v-if="gateways.length == 0" class="d-flex justify-content-center">
                <strong>{{$t('binding.noDevice')}}</strong>
            </div>
            <!-- data table component end -->
            <div class="category_position">
                <div class="d-flex">
                    <div class="mr-2" v-if="tableData.flag == 1">
                        <!-- call scan function on line 381 -->
                        <button class="btn btn-primary" @click="openModal('addModal','')">
                            {{$t('add')}}
                        </button>
                    </div>

                    <!-- gateway status condition -->
                    <div :class="[{ 'col-sm-12': tableData.flag === 9 }]">
                        <!-- call getData function on line when category is change -->
                        <select class="custom-select custom-select-gateway" v-model="tableData.flag" @change="changeCategory()">
                            <option value="1">{{$t('gateway.gatewayCat.regist')}}</option>
                            <option value="9">{{$t('gateway.gatewayCat.deleted')}}</option>
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
            <div class="d-flex justify-content-center" :class="gateways.length > 24 ? '':'custom-pagination-white'">
                <b-pagination :total-rows="table.totalRows" :per-page="table.perPage" v-model="table.currentPage" />
            </div>
        </div>

        <!-- add modal component -->
        <div v-if="chooseModal == 'addModal'">
            <AddModal :show="activeModal" :cat="category" :currentPage="table.currentPage" @loaddata="getData($event)" @close="closeModal"></AddModal>
        </div>
    </div>
    <!-- card-body-end -->

</template>
<script>
    // imported components
    import AddModal from '../../Modals/AddModBusModal.vue';

    export default {
        //declare components
        components: { AddModal},
        created() {
            //call getData function
            this.getData();
            this.$bus.$on('getData' , data =>{
                this.getData();
            });
            this.$bus.$on('changeTab' , data =>{
                this.selectedRow = '';
            });
        },
        data() {
            return {
                // where data variables is declare and initialize
                table:{
                    fields:[
                        {key:'GATEWAY_ID', label:'ID'},
                        {key:'floor.FLOOR_NAME', label:'FLOOR NAME'},
                        {key:'GATEWAY_NAME', label:"ROOM NAME"},
                        {key:'GATEWAY_IP', label:"DEVICE NAME"}
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
                chooseModal: '',
                selectedRow: '',
            }
        },

        methods: {
            //Function Name: getData
            //Function Description: Get gateway data depending on REG_FLAG
            //Param: pages, tableData.flag
            getData(pages) {
                //get gateway data depends of REG-FLAG
                let page_url;
                if(this.tableData.flag == 1){
                    page_url = 'gateways/registered?' || 'gateways/registered';
                }else if(this.tableData.flag == 9){
                    page_url = 'gateways/deleted?' || 'gateways/deleted';
                }
                //ajax call
                //PARAM: pageLength, include, sortBy, sortval, search, page
                axios.get(page_url, {
                    params:{
                        manufacturerID: 2,
                        pageLength: this.table.perPage,
                        include: "floor>room",
                        search: this.tableData.search
                    }
                })
                .then(response => {
                    let data = response.data;
                    this.gateways = data.data;
                    this.table.totalRows = this.gateways.length;
                })
                .catch(errors => {
                    console.log(errors);
                });
            },
            //Function Name: showGatewayDetails
            //Function Description: shows modbus gateway details
            //Param: details (gateway)
            showGatewayDetails(details){
                this.$set(details, "category", "modbusGateway");
                this.$bus.$emit('selectedData', details);
                this.selectedRow = details.GATEWAY_ID;
            },
            //Function Name: changeCategory
            //Function Description: changes category displayed
            changeCategory(){
                var details = '';
                this.$bus.$emit('selectedData', details);
                this.selectedRow = '';
                this.getData();
            },
            //Funciton Name: openModal
            //Function Description: opens specific modal function
            //Param: modal (modal name), item (device)
            openModal(modal,item){
                //dispaly modal
                this.chooseModal = modal;

                this.activeModal = 'd-block';
                // determine if the modal is gateway or device
                this.category = 'modBusGateway';
                // set data
                this.modalData = item;
            },
            //Function Name: closeModal
            //Function Description: close modal function
            closeModal(){
                //clear the data
                this.category = this.modalData = this.activeModal = '';
            },
        },
        computed:{
            //change table height according to data length
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