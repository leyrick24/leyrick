<template>
    <div>
        <div class="wrapper hardware-img-bg">
            <div class="container-fluid">
                <div class="col-sm-auto d-flex justify-content-between align-items-center px-0 py-3">
                    <div>User Manual </div>
                    <clock></clock>
                </div>
                <div class="tab-bg-blue col-sm-12">
                    <!-- <h1>Select</h1>
                    <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                      <div class="carousel-inner">
                        <div class="carousel-item active">
                          <img class="d-block w-100" src="/img/Help/Login.PNG" alt="User Manual">
                        </div>
                        <div class="carousel-item" v-for="modules,key in modules" :key="key">
                            <img class="d-block w-100" :src="modules.MODULE_IMG" :alt="modules.MODULE_NAME">
                        </div>
                      </div>
                      <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                      </a>
                      <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                      </a>
                    </div> -->
                    <div class="justify-content-between align-items-center px-0 py-3">
                        <!-- <div class="text-right p-0">
                            <a style="font-size: 14px; align-content: center" href="manual">Download user manual</a>
                        </div> -->
                        <h1>{{mName}}</h1>
                    </div>
                    <carousel v-model="moduleName" :navigationEnabled="true" :perPage="1" :paginationEnabled="false">
                        <slide v-for="modules,key in modules" :key="key">
                            <img :src="modules.MODULE_IMG" onerror="javas   cript:this.src='/img/image_not_found.png'" :alt="modules.MODULE_NAME" class="img-fluid">
                            <p class="text-center">{{modules.MODULE_NAME}}</p>
                        </slide>
                        <slide>
                            <img :src="'/img/Help/Notification.PNG'" onerror="javas   cript:this.src='/img/image_not_found.png'" :alt="'Notifications'" class="img-fluid">
                            <p class="text-center">Notification</p>
                        </slide>
                        <slide>
                            <img src="/img/Help/Help.PNG" onerror="javas   cript:this.src='/img/image_not_found.png'" :alt="'Help'" class="img-fluid">
                            <p class="text-center">Help</p>
                        </slide>
                    </carousel>
                </div>
                <div v-if="moduleName == 0" class="tab-bg-blue col-sm-12 container px-0 py-3 d-flex justify-content-between">
                    <div class="col-sm-6">
                        <img src="/img/image_not_found.png" class="img-fluid">
                    </div>
                    <div class="col-sm-6">
                        <p>
                            Through the Dashboard page, the users will be able to view quick reports. The summary graph of the status of all the devices in the building, its total number and whether it is online or offline; and the summary of gateways used in the building will be displayed.
                        </p>
                        <p>
                            In the Dashboard page, the electricity summary is also displayed. The users will be able to monitor the electric consumption for the latest period and compare the consumption results over time.
                        </p>
                        <p>
                            Added feature is the Alarm event which is also displayed in the Dashboard page. Any latest alarm will be displayed to the page and will inform the user of the portal on the location of the alarm event – what floor and room, date and time.
                        </p>
                    </div>
                </div>
                <div v-else-if="moduleName == 1" class="tab-bg-blue col-sm-12 container px-0 py-3 d-flex justify-content-between">
                    <div class="col-sm-6">
                        <img src="/img/image_not_found.png" class="img-fluid">
                    </div>
                    <div class="col-sm-6">
                        <p>
                            Device Operation is an integral part of managing the building. In this module, the user will be able to control and manage all the devices enrolled per room and floor like the motion detector, switches, light, smoke detector, door lock, aircon, television, water valve, etc.
                        </p>
                    </div>
                </div>
                <div v-else-if="moduleName == 2" class="tab-bg-blue col-sm-12 container px-0 py-3 d-flex justify-content-between">
                    <div class="col-sm-6">
                        <img src="/img/image_not_found.png" class="img-fluid">
                    </div>
                    <div class="col-sm-6">
                        <p>
                            The User Management module is designed to manage the users of the iBMS Portal. This module is intended to add and disable users or edit user information.
                        </p>
                    </div>
                </div>
                <div v-else-if="moduleName == 3|| moduleName == 4 || moduleName == 5 || moduleName == 6" class="tab-bg-blue col-sm-12 container px-0 py-3 d-flex justify-content-between">
                    <div class="col-sm-6">
                        <img src="/img/image_not_found.png" class="img-fluid">
                    </div>
                    <div class="col-sm-6">
                        <p>
                            The Hardware Management module helps the user in managing the gateways, devices, binding and IRs per floor.
                        </p>
                    </div>
                </div>
                <div v-else-if="moduleName == 7" class="tab-bg-blue col-sm-12 container px-0 py-3 d-flex justify-content-between">
                    <div class="col-sm-6">
                        <img src="/img/image_not_found.png" class="img-fluid">
                    </div>
                    <div class="col-sm-6">
                        <p>
                            Users will be able to manage the floors and rooms through the Floor Management module where he/she can add or edit floors, edit room information and the device mapping will be displayed. In this module, the user will be able to edit the devices per room and floor, and the location of devices in the room.
                        </p>
                    </div>
                </div>
                <div v-else-if="moduleName == 8" class="tab-bg-blue col-sm-12 container px-0 py-3 d-flex justify-content-between">
                    <div class="col-sm-6">
                        <img src="/img/image_not_found.png" class="img-fluid">
                    </div>
                    <div class="col-sm-6">
                        <p>
                            The Logs page is where the user will be able to view all the system and audit trail logs. Through this, the user will be able to monitor the events taking place in the system.
                        </p>
                    </div>
                </div>
                <div v-else-if="moduleName == 9" class="tab-bg-blue col-sm-12 container px-0 py-3 d-flex justify-content-between">
                    <div class="col-sm-6">
                        <img src="/img/image_not_found.png" class="img-fluid">
                    </div>
                    <div class="col-sm-6">
                        <p>
                            The Notification feature notifies the user about the latest alarm events happening in the system.
                        </p>
                    </div>
                </div>
                <div v-else-if="moduleName == 10" class="tab-bg-blue col-sm-12 container px-0 py-3 d-flex justify-content-between">
                    <div class="col-sm-6">
                        <img src="/img/image_not_found.png" class="img-fluid">
                    </div>
                    <div class="col-sm-6">
                        <p>
                            The Help page guides the user on how to use the portal.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <Footer></Footer>
    </div>
</template>
<script type="text/javascript">
    export default{
        components:{
		},
		props:{

		},
		data(){
			return{
                modules:[],
                defaultData: '',
                moduleName:0,
                mName:'Dashboard',
                user:'',
			};
		},
		created(){
            this.getModules();
		},
		methods:{
            getModules(){
                axios.get('modules')
                .then(response=>{
                    response.data.map((val,key) => {
                    val.MODULE_IMG = '/img/Help/'+val.MODULE_NAME+'.PNG';
                    this.modules.push(val);
                    });
                    this.defaultData = response.data[0];
                })
            }
		},
        computed:{
            title: function() {
                if (this.moduleName == 9) {
                    return this.mName = 'Notifications';
                }else if (this.moduleName == 10) {
                    return this.mName = 'Help';
                }else{
                    return this.mName = this.modules[this.moduleName]['MODULE_NAME'];
                }
            }
        }
	};
</script>