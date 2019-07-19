
<!--  <System Name> iBMS
 <Program Name> FloorManagement.vue

 <Create> 2018.9.27  TP Harvey
 <Update> 2018.9.28  TP Harvey Table View
          2018.10.26 TP Harvey Bug Fix
          2018.10.2  TP Harvey Bug Fix
          2018.11.5  TP Harvey Remove Device Mapping
          2019.5.23  TP Jethro Implemented coding standard and removed unecessary code
 -->
<template>
    <div>
        <!-- content-wrapper -->
        <div class="wrapper hardware-img-bg">
            <!-- content-fluid -->
            <div class="container-fluid container-bg">
                <!-- breadcumd -->
                <div class="row">
                    <div class="col-md-6 my-2">
                        <h6 class="my-1 right-250">
                            {{$t('floor.header')}}
                        </h6>
                    </div>
                    <div class="col-md-6 my-2">
                        <div class="my-1">
                            <clock size="large" :locale="$i18n.locale"></clock>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <!-- Nav Bar Header -->
                         <div class="d-flex justify-content-between align-items-center">
                            <div class="nav nav-tabs nav-line w-100" role="tablist">
                                <a class="nav-item nav-link nav-header-blue active" data-toggle="tab" href="#gateway-list" role="tab" aria-controls="nav-gateway" aria-selected="true">{{$t('user.users')}}</a>
                            </div>
                            <!-- Search Text -->
                            <div class="float-right">
                                <div class="input-group border border-secondary rounded">
                                    <input v-model="floorSearch" type="text" class="form-control form-control-sm" :placeholder="$t('floor.searchFloor')">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fa fa-search"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Floor Table -->
                        <div class="tab-content tab-bg-2 h-470" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="floor-tab" role="tabpanel">
                                <!-- gateway component -->
                                <Floor :floorSearch="floorSearch" @chooseFloor="chooseFloor" @openFloorPlotting="openFloorPlotting"></Floor>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <!-- Nav Bar Header -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="nav nav-tabs nav-line w-100" role="tablist">
                                <a id="nav-room-tab" @click="changeView(2)"  class="nav-item nav-link nav-header-gray px-4 active" data-toggle="tab" href="#nav-room" role="tab" aria-controls="nav-room" aria-selected="false">
                                    {{$t('floor.room')}}
                                </a>
                            </div>
                            <!-- Search Text -->
                            <div class="float-right">
                                <div class="input-group border border-secondary rounded">
                                    <input v-model="roomSearch" type="text" class="form-control form-control-sm" :placeholder="$t('floor.searchRoom')">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fa fa-search"></i></span>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- Room Table -->
                        <div class="tab-content tab-bg-2 h-470" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-room" role="tabpanel" aria-labelledby="nav-room-tab">
                                <Room :locale="$i18n.locale" :roomSearch="roomSearch" :floorID="floorID" @openDeviceMapping="openDeviceMapping"></Room>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <transition name="fade">
                            <div v-if="bottomSection!=''">
                                <div v-if="bottomSection=='device-mapping'" class="tab-bg-2">
                                    <DeviceMapping :roomData="chosenRoom" :floorData="chosenFloor"></DeviceMapping>
                                </div>
                                <div v-if="bottomSection=='floor-plotting'" class="tab-bg-2">
                                    <FloorPlotting :floorData="chosenFloor" ></FloorPlotting>
                                </div>
                            </div>
                        </transition>
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

    import Floor from './Floor/Floor.vue';
    import Room from './Room/Room.vue';

    //Device Mapping
    import DeviceMapping from "./Room/DeviceMapping.vue";
    import FloorPlotting from "./Floor/FloorPlotting.vue"

    export default {
        //initialize component
        components: {Floor,Room,DeviceMapping,FloorPlotting},
        data() {
            return {
                floorID:'',          //Will temporarily use for device Plotting from Floor View to Room View
                bottomSection:'',
                chosenFloor:'',
                chosenRoom:'',
                floorSearch:'',
                roomSearch:'',
                user:'',
            }
        },
        created(){
        },
        beforeDestroy(){
            this.$bus.$off("directPlotDevice");
        },
        methods: {
            //Function Name: chooseFloor
            //Function Description: Choose Floor call from Floor Component
            //Param: data (floor)
            chooseFloor(data){

                this.bottomSection ='floor-plotting';
                this.floorID = data['FLOOR_ID'];
                this.chosenFloor = data;
            },
            //Function Name: chooseRoom
            //Function Description: Choose Room call from Room Component
            //Param: data (room)
            chooseRoom(data){
                this.chosenRoom = data;
            },
            //Function Name: openFloorPlotting
            //Function Description: Open Floor Mapping
            //Param: data (floor)
            openFloorPlotting(data){
                this.floorID = data['FLOOR_ID'];
                this.chosenFloor = data;
                this.bottomSection ='floor-plotting';
            },
            //Function Name: openDeviceMapping
            //Function Description: Open Device Mapping
            //Param: data (room)
            openDeviceMapping(data){
                this.chosenRoom = data;
                this.bottomSection ='device-mapping';
            },
        },
        mounted () {
            this.$i18n.locale = this.$parent.locale;
        }
    };
</script>