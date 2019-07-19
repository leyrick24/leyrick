<template>
    <div>
        <div class="row">
            <div class="card-body col-sm-12 text-dark h-526">
                <div class="form-group">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-dark">{{$t('binding.targetDevice')}}</h5>
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
                        <!-- search end-->
                    </div>
                    <div class="divider-line"></div>
                    <!-- select all time -->
                    <div class="d-flex justify-content-between align-items-center pb-1">
                        <div>
                            <div class="form-check">
                              <label class="form-check-label unselectable">
                                <!-- call selectAllDevice funtion on line 403 -->
                                <!-- enable /disable the room select using selectAllDisabled function on line 534 -->
                                <input type="checkbox" class="form-check-input" @click="selectAllDevice" v-model="allDeviceSelected">
                                <span>{{$t('binding.selectAllDev')}}</span>
                              </label>
                            </div>
                        </div>
                        <div>
                          <label class="unselectable">
                                <!-- call selectAllTime function on line 403 -->
                                <!-- enable /disable the room select using selectAllDisabled function on line 534 -->
                            <select class="custom-select h-75" v-model="allDeviceTime" @change="selectAllTime" :disabled="selectAllDisabled">
                                <!-- loop timerValue data -->
                                <option v-for="timer in timerValue" :value="timer.VAL" :selected="timer.VAL == allDeviceTime">{{ timer.VAL }} min</option>
                            </select>
                          </label>
                        </div>
                    </div>
                    <!-- select all end -->

                    <!-- search, page length and set all time -->
                    <!-- search, page length and set all time end -->

                    <!-- table component -->
                    <div class="table-responsive h-374">

                        <b-table  :items="renderData"
                                  :fields="table.fields"
                                  :current-page="table.currentPage"
                                  :per-page="table.perPage"
                                  :filter="tableData.search">
                            <template slot="target_device.DEVICE_TYPE" slot-scope="row">
                                <input type="checkbox" v-model="binding.TARGET_DEVICES" @change="selectDevice(row.item.target_device.DEVICE_ID)" @click="validateData(binding.SOURCE_DEVICE_ID,row.item.target_device.DEVICE_ID,row.item.binding_list.BINDING_LIST_ID,row.item.binding_list.TARGET_DEVICE_TYPE,$event)" :value="{SOURCE_DEVICE_ID:binding.SOURCE_DEVICE_ID,TARGET_DEVICE_ID:row.item.target_device.DEVICE_ID,BINDING_LIST_ID:row.item.binding_list.BINDING_LIST_ID,TIME:row.item.TIME_INTERVAL}" :class="{'d-none': row.item.binding != null}">
                                <i class="fa fa-check-square" v-if="row.item.binding != null"></i>
                                {{row.item['target_device']['DEVICE_TYPE']}}
                            </template>
                            <template slot="time" slot-scope="row">
                                <select v-if="row.item.binding != null" class="custom-select" :disabled="disableTime || row.item.binding != null">
                                    <option v-for="timer in timerValue" :value="timer.VAL" :selected="timer.VAL == row.item.binding.TIME_INTERVAL">{{ timer.VAL }} {{$t('binding.min')}}</option>
                                </select>
                                <select v-else class="custom-select" v-model="row.item.TIME_INTERVAL" @change="updateTimeInterval(row.item.binding_list.BINDING_LIST_ID,row.item.TIME_INTERVAL)" :disabled="disableTime || row.item.binding != null">
                                    <!-- loop timerValue data -->
                                    <option v-for="timer in timerValue" :value="timer.VAL">{{ timer.VAL }} {{$t('binding.min')}}</option>>
                                </select>
                            </template>
                        </b-table>

                    </div>
                    <!-- pagination component -->
                    <!-- call getData function when prev, next and number is click -->
                    <div v-if="showPaginate" class="d-flex justify-content-between">
                        <div class="custom-pagination-white ml-auto">
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
                            <button type="button" class="btn btn-secondary" @click="reset()">{{$t('reset')}}</button>
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
            propsSelectedRoomTarget:'',
        },
        created (){

            this.getFloors();
            this.$bus.$on('setDeviceBindingData' , data =>{
                this.bindedList = data;
            });

            //Harvey

            this.inputDefaultId();

            //------------

        },
        data(){
            return{
                table:{
                    fields:[
                        {key:'target_device.DEVICE_TYPE', label:'DEVICE TYPE'},
                        {key:'target_device.DEVICE_NAME', label:'DEVICE NAME'},
                        {key:'binding_list.TARGET_DEVICE_CONDITION', label:"DEVICE CONDITION"},
                        {key:'time', label:'TIME_INTERVAL'},
                    ],
                    currentPage:1,
                    totalRows:0,
                    perPage:5,
                },
                tableData: {
                    search: '',
                },
                bindedList: null,
                binding:{
                    FLOOR_ID: null,
                    ROOM_ID: null,
                    GATEWAY_ID: null,
                    TARGET_DEVICES:[],
                    TARGET_DEVICE_CATEGORY: 0,
                    SOURCE_DEVICE_ID: null,
                    SOURCE_DEVICE_CONDITION: null,
                },
                floors:{},
                rooms:{},
                gateways:{},
                devices:{},
                deviceConditions:{
                    SELECTED: null,
                    DATA: [],
                },
                sourceDevice:'',
                bindingLists:[],
                tempValues:[{"tempDown":'TEMP_16'}, {"tempDown":'TEMP_17'}, {"tempDown":'TEMP_18'}, {"tempDown":'TEMP_19'}, {"tempDown":'TEMP_20'}, {"tempDown":'TEMP_21'}, {"tempDown":'TEMP_22'}, {"tempDown":'TEMP_23'}, {"tempDown":'TEMP_24'}, {"tempDown":'TEMP_25'}, {"tempUp":'TEMP_26'}, {"tempUp":'TEMP_27'}, {"tempUp":'TEMP_28'}, {"tempUp":'TEMP_29'}, {"tempUp":'TEMP_30'}],
                timerValue:[{"VAL":1},{"VAL":5},{"VAL":10},{"VAL":15},{"VAL":20},{"VAL":0}],
                loading: false,
                allDeviceSelected: false,
                allDeviceTime: 5,
                required: false,
                bindCount: 0,
                bindExistCount: 0,
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
            //Function Name: getSourceDevices
            //Function Description: get source device for binding
            //PARAM: GATEWAY_ID
            getSourceDevices(){
                // axios.get('gateways/'+this.binding.GATEWAY_ID+'/devices?REG_FLAG=1')
                axios.get('gateways/'+this.binding.GATEWAY_ID+'/devices?REG_FLAG=1&filter=DEVICE_CATEGORY:1')
                .then(response => {
                    this.devices = response.data,
                    this.binding.SOURCE_DEVICE_ID = null,
                    this.binding.SOURCE_DEVICE_CONDITION = null,
                    this.deviceConditions.SELECTED = null,
                    this.deviceConditions.DATA = []
                })
                .catch(errors => {
                    console.log(errors);
                });
            },


            //Harvey 20190221
            //Function Name: deviceSourceCondition
            //Function Description: get device condition
            //PARAM: SOURCE_DEVICE_ID
            deviceSourceCondition(){
                this.bindingLists = [],
                axios.get('devices/'+this.binding.SOURCE_DEVICE_ID+'/binding-list-condition')
                .then(response => {
                    this.deviceConditions.SELECTED = response.data[0].SELECTED;
                    this.deviceConditions.DATA = response.data[0].DATA;
                    this.binding.SOURCE_DEVICE_CONDITION = response.data[0].SELECTED;
                    this.bindingList();
                })
                .catch(errors => {
                    console.log(errors);
                });
                axios.get('devices/'+this.binding.SOURCE_DEVICE_ID)
                .then(response => {
                    this.sourceDevice = response.data;
                })
                .catch(errors => {
                    console.log(errors);
                });
            },
            //Funciton Name: chooseTargetDevice
            //Function Description: emits chosen target device
            //Param: targetDeviceData (device)
            chooseTargetDevice(targetDeviceData){
                let deviceType = targetDeviceData['target_device']['DEVICE_TYPE'];
                this.$emit("chooseTargetDevice",deviceType);
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

                    this.bindingLists = response.data;
                    this.table.totalRows = this.bindingLists.length;

                    for(var key in this.bindingLists){
                        if(this.bindingLists[key].binding == null){
                            //set a default value of time to each devices
                            var source_device_type = this.bindingLists[key].binding_list.SOURCE_DEVICE_TYPE;
                            if(source_device_type == 'light_detector'){
                                this.allDeviceTime = 0;
                                Vue.set(this.bindingLists[key], 'TIME_INTERVAL', this.allDeviceTime);
                            }else if(this.binding.SOURCE_DEVICE_CONDITION.includes("NORMAL") || this.binding.SOURCE_DEVICE_CONDITION.includes("COMFORT") || this.binding.SOURCE_DEVICE_CONDITION.includes("GOOD") || this.binding.SOURCE_DEVICE_CONDITION.includes("DAY")){
                                this.allDeviceTime = 5;
                                Vue.set(this.bindingLists[key], 'TIME_INTERVAL', this.allDeviceTime);
                            }else{
                                this.allDeviceTime = 5;
                                Vue.set(this.bindingLists[key], 'TIME_INTERVAL', this.allDeviceTime);
                            }
                        }
                        else{
                            //convert the time to minute if its already binded
                            let time = this.bindingLists[key].binding.TIME_INTERVAL;
                            time = time / 60;
                            Vue.set(this.bindingLists[key].binding, 'TIME_INTERVAL', time);
                        }
                    }
                    if(this.allDeviceSelected){
                         this.selectNextAllDevice();
                    }
                    console.log("TOTAL BINDED" + this.bindExistCount);
                })
                .catch(errors => {
                    console.log(errors);
                });
            },
            //Function Name: selectAllDevice
            //Function Description: select all device for binding
            selectAllDevice() {
                    let time;
                    //check if the select all device is checked
                    if (!this.allDeviceSelected) {
                        for (var key in this.bindingLists) {
                            //check is the binding list is not null
                            if(this.bindingLists[key].binding === null){
                                //add data to the binding.TARGET_DEVICES array using array push
                                this.binding.TARGET_DEVICES.push({"SOURCE_DEVICE_ID":this.binding.SOURCE_DEVICE_ID,"TARGET_DEVICE_ID":this.bindingLists[key].target_device.DEVICE_ID,"BINDING_LIST_ID":this.bindingLists[key].binding_list.BINDING_LIST_ID,"TIME":this.bindingLists[key].TIME_INTERVAL});
                            }
                        }
                    }else{
                        this.binding.TARGET_DEVICES = [];
                    }
            },
            //Function Name: selectNextAllDevice
            //Function Description:
            selectNextAllDevice() {
                    let time;
                    //check if the select all device is checked
                    if (this.allDeviceSelected) {
                        for (var key in this.bindingLists) {
                            //check is the binding list is not null
                            if(this.bindingLists[key].binding === null){
                                //add data to the binding.TARGET_DEVICES array using array push
                                this.binding.TARGET_DEVICES.push({"SOURCE_DEVICE_ID":this.binding.SOURCE_DEVICE_ID,"TARGET_DEVICE_ID":this.bindingLists[key].target_device.DEVICE_ID,"BINDING_LIST_ID":this.bindingLists[key].binding_list.BINDING_LIST_ID,"TIME":this.bindingLists[key].TIME_INTERVAL});
                            }
                        }
                    }else{
                        this.binding.TARGET_DEVICES = [];
                    }
            },
            //Function Name: selectDevice
            //Function Description: check all the target devices is check
            //Param: deviceID
            selectDevice(deviceID) {
                var totalList = this.table.totalRows;
                let data = this.binding.TARGET_DEVICES;
                if(this.bindingLists.binding == null){
                    var exist = data.filter(function(i){
                        if(i.TARGET_DEVICE_ID == deviceID){
                            return true;
                        }
                    });
                    if(exist.length > 0){
                        this.bindCount++;
                    }else{
                        if(this.bindCount != 0){
                            this.bindCount--;
                        }
                    }
                }
                if(totalList == this.bindCount){
                    this.allDeviceSelected = true;
                }else{
                    this.allDeviceSelected = false;
                }
                console.log("BINDCOUNT " + this.bindCount);

            },
            //Function Name: selectAllTime
            //Function Description: select time for all time interval
            selectAllTime(){
                for (var key in this.bindingLists) {
                    Vue.set(this.bindingLists[key], 'TIME_INTERVAL', this.allDeviceTime)
                }
            },
            //Function Name: updateTimeInterval
            //Function Description: update the time interval of each device
            //PARAM : id = TARGET_DEVICE_ID, time = time interval of selected device
            updateTimeInterval(id,time){
                let data = this.binding.TARGET_DEVICES;
                //check if the target devices has data
                if(data.length > 0){
                    //loop the data if it has data
                    for(var key in data){
                        //check if the binding list id is equal to the current id
                        if(data[key].BINDING_LIST_ID == id){
                            data[key].TIME = time
                        }
                    }
                }
            },
            //Funciton Name: validateData
            //Function Description: validate data being sent
            //Param: binding.SOURCE_DEVICE_ID,bindingList.target_device.DEVICE_ID,bindingList.binding_list.BINDING_LIST_ID,bindingList.binding_list.TARGET_DEVICE_TYPE,$event
            validateData(sourceID,targetID,bindingListID,targetType,event){
                let data = this.binding.TARGET_DEVICES;
                let data2 = this.bindedList;
                console.log(targetType);
                if(targetType == 'ir_remote'){
                    var exist,exist2;
                    exist = data.filter(function(i){
                        if(i.TARGET_DEVICE_ID == targetID){
                            return i.TARGET_DEVICE_ID;
                        }
                    });
                    if(exist.length > 0){
                        for(var i in data){
                            if(data[i].BINDING_LIST_ID != bindingListID){
                                event.preventDefault();
                            }
                        }
                    }
                    if(data2.length > 0){
                        for(var i in data2){
                            if (data2[i].DEVICE_ID == sourceID) {
                                exist2 = data2[i].bindings.filter(function(i){
                                    if(i.SOURCE_DEVICE_ID == sourceID && i.TARGET_DEVICE_ID == targetID){
                                        return i.TARGET_DEVICE_ID;
                                    }
                                });
                                console.log(exist2);
                                break;
                            }
                        }
                        if(exist2.length > 0){
                            event.preventDefault();
                        }
                    }


                }
            },
            //Function Name: reset
            //Function Description: reset target devices
            reset(){
                this.binding.TARGET_DEVICES = [];
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
                        title: 'Device Binding Successfully Save',
                        showConfirmButton: false,
                        timer: 1200
                    }),
                )
                .catch((error) => console.log(error));
                this.$bus.$emit('getDeviceBindingData');
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
            //disbale save button
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
            totalBinded(){
                var cnt = 0;
                for (var key in this.bindingLists) {
                    //check is the binding list is not null
                    if(this.bindingLists[key].binding == null){
                        cnt++;
                    }
                }
                return cnt;
            },
            saveDisabled () {
                if(this.binding.TARGET_DEVICES.length > 0){
                    return false;
                }else{
                    return true;
                }

            },
            //disable room select
            roomDisabled(){
                if(this.binding.FLOOR_ID == null){
                    return true;
                }else{
                    return false;
                }
            },
            //disable gateway select
            gatewayDisabled(){
                if(this.binding.ROOM_ID == null){
                    return true;
                }else{
                    return false;
                }
            },
            //disable source device select
            sourceDisabled(){
                if(this.binding.GATEWAY_ID == null){
                    return true;
                }else{
                    return false;
                }
            },
            //disbaled condition select
            sourceConditionDisabled(){
                if(this.deviceConditions.DATA.length > 0){
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
                        //check source if light detector
                        if (this.sourceDevice.DEVICE_TYPE == 'light_detector') {
                            return true;
                        }else{
                            //check for selected device
                            if (this.binding.TARGET_DEVICES.length > 0) {
                                return true;
                            }else{
                                return false;
                            }
                        }
                    }
                }else{
                    return true;
                }
            },
            //show or hide pagination
            showPaginate(){
                return _.size(this.bindingLists) > 0
            },
            disableTime(){
                if (this.sourceDevice.DEVICE_TYPE == 'light_detector') {
                    return true;
                }else{
                    return false;
                }
            }
        },
    }
</script>