<template>
    <li class="nav-item dropdown">
        <a @click="updateCount()" class="nav-link dropdown-toggle nav-profile text-white" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-fw fa-bell mt-2"></i>
            <span class="fa-stack nav-notif">
                <span class="fa fa-circle fa-stack-2x text-orange"></span>
                <strong class="fa-stack-1x" style="font-size:0.7em">
                    {{ updateNotifCount }}
                </strong>
            </span>
        </a>
        <div v-if="notifListCount > 0" class="dropdown-menu dropdown-menu-right h-400">
            <h6 class="dropdown-header mb-2">{{$t('notifs.newNotifs')}}</h6>
            <!-- <div class="dropdown-divider"></div> -->
            <div class="custom-scroll-bar h-300">
                <a v-for="notifList,key in orderedNotifLists" class="dropdown-item p-0 border border-light" @click="updateNotifications(key)" @mouseover="updateNotifications(key)">
                    <div class="d-flex align-items-center mx-1">
                        <div  class="p-3 mr-2" :class="notifBG(notifList.SEEN_FLAG,notifList.ERROR_FLAG)">
                            <i class="fa fa-fw fa-lg" :class="notifIcon(notifList.ERROR_FLAG)"></i>
                        </div>
                        <div class="text-dark">
                            <div class="d-flex justify-content-between">
                                <div class="mr-5">
                                    <strong>
                                        {{ notifList.SUBJECT }}
                                    </strong>
                                </div>
                                <div class="small">{{ notifList.CREATED_AT }}</div>
                            </div>
                            <div class="dropdown-message small"><strong>{{ notifList.CONTENT }}</strong></div>
                        </div>
                    </div>
                </a>
            </div>
            <a class="dropdown-item small text-center mt-2" href="logs">{{$t('notifs.viewAll')}}</a>
        </div>
        <div v-else class="dropdown-menu" style="left:-100%">
            <h6 class="dropdown-header">{{$t('notifs.noNew')}}</h6>
        </div>
    </li>
</template>
<script>

export default {
    components: {},
    props: ['userid'],
    created(){
        this.getNotifications();
    },
    data() {
        return {
            userId: this.userid,
            notifLists: [],
            notifCount : 0,
            user: '',
        }
    },
    methods: {
        //Function Name: getNotifications
        //Function Description: Get Notifications for user
        //Param: userId
        getNotifications(){
            axios.get('notification-data/'+ this.userId +'/getNotif')
            .then(response => {
                this.notifLists = [];
                let data = response.data;
                for(var i in data){
                    // if (response.data.SEEN_FLAG == 0) {
                    this.notifLists.push(data[i]);
                    // }
                }
            })
            .catch(errors => {
                console.log(errors);
            });
        },
        //Function Name: notifBG
        //Function Description: change notif bg according to errorFlag
        //Param: seenFlag, errorFlag
        notifBG(seenFlag,errorFlag){
            if(seenFlag == 0){
                if(errorFlag == 1 ){
                    //success
                    return 'bg-success';
                }else if(errorFlag == 2 ){
                    //warning
                    return 'bg-warning';
                }else if(errorFlag == 3 ){
                    //failure
                    return 'bg-danger';
                }else if(errorFlag == 4 ){
                    //danger
                    return 'bg-danger';
                }
            }else{
                return 'bg-secondary';
            }
        },
        //Function Name: updateNotifications
        //Function Description: Update notification seen flag on click
        //Param: key (index in notifList)
        updateNotifications(key){
            let data = this.notifLists[key];
            this.notifLists[key].SEEN_FLAG = 1;
            axios.post('/notification-data/updateNotif', {
                NOTIFICATION_ID: data.NOTIFICATION_ID,
                USER_ID: this.userId
            })
            .then(function (response) {
                // console.log(response);
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        //Function Name: showNotif
        //Function Description: shows notifs and resets notif count
        showNotif(){
            this.$root.$data.messages.notification = [];
            this.notifCount = 0;
        },
        //Function Name: notifIcon
        //Function Description: changes icon next to notif according to errorFlag
        //Param: errorFlag
        notifIcon(errorFlag){
            if(errorFlag == 1 ){
                //success
                return 'fa-check-circle text-white';
            }else if(errorFlag == 2 ){
                //warning
                return 'fa-exclamation-triangle text-white';
            }else if(errorFlag == 3 ){
                //failure
                return 'fa-exclamation-circle text-white';
            }else if(errorFlag == 4 ){
                //danger
                return 'fa-times-circle text-white';
            }else{
                return 'fa-question-circle';
            }
        },
        //Function Name: updateCount
        //Function Description: update notif count
        updateCount(){
            this.$root.$data.messages.notification = [];
            this.notifCount = 0;
        },
        //Function Name: playNotif
        //Funciton Description: Play sound on new notif
        playNotif() {
            let sound = '../../../audio/ding.mp3';
            if(sound) {
                var audio = new Audio(sound);
                audio.play();
            }
        },
    },
    computed:{
        orderedNotifLists: function(){
            return _.orderBy(this.notifLists, ['NOTIFICATION_ID'], ['desc'])
        },
        updateNotifCount: function(){
            let dataLength = this.$root.$data.messages.notification;
            if(dataLength.length > 0){
                let data = this.$root.$data.messages.notification[dataLength.length - 1].NOTIFICATION;
                Vue.set(this.notifLists, this.notifListCount, data);
                this.notifCount++;
                this.playNotif();
            }
            return this.notifCount;
        },
        notifListCount: function(){
            let count = this.notifLists.length;
            return count;
        },
    },
    watch:{
        notifCount(count){
            localStorage.notifCount = count;
        }
    },
    mounted() {
        if(localStorage.notifCount){
            this.notifCount = localStorage.notifCount;
        }
    },
};
</script>