<!--
  Created: December 7, 2018 By: Jethro
  Update: December 17, 2018 By: TP Robert
  Update: March 26, 2019 By: Jethro
-->
<template>
  <div>
    <div v-if="lineChartData.length > 0">
      <ejs-chart style='display:block' :theme="theme" :background="'#0000'" align='center' id='chartcontainer' :title='title' :primaryXAxis='primaryXAxis' :primaryYAxis='primaryYAxis' :tooltip='tooltip' :chartArea='chartArea' :width='width' :height="height" :zoomSettings='zoom' :crosshair="crosshair">
        <e-series-collection>
          <e-series v-if="device.DEVICE_TYPE == 'electric_meter' || device.DEVICE_TYPE == 'panic_button'" :dataSource="lineChartData" :fill="'orange'" type='Column' xName='x' yName='y' width=2></e-series>
          <e-series v-if="device.DEVICE_TYPE == 'wall_switch_2' || device.DEVICE_TYPE == 'wall_switch_3' || device.DEVICE_TYPE == 'embedded_switch_2' || device.DEVICE_TYPE =='embedded_switch_3' || device.DEVICE_TYPE == 'temp_hum'" :dataSource="lineChartData" :fill="'orange'" type='Line' xName='x' yName='y1' :name="device.DEVICE_TYPE == 'temp_hum' ? 'Temp' : 'Switch 1'" width=1> </e-series>
          <e-series v-if="device.DEVICE_TYPE == 'wall_switch_2' || device.DEVICE_TYPE == 'wall_switch_3' || device.DEVICE_TYPE == 'embedded_switch_2' || device.DEVICE_TYPE =='embedded_switch_3' || device.DEVICE_TYPE == 'temp_hum'" :dataSource="lineChartData" :fill="'red'" type='Line' xName='x' yName='y2' :name="device.DEVICE_TYPE == 'temp_hum' ? 'Humidity' : 'Switch 2'" width=1> </e-series>
          <e-series v-else :dataSource="lineChartData" :fill="'#FF6600'" type='Line' xName='x' yName='y' :name="'Status'" width=2> </e-series>
          <e-series v-if="device.DEVICE_TYPE == 'wall_switch_3' || device.DEVICE_TYPE == 'embedded_switch_3'" :dataSource="lineChartData" :fill="'yellow'" type='Line' xName='x' yName='y3' :name="'Switch 3'" width=1> </e-series>
        </e-series-collection>
      </ejs-chart>
    </div>
  </div>
