<template>
    <div>
        <!-- content-wrapper -->
        <div class="wrapper hardware-img-bg">
            <!-- content-fluid -->
            <div class="container-fluid h-880">
                <div class="d-flex justify-content-between py-3">
                    <div>
                        <div>{{$t('binding.header')}} </div>
                    </div>
                    <clock :locale="$i18n.locale"></clock>
                </div>
                <!-- View Binding List Mode -->
                <div v-if="bindingMode=='viewBindingList'" class="row h-826">
                    <div class="col-md-4 mb-3 mt-2">
                        <div class="background-orange">
                            <div class="pt-2 pb-1 pl-3">
                                <h5><b>{{$t('binding.floor')}}</b></h5>
                            </div>
                        </div>
                        <div class="tab-bg-2 custom-scroll-bar">
                            <!-- Looping Floor -->
                            <h1 v-if="floorList.length > 0" v-for="floor in limitedFloors" @click="chooseListBindingFloor(floor)" class="pl-2 text-dark pointer binding-lists"
                                :class="floor['FLOOR_ID']==selectedBindingListFLoor['FLOOR_ID'] ? 'binding-selected':''">
                                <b>{{floor['FLOOR_NAME']}}</b>
                            </h1>
                            <div v-if="floorList.length > 4" class="d-flex justify-content-center custom-pagination-orange">
                                    <b-pagination :total-rows="floorList.length" :per-page="listPagination.paginationLimit" v-model="listPagination.floorCurrentPage">
                                    </b-pagination>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 mb-3 mt-2">
                        <div class="binding-map-bar card-body border border-dark h-271 tab-bg-2">
                            <div class="row mt-3" v-if="selectedBindingListFLoor!=[]">
                                <BindingMapView v-if="floorList.length > 0" :currentFloor="selectedBindingListFLoor" :currentRoom="selectedRoom" @selectRoom="mapViewSelect"></BindingMapView>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3 mt-2">
                        <!-- tab-navigation -->
                        <div class="d-flex justify-content-between align-items-center nav-line">
                            <!-- div-tab-list -->
                            <!-- Nav Bar -->
                            <div class="nav nav-tabs nav-line w-100" role="tablist">
                                <a class="nav-item nav-link nav-header-blue" :class="selectedCategory=='Device Binding' ? 'active':''" data-toggle="tab" href="#binding-device-list" role="tab" aria-controls="nav-binding" aria-selected="true" @click="tabShow('Device Binding')">{{$t('binding.sensorDevice')}}</a>
                                 <a class="nav-item nav-link nav-header-blue" :class="selectedCategory=='Sensor Binding' ? 'active':''" data-toggle="tab" href="#binding-sensor-list" role="tab" aria-controls="nav-binding" aria-selected="false" @click="tabShow('Sensor Binding')">{{$t('binding.sensorSensor')}}</a>
                            </div>
                            <!-- div-tablist-end -->
                            <button v-if="selectedCategory=='Device Binding'" @click="clickAddBinding('device')" class="btn btn-sm btn-light">{{$t('binding.addDevice')}}</button>
                            <button v-if="selectedCategory=='Sensor Binding'" @click="clickAddBinding('sensor')" class="btn btn-sm btn-light">{{$t('binding.addSensor')}}</button>
                        </div>
                        <div class="tab-content tab-bg-2" id="nav-tabContent">
                            <div class="tab-pane fade show active" :class="selectedCategory=='Device Binding' ? 'show active':''" id="binding-device-list" role="tabpanel">
                                <DeviceBindingManagement :selectedRoom="selectedRoom" :selectedBindingListFLoor="selectedBindingListFLoor" @change-size="changeSize($event)"></DeviceBindingManagement>
                            </div>
                            <div class="tab-pane fade" :class="selectedCategory=='Sensor Binding' ? 'show active':''" id="binding-sensor-list" role="tabpanel">
                                <SensorBindingManagement :selectedRoom="selectedRoom" :selectedBindingListFLoor="selectedBindingListFLoor" @change-size="changeSize($event)"></SensorBindingManagement>
                            </div>
                        </div>
                        <!-- divtab-content-end -->
                    </div>
                </div>
                <!-- Add Binding Mode -->
                <div v-if="bindingMode=='addBinding'">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="nav nav-tabs w-100" role="tablist">
                            <a  v-if="selectedCategory == 'Device Binding'" class="nav-item nav-link px-4 active" data-toggle="tab" role="tab"  aria-controls="nav-binding" aria-selected="true">{{$t('binding.createDevice')}}</a>
                            <a  v-else-if="selectedCategory == 'Sensor Binding'" class="nav-item nav-link px-4 active" data-toggle="tab" role="tab"  aria-controls="nav-binding" aria-selected="true">{{$t('binding.createSensor')}}</a>
                        </div>
                        <!-- Breadcrumb -->
                        <div v-if="selectedCategory!=''" class="text-dark text-nowrap"><span class="breadcrumb-custom" @click="clickBreadcrumb('category')">{{selectedCategory}}</span></div>      <!--Floors-->
                        <div v-if="selectedFloor!=''" class="text-dark text-nowrap">/<span class="breadcrumb-custom" @click="clickBreadcrumb('floor')">{{selectedFloor['FLOOR_NAME']}}</span></div>      <!--Floors-->
                        <div v-if="selectedRoom!=''" class="text-dark text-nowrap">/<span class="breadcrumb-custom" @click="clickBreadcrumb('room')">{{selectedRoom['ROOM_NAME']}}</span></div>         <!--Room-->
                        <div v-if="selectedGateway!=''" class="text-dark text-nowrap">/<span class="breadcrumb-custom" @click="clickBreadcrumb('gateway')">{{selectedGateway['GATEWAY_NAME']}}</span></div><!--Gateway-->
                        <div v-if="selectedDevice!=''" class="text-dark text-nowrap">/<span class="breadcrumb-custom" @click="clickBreadcrumb('device')">{{selectedDevice['DEVICE_NAME']}}</span></div><!--Gateway-->
                    </div>
                    <div class="row">
                        <div class="col-md-4 mt-2">
                            <!-- Floor List -->
                            <div v-if="viewList=='floors'">
                                <div class="background-orange">
                                    <div class="pt-2 pb-1 pl-3">
                                        <h5><b>{{$t('binding.floor')}}</b></h5>
                                    </div>
                                </div>
                                <div class="binding-bg custom-scroll-bar">
                                    <!-- Looping Floor -->
                                    <h2 v-if="floorList.length > 0" v-for="floor in limitedFloors" @click="chooseFloor(floor)" class="pl-2 text-dark pointer binding-lists"
                                                                                    :class="floor['FLOOR_ID']==selectedFloor['FLOOR_ID'] ? 'binding-selected':''">
                                        <b>{{floor['FLOOR_NAME']}}</b>
                                    </h2>
                                    <div v-if="floorList.length > 4" class="d-flex justify-content-center custom-pagination-orange">
                                            <b-pagination :total-rows="floorList.length" :per-page="listPagination.paginationLimit" v-model="listPagination.floorCurrentPage">
                                            </b-pagination>
                                    </div>
                                </div>
                            </div>
                             <!-- Room List -->
                            <div v-else-if="viewList=='rooms'">
                                <div class="background-orange">
                                    <div class="pt-2 pb-1 pl-3">
                                        <h5><b>{{$t('binding.room')}}</b></h5>
                                    </div>
                                </div>
                                <div class="binding-bg custom-scroll-bar">
                                       <!-- Looping Floor -->
                                        <h2 v-if="roomList.length > 0" v-for="room in limitedRooms" @click="chooseRoom(room)" class="pl-2 text-dark pointer binding-lists"
                                                                                    :class="room['ROOM_ID']==selectedRoom['ROOM_ID'] ? 'binding-selected':''">
                                            <b>{{room['ROOM_NAME']}}</b>
                                        </h2>
                                        <div v-if="roomList.length > 4" class="d-flex justify-content-center custom-pagination-orange">
                                                <b-pagination :total-rows="roomList.length" :per-page="listPagination.paginationLimit" v-model="listPagination.roomCurrentPage">
                                                </b-pagination>
                                        </div>
                                </div>
                                <div></div><!--For Scroll Bar Bug-->
                            </div>
                        </div>
                        <div class="col-md-8 mt-2">
                            <div class="binding-bg-map">
                                <div class="row" v-if="selectedFloor!=''">
                                    <CreateBindingMap v-if="floorList.length > 0" :currentFloor="selectedFloor" :currentRoom="selectedRoom"></CreateBindingMap>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Harvey -->
                    <div class="row">
                        <div v-if="showGatewayList == true" class="col-md-4">
                            <!-- Gateway Header -->
                            <div class="background-orange">
                                <div class="pt-2 pb-1 pl-3">
                                    <h5><b>GATEWAY</b></h5>
                                </div>
                            </div>
                            <div class="binding-bg-gateway custom-scroll-bar">
                                <div v-for="gateway in limitedGateways" @click="chooseGateway(gateway)" class="text-dark pointer pl-2 pt-1 binding-lists"
                                                                                :class="gateway['GATEWAY_ID']==selectedGateway['GATEWAY_ID'] ? 'binding-selected':''">
                                    <h2><b>{{gateway['GATEWAY_NAME']}}</b></h2>
                                </div>
                                <div v-if="gatewayList.length > 4" class="d-flex justify-content-center custom-pagination-orange">
                                    <b-pagination :total-rows="gatewayList.length" :per-page="listPagination.paginationLimit" v-model="listPagination.gatewayCurrentPage">
                                    </b-pagination>
                                </div>
                            </div>
                        </div>
                        <div v-if="showDeviceList == true" class="col-md-4">
                            <!-- Device Header -->
                            <div class="background-orange">
                                <div class="pt-2 pb-1 pl-3">
                                    <h5><b>{{$t('binding.sensors')}}</b></h5>
                                </div>
                            </div>
                            <!-- Device List -->
                            <div class="binding-bg-gateway h-350 custom-scroll-bar">
                                <div v-if="deviceList.length > 0" v-for="device in limitedDevices" @click="chooseDevice(device)" class="text-dark pointer pl-2 pt-1 binding-lists" :class="device['DEVICE_ID']==selectedDevice['DEVICE_ID'] ? 'binding-selected':''">
                                    <h2><b>{{device['DEVICE_NAME']}}</b></h2>
                                </div>
                                <div v-if="deviceList.length > 4" class="d-flex justify-content-center custom-pagination-orange">
                                    <b-pagination :total-rows="deviceList.length" :per-page="listPagination.paginationLimit" v-model="listPagination.deviceCurrentPage">
                                    </b-pagination>
                                </div>
                                <h2 v-if="deviceList.length == 0">{{$t('binding.noDevice')}}</h2>
                            </div>
                            <!-- Device Condition List -->
                            <div v-if="deviceList.length > 0 && selectedRoomTarget.length != 0" class="background-orange">
                                    <div class="pt-2 pb-1 pl-3">
                                        <h5><b>{{$t('binding.devCondition')}}</b></h5>
                                    </div>
                            </div>
                            <div v-if="deviceList.length > 0 && selectedRoomTarget.length != 0" class="binding-bg-device custom-scroll-bar">
                                <div v-for="deviceCondition in deviceConditionList" @click="chooseDeviceCondition(deviceCondition['SOURCE_DEVICE_CONDITION'])" class="text-dark pointer pl-2 pt-1 binding-lists" :class="deviceCondition['SOURCE_DEVICE_CONDITION']==selectedDeviceCondition ? 'binding-selected':''">
                                    <h1><b>{{deviceCondition['SOURCE_DEVICE_CONDITION']}}</b></h1>
                                </div>
                            </div>
                        </div>
                        <!-- Binding Creation Form -->
                        <div v-if="showCreateForm == true" class="col-sm-8">
                            <div class="row">
                                <div v-if="showTargetInterface == 'floor'" class="col-md-12">
                                    <div class="pt-2 pb-1 pl-3 background-orange text-white">
                                        <h5><b>{{$t('binding.targetFloor')}}</b></h5>
                                    </div>
                                    <div class="tab-bg-2 custom-scroll-bar">
                                       <!-- Looping Room -->
                                        <h1 v-if="floorList.length > 0" v-for="floor in limitedFloors" @click="chooseTargetFloor(floor)" class="pl-2 text-dark pointer binding-lists"
                                                                                    :class="floor['FLOOR_ID']==selectedFloorTarget['FLOOR_ID'] ? 'binding-selected':''">
                                            <b>{{floor['FLOOR_NAME']}}</b>
                                        </h1>
                                        <div v-if="roomList.length > 4" class="d-flex justify-content-center custom-pagination-orange">
                                                <b-pagination :total-rows="roomTargetList.length" :per-page="listPagination.paginationLimit" v-model="listPagination.roomTargetCurrentPage">
                                                </b-pagination>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="showTargetInterface == 'room'" class="col-md-12">
                                        <div class="pt-2 pb-1 pl-3 background-orange text-white">
                                            <h5><b>{{$t('binding.targetRoom')}}</b></h5>
                                        </div>
                                        <div class="tab-bg-2 custom-scroll-bar">
                                           <!-- Looping Room -->
                                            <h1 v-if="roomTargetList.length > 0" v-for="room in limitedRoomsTarget" @click="chooseRoomTarget(room)" class="pl-2 text-dark pointer binding-lists"
                                                                                        :class="room['ROOM_ID']==selectedRoomTarget['ROOM_ID'] ? 'binding-selected':''">
                                                <b>{{room['ROOM_NAME']}}</b>
                                            </h1>
                                            <div v-if="roomList.length > 4" class="d-flex justify-content-center custom-pagination-orange">
                                                    <b-pagination :total-rows="roomTargetList.length" :per-page="listPagination.paginationLimit" v-model="listPagination.roomTargetCurrentPage">
                                                    </b-pagination>
                                            </div>
                                        </div>
                                </div>
                                <!-- <div v-if="showTargetInterface == 'devices'" class="col-md-12">
                                    <div class="binding-image-div text-center">
                                        <img class="binding-image" :src="bindingImagePreview">
                                    </div>
                                </div> -->
                                <div v-if="showTargetInterface == 'devices'" class="col-md-12">
                                    <div class="tab-bg-2" v-if="selectedCategory == 'Device Binding'">
                                        <CreateBindingDevice :propsFloorId="selectedFloorTarget['FLOOR_ID']"
                                                            :propsRoomId="selectedRoom['ROOM_ID']"
                                                            :propsGatewayId="selectedGateway['GATEWAY_ID']"
                                                            :propsDeviceId="selectedDevice['DEVICE_ID']"
                                                            :propsDeviceCondition="selectedDeviceCondition"
                                                            :propsSelectedRoomTarget = "selectedRoomTarget"
                                                            @chooseTargetDevice = "chooseTargetDevice"
                                                            ></CreateBindingDevice>
                                    </div>
                                    <div class="tab-bg-2" v-else>
                                        <CreateBindingSensor :propsFloorId="selectedFloorTarget['FLOOR_ID']"
                                                            :propsRoomId="selectedRoom['ROOM_ID']"
                                                            :propsGatewayId="selectedGateway['GATEWAY_ID']"
                                                            :propsDeviceId="selectedDevice['DEVICE_ID']"
                                                            :propsDeviceCondition="selectedDeviceCondition"
                                                            :propsSelectedRoomTarget = "selectedRoomTarget"
                                                            @chooseTargetDevice = "chooseTargetDevice"
                                                            ></CreateBindingSensor>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content-fluid-end -->
        </div>
        <!-- content-warpper-end -->
        <Footer></Footer>
    </div>

