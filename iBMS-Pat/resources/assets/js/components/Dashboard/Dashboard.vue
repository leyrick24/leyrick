<template>
    <div>
        <div class="wrapper">
            <div class="container-fluid container-bg">
                <div class="col-sm-auto d-flex justify-content-between align-items-center px-0 py-3">
                    <div>{{$t('navbar.dashboard')}}</div>
                    <!-- add prop to all clocks for language settings
                        pass locale to clock to enable moment().locale() -->
                    <clock :locale="$i18n.locale"></clock>
                </div>
                <div class="bg-color-am card border-0 rounded-0">
                	<div class="col-md-12 d-flex justify-content-end">
	                	<div :class="selectedDevice.DEVICE_TYPE != 'ir_remote' ? 'dashboard-floor-select' : 'pt-2 pr-2'">
		                	<select :class="selectedDevice.DEVICE_TYPE != 'ir_remote' ? '' : 'dashboard-select'" class="custom-select" v-model="selectedFloor" @change="getFloorRoom">
		                		<option v-for="floor in floors" :value="floor.FLOOR_ID">{{floor.FLOOR_NAME}}</option>
		                	</select>
	                	</div>
	                	<div class="selectedDevice.DEVICE_TYPE != 'ir_remote' ? 'dashboard-room-select' : 'pt-2'">
		                	<select :class="selectedDevice.DEVICE_TYPE != 'ir_remote' ? '' : 'dashboard-select'" class="custom-select" v-model="selectedRoom">
		                		<option v-for="room,index in rooms" :key="index" :value="room.ROOM_ID">{{room.ROOM_NAME}}</option>
		                	</select>
	                	</div>
                	</div>
                    <div id="ejsChart" class="card-body" v-if="theme != ''">
                        <ejs-chart v-if="selectedDevice.DEVICE_TYPE != 'ir_remote'" style='display:block' :theme="theme" align='center' id='chartcontainer' :title='deviceName' :primaryXAxis='primaryXAxis' :primaryYAxis='primaryYAxis' :tooltip='tooltip' :chartArea='chartArea' :width='width' :zoomSettings='zoom' :crosshair="crosshair">
                            <e-series-collection>
                                <e-series v-for="id,key in distinctDevices" :key="key" :dataSource="chartData[id]" :fill="key == 0 ? '#FF6600' : ''" type='Line' xName='x' yName='y' :name="'Device: '+id" width=1.5> </e-series>
                            </e-series-collection>
                        </ejs-chart>
                        <!-- show only when device type is ir remote -->
                        <div v-else class="h-450 background-white">
                        	<div>
	                        	<b-table responsive v-if="irlog.length > 0"
	                					:fields="table.fields"
	                					:items="irlog"
	                					:per-page="table.perPage"
	                					:current-page="table.currentPage">
	                        	</b-table>
                        	</div>
                        	<div :class="irlog.length > 50 ? '':'custom-pagination-black'" class="col-md-12 d-flex justify-content-center text-align-center" v-if="irlog.length > 10">
                        		<b-pagination class="pl-5" :total-rows="table.totalRows" :per-page="table.perPage" v-model="table.currentPage">
                        		</b-pagination>
                        	</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Devices Carousel-->
            <div class="col-md-12 px-0 mx-0 container-fluid container-bg">
                <deviceGraphCarousel :floor="selectedFloor" :room="selectedRoom" @changeTitle="changeTitle"></deviceGraphCarousel>
            </div>
            <div class="container-fluid pt-3">
                <div class="row pb-5">
                    <div class="col-md-6">
                        <!-- Start of Event -->
                        <events></events>
                        <!-- end of Event -->
                        <!-- Start of Electric Meter Graph -->
                        <div class="card mt-20px rounded-0 border border-dark bg-color-7accc8" id="dbEMeter">
                            <div class="card-body">
                                <h5 class="card-title border-bottom-black pb-2">
                                    <i class="fa fa-bolt"></i>
                                    <span>
                                        <!-- syntax for static text to be translated. call $t function from i18n -->
                                        {{ $t('dashboard.electric-summary') }}
                                    </span>
                                </h5>
                                <vuechart :height="150"></vuechart>
                            </div>
                        </div>
                        <!-- End of Electric Meter Graph -->
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-12 border border-dark px-0 online-summary">
                            <!-- Start Summary of Gateway -->
                            <div class="d-flex" id="summaryGateway">
                                <div class="col-md-7 m-auto">
                                    <div v-for="value in status" class="align-img">
                                        <div v-for="src in value.onlineGatewayImg" v-if="value.onlineGatewayImg.length > 2 ? 'number-wid-30' : 'number-wid-40'">
                                            <img :src="src" alt="" class="gateway-img-number">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="mt-5 pt-2">
                                        <div class="my-4">
                                            <h5 class="border-bottom-black pb-2 mb-3">
                                                <span class="font-weight-bold">
                                                    {{ $t('dashboard.gateway-summary') }}
                                                </span>
                                            </h5>
                                            <div class="col-sm-12 p-0">
                                                <div class="h6 d-flex justify-content-between align-items-center">
                                                    <span>{{ $t('dashboard.gateway-total') }}</span>
                                                    <span class="float-right" v-for="value in status">{{ value.totalGateway }}</span>
                                                </div>
                                                <div class="h6 d-flex justify-content-between align-items-center clickable" @click="openModal('summaryModal','onlineGateway')">
                                                    <span>{{ $t('dashboard.gateway-online') }}</span>
                                                    <span class="float-right" v-for="value in status">{{ value.onlineGateway }}</span>
                                                </div>
                                                <div class="h6 d-flex justify-content-between align-items-center clickable" @click="openModal('summaryModal','offlineGateway')">
                                                    <span>{{ $t('dashboard.gateway-offline') }}</span>
                                                    <span class="float-right" v-for="value in status">{{ value.offlineGateway }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Summary of Gateway -->
                            <!-- Start Summary of Devices -->
                            <div class="d-flex bg-devices" id="summaryDevices">
                                <div class="col-md-5">
                                    <div class="mt-4 pt-4">
                                        <div class="my-4 pt-1">
                                            <h5 class="border-bottom-black pb-2 mb-3">
                                                <span class="font-weight-bold">
                                                    {{ $t('dashboard.devices-summary') }}
                                                </span>
                                            </h5>
                                            <div class="col-sm-12 p-0">
                                                <div class="h6 d-flex justify-content-between align-items-center">
                                                    <span>{{ $t('dashboard.devices-total') }}</span>
                                                    <span class="float-right" v-for="value in status">{{ value.totalDevices }}</span>
                                                </div>
                                                <div class="h6 d-flex justify-content-between align-items-center clickable" @click="openModal('summaryModal','onlineDevices')">
                                                    <span>{{ $t('dashboard.devices-online') }}</span>
                                                    <span class="float-right" v-for="value in status">{{ value.onlineDevices }}</span>
                                                </div>
                                                <div class="h6 d-flex justify-content-between align-items-center clickable" @click="openModal('summaryModal','offlineDevices')">
                                                    <span>{{ $t('dashboard.devices-offline') }}</span>
                                                    <span class="float-right" v-for="value in status">{{ value.offlineDevices }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7 m-auto">
                                    <div v-for="value in status" class="align-img">
                                        <div v-for="src in value.onlineDevicesImg" v-if="value.onlineDevicesImg.length > 2 ? 'number-wid-30' : 'number-wid-40'">
                                            <img :src="src" alt="" class="gateway-img-number">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Summary of Devices -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Footer></Footer>
        <piechart class="card-text text-right" @ready="onStatusLoad"></piechart>
        <div v-if="chosenModal=='summaryModal'">
            <summaryModal :modalData="modalData" :status="roomDeviceStatus" @closeModal="closeModal"></summaryModal>
        </div>
    </div>
</template>
<script>
    import Browser from '@syncfusion/ej2-base';
    //enable line, legend, tooltip, zoom and scrollbar for vue chart
    import { ChartPlugin, LineSeries, Legend, Tooltip, DateTime, Zoom, ScrollBar, Crosshair} from "@syncfusion/ej2-vue-charts";
    import piechart from './Chart/PieChart.vue';
    import events from './Events/Events.vue';
    import modbus from '../ModBus/ModBus.vue';
    import vuechart from './Chart/VueChart.vue';
	import deviceGraphCarousel from './DeviceGraphCarousel/DeviceCarousel.vue';
    import summaryModal from '../Modals/SummaryModal.vue';
    export default {
        props: ['userid',],
        components: {piechart, events, modbus, vuechart, deviceGraphCarousel, summaryModal},
        created () {
            this.getFloors();
            //for Syncfusion Chart API
            let selectedTheme = location.hash.split("/")[1];
            selectedTheme = selectedTheme ? selectedTheme:"Material";
            this.theme = (selectedTheme.charAt(0).toUpperCase() + selectedTheme.slice(1)).replace(/-dark/i,"Dark");

            //Change Chart data based on emit by Device Carousel
            this.$bus.$on('activeDeviceData', data=>{
            	this.generateChart(data);
            });
        },
        data() {
            return {
                range:0,
                theme:'',
                selectedDevice:'',
                //object to push device data into
                chartData:{},
                //unique device ids
                distinctDevices:'',
                //Initializing Primary X Axis
                primaryXAxis: {
                    valueType: "DateTime",
                    labelFormat: "M/d hh:mm:ss",
                    intervalType: "Hours",
                    color: "black",
                    edgeLabelPlacement: "Shift",
                    labelIntersectionAction:'Rotate45',
                    title:'Date',
                    titleStyle: {
                    	color: 'black'
                    },
                    majorGridLines: { width: 0 },
                    labelStyle:{
                    	color: 'black'
                    }
                },
                //Initializing Primary Y Axis
                primaryYAxis: {
                    labelFormat: "{value}",
                    rangePadding: "None",
                    title:'Status',
                    titleStyle: {
                    	color: 'black'
                    },
                    labelStyle:{
                    	color: 'black'
                    },
                    lineStyle: { width: 0 },
                    majorTickLines: { width: 0 },
                    minorTickLines: { width: 0 }
                },
                chartArea: {
                    border: {
                      width: 1
                    }
                },
                width : '100%',
                tooltip: {
                    enable: true,
                    shared: true
                },
                crosshair: {
                    enable: true,
                    lineType: 'Vertical'
                },
                marker: {
                    visible: true,
                },
                //ir Logs
                table:{
                	fields:[
                		{key:'Target',label:'Target Appliance'},
                		{key:'Device',label:'Device'},
                		{key:'Command',label:'Command'},
                		{key:'Date',label:'Date'}
                	],
                	totalRows:0,
                	perPage:10,
                	currentPage:1
                },
                //stored ir logs data
                irlog:[],
                selectedFloor:'',
                floors:'',
                selectedRoom:'',
                rooms:'',
                deviceName: '',
                showDevicePie: 'device',
                showGatewayPie: 'gateway',
                pieChartTotalDevice: 0,
                gatewayStatusList:[],
                deviceStatusList:[],
                status:[],
                eventLists:[
                    {
                        NOTIFICATION_ID: 1,
                        message:"There is a alarm in Second Floor Room 201",
                        date:"Sep 3, 2018 8:10 AM",
                    },
                    {
                        NOTIFICATION_ID: 2,
                        message:"There is a alarm in Third Floor Room 301",
                        date:"Sep 3, 2018 8:12 AM",
                    },
                    {
                        NOTIFICATION_ID: 3,
                        message:"There is a alarm in Fourth Floor Room 401",
                        date:"Sep 3, 2018 8:15 AM",
                    },
                    {
                        NOTIFICATION_ID: 4,
                        message:"There is a alarm in Second Floor Room 201",
                        date:"Sep 3, 2018 8:17 AM",
                    },
                ],
                chosenModal:'',
                modalData:'',
                roomDevices:'',
                roomDeviceStatus:'',
                //chart zoom
                zoom:
                {
                    enableSelectionZooming: true,
                    enableMouseWheelZooming: true,
                    enableScrollbar: true,
                    toolbarItems: ['Zoom', 'Pan', 'Reset']
                },
            }
        },
        //for Syncfusion Chart API
        provide: {
          chart: [LineSeries, Legend, Tooltip, DateTime, Zoom, ScrollBar, Crosshair]
        },
        methods: {
            //Function Name: getFloors
            //Function Description: Retrieves all floors available and sets the first floor as default selected floor
            //Used by: Summary Chart, Dashboard Carousel, Online Device Modal
            getFloors(){
                axios.get('floors')
                .then(response=>{
                    this.floors = response.data;
                    this.selectedFloor = response.data[0].FLOOR_ID;
                    this.getFloorRoom();
                });
            },
            //Function Name: getFloorRoom
            //Function Description: Retrieves all rooms in selected floor
            //Params: selectedFloor
            //Used by: Summary Chart, Dashboard Carousel
            getFloorRoom(){
            	this.selectedRoom='';
            	axios.get('floors/'+this.selectedFloor+'/rooms')
            	.then(response=>{
            		this.rooms = response.data;
            		this.selectedRoom = response.data[0].FLOOR_ID;
            	});
            },
            //Function Name: onStatusLoad
            //Function Description: Loads data for Summary after emit from Piechart.vue
            //Used By: Device/Gateway Summary
            onStatusLoad (value) {
                this.status = value;
            },
            //Function Name: getGatewayStatusData
            //Function Description: Pushes gateway data from displayStatus function to local variable
            //Used By: Gateway Summary
            getGatewayStatusData(data){
                data.forEach((i) => this.gatewayStatusList.push(i));
            },
            //Function Name: getDeviceStatusData
            //Function Description: Pushes device data from displayStatus function to local variable
            //Used By: Device Summary
            getDeviceStatusData(data){
                data.forEach((i) => this.deviceStatusList.push(i));
            },
            //Function Name: displayStatus
            //Function Description: Get response data from DashboardController@gatewayStatus and DashboardController@deviceStatus
            //Used By: Gateway/Device Summary
            displayStatus(){
                axios({url: 'gatewayStatus',method: 'post',data: {}})
                .then((response)=>
                  this.getGatewayStatusData(response.data),
                )
                .catch((error) => console.log(error));
                axios({url: 'deviceStatus',method: 'post',data: {}})
                .then((response)=>
                  this.getDeviceStatusData(response.data),
                )
                .catch((error) => console.log(error));
            },
            //Function Name: changeTitle
            //Function Description: Changes Title after selecting device from Dashboard Carousel
            //Used By: Device Graph
            changeTitle(data){
                // console.log(data);
                this.deviceName = data+' Data';
            },
            //Function Name: getRoomDevices
            //Function Description: Retrieves room data including online devices and registered gateways
            //Used By: Online Device Modal
            getRoomDevices(){
                axios.get('rooms?include=onDevices>sysRegGateways')
                .then(response=>{
                    this.roomDevices = response.data;
                })
            },
            //Function Name: openModal
            //Function Description: Change data for modal based on button clicked
            //Params: modal, data
            //Used By: SummaryModal
            openModal(modal,data){
                if(modal=="summaryModal"){
                    this.chosenModal = modal;
                    let tempData = [];
                    let tempDataa = [];
                    if(data=="onlineGateway"){
                        tempData.push({'title': 'Online Gateway'});
                        tempData.push({'data': []});
                        this.gatewayStatusList.forEach((value, index) => {
                            if(value.onlineGatewayIP){
                                tempData[1].data.push({'IP Address':value.onlineGatewayIP,'Floor':value.onlineFloor,'Room':value.onlineRoom});
                            }
                        });
                        this.modalData = tempData;
                        this.roomDeviceStatus = '';
                    }
                    else if(data=="offlineGateway"){
                        tempData.push({'title': 'Offline Gateway'});
                        tempData.push({'data': []});
                        this.gatewayStatusList.forEach((value, index) => {
                            if(value.offlineGatewayIP){
                                tempData[1].data.push({'IP Address':value.offlineGatewayIP,'Floor':value.offlineFloor,'Room':value.offlineRoom});
                            }
                        });
                        this.modalData = tempData;
                        this.roomDeviceStatus = '';
                    }
                    else if(data=="onlineDevices"){
                        tempData.push({'title': 'Online Devices'});
                        tempData.push({'data': []});
                        this.roomDevices.forEach((value, index) => {
                            tempData[1].data.push({'Room':value.ROOM_NAME, 'Count':value.on_devices.length, 'Devices':value.on_devices});
                        });
                        this.modalData = tempData;
                        this.roomDeviceStatus = 'onlinedevices';
                    }
                    else if(data=="offlineDevices"){
                        tempData.push({'title': 'Offline Devices'});
                        tempData.push({'data': []});
                        this.deviceStatusList.forEach((value, index) => {
                            if(value.offlineDevice){
                                tempData[1].data.push({'Device':value.offlineDevice,'Floor':value.offlineFloor,'Room':value.offlineRoom});
                            }
                        });
                        this.modalData = tempData;
                        this.roomDeviceStatus = '';
                    }
                }
            },
            //Function Name: closeModal
            //Function Description: Close all modals
            closeModal(){
                this.chosenModal = '';
            },
            //Function Name: customCSS
            //Function Description: custom css for selected elements
            customCSS(){
                var gatewayHeight = $('#dbEvents').innerHeight()+22;
                var devicesHeight = $('#dbEMeter').innerHeight()-6;
            },
            //Function Name: irData
            //Functin Description: Parse through IR remote data for Logs display
            //Used By: IR Remote logs
            irData(data){
            	//empty chart data and ir logs before pushing new data in
            	this.chartData = {};
                //set as array for bootstrap vue table
            	this.irlog = [];
            	for(var i in data){
            		let command = '';
            		//set command based on processed data
	            	if (data[i].DATA[0].type == 'AC') {
	            		if (data[i].DATA[0].status == null) {
	            			command = 'Temp: '+data[i].DATA[0].temp_value+'°C';
	            		}else if(data[i].DATA[0].status == 1){
	            			command = 'AC ON';
	            		}else{
	            			command = 'AC OFF'
	            		}
	            	}else{
	            		if (data[i].DATA[0].status == 0) {
	            			command = 'TV OFF';
	            		}else{
	            			command = 'TV ON';
	            		}
	            	}
                    //push modified data into array
	            	this.irlog.push({
	            		Target: data[i].DATA[0].type + ' - ' + data[i].DATA[0].brand,
	            		Device: data[i].device.DEVICE_NAME,
	            		Command: command,
	            		Date: data[i].CREATED_AT,
	            	});
	            }
                //for pagination
	            this.table.totalRows = this.irlog.length;
            },
            //Function Name: generateChart
            //Function Description: generate chart data based on data received and refresh chart on change
            //Param: data (processed data), device_type
            generateChart(data){
                //check if data is empty
                if (data.length > 0) {
                    //empty chart before pushing in new device type data
                    this.chartData = {};
                    this.selectedDevice = data[0].device;
                    //take first entry's date
                    var from = new Date(data[0].CREATED_AT);
                    var to = new Date(from);
                    //used for data with binary status
                    let count = 0;
                    //take unique devices from data recieved and store in array
                    this.distinctDevices = [...new Set(data.map(x => x.device.DEVICE_NAME))];
                    //to store max value for each device
                    let max = [];
                    for (var i in this.distinctDevices){
                        //create an array for each unique device in chart data
                        this.chartData[this.distinctDevices[i]] = [];
                        for (var j in data){
                            if (this.distinctDevices[i] == data[j].device.DEVICE_NAME) {
                                //object to store x and y chart data
                                var newData = {};
                                //check for device type
                                if (data[j].device.DEVICE_TYPE == 'wall_switch_1' || data[j].device.DEVICE_TYPE == 'embedded_switch_1' || data[j].device.DEVICE_TYPE == 'motion_detector' || data[j].device.DEVICE_TYPE == 'gas_valve' || data[j].device.DEVICE_TYPE == 'smoke_detector' || data[j].device.DEVICE_TYPE == 'h2o_detector' || data[j].device.DEVICE_TYPE == 'water_valve' || data[j].device.DEVICE_TYPE == 'gas_detector' || data[j].device.DEVICE_TYPE == 'temp_light' || data[j].device.DEVICE_TYPE == 'door_lock') {
                                    //store device status
                                    let values = '';
                                    if (data[j].device.DEVICE_TYPE == 'wall_switch_1' || data[j].device.DEVICE_TYPE == 'embedded_switch_1') {
                                        values = data[j].DATA[0].status;
                                    }else if(data[j].device.DEVICE_TYPE == 'door_lock'){
                                        values = data[j].DATA.status_lock;
                                    }else{
                                        values = data[j].DATA.status;
                                    }
                                    let now = new Date(data[j].CREATED_AT);
                                    //count number of times device was triggered depending on device type
                                    if (now <= to) {
                                        if (data[j].device.DEVICE_TYPE == 'door_lock') {
                                            if (values == "0808") {
                                                count++;
                                            }
                                        }else{
                                            if (values == 1) {
                                                count++;
                                            }
                                        }
                                    }else{
                                        //count trigger if date exceeds limit
                                        if (data[j].device.DEVICE_TYPE == 'door_lock') {
                                            if (values == "0808") {
                                                count++;
                                            }
                                        }else{
                                            if (values == 1) {
                                                count++;
                                            }
                                        }
                                        var day = from;
                                        this.chartData[this.distinctDevices[i]].push({
                                            x: day,
                                            y: count
                                        });
                                        from = new Date(now);
                                        to = new Date(from);
                                        //set next interval from last date
                                        to.setMinutes(to.getMinutes() + 30);
                                        //reset count
                                        count = 0;
                                    }
                                //store temperature from temperature and humidity
                                }else if (data[j].device.DEVICE_TYPE == 'temp_hum'){
                                    newData['x'] = data[j].CREATED_AT;
                                    newData['y'] = data[j].DATA.temp;
                                    this.chartData[this.distinctDevices[i]].push(newData);
                                }else if(data[j].device.DEVICE_TYPE == 'ir_remote'){
                                    //stop loop and pass data to log creation function for remote
                                    this.irData(data);
                                    break;
                                //count up every time a switch is triggered
                                }else if(data[j].device.DEVICE_TYPE == 'wall_switch_2' || data[j].device.DEVICE_TYPE == 'wall_switch_3' || data[j].device.DEVICE_TYPE == 'embedded_switch_2' || data[j].device.DEVICE_TYPE == 'embedded_switch_3'){
                                    let values = '';
                                    values = data[j].DATA;
                                    let now = new Date(data[j].CREATED_AT);
                                    if (now <= to) {
                                        if (values[0].status == 1) {
                                            count++;
                                        }
                                        if (values[1].status == 1) {
                                            count++;
                                        }
                                        if (data[j].device.DEVICE_TYPE == 'wall_switch_3' || data[j].device.DEVICE_TYPE == 'embedded_switch_3') {
                                            if (values[2].status == 1) {
                                                count++;
                                            }
                                        }
                                    }else{
                                        if (values[0].status == 1) {
                                            count++;
                                        }
                                        if (values[1].status == 1) {
                                            count++;
                                        }
                                        if (data[j].device.DEVICE_TYPE == 'wall_switch_3' || data[j].device.DEVICE_TYPE == 'embedded_switch_3') {
                                            if (values[2].status == 1) {
                                                count++;
                                            }
                                        }
                                        var day = from;
                                        this.chartData[this.distinctDevices[i]].push({
                                            x: day,
                                            y: count
                                        });
                                        from = new Date(now);
                                        to = new Date(from);
                                        to.setMinutes(to.getMinutes() + 30);
                                        count = 0;
                                    }
                                }else{
                                    //set date and status as is
                                    newData['x'] = data[j].CREATED_AT;
                                    newData['y'] = data[j].DATA.status;
                                    this.chartData[this.distinctDevices[i]].push(newData);
                                }
                            }
                        }
                        //push maximum values for each device into an array
                        if (this.selectedDevice.DEVICE_TYPE !== 'ir_remote') {
                            max.push(Math.max.apply(Math, this.chartData[this.distinctDevices[i]].map(function(o) { return o.y; })));
                        }
                    }
                    //get maximum value from max array to modify chart max value
                    if (this.selectedDevice.DEVICE_TYPE !== 'ir_remote') {
                        this.range = Math.max(...max);
                    }
                    //change title based on device_type
                    if (data[0].device.DEVICE_TYPE == 'temp_hum') {
                        this.$children[1].ej2Instances.primaryYAxis.title = 'Temperature';

                    }else if (data[0].device.DEVICE_TYPE.includes('switch') || data[0].device.DEVICE_TYPE == 'gas_valve' || data[0].device.DEVICE_TYPE == 'gas_detector' || data[0].device.DEVICE_TYPE == 'door_lock' || data[0].device.DEVICE_TYPE == 'motion_detector'){
                        this.$children[1].ej2Instances.primaryYAxis.title = 'Times Opened';
                    }else {
                        this.$children[1].ej2Instances.primaryYAxis.title = 'Status';
                    }
                    //set maximum on Y axis based on maximum value for device type and refresh chart
                    this.$children[1].ej2Instances.primaryYAxis.maximum = this.range*1.5;
                    this.$children[1].ej2Instances.refresh();
                //if no devices sent, empty chart data and distinct devices
                }else{
                    this.chartData = {};
                    this.distinctDevices = '';
                    //refresh chart instance
                    // this.$children[1].ej2Instances.refresh();
                }
            }
        },
        computed:{
            //Function Name: eventListOrdered
            //Function Description: return eventLists as order by descending
            eventListOrdered: function(){
                return _.orderBy(this.eventLists, ['NOTIFICATION_ID'], ['desc'])
            },
        },
        mounted(){
            this.customCSS();
            this.displayStatus();
            this.getRoomDevices();
            //set locale to parent locale
            this.$i18n.locale = this.$parent.locale;
        }
    }
</script>>