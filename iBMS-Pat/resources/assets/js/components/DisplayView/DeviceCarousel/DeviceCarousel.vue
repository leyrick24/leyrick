<template>
	<div>
		<div v-if="currentDevices.length > 0" class="card-body device-operation-carousel-bg carousel-custom">
			<carousel :perPage="$isMobile.isMobile==true ? 2 : 6 " :navigate-to="currentDevices" :mouse-drag='true'>
				<slide v-if="device['hide']!=true" v-for="device,key in currentDevices" :key="key">
					<!-- Inside Carousel -->
					<div @click="chooseDevice(key,device.ONLINE_FLAG)" :id="'device-' + key" class="db-device device-carousel" :title="device.ONLINE_FLAG == 0 ? 'Device is Offline':''" v-b-tooltip.hover>
						<!-- <img class="device-operation-carousel-image" :src="'img/devices/' + device['DEVICE_TYPE'] +'.png'"/> -->
						<img class="device-operation-carousel-image" :src="'img/device_carousel/' + device['DEVICE_TYPE'] + '.png'" onerror="this.src='img/image_not_found.png'" :class="device.ONLINE_FLAG == 0 ? 'grayscale':''"/>
						<!-- <i class="fa fa-exclamation-circle fa-2x position-absolute color-orange" style="top:-100px;left:130px;" aria-hidden="true"></i> -->
					</div>
					<p class="text-center">{{device['DEVICE_NAME']}}</p>
				</slide>
			</carousel>
		</div>
		<div v-else class="card-body bg-dark text-white text-center h-180">
			<h3>{{$t('devop.carouselnodevice')}}</h3>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default{
		props:{
			currentDevices:'',
			currentRoom:'',
			searchDevice:'',
		},
		watch:{
			searchDevice:function(){
				this.$forceUpdate();
			},

		},
		methods:{
			//Function Name: chooseDevice
			//Function Description: Choose device on click and emits to parent
			//Param: key (index in currentDevices), flag (device.ONLINE_FLAG)
			chooseDevice(key,flag){
				this.$emit("chooseDevice",key);
			},
		}
	};
</script>