<template>
    <div v-if="show" class="col-sm-6">
        <div class="col-sm-12" :class="editMode ? '' : 'py-5'">
            <div class="hardware-right-pane">
                <div class="box-inside-right-pane">
                    <div class="hardware-action-position">
                        <a v-if="deviceData.REG_FLAG == 1" class="pointer" @click="edit()">
                            <span>
                                <i aria-hidden="true" class="text-white fa fa-edit"></i>
                            </span>
                        </a>
                        <a v-if="deviceData.REG_FLAG == 9" class="pointer" @click="active()">
                            <span>
                                <i aria-hidden="true" class="text-white fa fa-check"></i>
                            </span>
                        </a>
                        <a v-if="deviceData.REG_FLAG == 1" class="pointer" @click="del()">
                            <span>
                                <i aria-hidden="true" class="text-white fa fa-trash-o"></i>
                            </span>
                        </a>
                    </div>
                    <img :src="sourceImage" class="hardware-img-right-pane mt-4" @error="imgUrlAlt">
                </div>
                <div class="hardware-right-pane-details">
                    <div class="text-dark font-weight-bold h5">
                        <input v-if="deviceData.category == 'device'" type="text" v-model="deviceData.DEVICE_NAME" :disabled="!editMode" :class="editMode ? 'form-control col-sm-6 fade-in' : 'view col-sm-12 pl-0'" placeholder="Device Name">
                        <div v-else class="col-sm-12 pl-0">{{  deviceData.gateway.GATEWAY_NAME }}</div>
                    </div>

                    <!-- MODBUS EDIT -->
                    <div v-if="deviceData.category == 'modMeter'" class="text-dark line-height-1">{{$t('device.gatewayName')}}: {{ deviceData.gateway.GATEWAY_IP }}</div>
                    <div v-if="deviceData.category == 'modMeter'" class="text-dark line-height-1">
                        {{$t('device.modbusSerial')}}:
                        <span v-if="!editMode">{{ deviceData.SERIAL_NO }}</span>
                        <input v-if="editMode" v-model="deviceData.SERIAL_NO" type="text" :class="editMode ? 'form-control col-sm-6 fade-in' : 'view col-sm-12 pl-0'">
                    </div>
                    <div v-if="deviceData.category == 'modMeter'" class="text-dark line-height-1">
                        <span>{{$t('device.modbusSlave')}}:</span>
                        <span v-if="!editMode">{{ deviceData.SLAVE_ID }}</span>
                        <input v-if="editMode" v-model="deviceData.SLAVE_ID" type="text" :class="editMode ? 'form-control col-sm-6 fade-in' : 'view col-sm-12 pl-0'">
                    </div>

                    <!-- DEVICE EDIT -->
                    <div v-if="!editMode" class="col-sm-12 pl-0">
                        <div v-if="deviceData.category == 'device'" class="text-dark line-height-1">
                            {{$t('device.gatewayName')}}:
                            <span>{{ deviceData.gateway.GATEWAY_NAME }}</span>
                        </div>
                        <div v-if="deviceData.category == 'device'" class="text-dark line-height-1">
                            {{$t('device.devType')}}:
                            <span>{{ deviceData.DEVICE_TYPE }}</span>
                        </div>
                        <div v-if="deviceData.category == 'device'" class="text-dark line-height-1">
                            {{$t('device.devSerial')}}:
                            <span>{{ deviceData.DEVICE_SERIAL_NO }}</span>
                        </div>
                        <div v-if="deviceData.category == 'device'" class="text-dark line-height-1">
                            {{$t('device.devCat')}}:
                            <span v-for="category in devCategory">
                                <span v-if="deviceData.DEVICE_CATEGORY == category.ID">{{ category.NAME }}</span>
                            </span>
                        </div>
                    </div>
                    <div v-else>
                        <div v-if="deviceType" class="row col-sm-12 px-0 mx-0">
                            <div class="col-sm-6 pl-0 fade-in">
                                <div v-if="deviceData.category == 'device'" v-for="gangs,key in deviceData.DATA"class="text-dark line-height-1">
                                    {{$t('device.device')}} {{ key + 1 }} {{$t('device.gang')}}:
                                    <input v-model="deviceData.DATA[key].device_name" type="text" class="form-control col-sm-12">
                                    <small v-if="errors['DATA.[key].device_name']" class="text-danger">{{ error_text }}</small>
                                </div>


                                <div v-if="deviceData.category == 'device'" class="text-dark line-height-1">
                                    {{$t('device.devCat')}}:
                                    <div>
                                        <select v-if="editMode" v-model="deviceData.DEVICE_CATEGORY" class="custom-select">
                                            <option v-for="category in devCategory" :selected="deviceData.DEVICE_CATEGORY == category.ID" :value="category.ID">
                                                {{ category.NAME }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 pr-0 fade-in">
                                <div v-if="deviceData.category == 'device'" class="text-dark line-height-1">
                                    {{$t('device.gatewayName')}}:
                                    <input v-model="deviceData.gateway.GATEWAY_NAME" type="text" class="form-control col-sm-12" readonly>
                                </div>
                                <div v-if="deviceData.category == 'device'" class="text-dark line-height-1">
                                    {{$t('device.devType')}}:
                                    <input v-model="deviceData.DEVICE_TYPE" type="text" class="form-control col-sm-12" readonly>
                                </div>
                                <div v-if="deviceData.category == 'device'" class="text-dark line-height-1">
                                    {{$t('device.devSerial')}}:
                                    <input v-model="deviceData.DEVICE_SERIAL_NO" type="text" class="form-control col-sm-12" readonly>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <div class="col-sm-12 px-0 fade-in">
                                <div v-if="deviceData.category == 'device'" class="text-dark line-height-1">
                                    {{$t('device.gatewayName')}}:
                                    <input v-model="deviceData.gateway.GATEWAY_NAME" type="text" class="form-control col-sm-12" readonly>
                                </div>
                                <div v-if="deviceData.category == 'device'" class="text-dark line-height-1">
                                    {{$t('device.devType')}}:
                                    <input v-model="deviceData.DEVICE_TYPE" type="text" class="form-control col-sm-12" readonly>
                                </div>
                                <div v-if="deviceData.category == 'device'" class="text-dark line-height-1">
                                    {{$t('device.devSerial')}}:
                                    <input v-model="deviceData.DEVICE_SERIAL_NO" type="text" class="form-control col-sm-12" readonly>
                                </div>
                                <div v-if="deviceData.category == 'device'" class="text-dark line-height-1">
                                    {{$t('device.devCat')}}:
                                    <div>
                                        <select v-if="editMode" v-model="deviceData.DEVICE_CATEGORY" class="custom-select">
                                            <option v-for="category in devCategory" :selected="deviceData.DEVICE_CATEGORY == category.ID" :value="category.ID">
                                                {{ category.NAME }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div v-if="deviceData.category == 'device'" class="text-dark line-height-1">{{$t('device.onStatus')}}:
                        <span v-if="deviceData.ONLINE_FLAG == 1"> {{$t('online')}} </span>
                        <span v-else>{{$t('offline')}}</span>
                    </div>
                    <div v-if="editMode" class="fade-in" :class="detailsClass">
                        <button @click="save()" class="btn btn-primary" :disabled="disableSave">{{$t('user.save')}}</button>
                        <button @click="cancelEdit()" class="btn btn-danger">{{$t('user.cancel')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        created() {
            this.$bus.$on('selectedDeviceData', data => {
                this.showPanel(data);
            });
        },
        data() {
            return {
                deviceData: {},
                devCategory: [{'ID': 0, 'NAME': 'Device'},{'ID': 1, 'NAME': 'Sensor'}],
                editMode: false,
                show: false,
                errors:{},
            }
        },
        methods: {
            //Function Name: showPanel
            //Function Description: show selected device details
            //Param: data (device)
            showPanel(data){
                if(data.REG_FLAG == 1 || data.METER_ID){
                    this.show = true;
                    this.deviceData = Object.assign({}, data);
                }else{
                    this.show = false;
                }
            },
            //Function Name: active
            //Function Description: Activate modbus meter
            active(){
                this.$swal({
                    title:"Activate Modbus Meter",
                    text:"Are you sure?",
                    type:"warning",
                    showCancelButton:true,
                    confirmButtonColor:"#3085d6",
                    cancelButtonColor:"#d33",
                    confirmButtonText:"Yes"
                }).then((result) =>{
                    if(result.value){
                        axios.post('meters/unDelete',{
                            METER_ID: this.deviceData.METER_ID
                        }).then(response => {
                            if(response.data == 'success'){
                                this.$swal({
                                    title:'Actived Modbus Meter',
                                    text:"Modbus Meter has been Activated.",
                                    type:'success',
                                    timer:1500,
                                    showConfirmButton:false
                                });
                            }else{
                                this.$swal(
                                    'Error',
                                    "Modbus Meter can't Activated.",
                                    'error'
                                );
                            }
                            this.$bus.$emit('getDeviceData', response.data);
                            this.show = false;
                        });
                    }
                });

            },
            //Function Name: edit
            //Function Description: enable editting of device
            edit(){
                this.cachedData = Object.assign({}, this.deviceData);
                this.editMode = true;
            },
            //Function Name: del
            //Function Description: opens modal to delete device
            del(){
                this.openModal(0);
            },
            //Function Name: save
            //Function Description: updates device details
            save(){
                if(this.deviceData.category == 'device'){
                    axios({
                      url: 'devices/update',
                      method: 'post',
                      data: {
                        'FLOOR_ID':this.deviceData.floor.FLOOR_ID,
                        'ROOM_ID':this.deviceData.room.ROOM_ID,
                        'GATEWAY_ID':this.deviceData.gateway.GATEWAY_ID,
                        'DEVICE_ID':this.deviceData.DEVICE_ID,
                        'DEVICE_NAME':this.deviceData.DEVICE_NAME,
                        'DEVICE_CATEGORY':this.deviceData.DEVICE_CATEGORY,
                        'DEVICE_DATA':this.deviceData.DATA
                        }
                    })
                    .then((response)=>
                        this.$bus.$emit('getDeviceData', response.data),
                        this.openModal(1),
                        this.editMode = false,
                    )
                    .catch((error) => this.errs(error));
                }else{
                    axios({
                      url: 'meters/update',
                      method: 'post',
                      data: {
                        'METER_ID':this.deviceData.METER_ID,
                        'SERIAL_NO':this.deviceData.SERIAL_NO,
                        'SLAVE_ID':this.deviceData.SLAVE_ID
                        }
                    })
                    .then((response)=>
                        this.$bus.$emit('getDeviceData', response.data),
                    this.openModal(1),
                    this.editMode = false,
                    )
                    .catch((error) => this.errs(error));
                }



            },
            //Function Name: cancelEdit
            //Function Description: cancel edit mode
            cancelEdit(){
                this.deviceData = Object.assign({}, this.cachedData);
                this.editMode =  false;
            },
            //Function Name: errs
            //Funciton Description: store errors
            //Param: error
            errs(error){
                this.errors = error.response.data.errors
            },
            //Function Name: openModal
            //Function Description: opens specific modals and alerts
            //Param: data
            openModal(data){
                var cat = '';
                var url = '';
                var id = '';

                if(this.deviceData.category == 'device'){
                    cat = 'Device';
                    url = 'devices/delete';
                    id = this.deviceData.DEVICE_ID;
                }else{
                    cat = 'Modbus Meter';
                    url = 'meters/delete';
                    id = this.deviceData.METER_ID;
                }

                if(data == 1){
                    this.$swal({
                        position: 'center',
                        type: 'success',
                        title: cat + ' Successfully Updated',
                        showConfirmButton: false,
                        timer: 1200
                    });
                }else{
                    this.$swal({
                        title:"Delete " + cat,
                        text:"Are you sure?",
                        type:"warning",
                        showCancelButton:true,
                        confirmButtonColor:"#3085d6",
                        cancelButtonColor:"#d33",
                        confirmButtonText:"Yes"
                    }).then((result) =>{
                        if(result.value){
                            axios.post(url,{
                                DEVICE_ID: id
                            }).then(response => {
                                if(response.data == 'success'){
                                    this.$swal({
                                        title:'Deleted',
                                        text: cat + " has been deleted.",
                                        type:'success',
                                        timer:1500,
                                        showConfirmButton:false
                                    });
                                }else{
                                    this.$swal(
                                        'Error',
                                        cat + " can't delete.",
                                        'error'
                                    );
                                }
                                this.$bus.$emit('getDeviceData', response.data);
                                this.show = false;
                            });
                        }
                    });
                }
            },
            //Function Name: imgUrAlt
            //Function Description: change image target to not found on error
            //Param: event (404)
            imgUrlAlt(event) {
                event.target.src = "img/image_not_found.png"
            },
        },
        beforeDestroy() {
            this.$bus.$off('selectedDeviceData');
        },
        watch:{
            deviceData:function(){
                this.editMode = false;
            }
        },
        computed:{
            //returns class name according to device type
            detailsClass(){
                if (this.deviceData.DEVICE_TYPE) {
                    if (this.deviceData.DEVICE_TYPE.includes('switch') && !this.deviceData.DEVICE_TYPE.includes('switch_3')) {
                        return 'py-3';
                    }else{
                        return '';
                    }
                }else{
                    return '';
                }
            },
            //returns true to disable save button if parameters are missing
            disableSave(){
                if(this.deviceData.category == 'device'){
                    if(this.deviceData.DEVICE_NAME == '' || this.deviceData.DEVICE_NAME == null){
                        return true;
                    }
                }else{
                    if(this.deviceData.SERIAL_NO == '' || this.deviceData.SERIAL_NO == null){
                        return true;
                    }else if(this.deviceData.SLAVE_ID == '' || this.deviceData.SLAVE_ID == null){
                        return true;
                    }
                }
            },
            //returns true if device type is a switch
            deviceType(){
                if(this.deviceData.DEVICE_TYPE == 'embedded_switch_1' ||
                   this.deviceData.DEVICE_TYPE == 'embedded_switch_2' ||
                   this.deviceData.DEVICE_TYPE == 'embedded_switch_3' ||
                   this.deviceData.DEVICE_TYPE == 'wall_switch_1' ||
                   this.deviceData.DEVICE_TYPE == 'wall_switch_2' ||
                   this.deviceData.DEVICE_TYPE == 'wall_switch_3')
                {
                    return true;
                }else{
                    return false;
                }
            },
            //changes image source according to device type
            sourceImage(){
                var image_source = '';
                if(this.deviceData.category == 'modMeter'){
                        image_source = 'img/device/emeter.png';
                }else{
                    if(this.deviceData.DEVICE_TYPE == 'smoke_detector'){
                        image_source = 'img/device/smoke_detector.png';
                    }else if(this.deviceData.DEVICE_TYPE == 'gas_detector'){
                        image_source = 'img/device/gas_detector.png';
                    }else if(this.deviceData.DEVICE_TYPE == 'dust_detector'){
                        image_source = 'img/device/dust_detector.png';
                    }else if(this.deviceData.DEVICE_TYPE == 'co2_detector'){
                        image_source = 'img/device/co2_detector.png';
                    }else if(this.deviceData.DEVICE_TYPE == 'light_detector'){
                        image_source = 'img/device/light_detector.png';
                    }else if(this.deviceData.DEVICE_TYPE == 'motion_detector'){
                        image_source = 'img/device/motion_detector.png';
                    }else if(this.deviceData.DEVICE_TYPE == 'temp_hum'){
                        image_source = 'img/device/temp_hum.png';
                    }else if(this.deviceData.DEVICE_TYPE == 'h2o_detector'){
                        image_source = 'img/device/h2o_detector.png';
                    }else if(this.deviceData.DEVICE_TYPE == 'wall_switch_1'){
                        image_source = 'img/device/wall_switch_1.png';
                    }else if(this.deviceData.DEVICE_TYPE == 'wall_switch_2'){
                        image_source = 'img/device/wall_switch_2.png';
                    }else if(this.deviceData.DEVICE_TYPE == 'wall_switch_3'){
                        image_source = 'img/device/wall_switch_3.png';
                    }else if(this.deviceData.DEVICE_TYPE == 'embedded_switch_1'){
                        image_source = 'img/device/embedded_switch_1.png';
                    }else if(this.deviceData.DEVICE_TYPE == 'embedded_switch_2'){
                        image_source = 'img/device/embedded_switch_2.png';
                    }else if(this.deviceData.DEVICE_TYPE == 'embedded_switch_3'){
                        image_source = 'img/device/embedded_switch_3.png';
                    }else if(this.deviceData.DEVICE_TYPE == 'rgb_light'){
                        image_source = 'img/device/rgb_light.png';
                    }else if(this.deviceData.DEVICE_TYPE == 'temp_light'){
                        image_source = 'img/device/temp_light.png';
                    }else if(this.deviceData.DEVICE_TYPE == 'door_lock'){
                        image_source = 'img/device/door_lock.png';
                    }else if(this.deviceData.DEVICE_TYPE == 'curtain_1'){
                        image_source = 'img/device/curtain.png';
                    }else if(this.deviceData.DEVICE_TYPE == 'gas_valve'){
                        image_source = 'img/device/gas_valve.png';
                    }else if(this.deviceData.DEVICE_TYPE == 'water_valve'){
                        image_source = 'img/device/water_valve.png';
                    }else if(this.deviceData.DEVICE_TYPE == 'panic_button'){
                        image_source = 'img/device/panic_button.png';
                    }else if(this.deviceData.DEVICE_TYPE == 'ir_remote'){
                        image_source = 'img/device/ir_transmitter.png';
                    }else{
                        image_source = 'img/image_not_found.png';
                    }

                }
                return image_source;
            }
        },
    };
</script>