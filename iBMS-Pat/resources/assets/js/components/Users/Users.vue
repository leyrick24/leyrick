<template>
    <div>
        <div class="wrapper hardware-img-bg">
            <div class="container-fluid container-bg">
                <div class="col-sm-auto d-flex justify-content-between align-items-center px-0 py-3">
                    <div>{{$t('navbar.userm')}} </div>
                    <clock :locale="$i18n.locale"></clock>
                </div>
                <div class="row pb-5 h-826">
                    <div class="col-md-6">
                        <!-- tab-navigation -->
                        <div class="d-flex justify-content-between align-items-center">
                            <!-- div-tab-list -->
                            <div class="nav nav-tabs w-100" role="tablist">
                               <a id="floorTab" @click="changeView(1)" class="nav-item nav-link nav-header-gray px-4 active" data-toggle="tab" href="#floor-tab" role="tab" aria-controls="nav-gateway" aria-selected="true">
                                    {{$t('floor.floor')}}
                                </a>
                            </div>
                            <!-- div-tablist-end -->
                        </div>

                        <!-- tab-content -->
                        <!-- div-tab-content -->
                        <div class="tab-content tab-bg-blue" :class="tableHeight" id="nav-tabContent">
                            <div class="tab-pane fade show active h-100" id="gateway-list" role="tabpanel">
                                <!-- active-users component -->
                                <ActiveUsers :usertype="userType" @addUserModal="addUser"></ActiveUsers>
                        </div>
                        </div>
                        <!-- divtab-content-end -->
                    </div>
                    <transition name="slide-fade">
                        <UserProfile :usertype="userType" :loggedUser="loggedUser" @changePasswordModal="changePassword"></UserProfile>
                    </transition>
                </div>
            </div>


            <!-- Open Register Modal -->
            <div v-if="chosenModal=='registerModal'">
                <UserRegisterModal :usertype="userType" :modalData="modalData" :loggedUser="loggedUser" @closeModal="closeModal" ></UserRegisterModal>
            </div>
            <!-- Open Change Password Modal -->
            <div v-if="chosenModal=='changePasswordModal'">
                <UserChangePasswordModal :modalData="modalData" :loggedUser="loggedUser" @closeModal="closeModal" ></UserChangePasswordModal>
            </div>
        </div>
        <Footer></Footer>
    </div>
</template>
<script>
//Modals
import UserRegisterModal from './Modals/UserRegisterModal.vue';
import ActiveUsers from './ActiveUsers.vue';
import InActiveUsers from './InActiveUsers.vue';
import UserProfile from './UserProfile.vue';
import UserChangePasswordModal from './Modals/UserChangePasswordModal.vue';
export default {
    components: {UserRegisterModal,ActiveUsers,InActiveUsers, UserProfile, UserChangePasswordModal},
    props: {
        user: Object,
    },
    data() {
        return {
            userType:this.user.USER_TYPE,
            loggedUser: this.user,
            chosenModal:'',
            modalData:'',
        }
    },
    mounted () {
        this.$i18n.locale = this.$parent.locale;
    },
    created(){
    },
    methods: {
        //Function name: openModal
        //Function Description: opens specific modal
        //Param: modal (registerModal/changePasswordModal), data (user)
        openModal(modal,data){
            //Open Modal
            if(modal=="registerModal"){
                this.chosenModal = modal;
            }else if(modal=="changePasswordModal"){
                this.chosenModal = modal;
            }
        },
        //Function Name: addUser
        //Function Description: opens add user modal
        //Param: data
        addUser(data){
            this.modalData = data;
            this.openModal('registerModal', data);
        },
        //Function Name: changePassword
        //Function Description: opens change password modal
        //Param: data
        changePassword(data){
            this.modalData = data;
            this.openModal('changePasswordModal', data);
        },
        //Function Name: closeModal
        //Function Description: Close All Modal
        closeModal(){
            this.chosenModal = '';
        }
    },
    computed:{
        tableHeight(){
            var tbHeight = '';
            if(this.$screenHeight > 900){
                if(!this.$isMobile.isMobile){
                    tbHeight = 'h-75vh';
                }else{
                    if(this.pagination.total >= 10){
                        tbHeight = 'h-um';
                    }else{
                        tbHeight = 'h-75vh';
                    }
                }
            }else{
                tbHeight = 'h-um';
            }
            return tbHeight;
        }
    },
};
</script>