<template>
    <div>
        <div class="row">
            <div class="card-body col-sm-12 text-dark">
                <div class="form-group">
                    <div class="d-flex justify-content-between">
                        <h4 class="text-dark">{{$t('binding.targetDevice')}}</h4>
                        <!-- search -->
                        <div class="input-group col-sm-3 pl-0 h-75 float-right">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">
                                    <i class="fa fa-search" aria-hidden="true"></i>
                                </span>
                            </div>
                            <!-- call bindingList function on line 374 -->
                            <!-- enable /disable the room select using selectAllDisabled function on line 534 -->
                            <input type="text" class="form-control" placeholder="Search" v-model="tableData.search" @input="bindingList()" :disabled="selectAllDisabled">
                        </div>
                    </div>
                    <!-- search end-->
                    <div class="divider-line-white"></div>
                    <!-- select all time -->
                    <div class="d-flex justify-content-between align-items-center pb-1">
                        <div>
                            <div class="form-check">
                              <label class="form-check-label unselectable">
                                <!-- call selectAllDevice funtion on line 403 -->
                                <!-- enable /disable the room select using selectAllDisabled function on line 534 -->
                                <input type="checkbox" class="form-check-input" @click="selectAllDevice" v-model="allDeviceSelected" :disabled="selectAllDisabled">
                                <span>{{$t('binding.selectAllSen')}}</span>
                              </label>
                            </div>
                        </div>
                    </div>
                    <div class="h-374">
                        <b-table  :items="renderData"
                                  :fields="table.fields"
                                  :current-page="table.currentPage"
                                  :per-page="table.perPage"
                                  :filter="tableData.search">
                            <template slot="target_device.DEVICE_TYPE" slot-scope="row">
                                <input type="checkbox" v-model="binding.TARGET_DEVICES" @change="selectDevice" :value="{SOURCE_DEVICE_ID:binding.SOURCE_DEVICE_ID,TARGET_DEVICE_ID:row.item.target_device.DEVICE_ID,BINDING_LIST_ID:row.item.binding_list.BINDING_LIST_ID,TIME:0}" :class="{'d-none':row.item.binding != null}">
                                <i class="fa fa-check-square" v-if="row.item.binding != null"></i>
                                {{row.item['target_device']['DEVICE_TYPE']}}
                            </template>
                        </b-table>
                    </div>
                    <!-- datatable component end -->
                    <div v-if="showPaginate" class="d-flex justify-content-between">
                        <div v-if="renderData.length > table.perPage" class="ml-auto" :class="renderData.length > 60 ? '':'custom-pagination-white'">
                            <b-pagination :total-rows="table.totalRows" :per-page="table.perPage" v-model="table.currentPage" />
                        </div>
                        <div class="ml-auto">
                            <button class="btn btn-primary" @click="saveBinding" :class="{ disabled: saveDisabled }" :disabled="saveDisabled">
                                <!-- display loading animation -->
                                <span class="pull-left" v-if="loading">
                                    <i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
                                </span>
                                <span> {{$t('user.save')}} </span>
                            </button>
                            <button type="button" class="btn btn-secondary">{{$t('reset')}}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default{
        props:{
            //Harvey
            propsFloorId:'',
            propsRoomId:'',
            propsGatewayId:'',
            propsDeviceId:'',
            propsDeviceCondition:'',
            propsSelectedRoomTarget:''
        },
        created (){
            this.getFloors();

            //Harvey

            this.inputDefaultId();

            //------------
        },
        data(){
            return{
                table:{
                    fields:[
                        {key:'target_device.DEVICE_TYPE', label:'SENSOR TYPE'},
                        {key:'target_device.DEVICE_NAME', label:'SENSOR NAME'},
                        {key:'binding_list.TARGET_DEVICE_CONDITION', label:"SENSOR CONDITION"}
                    ],
                    currentPage:1,
                    totalRows:0,
                    perPage:10,
                },
                tableData: {
                    search: '',
                },
                binding:{
                    FLOOR_ID: null,
                    ROOM_ID: null,
                    GATEWAY_ID: null,
                    TARGET_DEVICES:[],
                    TARGET_DEVICE_CATEGORY: 1,
                    SOURCE_DEVICE_ID: null,
                    SOURCE_DEVICE_CONDITION: null,
                },
                bindingCheck:{},
                floors:{},
                rooms:{},
                gateways:{},
                devices:{},
                deviceConditions:{
                    SELECTED: null,
                    DATA: [],
                },
                bindingLists:[],
                loading: false,
                allDeviceSelected: false,
                required: false,
            }
        },
        methods: {
            //Harvey
            //Function Name: inputDefaultId
            //Function Description: sets default data
            inputDefaultId(){
                this.binding.FLOOR_ID = this.propsFloorId;
                this.binding.ROOM_ID = this.propsRoomId;
                this.binding.GATEWAY_ID = this.propsGatewayId;
                this.binding.SOURCE_DEVICE_CONDITION = this.propsDeviceCondition;
                this.binding.SOURCE_DEVICE_ID = this.propsDeviceId;
                this.deviceSourceCondition();
            },

            //Funciton Name: chooseTargetDevice
            //Function Description: emits chosen target device
            //Param: targetDeviceData (device)
            chooseTargetDevice(targetDeviceData){
                let deviceType = targetDeviceData['target_device']['DEVICE_TYPE'];
                this.$emit("chooseTargetDevice",deviceType);
            },

            //Function Name: getFloors
            //Function Description: get floors
            getFloors(){
                axios.get('floors')
                .then(response => {
                    this.floors = response.data
                })
                .catch(errors => {
                    console.log(errors);
                });
            },
            //Function Name: getRooms
            //Function Description: get room
            //PARAM: FLOOR_ID
            getRooms(){
                axios.get('floors/'+this.binding.FLOOR_ID+'/rooms')
                .then(response => {
                    this.rooms = response.data,
                    this.binding.ROOM_ID = null,
                    this.binding.GATEWAY_ID = null,
                    this.binding.SOURCE_DEVICE_ID = null,
                    this.binding.SOURCE_DEVICE_CONDITION = null,
                    this.gateways = {},
                    this.devices = {},
                    this.deviceConditions.SELECTED = null,
                    this.deviceConditions.DATA = [],
                    this.bindingLists = []
                })
                .catch(errors => {
                    console.log(errors);
                });
            },
            //get gateway
            //PARAM: ROOM_ID
            getGateways(){
                axios.get('rooms/'+this.binding.ROOM_ID+'/gateways')
                .then(response => {
                    this.gateways = response.data,
                    this.binding.GATEWAY_ID = null,
                    this.binding.SOURCE_DEVICE_ID = null,
                    this.binding.SOURCE_DEVICE_CONDITION = null,
                    this.devices = {},
                    this.deviceConditions.SELECTED = null,
                    this.deviceConditions.DATA = [],
                    this.bindingLists = []
                })
                .catch(errors => {
                    console.log(errors);
                });
            },
            //Function Name: getSourceSensors
            //Function Description: get source sensor device for binding
            //PARAM: GATEWAY_ID
            getSourceSensors(){
                var sensors = 'co2_detector,gas_detector,smoke_detector';
                axios.get('gateways/'+this.binding.GATEWAY_ID+'/devices?REG_FLAG=1&filter=DEVICE_TYPE:'+sensors)
                .then(response => {
                    this.devices = response.data,
                    this.binding.SOURCE_DEVICE_ID= null,
                    this.binding.SOURCE_DEVICE_CONDITION = null,
                    this.deviceConditions.SELECTED = null,
                    this.deviceConditions.DATA = []
                })
                .catch(errors => {
                    console.log(errors);
                });
            },
            //Function Name: deviceSourceCondition
            //Function Description: get device condition
            //PARAM: SOURCE_DEVICE_ID
            deviceSourceCondition(){
                this.bindingLists = [],
                axios.get('devices/'+this.binding.SOURCE_DEVICE_ID+'/binding-list-condition')
                .then(response => {
                    // this.deviceConditions = response.data
                    this.deviceConditions.SELECTED = response.data[0].SELECTED;
                    this.deviceConditions.DATA = response.data[0].DATA;
                    this.binding.SOURCE_DEVICE_CONDITION = response.data[0].SELECTED;
                    this.bindingList();
                })
                .catch(errors => {
                    console.log(errors);
                });
            },
            //Function Name: bindingList
            //Function Description: display the device from the binding list
            //PARAM: SOURCE_DEVICE_ID, SOURCEDEVICE_CONDITION,pages
            bindingList(pages){
                pages = pages || '';
                axios.get('devices/'+this.binding.SOURCE_DEVICE_ID+'/binding-list-devices/'+this.binding.SOURCE_DEVICE_CONDITION+'/'+this.binding.TARGET_DEVICE_CATEGORY, {
                    params:{
                        targetRoomId: this.propsSelectedRoomTarget.ROOM_ID
                    }
                })
                .then(response => {
                    console.log(response.data);
                    this.bindingLists = response.data;
                    this.table.totalRows = this.bindingLists.length;
                })
                .catch(errors => {
                    console.log(errors);
                });
            },
            //Function Name: selectAllDevice
            //Function Description: select all device for binding
            selectAllDevice() {
                    this.binding.TARGET_DEVICES = [];
                    let time;
                    //check if the select all device is checked
                    if (!this.allDeviceSelected) {
                        for (var key in this.bindingLists) {
                            //check is the binding list is not null
                            if(this.bindingLists[key].binding === null){
                                //add data to the binding.TARGET_DEVICES array using array push
                                this.binding.TARGET_DEVICES.push({"SOURCE_DEVICE_ID":this.binding.SOURCE_DEVICE_ID,"TARGET_DEVICE_ID":this.bindingLists[key].target_device.DEVICE_ID,"BINDING_LIST_ID":this.bindingLists[key].binding_list.BINDING_LIST_ID});
                            }
                        }
                    }
            },
            //Function Name: selectDevice
            //Function Description: check all the target devices is check
            //Param: deviceID
            selectDevice() {
                var cnt = 0;
                for(var key in this.bindingLists){
                    if(this.bindingLists[key].binding == null){
                        cnt++;
                    }
                }
                if(this.binding.TARGET_DEVICES.length == cnt){
                    this.allDeviceSelected = true;
                }else{
                    this.allDeviceSelected = false;
                }

            },
            //Function Name: saveBinding
            //Function Description: save binding function
            saveBinding(){
                axios({
                  url: 'bindings/new',
                  method: 'post',
                  data: {
                    'TARGET_DEVICES':this.binding.TARGET_DEVICES
                  }
                })
                .then((response)=>
                    this.$swal({
                        position: 'center',
                        type: 'success',
                        title: 'Sensor Binding Successfully Save',
                        showConfirmButton: false,
                        timer: 1200
                    })
                )
                .catch((error) => console.log(error));
                this.$bus.$emit('getSensorBindingData');
            },
            //Function name: close
            //Function Description: close modal function
            close(){
                this.binding.FLOOR_ID = null,
                this.binding.ROOM_ID = null,
                this.binding.GATEWAY_ID = null,
                this.binding.TARGET_DEVICES = [],
                this.binding.SOURCE_DEVICE_ID = null,
                this.binding.SOURCE_DEVICE_CONDITION = null,
                this.rooms = {},
                this.gateways = {},
                this.devices = {},
                this.deviceConditions.SELECTED = null,
                this.deviceConditions.DATA = [],
                this.bindingLists = [],
                this.allDeviceSelected = false,
                this.loading = false,
                this.$bus.$emit('getSensorBindingData')
            },
        },
        watch:{
            //Trigger when device outisde component is clicked
            propsDeviceId:function(){
                this.binding.SOURCE_DEVICE_ID = this.propsDeviceId;
                this.deviceSourceCondition();
                //this.getSourceDevices();
            },
            //Trigger when device condition outisde component is clicked
            propsDeviceCondition:function(){
                this.deviceConditions.SELECTED = this.propsDeviceCondition;         //ON/OFF/ALARM/NORMAL
                this.binding.SOURCE_DEVICE_CONDITION = this.propsDeviceCondition;   //ON/OFF/ALARM/NORMAL
                this.bindingList();

            }
        },
        computed: {
            renderData(){
                var data = this.bindingLists;
                var search = this.tableData.search;
                var newData = [];
                if(search != ''){
                    for(var i in data){
                        for(var key in data[i].target_device){
                            var index = data[i].target_device[key];
                            if(key != 'DEVICE_ID'){
                                if(index.toLowerCase().indexOf(search.toLowerCase()) >= 0){
                                    newData.push(data[i]);
                                    break;
                                }
                            }
                        }
                    }
                    this.table.totalRows = newData.length;
                    return newData;
                }else{
                    this.table.totalRows = data.length;
                    return data;
                }
            },
            //disbale save button
            saveDisabled () {
                if(this.binding.TARGET_DEVICES.length > 0){
                    return false;
                }else{
                    return true;
                }

            },
            //diabled select all checkbox and time interval all
            selectAllDisabled(){
                if(_.size(this.bindingLists) > 0){
                    var cnt = 0;
                    //coount the binding not equal to bull
                    for(var key in this.bindingLists){
                        if(this.bindingLists[key].binding != null){
                            cnt++;
                        }
                    }
                    //compare the length to binding list and binding not equal to null
                    if(this.bindingLists.length == cnt){
                        return true;
                    }else{
                        return false;
                    }
                }else{
                    return true;
                }
            },
            //show or hide pagination
            showPaginate(){
                return _.size(this.bindingLists) > 0
            },
        }
    }
</script>