<template>
    <div v-if="showPieChart == 'device'" class="">
        <div id="pieChartDevice" class="chart-div"></div>
        <div id="pieChartLoading" class="chart-loading">
          <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
        </div>
    </div>
    <div v-else-if="showPieChart == 'gateway'" class="">
        <div id="pieChartGateway" class="chart-div"></div>
        <div id="pieChartLoading2" class="chart-loading">
          <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
        </div>
    </div>
</template>
<script>
    export default {
        props: ['showPieChart'],
        components: {},
        created () {
        },
        data() {
            return {
                status:[],
            }
        },
        methods: {
            //Function Name: getStatus
            //Function Description: Changes status data and adds image sources to be used in dashboard
            //Params: Data from displayData()
            getStatus(data){
                data.forEach((i) => this.status.push(i));

                if (this.status[0].onlineGateway > 9)  {
                  var src = this.status[0].onlineGateway.toString().split('');
                  if (this.status[0].offlineGateway > 0) {
                    src.forEach((v,i)=> src[i] = '/storage/app/public/dashboard/numbers/'+v+'.png');
                  }else{
                    src.forEach((v,i) => src[i] = '/storage/app/public/dashboard/numbers/'+v+'.png');
                  }
                  this.$set(this.status[0], "onlineGatewayImg", src);
                }else if (this.status[0].onlineGateway < 10 && this.status[0].onlineGateway >= 0) {
                  var src = ("0" + this.status[0].onlineGateway).slice(-2);
                  src = src.toString().split('');
                  if (this.status[0].offlineGateway > 0) {
                    src.forEach((v,i)=> src[i] = '/storage/app/public/dashboard/numbers/'+v+'.png');
                  }else{
                    src.forEach((v,i) => src[i] = '/storage/app/public/dashboard/numbers/'+v+'.png');
                  }
                  this.$set(this.status[0], "onlineGatewayImg", src);
                }else {
                  if (this.status[0].offlineGateway > 0) {
                    src.forEach((v,i)=> src[i] = '/storage/app/public/dashboard/numbers/'+v+'.png');
                  }else{
                    src.forEach((v,i) => src[i] = '/storage/app/public/dashboard/numbers/'+v+'.png');
                  }
                }


                // // Onlince Device Number Image
                if (this.status[0].onlineDevices > 9)  {
                  var src = this.status[0].onlineDevices.toString().split('');
                  if (this.status[0].offlineDevices > 0) {
                    src.forEach((v,i)=> src[i] = '/storage/app/public/dashboard/numbers/'+v+'.png');
                  }else{
                    src.forEach((v,i) => src[i] = '/storage/app/public/dashboard/numbers/'+v+'.png');
                  }
                  this.$set(this.status[0], "onlineDevicesImg", src);
                }else if (this.status[0].onlineDevices < 10 && this.status[0].onlineDevices >= 0) {
                  var src = ("0" + this.status[0].onlineDevices).slice(-2);
                  src = src.toString().split('');
                  if (this.status[0].offlineDevices > 0) {
                    src.forEach((v,i)=> src[i] = '/storage/app/public/dashboard/numbers/'+v+'.png');
                  }else{
                    src.forEach((v,i) => src[i] = '/storage/app/public/dashboard/numbers/'+v+'.png');
                  }
                  this.$set(this.status[0], "onlineDevicesImg", src);
                }else {
                  if (this.status[0].offlineDevices > 0) {
                    src.forEach((v,i)=> src[i] = '/storage/app/public/dashboard/numbers/'+v+'.png');
                  }else{
                    src.forEach((v,i) => src[i] = '/storage/app/public/dashboard/numbers/'+v+'.png');
                  }
                }

            },
            //Function Name: displayData
            //Function Description: Retrieve Gateway and Device status from database then emit data to dashboard
            displayData(){

                axios({url: 'status',method: 'post',data: {}})
                .then((response)=>
                  this.getStatus(response.data),
                )
                .catch((error) => console.log(error));

                this.$emit('ready', this.status);
            },
            //Function Name: replacementStatus
            //Function Description: Replaces status data with new data on status update event
            replacementStatus(data){
                data.forEach((i) => this.status.splice(0,1,i));

                if (this.status[0].onlineGateway > 9)  {
                  var src = this.status[0].onlineGateway.toString().split('');
                  if (this.status[0].offlineGateway > 0) {
                    src.forEach((v,i)=> src[i] = '/storage/app/public/dashboard/numbers/'+v+'.png');
                  }else{
                    src.forEach((v,i) => src[i] = '/storage/app/public/dashboard/numbers/'+v+'.png');
                  }
                  this.$set(this.status[0], "onlineGatewayImg", src);
                }else if (this.status[0].onlineGateway < 10 && this.status[0].onlineGateway >= 0) {
                  var src = ("0" + this.status[0].onlineGateway).slice(-2);
                  src = src.toString().split('');
                  if (this.status[0].offlineGateway > 0) {
                    src.forEach((v,i)=> src[i] = '/storage/app/public/dashboard/numbers/'+v+'.png');
                  }else{
                    src.forEach((v,i) => src[i] = '/storage/app/public/dashboard/numbers/'+v+'.png');
                  }
                  this.$set(this.status[0], "onlineGatewayImg", src);
                }else {
                  if (this.status[0].offlineGateway > 0) {
                    src.forEach((v,i)=> src[i] = '/storage/app/public/dashboard/numbers/'+v+'.png');
                  }else{
                    src.forEach((v,i) => src[i] = '/storage/app/public/dashboard/numbers/'+v+'.png');
                  }
                }


                // // Onlince Device Number Image
                if (this.status[0].onlineDevices > 9)  {
                  var src = this.status[0].onlineDevices.toString().split('');
                  if (this.status[0].offlineDevices > 0) {
                    src.forEach((v,i)=> src[i] = '/storage/app/public/dashboard/numbers/'+v+'.png');
                  }else{
                    src.forEach((v,i) => src[i] = '/storage/app/public/dashboard/numbers/'+v+'.png');
                  }
                  this.$set(this.status[0], "onlineDevicesImg", src);
                }else if (this.status[0].onlineDevices < 10 && this.status[0].onlineDevices >= 0) {
                  var src = ("0" + this.status[0].onlineDevices).slice(-2);
                  src = src.toString().split('');
                  if (this.status[0].offlineDevices > 0) {
                    src.forEach((v,i)=> src[i] = '/storage/app/public/dashboard/numbers/'+v+'.png');
                  }else{
                    src.forEach((v,i) => src[i] = '/storage/app/public/dashboard/numbers/'+v+'.png');
                  }
                  this.$set(this.status[0], "onlineDevicesImg", src);
                }else {
                  if (this.status[0].offlineDevices > 0) {
                    src.forEach((v,i)=> src[i] = '/storage/app/public/dashboard/numbers/'+v+'.png');
                  }else{
                    src.forEach((v,i) => src[i] = '/storage/app/public/dashboard/numbers/'+v+'.png');
                  }
                }

            },
        },
        mounted(){
          //run function after everything is loaded
          this.displayData();
          //Listen to incoming device update
          Echo.channel('test').listen('.onlineStatusEvent', (e) => {
            axios({url: 'status',method: 'post',data: {}})
            .then((response)=>
              this.replacementStatus(response.data),
            )
            .catch((error) => console.log(error));

            this.$emit('ready', this.status);
          }).listen('.onlineDeviceEvent', (e) => {
            console.log(e.data);
            axios({url: 'status',method: 'post',data: {}})
            .then((response)=>
              this.replacementStatus(response.data),
            )
            .catch((error) => console.log(error));

            this.$emit('ready', this.status);
          });
        }
    }
</script>>