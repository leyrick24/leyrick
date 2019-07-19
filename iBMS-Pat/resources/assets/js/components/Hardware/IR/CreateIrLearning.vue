<template>
    <div class="">
        <div class="d-flex">
            <div class="col-sm-12">
                <h5 class="py-2">Selections</h5>
                <div class="form-group">
                    <label class="label">Floor</label>
                    <div class="">
                        <select class="custom-select" v-model="irLearning.FLOOR_ID" @change="getRooms">
                            <!-- loop the floors data -->
                            <option v-for="floor,index in floors" :key="index" :value="floor.FLOOR_ID">{{floor.FLOOR_NAME}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="label">Room</label>
                    <div class="">
                        <select class="custom-select" v-model="irLearning.ROOM_ID" @change="getGateways" :disabled="roomDisabled">
                            <!-- loop the rooms data -->
                            <option v-for="room,index in rooms" :key="index" :value="room.ROOM_ID">{{room.ROOM_NAME}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="label">Gateway</label>
                    <div class="">
                        <select class="custom-select" v-model="irLearning.GATEWAY_ID" @change="getSourceDevices" :disabled="gatewayDisabled">
                            <!-- loop the gateways data -->
                            <option v-for="gateway,index in gateways" :key="index" :value="gateway.GATEWAY_ID">{{gateway.GATEWAY_NAME}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="label">IR Device</label>
                    <div class="">
                        <select class="custom-select" v-model="irLearning.SOURCE_DEVICE_ID" @change="getIrVal" :disabled="sourceDisabled">
                            <!-- loop the gateways data -->
                            <option v-for="device,index in devices" :key="index" :value="device.DEVICE_ID">{{device.DEVICE_NAME}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="label">Appliances</label>
                    <div class="">
                        <select class="custom-select" v-model="irLearning.TARGET_DEVICE" @change="learningNameList" :disabled="targetDisabled">
                            <!-- loop the gateways data -->
                            <option v-for="appliance,index in appliances" :key="index" :value="appliance.TARGET_DEVICE">{{appliance.TARGET_DEVICE}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="label">Appliance's Brand</label>
                    <div class="">
                        <select class="custom-select" v-model="irLearning.TARGET_BRAND" @change="targetBrandList(irLearning)" :disabled="targetBrandDisabled">
                            <option v-for="brand,index in brands" :key="index" :value="brand.BRAND_NAME">{{brand.BRAND_NAME}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="label">IR Learning Name</label>
                    <div class="">
                        <select class="custom-select" v-model="irLearning.LEARNING_NAME" @change="irValList" :disabled="targetDisabled">
                            <option  v-if="irLearning.TARGET_DEVICE =='Aircon'" v-for="learning_name,index in learning_names" :key="index" :value="learning_name">{{learning_name}}</option>

                            <option  v-if="irLearning.TARGET_DEVICE =='TV'" v-for="learning_name_tv,index in learning_names_tv" :key="index" :value="learning_name_tv">{{learning_name_tv}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="label">IR Learning Value</label>
                    <div class="">
                        <select class="custom-select" v-model="irLearning.LEARNING_VALUE" :disabled="irValueDisabled">
                            <option v-for="irSlot,index in irSlots" :key="index" :value="irSlot">{{irSlot}}</option>
                        </select>
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
            // this.$bus.$on('getData' , data =>{
            //     this.getData();
            // });
            this.$bus.$on('changeTabAdd' , data =>{
                this.getFloors();
            });
        },
        data(){
            return{
                category: '',
                irLearning:{
                    FLOOR_ID: null,
                    ROOM_ID: null,
                    GATEWAY_ID: null,
                    TARGET_BRAND: null,
                    TARGET_DEVICE: null,
                    LEARNING_NAME: null,
                    LEARNING_VALUE: null,
                    SOURCE_DEVICE_ID:null
                },
                learning_names: [
                    'POWER_OFF',
                    'POWER_ON',
                    'TEMP_16',
                    'TEMP_17',
                    'TEMP_18',
                    'TEMP_19',
                    'TEMP_20',
                    'TEMP_21',
                    'TEMP_22',
                    'TEMP_23',
                    'TEMP_24',
                    'TEMP_25',
                    'TEMP_26',
                    'TEMP_27',
                    'TEMP_28',
                    'TEMP_29',
                    'TEMP_30'
                ],
                learning_names_tv: [
                    'POWER'
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
                irNames:[],
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
                axios.get('floors')
                .then(response => {
                    this.floors = response.data,
                    this.irLearning.FLOOR_ID = null,
                    this.irLearning.ROOM_ID = null,
                    this.irLearning.GATEWAY_ID = null,
                    this.irLearning.SOURCE_DEVICE_ID = null,
                    this.irLearning.TARGET_DEVICE = null,
                    this.irLearning.TARGET_BRAND = null,
                    this.irLearning.LEARNING_NAME = null,
                    this.irLearning.LEARNING_VALUE = null,
                    this.gateways = {},
                    this.devices = {}

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
            //Function name: getIrVal
            //Function Description: get ir values
            getIrVal(){
                var details = '';
                this.$bus.$emit('selectedApplianceData', details);
                this.irLearning.TARGET_DEVICE = null,
                this.irLearning.TARGET_BRAND = null,
                this.irLearning.LEARNING_VALUE = null,
                this.irSlots = []

                axios.get('learning-list')
                .then(response => {
                    this.irLists = response.data

                    //put value 0-629
                    for (var i = 0; i < 630; i++) {
                        this.irSlots.push(i)
                    }
                    //loops irLists
                    for(var j in this.irLists){
                        // let index = this.yourArray.indexOf(arrayItem)
                        // this.yourArray.splice(index, 1);
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
            //Function name: learningNameList
            //Function Description: get learning name list
            learningNameList(){
                var details = '';
                this.$bus.$emit('selectedApplianceData', details);
                this.irLearning.TARGET_BRAND = null,
                this.irLearning.LEARNING_NAME = null,
                this.irLearning.LEARNING_VALUE = null

                axios.get('learning-list')
                .then(response => {
                    this.irLists = response.data

                })
                .catch(errors => {
                    console.log(errors);
                });
            },
            //Function Name: targetBrandList
            //Function Description: set learning names and brand, delete used learning names from learning list
            //Param: details (irLearning)
            targetBrandList(details){
                this.$set(details, "category", "addIrList");
                this.$bus.$emit('selectedApplianceData', details);

                this.irLearning.LEARNING_NAME = null,
                this.irLearning.LEARNING_VALUE = null,
                this.learning_names = ['POWER_OFF', 'POWER_ON',
                'TEMP_16', 'TEMP_17', 'TEMP_18', 'TEMP_19', 'TEMP_20',
                'TEMP_21', 'TEMP_22', 'TEMP_23', 'TEMP_24', 'TEMP_25',
                'TEMP_26', 'TEMP_27', 'TEMP_28', 'TEMP_29', 'TEMP_30'
                ],
                this.learning_names_tv = ['POWER']

                axios.get('learning-list')
                .then(response => {
                    this.irLists = response.data

                    // //loops irLists
                    for(var j in this.irLists){
                    //     // let index = this.yourArray.indexOf(arrayItem)
                    //     // this.yourArray.splice(index, 1);
                        if(this.irLists[j].SOURCE_DEVICE_ID == this.irLearning.SOURCE_DEVICE_ID){
                            if(this.irLearning.TARGET_BRAND == this.irLists[j].TARGET_BRAND){
                                if(this.irLearning.TARGET_DEVICE == this.irLists[j].TARGET_DEVICE){
                                    if(this.irLearning.TARGET_DEVICE == 'Aircon'){
                                        let index = this.learning_names.indexOf(this.irLists[j].LEARNING_NAME)
                                        // console.log(this.irLists[j].LEARNING_NAME);
                                        this.learning_names.splice(index, 1)
                                        // console.log('Name: ' + this.learning_names);

                                    }else{
                                        let index = this.learning_names_tv.indexOf(this.irLists[j].LEARNING_NAME)
                                        // console.log(this.irLists[j].LEARNING_NAME);
                                        this.learning_names_tv.splice(index, 1)
                                        // console.log('Name: ' + this.learning_names_tv);

                                    }
                                }
                            }
                        }
                    }
                })
                .catch(errors => {
                    console.log(errors);
                });
            },
            //Function Name: irValList
            //Function Description: update ir learning list
            irValList(){
                axios.get('learning-list')
                .then(response => {
                    this.irLists = response.data,
                    this.irLearning.LEARNING_VALUE = null

                })
                .catch(errors => {
                    console.log(errors);
                });

            },
            //Function Name: saveIrList
            //Function Description: save IR Learning List
            saveIrList(){
                this.loading = true;
                this.btn_text = 'Saving';
                this.commandDevice('learning',1,this.irLearning.LEARNING_VALUE);

                axios({
                  url: 'learning-list/new',
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
            //Function Name: Command Device
            //Function Description: Send to Room.vue to command ir transmitter to learn and Save to database
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
                    title: 'Successfully Added',
                    showConfirmButton: false,
                    timer: 2000
                });
            },
            //Function Name: reset
            //Function Description: close modal function
            reset(){
                var details = '';
                this.$bus.$emit('selectedApplianceData', details);
                this.irLearning.FLOOR_ID = null,
                this.irLearning.ROOM_ID = null,
                this.irLearning.GATEWAY_ID = null,
                this.irLearning.TARGET_DEVICE = null,
                this.irLearning.TARGET_BRAND = null,
                this.irLearning.LEARNING_NAME = null,
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

            //disable source device select
            irNameDisabled(){
                if(this.irLearning.TARGET_DEVICE.BRAND == null){
                    return true;
                }else{
                    return false;
                }
            },
            // //disable source device select
            // irValueDisabled(){
            //     if(this.irLearning.LEARNING_NAME.CONDITION == null){
            //         return true;
            //     }else{
            //         return false;
            //     }
            // },

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