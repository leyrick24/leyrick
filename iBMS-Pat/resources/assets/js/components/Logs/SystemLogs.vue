<template>
	<div class="row">
		<div v-if="loading == false && renderedData.length > 0" class="col-md-12 h-725 mb-1">
			<b-table small 	v-if="renderedData"
							:fields="table.fields"
							:items="renderedData"
							:per-page="table.perPage"
							:current-page="table.currentPage"
							:filter="searchText">
				<template slot="CONTENT" slot-scope="data">
					<div v-b-tooltip.hover :title="data.item.CONTENT">
						{{ checkTextSize(data.item.CONTENT) }}
					</div>
				</template>
			</b-table>
		</div>
		<div v-else-if="loading == false && renderedData.length == 0" class="logs-div h-725">
			<div>{{$t('logs.noData')}}</div>
		</div>
		<div v-else class="logs-div h-725">
			<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
		</div>
		<div class="col-md-12">
			<div class="d-flex justify-content-between" :class="customPagination">
				<div></div>
				<b-pagination class="pl-5" :total-rows="table.totalRows" :per-page="table.perPage" v-model="table.currentPage">
				</b-pagination>

				<div class="">
						<button id="exportLogsBtn" class="btn btn-primary btn-sm mr-3">
							{{$t('logs.export')}}
						</button>
						<b-popover target="exportLogsBtn" placement="top" title="Choose File Format" triggers="focus">
							<div class='d-flex justify-content-around'>
								<button @click='exportPDF' type='button' class='btn btn-danger btn-sm'>PDF</button>
								<spinner v-if="showExportIcon"></spinner>
								<button @click='exportCSV' type='button' class='btn btn-success btn-sm'>CSV</button>
							</div>
						</b-popover>
				</div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">

	var jsPDF = require('jspdf');
	require('jspdf-autotable');

	export default{
		components:{

		},
		props:{
			searchText:'',
			startDate:'',
			endDate:'',
			user:'',
		},
		data(){
			return{
				table:{
                    fields:[
                        {key:'LOGS_ID',label:'ID'},
                        {key:'TYPE',label:'Type'},
                        {key:'INSTRUCTION_TYPE',label:'Instruction Type'},
                        {key:'CONTENT',label:'Content'},
                        // {key:'IP',label:'Sender'},
                        {key:'HOST',label:'Sender'},
                        {key:'CREATED_AT',label:'Date'},
                    ],
                    totalRows:0,
                    perPage:20,
                    currentPage:1
                },
                logs:'',
                renderedData:'',
                showExportIcon:false,
                loading:true
			};
		},
		created(){
			//get first 20 to load first batch of data then load the rest
			axios.get('logs/systemLogs?LIMIT=20')
			.then(response=>{
			 	this.logs = response.data;
			 	this.renderedData = this.logs;
			 	this.loading = false;
			 	axios.get('logs/systemLogs')
			 	.then(response=>{
			 		this.logs = response.data;
			 		this.renderedData = this.logs;
			 		this.table['totalRows'] = response.data.length;
			 		this.filterByDate();
			 	});
			});
		},
		methods:{
			//Function Name: checkTextSize
			//Function Description: Checks if text is more than 90 characters long and will shorten to fit into table
			//Param: content ('string')
			checkTextSize(content){
            	if(content && content.length > 90){
            		content = content.substring(0,100) + "...";
            	}
                return content;
            },
            //Function Name: exportCSV
			//Function Description: Create a csv file and download on click
			//Param: renderedData (logs)
			exportCSV: function () {
		      var csvContent = "data:text/csv;charset=utf-8,";
		      csvContent +="\n"
		      csvContent +="ID,Type,Instruction_Type,Content,IP,Host,Date\n"
		      csvContent += this.renderedData.map(function(d,e,f){
		      	let tempArrayData = [];
		        for(var i in d){
		        	tempArrayData.push(d[i]);
		        }
		        return tempArrayData;
		      })

		      .join('\n')
		      .replace(/(^\{)|(\}$)/mg, '');
		      console.log(csvContent);

		      var link = document.createElement('a');
		      link.download = this.exportFilename + '.csv';
		      link.href = csvContent;
		      link.click();

		    },
		    //Function Name: exportPDF
			//Function Description: Export pdf file
		    exportPDF(){
		    	this.$swal({
		    		position: 'center',
		    		type: 'info',
		    		title: 'The pdf file will be downloaded shortly. Please Wait.',
		    		showConfirmButton: false,
		    		timer: 2000,
		    	});
				this.showExportIcon = true;
				setTimeout(this.processexportPDF,1000);
		    },
		    //Function Name: processexportPDF
		    //Function Description: generate pdf for export
		    //Param: renderedData (logs), username
			processexportPDF(){
				this.showExportIcon = true;
				var vm = this;
				if (vm.renderedData.length <= 50000) {
					try{
						var username = this.user.USERNAME;
						var companyLogo = new Image;
						var companyName = "RS Hospital System Logs Report";		//Comapny Name
						companyLogo.src = "img/gti-logo.png";							//Company Logo
						console.log(companyLogo);
						var columns = [
										{title:"ID", dataKey:"LOGS_ID"},
										{title:"Type", dataKey:"TYPE"},
										{title:"Instruction Type", dataKey:"INSTRUCTION_TYPE"},
										{title:"Content", dataKey:"CONTENT"},
										{title:"IP", dataKey:"IP"},
										{title:"Date",dataKey:"CREATED_AT"}
										];

						var doc = new jsPDF({
							orientation:'landscape',
							format:'legal'
						});

						var totalPagesExp = "{total_pages_count_string}";
						var pageWidth = doc.internal.pageSize.width || doc.internal.pageSize.getWidth();
						var columnStyles = {
											LOGS_ID:{			columnWidth:15},
											TYPE:{				columnWidth:15},
											INSTRUCTION_TYPE:{	columnWidth:32},
											CONTENT:{			columnWidth:''},
											IP:{				columnWidth:30},
											CREATED_AT:{		columnWidth:40}
											}
						var pageContent = function(data){
							//HEADER
							doc.setFontSize(15);
							doc.setTextColor(40);
							doc.setFontStyle('normal');
							doc.addImage(companyLogo,data.settings.margin.left,10,15,15);
							doc.text(companyName,(pageWidth / 2) -145, 20);
			                // FOOTER
			                var str = "Page " + data.pageCount;
			                // Total page number plugin only available in jspdf v1.0+
			                if (typeof doc.putTotalPages === 'function') {
			                    str = str + " of " + totalPagesExp;
			                }
			                doc.setFontSize(10);
			                var pageHeight = doc.internal.pageSize.height || doc.internal.pageSize.getHeight();
			                doc.text("Author: " + username, data.settings.margin.left, pageHeight  - 15);
			                doc.text("Date Generated: " + vm.timestamp, data.settings.margin.left, pageHeight  - 10);
			                doc.text(str, (pageWidth / 2) - 20, pageHeight  - 10);
						};
						doc.autoTable(columns, vm.renderedData, {
			                addPageContent: pageContent,
			                margin: {top: 30},
			                theme: 'grid',
			                columnStyles:columnStyles
		            	});
		            	if(typeof doc.putTotalPages === 'function'){
		            		doc.putTotalPages(totalPagesExp);
		            	}
		            	doc.save(vm.exportFilename);
		            	this.showExportIcon = false;
		            }catch(error) {
		            	if(error.message.includes("UNKNOWN")){
		            		vm.$refs.pdf.click();
		            	}else{
		            		this.$swal({
					    		position: 'center',
					    		type: 'error',
					    		title: 'An error has occured!',
					    		showConfirmButton: false,
					    		timer: 2000,
							});
							console.log(error);
				    		this.showExportIcon = false;
		            	}
		            }
				}else{
					this.$swal({
		    		position: 'center',
		    		type: 'error',
		    		title: 'Too many rows!!',
		    		showConfirmButton: false,
		    		timer: 2000,
					});
		    		this.showExportIcon = false;
				}
			},
			//Function Name: filterByDate
			//Function Description: Filter Logs By Date
			//Param: startDate, endDate
			filterByDate(){
				let self = this;
				this.renderedData = _.filter(this.logs,function(data){
					if(self.startDate != '' && self.endDate != ''){
						return data.CREATED_AT >= self.startDate + ' 00:00:00' && data.CREATED_AT <= self.endDate + '24:59:59';
					}else{
						return self.logs;
					}
				});
			}
		},
		computed:{
			// Change pagination interface if too many data.
			customPagination:function(){

				if(this.renderedData.length <= 60){
					return 'custom-pagination-white';
				}
			},
			//return current date
			timestamp: function () {
            	return this.$moment().format('MM/DD/YYYY HH:mm:ss');
        	},
        	//returns file name with attached date
        	exportFilename:function(){
        		return 'logs_' + this.$moment().format('MMDDYYYYHHmmss');
        	}
		},
		watch:{
			//filter data on change of end date
			endDate:function(){
				this.filterByDate();
			},
			//filter data on change of start date
			startDate:function(){
				this.filterByDate();
			},
			//change total rows to update table pagination on logs update
			renderedData:function(){
				this.table['totalRows'] = this.renderedData.length;
			},
			//search function
			searchText:function(){
				this.$forceUpdate();
			}
		}
	};
</script>