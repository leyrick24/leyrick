<template>
    <div>
        <!-- content-wrapper -->
        <div class="wrapper hardware-img-bg">
            <!-- content-fluid -->
            <div class="container-fluid">
                <div class="d-flex justify-content-between py-3">
                    <div>
                        <div>{{$t('device.header')}}</div>
                    </div>
                    <clock :locale="$i18n.locale"></clock>
                </div>
                <div class="row pb-5 h-826">
                    <div class="pb-3" :class="isUnregister ? 'col-sm-12 grow' : 'col-sm-6 shrink'">
                        <!-- tab-navigation -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="nav nav-tabs nav-line w-100" role="tablist">
		                        <a class="nav-item nav-link nav-header-blue active" data-toggle="tab" href="#system-device-list" role="tab" aria-controls="nav-device" aria-selected="true" @click="tabShow('sysDevice')"> {{$t('device.sysDevice')}}</a>
		                        <a class="nav-item nav-link nav-header-blue" data-toggle="tab" href="#modbus-device-list" role="tab" aria-controls="nav-device" aria-selected="false" @click="tabShow('modDevice')"> {{$t('device.modDevice')}}</a>
		                    </div>
                        </div>
                        <div class="tab-content tab-bg-blue" id="nav-tabContent">
                            <div class="tab-pane fade show active" style="height:705px" id="system-device-list" role="tabpanel">
                        		<DeviceManagement @change-size="changeSize($event)"></DeviceManagement>
                            </div>
                            <div class="tab-pane fade" style="height:705px" id="modbus-device-list" role="tabpanel">
                        		<ElectricMeterDeviceManagement @change-size="changeSize($event)"></ElectricMeterDeviceManagement>
                            </div>
                        </div>
                        <!-- divtab-content-end -->
                    </div>
                    <DeviceDetails></DeviceDetails>
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
    import DeviceManagement from './DeviceManagement.vue';
    import ElectricMeterDeviceManagement from './ElectricMeterDeviceManagement.vue';
    import DeviceDetails from './DeviceDetails.vue';
    export default {
        //initialize component
        components: {DeviceManagement, ElectricMeterDeviceManagement, DeviceDetails},
        data() {
            return {
                isUnregister: false,
                user:'',
            }
        },
        mounted(){
            this.$i18n.locale = this.$parent.locale;
        },
        methods: {
            //Function Name: changeSize
            //Function Description: Change table div size
            //Param: data (boolean)
        	changeSize(data){
                this.isUnregister = data;
            },
            //Function Name: tabShow
            //Function Description: change tab shown
            //Param: data (sysDevice or modDevice)
            tabShow(data){
                var details = '';
                this.$bus.$emit('selectedDeviceData', details);
                if(data == 'sysDevice'){
                    this.$bus.$emit('changeDeviceTab', data);
                }else{
                    this.isUnregister = false;
                }
            }
        },
    };
</script>