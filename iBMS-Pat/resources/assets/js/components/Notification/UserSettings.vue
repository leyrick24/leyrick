<template>
    <a class="nav-link dropdown-toggle nav-profile text-white" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <img :src="user.USER_LOGO" onerror="javascript:this.src='/img/image_not_found.png'" alt="Avatar" class="avatar">
    </a>
</template>
<script>

export default {
    components: {},
    props: ['userid'],
    created(){
        this.getUser();
    },
    data() {
        return {
            userId: this.userid,
            user: '',
        }
    },
    methods: {
        //Function Name: getUser
        //Function Description: Get logged in user info
        //Param: user_id, user_status, user_type
        getUser(){
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
    },
    watch:{
    },
    mounted() {
    },
};
</script>