</template>
<script>
import Browser from '@syncfusion/ej2-base';
//enable line, legend, tooltip, zoom and scrollbar for vue chart
import { ChartPlugin, LineSeries, ColumnSeries, Legend, Tooltip, DateTime, Zoom, ScrollBar, Crosshair} from "@syncfusion/ej2-vue-charts";
export default {
  components: {},
  created () {
    this.generateChartData();
    let selectedTheme = location.hash.split("/")[1];
    selectedTheme = selectedTheme ? selectedTheme:"Material";
    this.theme = (selectedTheme.charAt(0).toUpperCase() + selectedTheme.slice(1)).replace(/-dark/i,"Dark");
  },
  props:{
    device:'',
    deviceStatusChart:Object,
  },
  data() {
    return {
      lineChart: '',
      loadingInterval: 1000,
      lineChartData: [],
      chartCursor: '',
      day: 0,
      processedData: [],
      created:'',
      dataLength: 0,
      startDate: new Date(),
      endDate: new Date(),
      startTime:new Date(),
      endTime:new Date(),
      //for binary data
      calledTimes:0,
      //for switches
      calledTimes1:0,
      calledTimes2:0,
      dateFrom:'',
      dateTo:'',
      count:0,
      from: new Date(),
      to: new Date(),
      //syncfusion chart data
      theme:'',
      title:'',
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
      height: '280px',
      tooltip: {
          enable: true,
          shared:true
      },
      crosshair: {
        enable:true,
        lineType:'Vertical'
      },
      zoom: {
        enableSelectionZooming: true,
        enableMouseWheelZooming: true,
        enableScrollbar: true,
        toolbarItems: ['Zoom', 'Pan', 'Reset']
      }
    }
  },
  //components chart will use declared on import
  provide: {
    chart: [LineSeries, ColumnSeries, Legend, Tooltip, DateTime, Zoom, ScrollBar, Crosshair]
  },
  methods: {
    //Function Name: generateChartData
    //Function Description: Generate chart data based on selected device
    //Param: device
    generateChartData() {
      var id = this.device.DEVICE_ID;
      if (this.device.DEVICE_TYPE != 'electric_meter') {
        //get device status
        axios.get('devices/' + id + '/processed-data')
          .then(response=>{
            if(response.data.length > 0){
              this.processedData = response.data;
              //render chart data based on device type
              this.dateFrom = new Date(this.processedData[0]['CREATED_AT']);
              if (this.device['DEVICE_TYPE'] == 'motion_detector' || this.device['DEVICE_TYPE']=='wall_switch_1' || this.device['DEVICE_TYPE']=='embedded_switch_1' || this.device['DEVICE_TYPE']=='gas_valve' || this.device['DEVICE_TYPE']=='smoke_detector' || this.device['DEVICE_TYPE']=='h2o_detector' || this.device['DEVICE_TYPE'] == 'water_valve' || this.device['DEVICE_TYPE']=='temp_light') {

                this.binaryData(this.processedData, this.dateFrom);

              }else if(this.device['DEVICE_TYPE'] == 'co2_detector' || this.device['DEVICE_TYPE'] == 'light_detector' || this.device['DEVICE_TYPE'] == 'dust_detector' || this.device['DEVICE_TYPE'] == 'curtain_1' || this.device['DEVICE_TYPE'] == 'gas_detector') {

                this.rangeData(this.processedData, this.dateFrom);

              }else if(this.device['DEVICE_TYPE'] == 'wall_switch_3' || this.device['DEVICE_TYPE'] == 'embedded_switch_3' || this.device['DEVICE_TYPE'] == 'wall_switch_2' || this.device['DEVICE_TYPE'] == 'embedded_switch_2'){
                console.log(this);
                this.switch3Data(this.processedData, this.dateFrom);

              }else if(this.device['DEVICE_TYPE'] == 'door_lock'){

                this.doorData(this.processedData, this.dateFrom);

              }else if (this.device['DEVICE_TYPE'] == 'temp_hum') {

                this.tempHumData(this.processedData, this.dateFrom);

              }else if(this.device['DEVICE_TYPE'] == 'panic_button') {

                this.panicData(this.processedData, this.dateFrom);

              }
            }else{
              this.processedData = response.data;
            }
        });
      }else{
        //for electric meters
        axios.post('modbus-data/indv-month', this.device)
        .then(response=>{
          this.processedData = response.data;
          let chartData= [];
          for(var i in this.processedData){
            chartData.push({
              x: new Date(this.processedData[i].months),
              y: this.processedData[i].usage
            });
          }
          this.lineChartData = chartData;
        })
      }
    },
    //Function Name: binaryData
    //Function Description: Generates chart data for devices with 1 and 0 status
    //Param: data (processedData), dateFrom: (dateFrom)
    binaryData(data, dateFrom) {
      var chartData = [];
      this.dataLength=data.length;
      this.from = dateFrom;
      this.to = new Date(this.from);
      this.to.setMinutes(this.to.getMinutes() + 10);
      this.startDate.setMonth(this.startDate.getMonth()-1);
      this.endDate = new Date();

      for(var j = 0; j < data.length; j++){
        //count status
        if (this.device['DEVICE_TYPE'] == 'wall_switch_1' || this.device['DEVICE_TYPE'] == 'embedded_switch_1') {
          this.count = data[j].DATA[0].status;
        }else{
          this.count = data[j].DATA.status;
        }
        var dateFrom = new Date(data[j].CREATED_AT);
        //check if data is from within this last month
        if (dateFrom >= this.startDate) {
          if (dateFrom <= this.endDate) {
            //check if data is within time interval
            if (dateFrom <= this.to) {
              //add up if status = 1
              if (this.count == 1) {
                this.calledTimes++;
              }
            //if data is outside time interval
            }else {
              if (this.count == 1) {
                this.calledTimes++;
              }
              var day = this.from;
              day.setMinutes(day.getMinutes());
              chartData.push({
                x: day,
                y: this.calledTimes
              });
              this.from = new Date(data[j].CREATED_AT);
              this.to = new Date(this.from);
              this.to.setMinutes(this.to.getMinutes() + 10);
              //set count up to 0 for next interval
              this.calledTimes=0;
            }
          }
        }
      }
      this.lineChartData = chartData;
    },
    //Function Name: rangeData
    //Function Description: Generates chart data for devices with no fixed statuses
    //Param: data (processedData), dateFrom (dateFrom)
    rangeData(data, dateFrom) {
      var chartData = [];
      this.dataLength=data.length;
      this.from = dateFrom;
      this.to = new Date(this.from);
      this.to.setMonth(this.to.getMonth() + 1);
      this.startDate.setMonth(this.startDate.getMonth()-1);
      this.endDate = new Date();

      for(var j = 0; j < data.length; j++){
        //push status and date as is
        this.count = data[j].DATA.status;
        var dateFrom = new Date(data[j].CREATED_AT);
        var countCurrent =0;
        if (dateFrom >= this.startDate) {
          if (dateFrom <= this.endDate) {
            var day = dateFrom;
            day.getMonth(day.getMonth());
            chartData.push({
              x: day,
              y: this.count
            });
          }
        }

      }
      if (chartData.length > 0) {
        this.lineChartData = chartData;
      }
    },
    //Function Name: switch3Data
    //Function Description: Generates chart data for devices with 2 or 3 switches
    //Param: data (processedData), dateFrom (dateFrom)
    switch3Data(data, dateFrom) {
      var chartData = [];
      this.dataLength= data.length;
      this.from = dateFrom;
      this.to = new Date(this.from);
      this.to.setMinutes(this.to.getMinutes() + 30);
      this.startDate.setMonth(this.startDate.getMonth()-1);
      this.endDate = new Date();

      for(var j = 0; j < data.length; j++){

        //count status
        this.count = data[j].DATA;
        var dateFrom = new Date(data[j].CREATED_AT);

        var countCurrent =0;
        if (dateFrom >= this.startDate) {
          if (dateFrom <= this.endDate) {
            //Check number of times opened per day
            if (dateFrom <= this.to) {
              //Get status per switch
              if (this.count[0].status==1) {
                this.calledTimes++;
              }
              if (this.count[1].status==1) {
                this.calledTimes1++;
              }
              //for 3 gang switch, count additional switch data
              if (this.device['DEVICE_TYPE'] == 'wall_switch_3' || this.device['DEVICE_TYPE'] == 'embedded_switch_3') {
                if (this.count[2].status==1) {
                  this.calledTimes2++
                }
              }
              //set data if last entry
              if (j == data.length) {
                var day = this.from;
                day.setMinutes(day.getMinutes());
                if (this.device['DEVICE_TYPE'] == 'wall_switch_3' || this.device['DEVICE_TYPE'] == 'embedded_switch_3') {
                  chartData.push({
                    x: day,
                    y1: this.calledTimes,
                    y2: this.calledTimes1,
                    y3: this.calledTimes2
                  });
                }else{
                  chartData.push({
                    x: day,
                    y1: this.calledTimes,
                    y2: this.calledTimes1
                  });
                }
                this.from = dateFrom;
                this.to = new Date(this.from);
                this.calledTimes=0;
                this.calledTimes1=0;
                this.calledTimes2=0;
              }
            } else {
              if (this.count[0].status==1) {
                this.calledTimes++;
              }
              if (this.count[1].status==1) {
                this.calledTimes1++;
              }
              if (this.device['DEVICE_TYPE'] == 'wall_switch_3' || this.device['DEVICE_TYPE'] == 'embedded_switch_3') {
                if (this.count[2].status==1) {
                  this.calledTimes2++
                }
              }
              var day = this.from;
              day.setMinutes(day.getMinutes());
              if (this.device['DEVICE_TYPE'] == 'wall_switch_3' || this.device['DEVICE_TYPE'] == 'embedded_switch_3') {
                chartData.push({
                  x: day,
                  y1: this.calledTimes,
                  y2: this.calledTimes1,
                  y3: this.calledTimes2
                });
              }else{
                chartData.push({
                  x: day,
                  y1: this.calledTimes,
                  y2: this.calledTimes1
                });
              }
              this.from = dateFrom;
              this.to = new Date(this.from);
              //set duration to every 30 minutes
              this.to.setMinutes(this.to.getMinutes() + 30);
              this.calledTimes=0;
              this.calledTimes1=0;
              this.calledTimes2=0;
            }
          }
        }
      }
      this.lineChartData = chartData;
    },
    //Function Name: doorData
    //Function Description: Generates chart data for door lock devices
    //Param: data (processedData), dateFrom (dateFrom)
    doorData(data, dateFrom) {
      var chartData = [];
      this.dataLength=data.length;
      this.from = dateFrom;
      this.to = new Date(this.from);
      this.to.setMinutes(this.to.getMinutes() + 1)
      this.startDate.setMonth(this.startDate.getMonth()-1);
      this.endDate = new Date();

      for(var j = 0; j < data.length; j++){

        //get door lock status
        this.count = data[j].DATA.status_lock;
        var dateFrom = new Date(data[j].CREATED_AT);

        var countCurrent =0;

        if (dateFrom >= this.startDate) {
          if (dateFrom <= this.endDate) {
            if (dateFrom <= this.to) {
              //check status_lock if opened
              if (this.count == "0808") {
                this.calledTimes++;
              }

            } else {
              if (this.count == "0808") {
                this.calledTimes++;
              }
              var day = this.from;
              day.setMinutes(day.getMinutes());
              chartData.push({
                x: day,
                y: this.calledTimes
              });
              this.from = new Date(dateFrom);
              this.to = new Date(this.from);
              this.to.setMinutes(this.to.getMinutes() + 30);
              this.calledTimes=0;
            }
          }
        }
      }
      this.lineChartData = chartData;
    },
    //Function Name: tempHumData
    //Function Description: Generates chart data for temperature and humidity sensor devices
    //Param: data (processedData), dateFrom (dateFrom)
    tempHumData(data, dateFrom) {
      var chartData = [];
      this.dataLength= data.length;
      this.startDate.setMonth(this.startDate.getMonth()-1);
      this.endDate = new Date();
      for(var j = 0; j < data.length; j++){

        var dateFrom = new Date(data[j].CREATED_AT);
        if (dateFrom >= this.startDate) {
          if (dateFrom <= this.endDate) {
            var day = dateFrom;
            day.setMinutes(day.getMinutes());
            chartData.push({
              x: day,
              y1: data[j].DATA.temp,
              y2: data[j].DATA.hum
            });
          }
        }
      }
      this.lineChartData = chartData;
    },
    //Function Name: panicData
    //Function Description: Generates chart data for panic button devices
    //Param: data (processedData), dateFrom (dateFrom)
    panicData(data, dateFrom){
      var chartData = [];
      this.dataLength=data.length;
      this.from = dateFrom;
      this.to = new Date(this.from);
      this.to.setMinutes(this.to.getMinutes() + 5);
      this.startDate.setMonth(this.startDate.getMonth()-1);
      this.endDate = new Date();


      for(var j = 0; j < data.length; j++){
        //count status
        if (this.device['DEVICE_TYPE'] == 'wall_switch_1' || this.device['DEVICE_TYPE'] == 'embedded_switch_1') {
          this.count = data[j].DATA[0].status;
        }else{
          this.count = data[j].DATA.status;
        }
        var dateFrom = new Date(data[j].CREATED_AT);
        //check if data is from within this last month
        if (dateFrom >= this.startDate) {
          if (dateFrom <= this.endDate) {
            var day = dateFrom;
            day.setSeconds(day.getSeconds());
            chartData.push({
              x: day,
              y: 1,
            });
          }
        }
      }
      this.lineChartData = chartData;
    }
  },
  watch: {
    device: function () {
      // console.log('Device Changed: '+this.device['DEVICE_TYPE']);
      // console.log('blahblahblah')
      this.generateChartData();
    }
  }
}
</script>