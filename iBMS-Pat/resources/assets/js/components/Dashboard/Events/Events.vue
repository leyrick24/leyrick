<template>
    <!-- <div class="mt-3">             -->
        <div class="card rounded-0 border border-dark">
            <div class="card-body" id="dbEvents">
                <h5 class="card-title border-bottom-black pb-2">
                    <span class="font-weight-bold">
                        {{ $t('dashboard.event') }}
                    </span>
                </h5>
                <div class="">
                    <div class="custom-event-list" v-for="eventList in eventLists" v-b-tooltip.hover :title="eventList.CONTENT">
                        <div>
                            <i class="fa fa-fw fa-lg" :class="notifIcon(eventList.ERROR_FLAG)"></i>
                            {{checkEventSize(eventList.CONTENT)}}
                        </div>
                        <div>
                            {{ eventList.CREATED_AT}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!-- </div> -->
</template>
<script>
    export default {
        components: {},
        created () {
            this.getLogs();
        },
        data() {
            return {
                eventLists:[],
            }
        },
        methods: {
            //Function Name: getLogs
            //Function Description: Retrieves the four latest entries in system logs
            getLogs(){
                axios.get('logs/systemLogs?filter=TYPE:0&LIMIT=4')
                .then(response =>{
                    // console.log(response.data);
                    this.eventLists = response.data;
                    this.eventLists.forEach((v,i)=>this.eventLists[i]['ERROR_FLAG'] = 1);
                });
            },
            //Function Name: notifIcon
            //Function Description: Changes events icon based on error-flag
            //Params: errorFlag
            notifIcon(errorFlag){
                if(errorFlag == 1 ){
                    //success
                    return 'fa-check-circle text-success';
                }else if(errorFlag == 2 ){
                    //warning
                    return 'fa-exclamation-triangle text-warning';
                }else if(errorFlag == 3 ){
                    //failure
                    return 'fa-exclamation-circle text-danger';
                }else if(errorFlag == 4 ){
                    //danger
                    return 'fa-times-circle text-danger';
                }
            },
            //Function Name: checkEventSize
            //Function Description: Checks event string size and shortens string if too large
            //Params: content (eventList['CONTENT'])
            checkEventSize(content){
            	if(content && content.length > 45){
            		content = content.substring(0,50) + "...";
            	}
                return content;
            }
        },
        mounted(){
            //listen for online status event
            Echo.channel('test').listen('.onlineStatusEvent', (e) => {
            })
        }
    }
</script>