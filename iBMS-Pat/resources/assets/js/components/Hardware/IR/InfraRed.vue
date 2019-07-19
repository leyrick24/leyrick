<template>
    <div>
        <!-- content-wrapper -->
        <div class="wrapper hardware-img-bg">
            <!-- content-fluid -->
            <div class="container-fluid">
                <div class="d-flex justify-content-between py-3">
                    <div>
                        <div>Hardware Management / IR Management </div>
                    </div>
                    <clock></clock>
                </div>
                <div class="row pb-1 h-826">
                    <div class="col-lg-6 mb-3">
                        <!-- tab-navigation -->
                        <div class="d-flex justify-content-between align-items-center">
                            <!-- div-tab-list -->
                            <div class="nav nav-tabs w-100" role="tablist">
                                <a class="nav-item nav-link nav-header-blue active" data-toggle="tab" href="#create-list" role="tab" aria-controls="nav-gateway" aria-selected="true" @click="tabShow('addIrList')">Add IR List</a>
                                <a class="nav-item nav-link nav-header-blue" data-toggle="tab" href="#update-list" role="tab" aria-controls="nav-device" aria-selected="false" @click="tabShow('updateIrList')">Update IR List</a>
                            </div>
                            <!-- div-tablist-end -->
                        </div>

                        <!-- tab-content -->
                        <!-- div-tab-content -->
                        <div class="tab-content tab-bg-blue" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="create-list" role="tabpanel">
                                <!-- IR component -->
                                <CreateIrLearning></CreateIrLearning>
                            </div>
                            <div class="tab-pane fade" id="update-list" role="tabpanel">
                                <UpdateIrLearning></UpdateIrLearning>
                            </div>
                        </div>
                        <!-- divtab-content-end -->
                    </div>
                    <RemoteDetails></RemoteDetails>
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
    import CreateIrLearning from './CreateIrLearning.vue';
    import UpdateIrLearning from './UpdateIrLearning.vue';
    import RemoteDetails from './RemoteDetails.vue';
    export default {
        //initialize component
        components: {CreateIrLearning,UpdateIrLearning, RemoteDetails},
        created() {
            this.$bus.$on('commandDevice',data => {
                //Command to DB
                this.commandDevice(data['GATEWAY_ID'],data['DEVICE_ID'],data['DEVICE_TYPE'],data['key'],data['command'],data['value'],data['addlValue']);
            });
        },
        data() {
            return {
                user:'',
            }
        },
        methods: {
            //Function Name: tabShow
            //Function Description: clear data on tab change
            //Param: data
            tabShow(data){
                //clear data if changed tab
                var details = '';
                this.$bus.$emit('selectedApplianceData', details);
                if(data == 'addIrList'){
                    this.$bus.$emit('changeTabAdd', data);
                }else{
                    this.$bus.$emit('changeTabUpdate', data);
                }
            },
            //Function Name: commandDevice
            //Function Description: Command Device
            //Param: gateway_id  : Gateway ID
            //       device_id   : Device ID
            //       device_type : device Type
            //       key         : key
            //       command     : Command
            //       value       : Value
            commandDevice(gateway_id,device_id,device_type,key,command,value,irVal){
                //Initialize varables
                var gatewayID = gateway_id;
                var deviceID = device_id;
                var learningVal = irVal;

                //this.retrieveDeviceData(device_id,device_type,'send');
                console.log(command + " : " + value);

                console.log(gatewayID);
                console.log(deviceID);
                console.log(command);
                console.log(value);
                console.log(learningVal);
                axios.post('instructions/send',
                {'GATEWAY_ID' : gatewayID,
                 'DEVICE_ID' : deviceID,
                 'COMMAND': command,
                 'VALUE' : value,
                 'addlValue' : learningVal})
                .then(response=>{
                    console.log(response.data);
                });
            },
        },
        mounted () {

        },
    };
</script>