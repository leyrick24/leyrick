<template>
    <div>
        <!-- content-wrapper -->
        <div class="wrapper hardware-img-bg">
            <!-- content-fluid -->
            <div class="container-fluid">
                <div class="d-flex justify-content-between py-3">
                    <div>
                        <div>{{$t('gateway.header')}} </div>
                    </div>
                    <clock :locale="$i18n.locale"></clock>
                </div>
                <div class="row pb-5 h-826">
                    <div class="pb-3" :class="isUnregister ? 'col-sm-12 grow' : 'col-sm-6 shrink'">
                        <!-- tab-navigation -->
                        <div class="d-flex justify-content-between align-items-center">
                            <!-- div-tab-list -->
                            <div class="nav nav-tabs nav-line" role="tablist">
                                <a class="nav-item nav-link nav-header-blue active" data-toggle="tab" href="#system-gateway-list" role="tab" aria-controls="nav-gateway" aria-selected="true" @click="tabShow('sysGateway')">{{$t('gateway.sysGateway')}}</a>
                                <a class="nav-item nav-link nav-header-blue" data-toggle="tab" href="#modbus-gateway-list" role="tab" aria-controls="nav-gateway" aria-selected="false" @click="tabShow('modGateway')">{{$t('gateway.modbusGateway')}}</a>
                            </div>
                            <!-- div-tablist-end -->
                        </div>

                        <!-- tab-content -->
                        <!-- div-tab-content -->
                        <div class="tab-content tab-bg-blue" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="system-gateway-list" role="tabpanel">
                                <!-- gateway component -->
                                <GatewayManagement @change-size="changeSize($event)"></GatewayManagement>
                            </div>
                            <div class="tab-pane fade" id="modbus-gateway-list" role="tabpanel">
                                <ModBusGatewayManagement @change-size="changeSize($event)"></ModBusGatewayManagement>
                            </div>
                        </div>
                        <!-- divtab-content-end -->
                    </div>
                    <GatewayDetails></GatewayDetails>
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
    import GatewayManagement from './GatewayManagement.vue';
    import ModBusGatewayManagement from './ModBusGatewayManagement.vue';
    import GatewayDetails from './GatewayDetails.vue';
    export default {
        //initialize component
        components: { GatewayManagement, ModBusGatewayManagement, GatewayDetails},
        data() {
            return {
                isUnregister: false,
                user:'',
            }
        },
        methods: {
            //Function Name: changeSize
            //Function Description: change table size if unregistered category
            //Param: data (boolean)
            changeSize(data){
                this.isUnregister = data;
            },
            //Function Name: tabShow
            //Function Description: Change tab
            //Param: data
            tabShow(data){
                var details = '';
                this.$bus.$emit('selectedData', details);
                if(data == 'sysGateway'){
                    this.$bus.$emit('changeTab', data);
                }else{
                    this.isUnregister = false;
                }
            }
        },
        mounted () {
            //set child locale to parent locale
            this.$i18n.locale = this.$parent.locale;
        }
    };
</script>