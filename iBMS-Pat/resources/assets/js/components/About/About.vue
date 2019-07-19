<template>
        <div class="wrapper hardware-img-bg">
            <div class="container-fluid h-880">
            
                <div>{{ $t('navbar.about') }}
                    <div class="btn" @click="deviceall(item)" v-for= "item in deviceData" style="background-color:  #574c48 ;" align="align-all">
                            <div class="btn" @click="deviceid(item.DEVICE_ID)">
                                <div class="col-md-6">{{item.DEVICE_ID}}<br><br></div>
                            </div>
                            <div class="btn" @click="deviceid(item.DEVICE_NAME)">
                                <div class="col-md-6">{{item.DEVICE_NAME}}<br><br></div>
                            </div>
                            <div class="btn"@click="deviceid(item.DEVICE_TYPE)">
                                <div class="col-md-6">{{item.DEVICE_TYPE}}</div>
                            </div>
                        </div>
                        <div v-if="show==true">
                            <abb :parentData="chosenDevice"></abb>
                        </div>
                </div>
                <clock :locale="$i18n.locale"></clock>

                



            </div>      
        </div>
</template>
<script type="text/javascript">
    import abb from './abb.vue';
    export default {
    components: {abb},

    
        created(){
            this.newDevs();
        },
        data() {
            return {
                userType:'',
                loggedUser: '',
                chosenModal:'',
                modalData:'',
                deviceData: {},
                show: false,
                chosenDevice:''
                
            }
        },

	methods:{
        newDevs(){
                axios.get("devices/newdev")
                .then(response => {
            this.deviceData=response.data
    
           
        });
        },
        deviceid(deviceid){
                this.$swal({

                            text: deviceid,
                            type:'success',
                            timer:1500,
                            showConfirmButton:false
                });
        },
        deviceall(item){
            this.chosenDevice = item;


            this.show=true;


            this.$swal({
                        title: item.DEVICE_ID,
                        text:item.DEVICE_NAME+''+''+item.DEVICE_TYPE,
                        type:'success',
                        timer:1500,
                        showConfirmButton:false
            });
        },

        localeChange(){
            this.$parent.locale = this.$i18n.locale;
        },
    },
    computed:{

    }
};
</script>