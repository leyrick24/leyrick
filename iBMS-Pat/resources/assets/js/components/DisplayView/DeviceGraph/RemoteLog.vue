<!-- Edited:
	April 25, 2019 By: Jethro -->
<template>
	<div>
		<div class="h-250" v-if="deviceData.length > 0">
			<b-table small
					:items="deviceData"
					:fields="table.fields"
					:current-page="table.currentPage"
					:per-page="table.perPage">
			</b-table>
		</div>
		<div v-if="deviceData.length > 7">
			<div class="d-flex justify-content-center" :class="table.totalRows > 20 ? '':'custom-pagination-black'">
				<b-pagination class="mb-0" :total-rows="table.totalRows" :per-page="table.perPage" v-model="table.currentPage" />
			</div>
		</div>
		<div v-if="deviceData.length == 0">
			<div>{{$t('devop.noremote')}}</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default{
		components: {
		},
		props:{
			device:'',
		},
		data() {
			return{
				deviceData:[],
				table:{
					currentPage:1,
					perPage:7,
					totalRows:0,
					fields:[
						{key:'TARGET',label:'Appliance'},
						{key:'BRAND',label:'Brand'},
						{key:'COMMAND',label:'Command'},
						{key:'DATE',label:'Date'},
					],
				},
			}
		},
		created() {
			this.getRemoteData();
		},
		methods:{
			//Function Name: getRemoteData
			//Function Description: Get processed data for IR remote
			//Param: device['DEVICE_ID']
			getRemoteData(){
				this.deviceData = [];
				axios.get('devices/'+this.device.DEVICE_ID+'/processed-data')
				.then(response=>{
					let processed = response.data;
					for (var i in processed){
						if (processed[i].DATA[0].status != null) {
							let irdata = {};
							irdata['TARGET'] = processed[i].DATA[0].type;
							irdata['BRAND'] = processed[i].DATA[0].brand;
							if (processed[i].DATA.status == 1) {
								irdata['COMMAND'] = 'Power On';
							}else{
								irdata['COMMAND'] = 'Power Off';
							}
							irdata['DATE'] = processed[i].CREATED_AT;
							this.deviceData.push(irdata);
						}else {
							let irdata = {};
							irdata['TARGET'] = processed[i].DATA[0].type;
							irdata['BRAND'] = processed[i].DATA[0].brand;
							irdata['COMMAND'] = 'Temp: ' + processed[i].DATA[0].temp_value;
							irdata['DATE'] = processed[i].CREATED_AT;
							this.deviceData.push(irdata);
						}
					}
					this.table.totalRows = this.deviceData.length;
				})
			}
		},
		watch:{
			device:function(){
				this.getRemoteData();
			}
		}
	}
</script>