<template>
    <div>
        <div class="wrapper hardware-img-bg">
            <div class="container-fluid container-bg">
                <div class="col-sm-auto d-flex justify-content-between align-items-center px-0 py-3">
                    <div>{{$t('navbar.logs')}} </div>
                    <clock :locale="$i18n.locale"></clock>
                </div>
                <div class="row h-826">
                    <div class="pb-3 col-md-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="nav nav-tabs nav-line w-100" role="tablist">
                                <a class="nav-item nav-link nav-header-blue active" data-toggle="tab" href="#system-logs" role="tab" aria-controls="nav-device" aria-selected="true">{{$t('logs.sysLogs')}}</a>
                                <a v-if="user.USER_TYPE != 3" class="nav-item nav-link nav-header-blue" data-toggle="tab" href="#audit-trail-logs" role="tab" aria-controls="nav-device" aria-selected="false">{{$t('logs.auditLogs')}}</a>
                            </div>
                            <!-- From-To Date -->
                           <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <input v-model="startDate" class="form-control form-control-sm" type="date" value="" id="date-from">
                                            </div>
                                            <div class="col-md-1 d-flex justify-content-center align-items-center px-0">
                                                <i class="fa fa-arrow-right fa-lg"></i>
                                            </div>
                                            <div class="col-md-5">
                                                <input v-model="endDate"class="form-control form-control-sm" type="date" value="" id="date-to">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Search Text -->
                                    <div class="input-group col-md-4 mb-1">
                                        <input v-model="searchText" type="text" class="form-control form-control-sm" placeholder="Search">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fa fa-search" aria-hidden="true"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="tab-content tab-bg-blue"  :class="tableHeight" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="system-logs" role="tabpanel">
                                <SystemLogs :user="user" :searchText="searchText" :startDate="startDate" :endDate="endDate"></SystemLogs>
                            </div>
                            <div class="tab-pane fade" id="audit-trail-logs" role="tabpanel">
                                <AuditTrailLogs :user="user" :searchText="searchText" :startDate="startDate" :endDate="endDate"></AuditTrailLogs>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Footer></Footer>
    </div>
</template>
<script type="text/javascript">
    import SystemLogs from './SystemLogs.vue';
    import AuditTrailLogs from './AuditTrailLogs.vue';
    export default{
        components:{
            SystemLogs,
            AuditTrailLogs
        },
        props: ['userid'],
        data(){
            return{
                userId: this.userid,
                user: 'hello',
                searchText:'',
                startDate:'',
                endDate:'',
                logs: 'hi',
                syslog: this.syslog
            };
        },
        created(){
            this.getUser();
            this.dateTime();
        },
        mounted(){
            this.$i18n.locale = this.$parent.locale;
        },
        watch:{
            //Prevent Special Characters
            searchText:function(newVal){
                let re= /[^A-Z0-9]/gi;
                this.$set(this,'searchText',newVal.replace(re,''));
            }
        },
        methods:{
            //Function Name: dateTime
            //Function Description: sets the filter for logs for 1 week
            dateTime(){
                var lastMonth = new Date();
                lastMonth.setDate(lastMonth.getDate() - 7);
                this.startDate = lastMonth.toISOString().slice(0,10);
                this.endDate = new Date().toISOString().slice(0,10);
            },
            //Function Name: getUser
            //Function Description: Get User
            //Param: user_id, user_status, user_type
            syslog(){
                axios.get('logs', {LOGS_ID:this.syslog,LOGS_ID:1,INSTRUCTION_TYPE:1})
                .then(response => {
                    this.user = response.data;
                })
                .catch(errors => {
                    console.log(errors);
                });

            },
            getAll(){
                axios.post('users/user-data/', {USER_ID:this.userId,USER_STATUS:1,USER_TYPE:1})
                .then(response => {
                    this.user = response.data;
                })
                .catch(errors => {
                    console.log(errors);
                });

            },
        },
        computed:{
            tableHeight(){
                var tbHeight = '';
                if(this.$screenHeight > 800){
                    if(!this.$isMobile.isMobile){
                        tbHeight = 'h-775';
                    }else{
                        if(this.pagination.total >= 10){
                            tbHeight = 'h-um';
                        }else{
                            tbHeight = 'h-775';
                        }
                    }
                }else{
                    tbHeight = 'h-um';
                }
                return tbHeight;
            }
        }
    };
</script>