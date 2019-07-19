<template>
    <div>
        <!-- content-wrapper -->
        <div class="wrapper text-color-white">
            <!-- content-fluid -->
            <div class="container-fluid">
                <div class="nav nav-tabs w-100" role="tablist">
                    <div class="d-flex justify-content-between align-items-center nav-line">
                        <a class="nav-item nav-link px-4 active" data-toggle="tab" role="tab"  aria-controls="nav-binding" aria-selected="true">Create Sensor to Device Binding</a>
                    </div>
                </div>

                <div class="d-flex">
                    <div class="col-sm-4">
                        <div class="floor-bar h-300">
                            <b-table small v-if="showList == ''"
                                           class="custom-table"
                                           :fields="table.floors"
                                           :items="listData"
                                           :per-page="table.perPage"
                                           :current-page="table.currentPage"
                                           @row-clicked="getRooms">
                            </b-table>
                            

                        </div>
                        
                    </div>
                    
                </div>





<!--                 <div class="row">
                    <div class="col-md-8 mb-3 mt-4"> -->
                        <!-- tab-navigation -->
                        <!-- <div class="d-flex justify-content-between align-items-center"> -->


                            <!-- div-tab-list -->
                            <!-- <div class="nav nav-tabs w-100" role="tablist">
                                
                            <div class="nav nav-tabs w-100" role="tablist">
                                <a class="nav-item nav-link nav-header-dark-gray active" data-toggle="tab" href="#binding-device-list" role="tab" aria-controls="nav-binding" aria-selected="true" @click="tabShow('devBinding')">Sensor to Device</a>
                                 <a class="nav-item nav-link nav-header-dark-gray" data-toggle="tab" href="#binding-sensor-list" role="tab" aria-controls="nav-binding" aria-selected="false" @click="tabShow('senBinding')">Sensor to Sensor</a>
                            </div> -->
                            <!-- div-tablist-end -->
                        <!-- </div> -->
<!--                         <div class="tab-content tab-bg-dark-gray" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="binding-device-list" role="tabpanel">
                                <DeviceBindingManagement @change-size="changeSize($event)"></DeviceBindingManagement>
                            </div>
                            <div class="tab-pane fade" id="binding-sensor-list" role="tabpanel">
                                <SensorBindingManagement @change-size="changeSize($event)"></SensorBindingManagement>
                            </div>
                        </div> -->
                        <!-- divtab-content-end -->
<!--                     </div>
                </div> -->
<!--                 <div class="tab-bg-blue col-sm-12">
                    <div v-if="selectedCategory == 'devBinding'">
                        <CreateBindingDevice></CreateBindingDevice>
                    </div>
                    <div v-else>
                        <CreateBindingSensor></CreateBindingSensor>
                    </div>
                </div> -->
            </div>
            <!-- content-fluid-end -->
        </div>
        <!-- content-warpper-end -->
    </div>
    
</template>
<script>
    //import components
    import DeviceBindingManagement from './DeviceBindingManagement.vue';
    import SensorBindingManagement from './SensorBindingManagement.vue';
    import CreateBindingDevice from './CreateBindingDevice.vue';
    import CreateBindingSensor from './CreateBindingSensor.vue';
    export default {
        //initialize component
        components: {DeviceBindingManagement, SensorBindingManagement, CreateBindingDevice, CreateBindingSensor},
        data() {
            return {
                table:{
                    floors:[
                        {key:'FLOOR_NAME'},
                    ],
                    rooms:[
                        {key:'ROOM_NAME'},
                    ],
                    devices:[
                        {key:'DEVICE_NAME'},
                    ],
                    target:[
                        {key:'TARGET_DEVICE'},
                    ],
                    totalRows:0,
                    perPage:4,
                    currentPage:1
                },
                isBinded: false,
                selectedCategory: 'devBinding',
                showList:'',
                //table 1 data
                listData:'',
            }
        },
        methods: {
            changeSize(data){
                this.isBinded = data;
            },
            tabShow(data){
                var details = '';
                this.$bus.$emit('selectedDeviceBindingData', details);
                if(data == 'devBinding'){
                    this.$bus.$emit('changeBindingTab', data);
                }else{
                    this.$bus.$emit('changeSensorBindingTab', data);
                }
                this.selectedCategory = data;
            },            //get room
            //PARAM: FLOOR_ID
            getRooms(key){
                var details = '';
                this.$bus.$emit('selectedApplianceData', details);

                this.irLearning.FLOOR = key,
                this.irLearning.ROOM_ID = null,
                this.irLearning.GATEWAY_ID = null,
                this.irLearning.SOURCE_DEVICE_ID = null,
                this.irLearning.TARGET_DEVICE = null,
                this.irLearning.TARGET_BRAND = null,
                this.irLearning.LEARNING_NAME = null,
                this.irLearning.OLD_LEARNING_VALUE = null,
                this.irLearning.LEARNING_VALUE = null,
                this.showList = 'rooms',
                this.gateways = {},
                this.devices = {}

                axios.get('floors/'+this.irLearning.FLOOR.FLOOR_ID+'/rooms')
                .then(response => {
                    this.rooms = response.data,
                    this.listData = response.data
                    console.log(this.listData);
                })
                .catch(errors => {
                    console.log(errors);
                });
            },
        },
        mounted () {
            Echo.channel('test').listen('.testBindingEvent', (e) => {
                console.log(e.data);
            })

        },
    };
</script>