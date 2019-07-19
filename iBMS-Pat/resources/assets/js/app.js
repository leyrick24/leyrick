
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require('./bootstrap');

window.Vue = require('vue');

//Vue Bus
var VueBus = require('vue-bus');

//Vue Color
var VueColor = require('vue-color');

//JQuery Zoom
var JQuery = require('jquery-zoom');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import Vue from 'vue'

//Boostrap Vue
import BootstrapVue from 'bootstrap-vue';
import 'bootstrap-vue/dist/bootstrap-vue.css';

//izitoast notification
import VueIziToast from "vue-izitoast";
import 'izitoast/dist/css/iziToast.min.css';

//sweetalert2 notification
import VueSweetalert2 from 'vue-sweetalert2';

//Vue Carousel
import VueCarousel from 'vue-carousel';

//amCharts
import AmCharts from 'amcharts3';
import AmSerial from 'amcharts3/amcharts/serial';
import AmPieChart from 'amcharts3/amcharts/pie';

//vue multiple select
import Multiselect from 'vue-multiselect';

//mobile detect
import isMobile from 'mobile-device-detect';

//Syncfusion Ej2 Charts
import { Browser } from '@syncfusion/ej2-base';
import { ChartPlugin, LineSeries, Legend, Tooltip, DateTime } from "@syncfusion/ej2-vue-charts";
Vue.use(ChartPlugin);

//for vue localization 18n
import VueI18n from 'vue-i18n';
Vue.use(VueI18n);

Vue.use(VueIziToast);
Vue.use(VueSweetalert2);
Vue.use(BootstrapVue);
Vue.use(VueCarousel);
Vue.prototype.$isMobile = isMobile;

//for session expire popup and cookie manipulation
Vue.use(require('vue-cookies'));

//moment for time
import moment from 'moment';
import 'moment/locale/ja';
Vue.use(require('vue-moment'));

Vue.component('multiselect', Multiselect);
Vue.component('login', require('./components/Login/Login.vue'));
Vue.component('dashboard', require('./components/Dashboard/Dashboard.vue'));
Vue.component('users', require('./components/Users/Users.vue'));
Vue.component('logs', require('./components/Logs/Logs.vue'));
Vue.component('gateway', require('./components/Hardware/Gateway/Gateway.vue'));
Vue.component('device', require('./components/Hardware/Devices/Device.vue'));
Vue.component('binding', require('./components/Hardware/Binding/Binding.vue'));
Vue.component('infrared', require('./components/Hardware/IR/InfraRed.vue'));
Vue.component('about', require('./components/About/About.vue'));
Vue.component('help', require('./components/Help/Help.vue'));
//Vue.component('displayview', require('./components/DisplayView/DisplayView.vue'));
Vue.component('deviceoperation', require('./components/DisplayView/DeviceOperation.vue'));
Vue.component('notification', require('./components/Notification/Notification.vue'));
Vue.component('usersettings', require('./components/Notification/UserSettings.vue'));
Vue.component('floor', require('./components/Floor/FloorManagement.vue'));
Vue.component('clock', require('./components/Dashboard/Events/Clock.vue'));
Vue.component('Footer', require('./components/Footer/Footer.vue'));

//spinner
Vue.component('spinner', require('vue-spinner-component/src/Spinner.vue'));

//for language select
//set languages to select from
const messages = {
    //retrieve message data from json files located in components/lang
    en: require('./lang/en.json'),
    ja: require('./lang/ja.json')
}
const dateTimeFormats = {
    'en': {
        long:{
            year: "numeric",
            month:"short",
            day: "numeric",
            hour:"numeric",
            minute:"numeric"
        }
    },
    'ja': {
        long:{
            year: "numeric",
            month:"short",
            day: "numeric",
            hour:"numeric",
            minute:"numeric"
        }
    }
}
//localization settings
const i18n = new VueI18n({
    locale:'ja',
    dateTimeFormats,
    //fallback in case of error in ja
    fallbackLocale:'en',
    messages,
})
//changed const to window for access in blade files
window.app = new Vue({
    //set i18n for all vue components
    i18n,
    el: '#app',
    data:{
    	messages:{
            notification:[]
        },
        screenH: '',
        user:'',
        page:'',
        user_id:'',
        //store locale for reactivity
        locale:'en',
    },
    created() {
        var url = window.location.href;
        url = url.split('#').pop().split('?').pop();
        this.page = url.substring(url.lastIndexOf('/') + 1);
        if (this.page !== 'login') {
            this.checkTimeout();
            //retrieve set language every redirect
            axios.get('session')
            .then(response=>{
                if (response.data.locale) {
                    this.locale = response.data.locale;
                }
            });
        }
        window.addEventListener('resize', this.handleResize)
        this.handleResize();
      },
    methods: {
        //Function Name: checkTimeout
        //Function Description: check for session expiration on all components every 30s
        //Param: user_id
        checkTimeout(){
            setInterval(() => {
                axios.get('/api/'+authUser.USER_ID+'/check-sess')
                .then(response => {
                    var timeout = response.data;
                    if (timeout == 'loggedOut') {
                        window.location.reload(true);
                    }
                });
            }, 30000);
        },
        //Function Name: handleResize
        //Funciton Description: change screensize
        handleResize() {
            Vue.prototype.$screenHeight = window.innerHeight;
            Vue.prototype.$screenWidth = window.innerWidth;
        },
        //Function Name: lowBattery
        //Function Description: displays warning after low battery event
        lowBattery(){
            this.$toast.warning("Your device needs new batteries!","Low Battery Alert",{position:'topRight'});
        },
        //Function Name: changeLocale
        //Function Description: store language setting to session on change
        //Param: data ('en' for english, 'ja' for nihonggo)
        changeLocale(data){
            this.locale = data;
            axios.get('locale/'+this.locale)
            .then(response=>{
            });
        },
    },
    mounted(){
    	Echo.channel('test').listen('.testEvent', (e) => {

           //

        }).listen('.notificationEvent', (e) => {
            //push notif only on sensor event
            if (e.data.NOTIFICATION.ERROR_FLAG == 4) {
                this.messages.notification.push(e.data);
            }
        }).listen('.LowBatteryDevice', (e) => {
            if (this.page !== 'login') {
                this.lowBattery();
            }
        });
    },
    watch:{
        //watch for change in langauge setting to change text in app.blade
        locale: function(){
            this.$i18n.locale = this.locale;
            this.$children[1].$i18n.locale = this.locale;
            var jp = this.$i18n.messages.ja.navbar;
            var en = this.$i18n.messages.en.navbar;
            //loop through messages to change text in navbar
            if (this.locale == 'ja') {
                Object.keys(jp).forEach(function(mess){
                    if (document.getElementById(mess)) {
                        document.getElementById(mess).innerHTML = jp[mess];
                    }
                })
            }else if (this.locale == 'en') {
                Object.keys(en).forEach(function(mess){
                    if (document.getElementById(mess)) {
                        document.getElementById(mess).innerHTML = en[mess];
                    }
                })
            }
        }
    },
    destroyed() {
        window.removeEventListener('resize', this.handleResize)
    },
});
