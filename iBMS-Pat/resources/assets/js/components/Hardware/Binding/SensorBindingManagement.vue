<template>
    <div class="card-body" >
        <div class="row">
            <div :class="fullWidth ? 'col-sm-6' : 'col-sm-12'">
                <div class="position-relative vh-40" v-if="bindLists.length === 0">
                    <div class="display-4 p-3 text-center" >
                        {{$t('binding.noBinding')}}
                    </div>
                </div>
                <div v-else class="position-relative" :class="totalRows ? '' : 'vh-40'">
                    <div role="tablist">
                        <div v-for="bindList,index in limitedBinding">
                          <div class="p-1" role="tab">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="#" v-b-toggle="'accordion'+index" class="text-dark">
                                        <h3 class="m-0">{{bindList.DEVICE_NAME}}</h3>
                                    </a>
                                    <div class="px-3">
                                        <a class="custom-pointer" @click="deleteBinding(bindList)">
                                            <span class="">
                                                <i class="text-danger fa fa-trash-o fa-lg" aria-hidden="true"></i>
                                            </span>
                                        </a>
                                        <a v-if="bindList.BINDING_STATUS_GROUP == 1" class="custom-pointer" @click="disableAllBindings(bindList)">
                                            <span class="">
                                                <i class="text-success fa fa-check-circle-o fa-lg" aria-hidden="true"></i>
                                            </span>
                                        </a>
                                        <a v-else class="custom-pointer" @click="enableAllBindings(bindList)">
                                            <span class="">
                                                <i class="text-warning fa fa-times-circle-o fa-lg" aria-hidden="true"></i>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                                <div class="divider-line-2">{{bindList.floor.FLOOR_NAME}} / {{bindList.room.ROOM_NAME}} - {{bindList.gateway.GATEWAY_NAME}}</div>
                          </div>
                          <b-collapse :id="'accordion'+index" accordion="my-accordion" role="tabpanel" class="target-devices">
                                <div class="d-flex justify-content-between align-items-center px-1" v-for="binding,key in bindList.bindings" @click="selectBindedSensor(binding)" :class="{'bg-white text-dark': (binding.BINDING_ID == selectedBinding)}">
                                    <div>
                                        <span class="w-25">{{binding.binding_list.SOURCE_DEVICE_CONDITION}}</span>
                                        <i class="fa fa-arrow-circle-o-right"></i>
                                        <span class="list-group-item-text">{{binding.target_device.DEVICE_NAME}}</span>
                                    </div>
                                    <!-- call delmodal function on line 178 -->
                                    <div class="d-flex">
                                        {{binding.binding_list.TARGET_DEVICE_CONDITION}}
                                        <div class="px-2">
                                            <a class="custom-pointer" @click="deleteSpecificBinding(binding)">
                                                <span class="">
                                                    <i class="text-danger fa fa-trash-o fa-lg" aria-hidden="true"></i>
                                                </span>
                                            </a>
                                            <a v-if="binding.BINDING_STATUS == 0" class="custom-pointer" @click="disableBinding(binding)">
                                                <span class="">
                                                    <i class="text-warning fa fa-times-circle-o fa-lg" aria-hidden="true"></i>
                                                </span>
                                            </a>
                                            <a v-else class="custom-pointer" @click="enableBinding(binding)">
                                                <span class="">
                                                    <i class="text-success fa fa-check-circle-o fa-lg" aria-hidden="true"></i>
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                          </b-collapse>
                        </div>
                    </div>
                    <div v-if="pagination.total > 5" :class="totalRows ? '' : 'custom-pagination-position'" class="custom-pagination-orange d-flex justify-content-center pt-3">
                        <b-pagination :total-rows="pagination.total" :per-page="drawData.length" v-model="pagination.currentPage"></b-pagination>
                    </div>
                </div>
            </div>
            <div class="col-sm-6" v-if="showSelectedBinding">
                <div class="border border-dark">
                    <div class="m-2">
                        <img :src="sourceImage" class="w-100">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        props:{
            //taken from parent vue
            selectedRoom:'',
            selectedBindingListFLoor:''
        },
        created() {
            //call getBinding function
            this.getBinding();
            this.$bus.$on('getSensorBindingData' , data =>{
                this.getBinding();
            });
            this.$bus.$on('changeSensorBindingTab' , data =>{
                this.changeSize();
            });
        },
        data() {
            return {
                // where data variables is declare and initialize
                bindLists:[],
                addActive: '',
                delActive: '',
                deleteActive: '',
                enableActive: '',
                disableActive: '',
                drawData: {
                    length: 10,
                    search: '',
                },
                pagination: {
                    currentPage:1,
                    total: 0,
                },
                createBinding: false,
                selectedBinding: '',
                binded: {
                    SOURCE_DEVICE_TYPE:'',
                    TARGET_DEVICE_TYPE:'',
                },
                isBinded: false,
                required: true,
            }
        },

        methods: {
            //Function Name: getBinding
            //Funciton Description: Get sensor to sensor binding
            //Param: pages(pagination), filterByFloor (filter by floor or room)
            getBinding(pages) {
                //get binded devices data
                let page_url =  'devices/with-bindings';

                //Filter by Floor ID -- Harvey
                let filterByFloor="";
                if(this.selectedBindingListFLoor['FLOOR_ID'] != null){
                    filterByFloor ="?filter=FLOOR_ID:" + this.selectedBindingListFLoor['FLOOR_ID'];

                    //To be filter by room.
                    if(this.selectedRoom.length != 0 ){
                        filterByFloor += "|ROOM_ID:" + this.selectedRoom['ROOM_ID'];
                    }
                }
                //ajax call
                //PARAMS: pageLength,include,search,page
                axios.get(page_url + filterByFloor, {
                    params:{
                        BINDING_CATEGORY: 1,
                        include: "floor>room>gateway",
                        search: this.drawData.search,
                    }
                })
                .then(response => {
                    let data = response.data;
                    this.bindLists = data;
                    this.pagination.total = data.length;


                    //Input an overall status for each group bindings
                    for(var i in data){
                        let statusCounter = 0;
                        for(var j in data[i]['bindings']){
                            //console.log(data.data[i]['bindings'][j]['BINDING_STATUS']);
                            if(data[i]['bindings'][j]['BINDING_STATUS'] == 1){
                                statusCounter ++;
                            }
                        }
                        if(statusCounter > 0 || statusCounter == data[i]['bindings'].length){
                            data[i]['BINDING_STATUS_GROUP'] = 1;
                        }else{
                            data[i]['BINDING_STATUS_GROUP'] = 0;
                        }
                    }
                    //------------------------------------------------
                })
                .catch(errors => {
                    console.log(errors);
                });
            },
            //Function name: deleteBinding
            //Function Description: Delete binding
            //Param: data (device)
            deleteBinding(data){
                this.$swal({
                    title:"Delete this Binding?",
                    text:"Are you sure?",
                    type:"warning",
                    showCancelButton:true,
                    confirmButtonColor:"#3085d6",
                    cancelButtonColor:"#d33",
                    confirmButtonText:"Yes"
                }).then((result) =>{
                    if(result.value){
                        axios.post('bindings/delete-binding',{
                            DEVICE_ID: data.DEVICE_ID
                        }).then(response => {
                            if(response.data == 'success'){
                                this.$swal({
                                    title:'Deleted',
                                    text: "Binding has been deleted.",
                                    type:'success',
                                    timer:1500,
                                    showConfirmButton:false
                                });
                            }else{
                                this.$swal(
                                    'Error',
                                    "Binding can't delete.",
                                    'error'
                                );
                            }
                            this.reloadData();
                        });
                    }
                });
            },
            //Function Name: deleteSpecificBinding
            //Function Description: deletes a specific binding with device
            //Param: data (binding)
            deleteSpecificBinding(data){
                this.$swal({
                    title:"Delete this Device Binding?",
                    text:"Are you sure?",
                    type:"warning",
                    showCancelButton:true,
                    confirmButtonColor:"#3085d6",
                    cancelButtonColor:"#d33",
                    confirmButtonText:"Yes"
                }).then((result) =>{
                    if(result.value){
                        axios.post('bindings/delete',{
                            BINDING_ID: data.BINDING_ID
                        }).then(response => {
                            if(response.data == 'success'){
                                this.$swal({
                                    title:'Deleted',
                                    text: "Device Binding has been deleted.",
                                    type:'success',
                                    timer:1500,
                                    showConfirmButton:false
                                });
                            }else{
                                this.$swal(
                                    'Error',
                                    "Device Binding can't delete.",
                                    'error'
                                );
                            }
                            this.reloadData();
                        });
                    }
                });
            },
            //Funciton Name: enableAllBindings
            //Function Description: enables group binding status
            //Param: data (device)
            enableAllBindings(data){
                this.$swal({
                    title:"Enable this Binding Group?",
                    text:"Are you sure?",
                    type:"warning",
                    showCancelButton:true,
                    confirmButtonColor:"#3085d6",
                    cancelButtonColor:"#d33",
                    confirmButtonText:"Yes"
                }).then((result) =>{
                    if(result.value){
                        axios.post('bindings/enableAll',{
                            BINDING: data.DEVICE_ID
                        }).then(response => {
                            if(response.data == 'success'){
                                this.$swal({
                                    title:'Enabled',
                                    text: "Group has been Enabled.",
                                    type:'success',
                                    timer:1500,
                                    showConfirmButton:false
                                });
                            }else{
                                this.$swal(
                                    'Error',
                                    "Device Binding can't Enabled.",
                                    'error'
                                );
                            }
                            this.$bus.$emit('getSensorBindingData');
                        });
                    }
                });
            },
            //Function Name: disableAllBindings
            //Function Description: disables group binding status
            //Param: data (device)
            disableAllBindings(data){
                this.$swal({
                    title:"Disable this Binding Group?",
                    text:"Are you sure?",
                    type:"warning",
                    showCancelButton:true,
                    confirmButtonColor:"#3085d6",
                    cancelButtonColor:"#d33",
                    confirmButtonText:"Yes"
                }).then((result) =>{
                    if(result.value){
                        axios.post('bindings/disableAll',{
                            BINDING: data.DEVICE_ID
                        }).then(response => {
                            if(response.data == 'success'){
                                this.$swal({
                                    title:'Disabled',
                                    text: "Group has been Disabled.",
                                    type:'success',
                                    timer:1500,
                                    showConfirmButton:false
                                });
                            }else{
                                this.$swal(
                                    'Error',
                                    "Device Binding can't Disable.",
                                    'error'
                                );
                            }
                            this.$bus.$emit('getSensorBindingData');
                        });
                    }
                });
            },
            //Funciton Name: enableBinding
            //Function Description: enables binding
            //Param: data (binding)
            enableBinding(data){
                this.$swal({
                    title:"Disable this Binding?",
                    text:"Are you sure?",
                    type:"warning",
                    showCancelButton:true,
                    confirmButtonColor:"#3085d6",
                    cancelButtonColor:"#d33",
                    confirmButtonText:"Yes"
                }).then((result) =>{
                    if(result.value){
                        axios.post('bindings/disable',{
                            BINDING_ID: data.BINDING_ID
                        }).then(response => {
                            if(response.data == 'success'){
                                this.$swal({
                                    title:'Disabled',
                                    text: "Device has been Disabled.",
                                    type:'success',
                                    timer:1500,
                                    showConfirmButton:false
                                });
                            }else{
                                this.$swal(
                                    'Error',
                                    "Device Binding can't Disable.",
                                    'error'
                                );
                            }
                            this.$bus.$emit('getSensorBindingData');
                        });
                    }
                });
            },
            //Funciton Name: disableBinding
            //Function Description: disables binding
            //Param: data (binding)
            disableBinding(data){
                this.$swal({
                    title:"Enable this Binding?",
                    text:"Are you sure?",
                    type:"warning",
                    showCancelButton:true,
                    confirmButtonColor:"#3085d6",
                    cancelButtonColor:"#d33",
                    confirmButtonText:"Yes"
                }).then((result) =>{
                    if(result.value){
                        axios.post('bindings/enable',{
                            BINDING_ID: data.BINDING_ID
                        }).then(response => {
                            if(response.data == 'success'){
                                this.$swal({
                                    title:'Enabled',
                                    text: "Device has been Enabled.",
                                    type:'success',
                                    timer:1500,
                                    showConfirmButton:false
                                });
                            }else{
                                this.$swal(
                                    'Error',
                                    "Device Binding can't Enabled.",
                                    'error'
                                );
                            }
                            this.$bus.$emit('getSensorBindingData');
                        });
                    }
                });
            },
            //Function Name: changeSize
            //Function Description: changes table size
            changeSize(){
                if(this.showSelectedBinding){
                    this.isBinded = true;
                }else{
                    this.isBinded = false;
                }
                this.$emit('change-size', this.isBinded);
            },
            //Function name: reloadData
            //Function Description: resets page data
            reloadData(){
                this.$bus.$emit('getSensorBindingData');
                this.selectedBinding = '';
                this.isBinded = false;
                this.$emit('change-size', this.isBinded);
            },
            //Function Name: selectBindedSensor
            //Function Description: selects binded sensor
            //Param: data (binding)
            selectBindedSensor(data){
                if(data != ''){
                    this.isBinded = true;
                }else{
                    this.isBinded = false;
                }
                this.selectedBinding = data.BINDING_ID;
                this.binded.SOURCE_DEVICE_TYPE = data.binding_list.SOURCE_DEVICE_TYPE;
                this.binded.TARGET_DEVICE_TYPE = data.binding_list.TARGET_DEVICE_TYPE;
                this.$emit('change-size', this.isBinded);
            },
        },
        computed:{
            //for binding list pagination
            limitedBinding(){
                let from = this.drawData.length * this.pagination.currentPage - 10;
                let to = this.drawData.length * this.pagination.currentPage;
                return this.bindLists.slice(from,to) ;
            },
            fullWidth(){
                if(this.bindLists.length === 0){
                    return false;
                }else{
                    if(this.selectedBinding == ''){
                        return false;
                    }else{
                        return true;
                    }
                }
            },
            showSelectedBinding(){
                if(this.selectedBinding != ''){
                    return true;
                }else{
                    return false;
                }
            },
            totalRows(){
                var total;
                var bindingList = this.bindLists;
                var bindingCount = 0;
                for(var x in bindingList){
                    var bindingLength = bindingList[x].bindings;
                    for(var i in bindingLength){
                        bindingCount++;
                    }
                }
                if(this.pagination.total >= 5){
                    total = true;
                }else{
                    if(bindingCount >= 16){
                        total = true;
                    }else{
                        total = false;
                    }
                }
                return total;
            },
            sourceImage(){
                var image_source = '';
                    if(this.binded.SOURCE_DEVICE_TYPE == 'smoke_detector'){
                        if(this.binded.TARGET_DEVICE_TYPE == 'wall_switch_3'){
                            image_source = 'img/bind/smoke detector/smoke-detector-wall-switch.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'curtain_1'){
                            image_source = 'img/bind/smoke detector/smoke detector-curtain.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'temp_light'){
                            image_source = 'img/bind/smoke detector/smoke-detector-temperature-light.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'door_lock'){
                            image_source = 'img/bind/smoke detector/smoke-detector-door-lock.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'dust_detector'){
                            image_source = 'img/bind/smoke detector/smoke-detector-dust-sensor.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'embedded_switch_2'){
                            image_source = 'img/bind/smoke detector/smoke-detector-embedded-switch.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'gas_detector'){
                            image_source = 'img/bind/smoke detector/smoke-detector-gas-detector.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'gas_valve'){
                            image_source = 'img/bind/smoke detector/smoke-detector-gas-manipulator.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'ir_remote'){
                            image_source = 'img/bind/smoke detector/smoke-detector-ir.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'light_detector'){
                            image_source = 'img/bind/smoke detector/smoke-detector-light-detector.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'motion_detector'){
                            image_source = 'img/bind/smoke detector/smoke-detector-motion.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == ''){
                            image_source = 'img/bind/smoke detector/smoke-detector-normal-light.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'panic_button'){
                            image_source = 'img/bind/smoke detector/smoke-detector-panic-button.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'temp_hum'){
                            image_source = 'img/bind/smoke detector/smoke-detector-temp.-humid.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'h2o_detector'){
                            image_source = 'img/bind/smoke detector/smoke-detector-water-leakage.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'water_valve'){
                            image_source = 'img/bind/smoke detector/smoke-detector-water-valve.gif';
                        }else{

                        }

                    }else if(this.binded.SOURCE_DEVICE_TYPE == 'gas_detector'){
                        if(this.binded.TARGET_DEVICE_TYPE == 'wall_switch_3'){
                            image_source = 'img/bind/gas detector/gas-detector-wall-switch.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'curtain_1'){
                            image_source = 'img/bind/gas detector/gas-detector-curtain.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'temp_light'){
                            image_source = 'img/bind/gas detector/gas-detector-temperature-light.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'door_lock'){
                            image_source = 'img/bind/gas detector/gas-detector-door-lock.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'dust_detector'){
                            image_source = 'img/bind/gas detector/gas-detector-dust-sensor.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'embedded_switch_2'){
                            image_source = 'img/bind/gas detector/gas-detector-embedded-switch.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'gas_valve'){
                            image_source = 'img/bind/gas detector/gas-detector-gas-manipulator.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'ir_remote'){
                            image_source = 'img/bind/gas detector/gas-detector-ir.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'light_detector'){
                            image_source = 'img/bind/gas detector/gas-detector-light-detector.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'motion_detector'){
                            image_source = 'img/bind/gas detector/gas-detector-motion.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == ''){
                            image_source = 'img/bind/gas detector/gas-detector-normal-light.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'panic_button'){
                            image_source = 'img/bind/gas detector/gas-detector-panic-button.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'smoke_detector'){
                            image_source = 'img/bind/gas detector/gas-detector-smoke-detector.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'temp_hum'){
                            image_source = 'img/bind/gas detector/gas-detector-temp.-humid.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'h2o_detector'){
                            image_source = 'img/bind/gas detector/gas-detector-water-leakage.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'water_valve'){
                            image_source = 'img/bind/gas detector/gas-detector-water-valve.gif';
                        }else{

                        }

                    }else if(this.binded.SOURCE_DEVICE_TYPE == 'co2_detector'){
                        if(this.binded.TARGET_DEVICE_TYPE == 'wall_switch_3'){
                            image_source = 'img/bind/C02/c02---wall-switch.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'curtain_1'){
                            image_source = 'img/bind/C02/c02---curtain.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'temp_light'){
                            image_source = 'img/bind/C02/c02---temperature-light.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'door_lock'){
                            image_source = 'img/bind/C02/c02---door-lock.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'dust_detector'){
                            image_source = 'img/bind/C02/c02---dust-sensor.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'embedded_switch_2'){
                            image_source = 'img/bind/C02/c02---embedded.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'gas_detector'){
                            image_source = 'img/bind/C02/c02---gas-detector.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'gas_valve'){
                            image_source = 'img/bind/C02/c02---gas-manipulator.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'ir_remote'){
                            image_source = 'img/bind/C02/c02---IR.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'light_detector'){
                            image_source = 'img/bind/C02/c02---light-detector.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'motion_detector'){
                            image_source = 'img/bind/C02/c02---motion-detector.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == ''){
                            image_source = 'img/bind/C02/c02---normal-light.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'panic_button'){
                            image_source = 'img/bind/C02/c02---panic-button.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'smoke_detector'){
                            image_source = 'img/bind/C02/c02---smoke-detector.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'temp_hum'){
                            image_source = 'img/bind/C02/c02---temp.-humid.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'h2o_detector'){
                            image_source = 'img/bind/C02/c02---water-leakage.gif';
                        }else if(this.binded.TARGET_DEVICE_TYPE == 'water_valve'){
                            image_source = 'img/bind/C02/c02---water-valve.gif';
                        }else{

                        }

                    }else{

                        image_source = 'img/binding-animation.gif';
                        // image_source = 'img/image_not_found.png';
                    }
                return image_source;
            }
        },
        watch:{
            selectedBindingListFLoor:function(){
                this.selectedBinding=[];
                this.getBinding();
            },
            //for map view select
            selectedRoom: function(){
                this.getBinding();
            },
        },
        mounted() {
            Echo.channel('test').listen('.testBindingEvent', (e) => {
                console.log(e.data);
                this.getBinding();
            })
        }
    };
</script>