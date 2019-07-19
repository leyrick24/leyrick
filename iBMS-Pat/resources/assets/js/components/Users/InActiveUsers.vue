<template> 
    <div>
        <!-- card-body -->
        <div class="px-0">
            <div class="px-0">
                <!-- Table -->
                <b-table hover :items="users" :fields="tableData.fields":current-page="tableData.currentPage" :per-page="tableData.perPage" :filter="tableData.filter" :sort-by.sync="tableData.sortBy" :sort-desc.sync="tableData.sortDesc">
                    <template slot="BUTTON" slot-scope="row">
                        <!-- Disable User Button -->
                        <a class="pointer" @click="openModal('enableModal',row.item)" data-toggle="popover" data-container="body" data-content="Enabled" data-placement="right">
                            <i class="text-primary fa fa-check fa-lg" aria-hidden="true"></i>
                        </a>
                    </template>
                </b-table>
                <!-- End Table -->
                <!-- pagination -->
                <b-row>
                    <b-col>
                        <b-pagination :total-rows="tableData.totalRows" :per-page="tableData.perPage" v-model="tableData.currentPage" />
                    </b-col>
                </b-row>
                <!-- end pagination -->
            </div>
            <!-- </div> -->
        </div>
        <div class="col-lg-4">
            <div class="d-flex pb-3">
                <!-- search -->
                <b-input-group class="input-grouppl-0">
                    <b-input-group-prepend>
                        <span class="input-group-text" id="basic-addon1">
                                <i class="fa fa-search" aria-hidden="true"></i>
                        </span>
                    </b-input-group-prepend>
                    <b-form-input v-model="tableData.filter" placeholder="Type to Search"/>
                </b-input-group>
                <!-- search end-->
            </div>
        </div>
    </div>
</template>
<script>
export default {
    components: {},
    props:['usertype'],
    data() {
        return {                                     
            //Users from Database
            users:[],
            tableData:{
                currentPage:1,
                perPage:10,
                totalRows:0,
                pageOptions:[5,10,15],
                filter:null,
                sortBy: 'USERROLE',
                sortDesc: false,
                fields:[
                    {key:'USERNAME',label:"USERNAME",sortable: true},
                    {key:'USER_ID',label:"USER ID",sortable: true},
                    {key:'USERROLE',label:"USER ROLE",sortable: true},
                ]
            },
            chosenModal:'',
            modalData:''
        }
    },
    created(){
        //getInActiveUsers
        this.getInActiveUsers();

        this.$bus.$on('getInActiveUsers',data => {
            this.getInActiveUsers();
        });
    },
    methods: {
        //Function Name: getInActiveUsers
        //Function Description: Get all Inactive users
        getInActiveUsers(){
            axios.post('users/users-data',{USER_TYPE:this.usertype,REG_FLAG:0})
            .then(response=>{
                this.users = response.data;
                this.tableData.totalRows =  response.data.length;
                $.each(this.users, function( key, value ) {
                    value["USER_ROLE"];
                    if(value.USER_TYPE == 1){
                        value.USERROLE = 'Admin';
                    }else if(value.USER_TYPE == 2){
                        value.USERROLE = 'Doctor';
                    }else if(value.USER_TYPE == 3){
                        value.USERROLE = 'Nurse';
                    }
                });
            });
        },
        //Function Name: openModal
        //Function Description: Open Specific Modal
        // Param: modal = name of modal
        //        data = data chosen
        openModal(modal,data){
            //Pass data to modal if Edit User Modal
            //Change Password User Modal
            if(modal=="enableModal"){
                //Sweet Alert
                this.$swal({
                        title:'Are you sure',
                        text:'do you want to enable?',
                        type:'warning',
                        showCancelButton:true,
                        confirmButtonColor:'#3085d6',
                        cancelButtonColor:'#d33',
                        confirmButtonText:'Enable'
                }).then((result)=>{
                    if(result.value){
                        this.enableUser(data.USER_ID);
                    }
                });
            }
            //Open Modal
            this.chosenModal = modal;
        },
        //Function Name: enableUser
        //Function Description: Enable the inactive user
        // Param: userid
        enableUser(userid){
            axios.post('users/enable-user',{USERID:userid})
            .then(response=>{
                this.$swal('Enabled!','This user has been enabled','success');
                this.getInActiveUsers();
                this.$bus.$emit('getActiveUsers');
            });
        },
        //Function closeModal
        //Function Description: Close All Modal
        closeModal(){
            this.chosenModal = '';
        }
    },
    mounted () {

    },
};
</script>