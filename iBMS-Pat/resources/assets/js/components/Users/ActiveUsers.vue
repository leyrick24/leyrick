<template>
    <div style="height:680px" class="d-flex justify-content-between flex-column">
        <div class="position-relative pt-2 mb-32px">
            <!-- card-body -->
            <div class="col-md-12 px-0">
                <div class="px-0">
                    <!-- Table -->
                    <b-table fixed
                        :items="users"
                        :fields="tableData.fields"
                        :current-page="tableData.currentPage"
                        :per-page="tableData.perPage"
                        :filter="tableData.filter"
                        :sort-by.sync="tableData.sortBy"
                        :sort-desc.sync="tableData.sortDesc"
                        @row-clicked="showUserProfile"
                        @filtered="onFiltered"

                    >
                        <template slot="USERNAME" slot-scope="row">
                            {{row.item.USERNAME}}
                        </template>
                        <template slot="STATUS" slot-scope="row">
                            <i class="fa fa-circle mr-1" :class="row.item.REG_FLAG == 1 ? 'status-active' : 'status-inactive'"></i>
                        </template>
                    </b-table>
                    <!-- End Table -->
                </div>
                <!-- </div> -->

                <!-- Open Edit Modal -->
                <div v-if="chosenModal=='updateUserModal'">
                    <UpdateUserModal :modalData="modalData" :user_type="userType" @closeModal="closeModal"></UpdateUserModal>
                </div>
                <!-- Open Change Password Modal -->
                <div v-else-if="chosenModal=='changePasswordModal'">
                    <UserChangePasswordModal :modalData="modalData" @closeModal="closeModal"></UserChangePasswordModal>
                </div>
            </div>
            <!-- search -->
            <div class="search col-md-4">
                <div class="d-flex">
                    <b-input-group class="input-grouppl-0">
                        <b-form-input v-model="tableData.filter" placeholder="Search here"/>
                        <i class="search-icon fa fa-search" aria-hidden="true"></i>

                    </b-input-group>
                </div>
            </div>
            <!-- search end-->
        </div>
        <div>
            <!-- Action Buttons -->
            <div class="col-md-12 d-flex justify-content-end mb-3">
                <!-- call getData function on line when category is change -->
                <select class="mr-2 custom-select width-100" v-model="tableData.flag" @change="changeCategory()">
                    <option value="1">{{$t('user.allusers')}}</option>
                    <option value="0">{{$t('user.active')}}</option>
                    <option value="4">{{$t('user.inactive')}}</option>
                </select>
                <button @click="modalAddUser" type="button" class="btn background-orange px-4" style="transition: all 0.3s">{{$t('user.add')}}</button>
                <transition name="fade">
                    <button type="button" class="btn btn-secondary ml-2" v-if="showRemove" @click="openModal('disableModal',selectedUserId)">{{$t('user.disable')}}</button>
                </transition>
                <transition name="fade">
                    <button type="button" class="btn btn-success ml-2" v-if="showEnable" @click="openModal('enableModal',selectedUserId)">{{$t('user.enable')}}</button>
                </transition>
            </div>
            <!-- End of Acton Buttons -->

            <!-- pagination -->
            <div :class="users.length > 40 ? '':'custom-pagination-orange'">
                <b-pagination v-model="tableData.currentPage" align="center"
                    :total-rows="tableData.totalRows"
                    :per-page="tableData.perPage"
                />
            </div>
            <!-- end pagination -->
        </div>


    </div>
</template>
<script>
//Modals
import UserRegisterModal from './Modals/UserRegisterModal.vue';
import UpdateUserModal from './Modals/UpdateUserModal.vue';
import UserChangePasswordModal from './Modals/UserChangePasswordModal.vue';

