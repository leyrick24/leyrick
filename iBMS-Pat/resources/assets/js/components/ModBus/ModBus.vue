<template>
    <div>
        <div v-for="modData in modBusData">
            <div class="card mt-1">
                <div class="card-body">
                    <div><b>{{ modData.FLOOR }}</b></div>
                    <div>Current: {{ modData.METER_DATA }} kWh</div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        //initialize component
        components: {},
        created(){
            //this.getData();
            this.displayData();
        },
        data() {
            return {
                modBusData: {
                },
            }
        },
        methods: {
            //Function name: displayData
            //Function Description: display modbus data
            displayData(){
                 axios({
                  url: 'modbus-data/display',
                  method: 'post',
                })
                .then((response)=>
                   this.getModBus(response.data),
                )
                .catch((error) => console.log(error));
            },
            //Function Name: getModbusData
            //Function Description: get modbus data
            //Param: data (modbus data)
            getModBus(data){
                this.modBusData = data;
                var tempF2 = {};
                tempF2['METER_DATA'] = '0.0000';
                tempF2['FLOOR'] = '2nd Floor';
                var tempF3 = {};
                tempF3['METER_DATA'] = '0.0000';
                tempF3['FLOOR'] = '3rd Floor';
                var tempF4 = {};
                tempF4['METER_DATA'] = '0.0000';
                tempF4['FLOOR'] = '4th Floor';
                this.modBusData.push(tempF2,tempF3,tempF4);
            },
        },
        mounted () {
        },
    };
</script>