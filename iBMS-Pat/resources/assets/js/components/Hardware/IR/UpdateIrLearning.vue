<template>
    <div>
        <div class="d-flex">
            <div class="col-sm-12">
                <h5 class="py-2">Selections</h5>
                <div class="form-group">
                    <label class="label">Floor</label>
                    <div class="">
                        <select class="custom-select" v-model="irLearning.FLOOR_ID" @change="getRooms">
                            <option v-for="floor,index in floors" :key="index" :value="floor.FLOOR_ID">{{floor.FLOOR_NAME}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="label">Room</label>
                    <div class="">
                        <select class="custom-select" v-model="irLearning.ROOM_ID" @change="getGateways" :disabled="roomDisabled">
                            <option v-for="room,index in rooms" :key="index" :value="room.ROOM_ID">{{room.ROOM_NAME}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="label">Gateway</label>
                    <div class="">
                        <select class="custom-select" v-model="irLearning.GATEWAY_ID" @change="getSourceDevices" :disabled="gatewayDisabled">
                            <option v-for="gateway,index in gateways" :key="index" :value="gateway.GATEWAY_ID">{{gateway.GATEWAY_NAME}}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="label">IR Device</label>
                    <div class="">
                        <select class="custom-select" v-model="irLearning.SOURCE_DEVICE_ID" @change="getIrList" :disabled="sourceDisabled">
                            <option v-for="device,index in devices" :key="index" :value="device.DEVICE_ID">{{device.DEVICE_NAME}}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="label">Appliances</label>
                    <div class="">
                        <select class="custom-select" v-model="irLearning.TARGET_DEVICE" @change="applianceList" :disabled="targetDisabled">
                            <option v-for="appliance,index in appliances" :key="index" :value="appliance.TARGET_DEVICE">{{appliance.TARGET_DEVICE}}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="label">Appliance's Brand</label>
                    <div class="">
                        <select class="custom-select" v-model="irLearning.TARGET_BRAND" @change="getlearningList" :disabled="targetBrandDisabled">
                            <option v-for="brand,index in brands" :key="index" :value="brand.BRAND_NAME">{{brand.BRAND_NAME}}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="label">IR Learning Name</label>
                    <div class="">
                        <select class="custom-select" v-model="irLearning.LEARNING_NAME" @change="getInitalValue(irLearning)" :disabled="irNameDisabled">
                            <option v-for="irSlotName,index in irSlotsName" :key="index" :value="irSlotName">{{irSlotName}}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <!-- <label class="label">Update IR Value</label> -->
                    <div class="d-flex">
                        <div class="col-sm-6 pl-0">
                            <label>IR Value From</label>
                            <select class="custom-select" v-model="irLearning.OLD_LEARNING_VALUE" @change="" :disabled="irValueDisabled">
                                <option v-for="" :key="" :value="irLearning.OLD_LEARNING_VALUE">{{irLearning.OLD_LEARNING_VALUE}}</option>
                            </select>
                        </div>
                        <div class="col-sm-6 pr-0">
                            <label>To</label>
                            <select class="custom-select" v-model="irLearning.LEARNING_VALUE" @change="" :disabled="irValueDisabled">
                                <option v-for="irSlot,index in irSlots" :key="index" :value="irSlot">{{irSlot}}</option>
                            </select>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="modal-footer padding-05">
            <button class="btn background-orange" @click="saveIrList" :class="{ disabled: saveDisabled }" :disabled="saveDisabled">
                <!-- display loading animation -->
                <span class="pull-left" v-if="loading">
                    <i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
                </span>
                <span> {{ btn_text }} </span>
            </button>
            <button type="button" @click="reset" class="btn btn-secondary">Clear</button>
        </div>

    </div>
</template>

<script>
    export default{
        //get the attributes from other components
        components: { },
        created (){
            this.getFloors();
            this.$bus.$on('changeTabUpdate' , data =>{
                this.getFloors();
            });
        },
        data(){
            return{
                irLearning:{
                    FLOOR_ID: null,
                    ROOM_ID: null,
                    GATEWAY_ID: null,
                    TARGET_BRAND: null,
                    TARGET_DEVICE: null,
                    LEARNING_NAME: null,
                    OLD_LEARNING_VALUE: null,
                    LEARNING_VALUE: null,
                    SOURCE_DEVICE_ID:null
                },
                learning_names: [
                    {CONDITION:'POWER_OFF'},
                    {CONDITION:'POWER_ON'},
                    {CONDITION:'TEMP_16'},
                    {CONDITION:'TEMP_17'},
                    {CONDITION:'TEMP_18'},
                    {CONDITION:'TEMP_19'},
                    {CONDITION:'TEMP_20'},
                    {CONDITION:'TEMP_21'},
                    {CONDITION:'TEMP_22'},
                    {CONDITION:'TEMP_23'},
                    {CONDITION:'TEMP_24'},
                    {CONDITION:'TEMP_25'},
                    {CONDITION:'TEMP_26'},
                    {CONDITION:'TEMP_27'},
                    {CONDITION:'TEMP_28'},
                    {CONDITION:'TEMP_29'},
                    {CONDITION:'TEMP_30'},
                ],
                brands: [
                    {BRAND_NAME:'Panasonic'},
                    {BRAND_NAME:'Midea'},
                ],
                appliances: [
                    {TARGET_DEVICE:'Aircon'},
                    {TARGET_DEVICE:'TV'},
                ],
                irSlots:[],
                irSlotsName: [],
                irSlotsVal: [],
                floors:{},
                rooms:{},
                gateways:{},
                devices:{},
                irLists:{},

                loading: false,
                btn_text: 'Save',
                allDeviceSelected: false,
                allDeviceTime: 5,

            }
        },
        methods: {
            //Function Name: getFloors
            //Function Description: get floor
            getFloors(){
                var details = '';
                this.$bus.$emit('selectedApplianceData', details);

                this.irLearning.FLOOR_ID = null,
                this.irLearning.ROOM_ID = null,
                this.irLearning.GATEWAY_ID = null,
                this.irLearning.SOURCE_DEVICE_ID = null,
                this.irLearning.TARGET_DEVICE = null,
                this.irLearning.TARGET_BRAND = null,
                this.irLearning.LEARNING_NAME = null,
                this.irLearning.OLD_LEARNING_VALUE = null,
                this.irLearning.LEARNING_VALUE = null,
                this.gateways = {},
                this.devices = {}

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
                var details = '';
                this.$bus.$emit('selectedApplianceData', details);

                this.irLearning.ROOM_ID = null,
                this.irLearning.GATEWAY_ID = null,
                this.irLearning.SOURCE_DEVICE_ID = null,
                this.irLearning.TARGET_DEVICE = null,
                this.irLearning.TARGET_BRAND = null,
                this.irLearning.LEARNING_NAME = null,
                this.irLearning.OLD_LEARNING_VALUE = null,
                this.irLearning.LEARNING_VALUE = null,
                this.gateways = {},
                this.devices = {}

                axios.get('floors/'+this.irLearning.FLOOR_ID+'/rooms')
                .then(response => {
                    this.rooms = response.data
                })
                .catch(errors => {
                    console.log(errors);
                });
            },
            //Function Name: getGateways
            //Function Description: get gateway
            //PARAM: ROOM_ID
            getGateways(){
                var details = '';
                this.$bus.$emit('selectedApplianceData', details);

                this.irLearning.GATEWAY_ID = null,
                this.irLearning.SOURCE_DEVICE_ID = null,
                this.irLearning.TARGET_DEVICE = null,
                this.irLearning.TARGET_BRAND = null,
                this.irLearning.LEARNING_NAME = null,
                this.irLearning.OLD_LEARNING_VALUE = null,
                this.irLearning.LEARNING_VALUE = null,
                this.devices = {}

                axios.get('rooms/'+this.irLearning.ROOM_ID+'/gateways')
                .then(response => {
                    this.gateways = response.data
                })
                .catch(errors => {
                    console.log(errors);
                });
            },
            //Function Name: getSourceDevices
            //Function Description: get source device
            //PARAM: GATEWAY_ID
            getSourceDevices(){
                var details = '';
                this.$bus.$emit('selectedApplianceData', details);

                this.irLearning.SOURCE_DEVICE_ID = null,
                this.irLearning.TARGET_DEVICE = null,
                this.irLearning.TARGET_BRAND = null,
                this.irLearning.LEARNING_NAME = null,
                this.irLearning.OLD_LEARNING_VALUE = null,
                this.irLearning.LEARNING_VALUE = null,
                this.irList = {}

                axios.get('gateways/'+this.irLearning.GATEWAY_ID+'/devices?filter=DEVICE_TYPE:ir_remote')
                .then(response => {
                    this.devices = response.data
                })
                .catch(errors => {
                    console.log(errors);
                });
            },
            //Function Name: getIrList
            //Function Description: Get ir learning list
            getIrList(){
                var details = '';
                this.$bus.$emit('selectedApplianceData', details);

                this.irLearning.TARGET_DEVICE = null,
                this.irLearning.TARGET_BRAND = null,
                this.irLearning.LEARNING_NAME = null,
                this.irLearning.OLD_LEARNING_VALUE = null,
                this.irLearning.LEARNING_VALUE = null,
                this.irSlotsName = []

                axios.get('learning-list')
                .then(response => {
                    this.irLists = response.data

                    //loops irLists per Object
                    for(var j in this.irLists){
                        var sourceID = this.irLists[j].SOURCE_DEVICE_ID;
                        var listsID = this.irLearning.SOURCE_DEVICE_ID;
                        if(sourceID == listsID){
                            if(this.irLists[j].TARGET_DEVICE == this.irLearning.TARGET_DEVICE){
                                if(this.irLists[j].TARGET_BRAND == this.irLearning.TARGET_BRAND){
                                    this.irSlotsName.push(this.irLists[j].LEARNING_NAME)
                                    this.irSlotsName.sort()
                                }
                            }
                        }

                    }
                })
                .catch(errors => {
                    console.log(errors);
                });
            },
            //Function Name: applianceList
            //Function Description: updates ir learning list
            applianceList(){
                var details = '';
                this.$bus.$emit('selectedApplianceData', details);
                this.irLearning.TARGET_BRAND = null,
                this.irLearning.LEARNING_NAME = null,
                this.irLearning.OLD_LEARNING_VALUE = null,
                this.irLearning.LEARNING_VALUE = null,
                this.irSlotsName = []

                axios.get('learning-list')
                .then(response => {
                    this.irLists = response.data

                })
                .catch(errors => {
                    console.log(errors);
                });
            },
            //Function Name: getLearningList
            //Function Description: push
            getlearningList(){
                var details = '';
                this.$bus.$emit('selectedApplianceData', details);
                this.irLearning.LEARNING_NAME = null,
                this.irLearning.OLD_LEARNING_VALUE = null,
                this.irLearning.LEARNING_VALUE = null,
                this.irSlotsName = []

                axios.get('learning-list')
                .then(response => {
                    this.irLists = response.data

                    //loops irLists per Object
                    for(var j in this.irLists){
                        var sourceID = this.irLists[j].SOURCE_DEVICE_ID;
                        var listsID = this.irLearning.SOURCE_DEVICE_ID;
                        if(sourceID == listsID){
                            if(this.irLists[j].TARGET_DEVICE == this.irLearning.TARGET_DEVICE){
                                if(this.irLists[j].TARGET_BRAND == this.irLearning.TARGET_BRAND){
                                    this.irSlotsName.push(this.irLists[j].LEARNING_NAME)
                                    this.irSlotsName.sort()
                                }
                            }
                        }

                    }
                })
                .catch(errors => {
                    console.log(errors);
                });
            },
            //Function Name: getInitaValue
            //Function Description: update irslots taken
            getInitalValue(details){
                this.$set(details, "category", "updateIrList");
                this.$bus.$emit('selectedApplianceData', details);

                this.irLearning.OLD_LEARNING_VALUE = null,
                this.irLearning.LEARNING_VALUE = null,
                this.irSlots = []

                axios.get('learning-list')
                .then(response => {
                    this.irLists = response.data

                    //put value 0-629
                    for (var i = 0; i < 630; i++) {
                        this.irSlots.push(i)
                    }

                    //loops irLists per Object
                    for(var j in this.irLists){
                        var sourceID = this.irLists[j].SOURCE_DEVICE_ID;
                        var listsID = this.irLearning.SOURCE_DEVICE_ID;
                        if(sourceID == listsID){
                            if(this.irLists[j].TARGET_DEVICE == this.irLearning.TARGET_DEVICE){
                                if(this.irLists[j].TARGET_BRAND == this.irLearning.TARGET_BRAND){
                                    if(this.irLists[j].LEARNING_NAME == this.irLearning.LEARNING_NAME){
                                        this.irLearning.OLD_LEARNING_VALUE = this.irLists[j].LEARNING_VALUE
                                    }
                                }
                                let index = this.irSlots.indexOf(this.irLists[j].LEARNING_VALUE)
                                this.irSlots.splice(index, 1)
                            }
                        }
                    }
                })
                .catch(errors => {
                    console.log(errors);
                });

            },
            //Function Name: updateIrList
            //Function Description: update Ir learning list and splice taken values
            updateIrList(){
                axios.get('learning-list')
                .then(response => {
                    this.irLists = response.data,
                    this.irSlots = []

                    //put value 0-629
                    for (var i = 0; i < 630; i++) {
                        this.irSlots.push(i)
                    }
                    //loops irLists
                    for(var j in this.irLists){
                        if(this.irLists[j].SOURCE_DEVICE_ID == this.irLearning.SOURCE_DEVICE_ID){
                            let index = this.irSlots.indexOf(this.irLists[j].LEARNING_VALUE)
                            this.irSlots.splice(index, 1)
                        }
                    }
                })
                .catch(errors => {
                    console.log(errors);
                });
            },
            //Function Name: saveIrList
            //Function Description: save binding function
            saveIrList(){
                this.loading = true;
                this.btn_text = 'Saving';
                this.commandDevice('learning',1,this.irLearning.LEARNING_VALUE);
                axios({
                  url: 'learning-list/update',
                  method: 'post',
                  data: {
                    'LEARNING_LIST':this.irLearning
                  }
                })
                .then((response)=>
                    setTimeout(() => {
                       this.close();
                        var details = '';
                        this.$bus.$emit('selectedApplianceData', details);
                    }, 1500)
                )
                .catch((error) => console.log(error));
            },
            //Function Name: commandDevice
            //Function Description: Send to Room.vue to command and Save to database
            commandDevice(command,value,learningValue){
                //Initialize varables
                var gateway_id = this.irLearning['GATEWAY_ID'];
                var device_id = this.irLearning['SOURCE_DEVICE_ID'];
                var device_type = 'ir_remote';
                var key = this.irLearning['key'];

                this.$bus.$emit('commandDevice',{'GATEWAY_ID':gateway_id,
                                                'DEVICE_ID':device_id,
                                                'DEVICE_TYPE':device_type,
                                                'key':key,
                                                'command':command,
                                                'value':value,
                                                'addlValue':learningValue});
            },
            //Function Name: close
            //Function Description: close modal function
            close(){
                this.irLearning.FLOOR_ID = null,
                this.irLearning.ROOM_ID = null,
                this.irLearning.GATEWAY_ID = null,
                this.irLearning.TARGET_DEVICE = null,
                this.irLearning.TARGET_BRAND = null,
                this.irLearning.LEARNING_NAME = null,
                this.irLearning.OLD_LEARNING_VALUE = null,
                this.irLearning.LEARNING_VALUE = null,
                this.irLearning.SOURCE_DEVICE_ID = null,
                this.rooms = {},
                this.gateways = {},
                this.devices = {},
                this.irLists = {},
                this.irSlots = [],

                this.loading = false,
                this.btn_text = 'Save',
                this.$emit('loaddata', '');
                this.$emit('close')
                this.$swal({
                    position: 'center',
                    type: 'success',
                    title: 'Successfully Updated',
                    showConfirmButton: false,
                    timer: 2000
                });
            },
            //Function Name: reset
            //Function Description: close modal function and reset page data
            reset(){
                var details = '';
                this.$bus.$emit('selectedApplianceData', details);
                this.irLearning.FLOOR_ID = null,
                this.irLearning.ROOM_ID = null,
                this.irLearning.GATEWAY_ID = null,
                this.irLearning.TARGET_DEVICE = null,
                this.irLearning.TARGET_BRAND = null,
                this.irLearning.LEARNING_NAME = null,
                this.irLearning.OLD_LEARNING_VALUE = null,
                this.irLearning.LEARNING_VALUE = null,
                this.irLearning.SOURCE_DEVICE_ID = null,
                this.rooms = {},
                this.gateways = {},
                this.devices = {},
                this.irLists = {},
                this.irSlots = []

            },

        },
        computed: {
            //disbale save button
            saveDisabled () {
                if(this.irLearning.LEARNING_VALUE == null){
                    return true;
                }else{
                    return false;
                }

            },
            //disable room select
            roomDisabled(){
                if(this.irLearning.FLOOR_ID == null){
                    return true;
                }else{
                    return false;
                }
            },
            //disable gateway select
            gatewayDisabled(){
                if(this.irLearning.ROOM_ID == null){
                    return true;
                }else{
                    return false;
                }
            },
            //disable source device select
            sourceDisabled(){
                if(this.irLearning.GATEWAY_ID == null){
                    return true;
                }else{
                    return false;
                }
            },

            //disable source device select
            targetDisabled(){
                if(this.irLearning.SOURCE_DEVICE_ID == null){
                    return true;
                }else{
                    return false;
                }
            },

            targetBrandDisabled(){
                if(this.irLearning.TARGET_DEVICE == null){
                    return true;
                }else{
                    return false;
                }
            },

            irNameDisabled(){
                if(this.irLearning.TARGET_BRAND == null){
                    return true;
                }else{
                    return false;
                }
            },

            irValueDisabled(){
                if(this.irLearning.LEARNING_NAME == null){
                    return true;
                }else{
                    return false;
                }
            },

            // //show or hide pagination
            // showPaginate(){
            //     return _.size(this.bindingLists) > 0
            // },
        }
    }
</script>