export default {
    components: { UserRegisterModal,UpdateUserModal,UserChangePasswordModal},
    props:['usertype', 'showProfile'],
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
                flag: 1,
                fields:[
                    {key:'USERNAME',label:'USERNAME',sortable: true},
                    {key:'USER_ID',label:"USER ID",sortable: true, 'class': 'text-center'},
                    {key:'USERROLE',label:"USER ROLE",sortable: true, 'class': 'text-center'},
                    {key:'STATUS',label:"STATUS",sortable: true, 'class': 'text-center'}
                ]
            },
            chosenModal:'UpdateUserModal',
            modalData:'UserRegisterModal',
            userType: this.usertype,
            isActive: false,
            showRemove: true,
            showEnable: false,
            selectedUserId:'',
            selectedRow:'',
        }
    },
    created(){
        //getActiveUsers
        this.getActiveUsers();
        this.$bus.$on('getActiveUsers',data => {
            this.getActiveUsers();
        });
    },
    mounted(){
    },
    methods: {
        //Function Name: getActiveUsers
        //Function Description: Get All Users from Database
        //Param: tableData.flag, user_type
        getActiveUsers(){
            let page_url;
            if(this.tableData.flag == 1){
                page_url = 'users/users-data?' || 'users/users-data';
            }else if(this.tableData.flag == 0){
                page_url = 'users/user-act?' || 'users/user-act';
            }else if(this.tableData.flag == 4){
                page_url = 'users/user-inact?' || 'users/user-inact';
            }
            axios.post(page_url,{USER_TYPE:this.usertype})
            .then(response=>{
                this.users = response.data;
                this.tableData.totalRows =  response.data.length;
                $.each(this.users, function( key, value ) {
                    value["USERROLE"];
                    if(value.USER_TYPE == 1){
                        value.USERROLE = 'Go Tensei Admin';
                    }else if(value.USER_TYPE == 2){
                        value.USERROLE = 'General Admin';
                    }else if(value.USER_TYPE == 3){
                        value.USERROLE = 'Nurse';
                    }
                });
            });

        },
        //Function Name: changeCategory
        //Function Description: Change category
        changeCategory(){
            this.getActiveUsers();
        },
        //Function Name: showUserProfile
        //Function Description: Show user profile
        //Param: value (user), key (index in user table)
        showUserProfile(value,key){
            this.selectedUserId = value.USER_ID;
            this.$bus.$emit('getActiveUser', value);
            this.$bus.$emit('getUsers', this.users);
            if(value.REG_FLAG == 1){
                this.showRemove = true;
                this.showEnable = false;
            }else if(value.REG_FLAG == 0){
                this.showEnable = true;
                this.showRemove = false;
            }

            //Clear the Selected hilight
            for(var i in this.users){
                this.$set(this.users[i],"_rowVariant","");
            }
            //Hilight selected
            this.$set(value,"_rowVariant","white");

        },
        //Function Name: modalAddUser
        //Function Description: Open add user modal
        modalAddUser(){
            this.$emit('addUserModal', this.users);
        },
        //Function Name: addHighlight
        //Function Description: Set highlight key to true or false
        addHighlight(){
            this.isActive = !this.isActive;

        },
        //function Name: onFiltered
        //Function Description: Update pagination on table filter
        //Param: filteredItems
        onFiltered (filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.tableData.totalRows = filteredItems.length
            this.tableData.currentPage = 1
        },
        //Function Name: openModal
        //Function Description: Open Specific Modal
        //Param: modal = name of modal
        //        data = data chosen
        openModal(modal,data){
            //Pass data to modal if Edit User Modal
            //Change Password User Modal
            if(modal=="updateUserModal"){
                this.modalData = data;
            }else if(modal=="changePasswordModal"){
                this.modalData = data;
            }else if(modal=="disableModal"){
                //Sweet Alert
                this.$swal({
                        title:'Are you sure',
                        text:'do you want to disable?',
                        type:'warning',
                        showCancelButton:true,
                        confirmButtonColor:'#ffb7c5',
                        cancelButtonColor:'#d33',
                        confirmButtonText:'Disable'
                }).then((result)=>{
                    if(result.value){
                        this.disableUser(data);
                    }
                });
            }else if(modal=="enableModal"){
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
                        this.enableUser(data);
                    }
                });
            }
        },
        //Function Name: disableUser
        //Function Description: Disable selected user
        //Param: userid
        disableUser(userid){
            axios.post('users/disable-user',{USERID:userid})
            .then(response=>{
                this.$swal('Disabled!','This user has been disabled','success');
                this.showRemove = false;
                this.getActiveUsers();
                this.$bus.$emit('hideActiveUser', false);
            });
        },
        //Function Name: enableUser
        //Function Description: enable selected user
        //Param: userid
        enableUser(userid){
            axios.post('users/enable-user',{USERID:userid})
            .then(response=>{
                this.$swal('Enabled!','This user has been enabled','success');
                this.showEnable = false;
                this.getActiveUsers();
                this.$bus.$emit('hideActiveUser', false);
            });
        },
        //Function closeModal
        //Function Description: Close All Modal
        closeModal(){
            this.chosenModal = '';
        },
    },
    mounted () {

    },
    computed:{
        tableHeight(){
            var tbHeight = '';
            if(this.$screenHeight > 800){
                if(!this.$isMobile.isMobile){
                    tbHeight = 'h-75vh';
                }else{
                    if(this.pagination.total >= 10){
                        tbHeight = 'h-100';
                    }else{
                        tbHeight = 'h-75vh';
                    }
                }
            }else{
                tbHeight = 'h-100';
            }
            return tbHeight;
        },
    },
};
</script>