</template>
<script>
    //import components
    import BindingMapView from './BindingMapView/BindingMapView.vue';
    import CreateBindingMap from './BindingMapView/CreateBindingMap.vue';
    import DeviceBindingManagement from './DeviceBindingManagement.vue';
    import SensorBindingManagement from './SensorBindingManagement.vue';
    import CreateBindingDevice from './CreateBindingDevice.vue';
    import CreateBindingSensor from './CreateBindingSensor.vue';
    export default {
        //initialize component
        components: {DeviceBindingManagement, SensorBindingManagement, CreateBindingDevice, CreateBindingSensor, BindingMapView, CreateBindingMap},
        data() {
            return {
                isBinded: false,
                selectedCategory: 'Device Binding',
                floorList:[],
                roomList:[],
                roomTargetList:[],
                gatewayList:[],
                deviceList:[],
                deviceConditionList:[],


                selectedFloor:[],
                selectedRoom:[],
                selectedFloorTarget:[],
                selectedRoomTarget:[],
                selectedGateway:[],
                selectedDevice:[],
                selectedDeviceCondition:[],
                selectedSourceDevice:[],

                selectedBindingListFLoor:[],

                bindingImagePreview:"",

                listPagination:{
                            floorCurrentPage:1,
                            roomCurrentPage:1,
                            roomTargetCurrentPage:1,
                            gatewayCurrentPage:1,
                            deviceCurrentPage:1,
                            paginationLimit:4,
                            },

                showTargetInterface:'room',
                showCreateForm:false,
                showGatewayList:false,
                showDeviceList:false,
                viewList:"floors",              // floors / rooms
                bindingMode:"viewBindingList",   //viewBindingList / addBinding
                user:''
            }
        },
        created(){
            this.getFloors();
        },
        methods: {
            //Harvey
            //Function Name: chooseListBindingFloor
            //Function Description: When Floor List is clicked in binding List
            //Param: floor (floor)
            chooseListBindingFloor(floor){
                this.selectedRoom = [];
                this.selectedBindingListFLoor = floor;
                this.listPagination.floorCurrentPage = 1;
                this.viewList = "floors";
            },
            //Function Name: chooseFloor
            //Function Description: choose floor
            //Param: data (floor)
            chooseFloor(data){
                this.selectedFloor = data;
                this.getRooms(data);
                this.listPagination.roomCurrentPage = 1;

            },
            //Function Name: clickAddBinding
            //Function Description: Display add binding
            clickAddBinding(data){
                this.bindingMode = "addBinding";
                this.viewList = "floors"
            },
            //Function Name: chooseRoom
            //Function Description: choose room
            //Param: roomData (room)
            chooseRoom(roomData){
                if(this.selectedFloor == ""){
                    this.$toast.error("Please select a floor.","Error",{position:'topCenter'});
                }else{
                    this.selectedRoom = roomData;

                    this.listPagination.deviceCurrentPage = 1;
                    this.deviceConditionLis = [];
                    this.showGatewayList = false;
                    this.showDeviceList = true;
                    this.selectedGateway = "";
                    this.selectedDevice = "";
                    this.selectedDeviceCondition = "";
                    this.showCreateForm = false;
                    this.showTargetInterface = "room";
                    this.getDevices();
                }
            },
            //Function Name: chooseRoomTarget
            //Function Description: Set targetted room
            //Param: roomData (room)
            chooseRoomTarget(roomData){
                this.selectedRoomTarget = roomData;
                this.showTargetInterface = 'devices'

            },
            //Function Name: chooseDevice
            //Function Description: set chosen device
            //Param: deviceData (device)
            chooseDevice(deviceData){
                this.listPagination.roomTargetCurrentPage = 1;
                this.selectedDevice = deviceData;
                this.showCreateForm = true;
                this.showTargetInterface = "room";
                this.bindingImagePreview = "";
                this.getDeviceSourceCondition();
            },
            //Function Name: chooseDeviceCondition
            //Function Description: set chosen device condition
            //Param: deviceConditionData (device condition)
            chooseDeviceCondition(deviceConditionData){
                this.selectedDeviceCondition = deviceConditionData;
                this.bindingImagePreview = "";
            },
            //Function Name: chooseTargetDevice
            //Function Description: set chosen target device
            //Param: targetDeviceType (device)
            chooseTargetDevice(targetDeviceType){
                let bindingImagePreview = this.getPreviewBindingImage(this.selectedDevice['DEVICE_TYPE'],targetDeviceType);
                this.bindingImagePreview = bindingImagePreview;
            },
            //Function Name: getFloors
            //Function Description: get floors
            getFloors(){
                axios.get('floors/').then(response=>{
                    if(response.data.length > 0){
                        this.floorList = response.data;
                        this.selectedFloor = response.data[0];
                        this.selectedBindingListFLoor = response.data[0];
                        this.getRooms(this.selectedBindingListFLoor);
                    }
                });
            },
            //Function Name: getRooms
            //Function Description: get rooms
            //Param: data (floor)
            getRooms(data){
                axios.post('floors/' + data['FLOOR_ID'] + '/rooms').then(response=>{
                    if(response.data.length > 0){
                        this.roomList = response.data;
                        this.viewList = "rooms";
                    }
                });
            },
            //Function Name: getRoomsTarget
            //Function Description: get rooms to select for target
            //Param: seletedFloorTarget
            getRoomsTarget(){
                axios.post('floors/' + this.selectedFloorTarget['FLOOR_ID'] + '/rooms').then(response=>{
                    if(response.data.length > 0){
                        this.roomTargetList = response.data;
                        this.showTargetInterface = 'room'
                    }
                });
            },
            //Function name: getDevices
            //Function Description: get devices depending on category
            //Param: selectedCategory
            getDevices(){
                if(this.selectedCategory == "Device Binding"){
                    var sensors = 'light_detector,motion_detector,panic_button,temp_hum';
                    axios.get('rooms/' + this.selectedRoom.ROOM_ID + '/devices?REG_FLAG=1&filter=DEVICE_TYPE:' + sensors)
                    .then(response => {
                        if(response.data.length > 0){
                            this.deviceList = response.data;
                        }else{
                            this.deviceList = [];
                        }
                    })
                    .catch(errors => {
                        console.log(errors);
                    });
                }else if(this.selectedCategory == "Sensor Binding"){
                    var sensors = 'co2_detector,gas_detector,smoke_detector,dust_detector';
                    axios.get('rooms/' + this.selectedRoom.ROOM_ID + '/devices?REG_FLAG=1&filter=DEVICE_TYPE:' + sensors)
                    .then(response => {
                        if(response.data.length > 0){
                            this.deviceList = response.data;
                        }else{
                            this.deviceList = [];
                        }
                    })
                    .catch(errors => {
                        console.log(errors);
                    });
                }
            },
            //Harvey 20190221
            //Function Name: getDeviceSourceCondition
            //Function Description: get device condition
            //PARAM: SOURCE_DEVICE_ID
            getDeviceSourceCondition(){
                this.bindingLists = {},
                axios.get('devices/' + this.selectedDevice.DEVICE_ID + '/binding-list-condition')
                .then(response => {
                    if(response.data.length > 0){
                        this.deviceConditionList = response.data[0].DATA;
                        this.selectedDeviceCondition = response.data[0].DATA[0];
                    }

                })
                .catch(errors => {
                    console.log(errors);
                });


                axios.get('devices/'+this.selectedDevice.DEVICE_ID)
                .then(response => {
                    this.selectedSourceDevice = response.data;
                })
                .catch(errors => {
                    console.log(errors);
                });
                this.showTargetInterface = 'floor';
            },
            //Function Name: chooseTargetFloor
            //Function Description: set chosen target floor
            //Param: data (floor)
            chooseTargetFloor(data){
                this.selectedFloorTarget = data;
                this.getRoomsTarget();
            },
            //Function Name: mapViewSelect
            //Function Description: select room from
            mapViewSelect(data){
                console.log(data);
                this.selectedRoom = data;
            },
            //Function Name: getPreviewbindingImage
            //Function Description: Show Image Preview for the device binding
            //Param(sourceDeviceType,targetDeviceType)
            getPreviewBindingImage(sourceDeviceType,targetDeviceType){
                var image_source = '';
                    if(sourceDeviceType == 'light_detector'){
                        if(targetDeviceType == 'wall_switch_3'){
                            image_source = 'img/light detector/light-detector-wall-switch.gif';
                        }else if(targetDeviceType == 'wall_switch_1'){
                            image_source = 'img/light detector/light-detector-wall-switch.gif';
                        }else if(targetDeviceType == 'curtain_1'){
                            image_source = 'img/light detector/light-detector-curtain.gif';
                        }else if(targetDeviceType == 'temp_light'){
                            image_source = 'img/light detector/light-detector-temperature-light.gif';
                        }else if(targetDeviceType == 'embedded_switch_2'){
                            image_source = 'img/light detector/light-detector-embedded-switch.gif';
                        }else{

                        }
                    }else if(sourceDeviceType == 'motion_detector'){
                        if(targetDeviceType == 'wall_switch_3'){
                            image_source = 'img/motion/motion-wall-switch.gif';
                        }else if(targetDeviceType == 'wall_switch_1'){
                            image_source = 'img/motion/motion-wall-switch.gif';
                        }else if(targetDeviceType == 'curtain_1'){
                            image_source = 'img/motion/motion-curtain.gif';
                        }else if(targetDeviceType == 'temp_light'){
                            image_source = 'img/motion/motion-temperature-light.gif';
                        }else if(targetDeviceType == 'embedded_switch_2'){
                            image_source = 'img/motion/motion-embedded-switch.gif';
                        }else if(targetDeviceType == 'water_valve'){
                            image_source = 'img/motion/motion-water-valve.gif';
                        }else if(targetDeviceType == 'gas_valve'){
                            image_source = 'img/motion/motion-gas-manipulator.gif';
                        }else{

                        }

                    }else if(sourceDeviceType == 'panic_button'){
                        if(targetDeviceType == 'wall_switch_3'){
                            image_source = 'img/panic/panic--wall-switch.gif';
                        }else if(targetDeviceType == 'wall_switch_1'){
                            image_source = 'img/panic/panic--wall-switch.gif';
                        }else if(targetDeviceType == 'curtain_1'){
                            image_source = 'img/panic/panic-curtain.gif';
                        }else if(targetDeviceType == 'temp_light'){
                            image_source = 'img/panic/panic--temperature-light.gif';
                        }else if(targetDeviceType == 'embedded_switch_2'){
                            image_source = 'img/panic/panic--embedded.gif';
                        }else if(targetDeviceType == 'water_valve'){
                            image_source = 'img/panic/panic--water-valve.gif';
                        }else if(targetDeviceType == 'door_lock'){
                            image_source = 'img/panic/panic-doorlock.gif';
                        }else if(targetDeviceType == 'gas_valve'){
                            image_source = 'img/panic/panic-gas-manipulator.gif';
                        }else{

                        }

                    }else if(sourceDeviceType == 'smoke_detector'){
                        if(targetDeviceType == 'wall_switch_3'){
                            image_source = 'img/smoke detector/smoke-detector-wall-switch.gif';
                        }else if(targetDeviceType == 'temp_hum'){
                            image_source = 'img/smoke detector/smoke-detector-temp.-humid.gif';
                        }else if(targetDeviceType == 'motion_detector'){
                            image_source = 'img/smoke detector/smoke-detector-motion.gif';
                        }else if(targetDeviceType == 'co2_detector'){
                            image_source = 'img/binding-animation.gif';
                        }else if(targetDeviceType == 'gas_detector'){
                            image_source = 'img/smoke detector/smoke-detector-gas-detector.gif';
                        }else if(targetDeviceType == 'dust_detector'){
                            image_source = 'img/smoke detector/smoke-detector-dust-sensor.gif';
                        }else if(targetDeviceType == 'gas_valve'){
                            image_source = 'img/smoke detector/smoke-detector-gas-manipulator.gif';
                        }else if(targetDeviceType == 'light_detector'){
                            image_source = 'img/smoke detector/smoke-detector-light-detector.gif';
                        }else{

                        }

                    }else if(sourceDeviceType == 'gas_detector'){
                        if(targetDeviceType == 'wall_switch_3'){
                            image_source = 'img/gas detector/gas-detector-wall-switch.gif';
                        }else if(targetDeviceType == 'temp_hum'){
                            image_source = 'img/gas detector/gas-detector-temp.-humid.gif';
                        }else if(targetDeviceType == 'motion_detector'){
                            image_source = 'img/gas detector/gas-detector-motion.gif';
                        }else if(targetDeviceType == 'co2_detector'){
                            image_source = 'img/binding-animation.gif';
                        }else if(targetDeviceType == 'smoke_detector'){
                            image_source = 'img/gas detector/gas-detector-smoke-detector.gif';
                        }else if(targetDeviceType == 'gas_valve'){
                            image_source = 'img/gas detector/gas-detector-gas-manipulator.gif';
                        }else if(targetDeviceType == 'light_detector'){
                            image_source = 'img/gas detector/gas-detector-light-detector.gif';
                        }else{

                        }

                    }else if(sourceDeviceType == 'co2_detector'){
                        if(targetDeviceType == 'wall_switch_3'){
                            image_source = 'img/C02/c02---wall-switch.gif';
                        }else if(targetDeviceType == 'gas_detector'){
                            image_source = 'img/C02/c02---gas-detector.gif';
                        }else if(targetDeviceType == 'smoke_detector'){
                            image_source = 'img/C02/c02---smoke-detector.gif';
                        }else if(targetDeviceType == 'gas_valve'){
                            image_source = 'img/C02/c02---gas-manipulator.gif';
                        }else if(targetDeviceType == 'motion_detector'){
                            image_source = 'img/C02/c02---motion-detector.gif';
                        }else if(targetDeviceType == 'temp_hum'){
                            image_source = 'img/C02/c02---temp.-humid.gif';
                        }else if(targetDeviceType == 'light_detector'){
                            image_source = 'img/C02/c02---light-detector.gif';
                        }else{

                        }

                    }else if(sourceDeviceType == 'temp_hum'){
                        if(targetDeviceType == 'ir_remote'){
                            image_source = 'img/temp humid/temp-humid-ir.gif';
                        }else if(targetDeviceType == 'curtain_1'){
                            image_source = 'img/temp humid/temp humid-curtain.gif';
                        }else if(targetDeviceType == 'temp_light'){
                            image_source = 'img/temp humid/temp-humid-temperature-light.gif';
                        }else{

                        }

                    }else if(sourceDeviceType == 'dust_detector'){
                        if(targetDeviceType == 'wall_switch_3'){
                            image_source = 'img/dust sensor/dust-sensor---wall-switch.gif';
                        }else if(targetDeviceType == 'wall_switch_1'){
                            image_source = 'img/dust sensor/dust-sensor---wall-switch.gif';
                        }else if(targetDeviceType == 'curtain_1'){
                            image_source = 'img/dust sensor/dust-sensor---curtain.gif';
                        }else if(targetDeviceType == 'temp_light'){
                            image_source = 'img/dust sensor/dust-sensor---temperature-light.gif';
                        }else if(targetDeviceType == 'gas_valve'){
                            image_source = 'img/dust sensor/dust-sensor---gas-manipulator.gif';
                        }else if(targetDeviceType == 'motion_detector'){
                            image_source = 'img/dust sensor/dust-sensor---motion.gif';
                        }else if(targetDeviceType == 'light_detector'){
                            image_source = 'img/dust sensor/dust-sensor---light-detector.gif';
                        }else if(targetDeviceType == 'temp_hum'){
                            image_source = 'img/dust sensor/dust-sensor---temp.-humid.gif';
                        }else{

                        }

                    }else{

                        image_source = 'img/binding-animation.gif';
                    }
                return image_source;
            },
            //Fucntion Name: clickBreadCrumb
            //Function Description: for breadcrumbs function
            //Param: data(category, floor, room, gateway, device)
            clickBreadcrumb(data){

            switch(data){
                case 'category':

                    this.bindingMode="viewBindingList";

                    this.gatewayList=[];
                    this.roomList=[];
                    this.deviceList=[];
                    this.deviceConditionList=[];

                    this.selectedFloor="";
                    this.selectedRoom="";
                    this.selectedGateway="";
                    this.selectedDevice="";
                    this.selectedCategory="Device Binding";

                    this.showGatewayList = false;
                    this.showDeviceList = false;
                    this.showCreateForm = false;
                    this.showTargetInterface = "room";
                    this.viewList="floors";

                    this.$forceUpdate();

                    break;
                case 'floor':

                    this.gatewayList=[];
                    this.deviceList=[];
                    this.deviceConditionList=[];

                    this.selectedFloor="";
                    this.selectedRoom="";
                    this.selectedGateway="";
                    this.selectedDevice="";

                    this.showGatewayList = false;
                    this.showDeviceList = false;
                    this.showCreateForm = false;
                    this.showTargetInterface = "room";
                    this.viewList="floors";

                    this.$forceUpdate();

                    break;
                case 'room':

                    this.gatewayList=[];
                    this.deviceList=[];
                    this.deviceConditionList=[];

                    this.selectedRoom="";
                    this.selectedGateway="";
                    this.selectedDevice="";
                    this.selectedRoomTarget="";

                    this.showGatewayList = false;
                    this.showDeviceList = false;
                    this.showCreateForm = false;
                    this.showTargetInterface = "room";
                    this.viewList="rooms";

                    this.$forceUpdate();

                    break;
                case 'gateway':

                    this.deviceList=[];
                    this.deviceConditionList=[];

                    this.selectedGateway="";
                    this.selectedDevice="";

                    this.showGatewayList = true;
                    this.showDeviceList = false;
                    this.showCreateForm = false;
                    this.showTargetInterface = "room";
                    this.$forceUpdate();

                    break;
                case 'device':



                    break;
            }

            },
            //function Name: changeSize
            //function Description: change size if isBinded = true
            //Param: data
            changeSize(data){
                this.isBinded = data;
            },
            //Function Name: tabShow
            //Function Description: change tab
            //Param: data
            tabShow(data){
                var details = '';
                this.$bus.$emit('selectedDeviceBindingData', details);
                if(data == 'Device Binding'){
                    this.$bus.$emit('changeBindingTab', data);
                }else{
                    this.$bus.$emit('changeSensorBindingTab', data);
                }
                this.selectedCategory = data;
            },

        },
        computed:{
            limitedFloors(){
                let from = this.listPagination.paginationLimit * this.listPagination.floorCurrentPage - 4;
                let to = this.listPagination.paginationLimit * this.listPagination.floorCurrentPage;
                return this.floorList.slice(from,to) ;
            },
            limitedRooms(){
                let from = this.listPagination.paginationLimit * this.listPagination.roomCurrentPage - 4;
                let to = this.listPagination.paginationLimit * this.listPagination.roomCurrentPage;
                return this.roomList.slice(from,to) ;
            },
            limitedRoomsTarget(){
                let from = this.listPagination.paginationLimit * this.listPagination.roomTargetCurrentPage - 4;
                let to = this.listPagination.paginationLimit * this.listPagination.roomTargetCurrentPage;
                return this.roomTargetList.slice(from,to) ;
            },
            limitedGateways(){
                let from = this.listPagination.paginationLimit * this.listPagination.gatewayCurrentPage - 4;
                let to = this.listPagination.paginationLimit * this.listPagination.gatewayCurrentPage;
                return this.gatewayList.slice(from,to) ;
            },
            limitedDevices(){
                let from = this.listPagination.paginationLimit * this.listPagination.deviceCurrentPage - 4;
                let to = this.listPagination.paginationLimit * this.listPagination.deviceCurrentPage;
                return this.deviceList.slice(from,to) ;
            }
        },
        mounted () {
            this.$i18n.locale = this.$parent.locale;
            Echo.channel('test').listen('.testBindingEvent', (e) => {
                console.log(e.data);
            })

        },
    };
</script>