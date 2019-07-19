<template>
			<!-- Open component base on Device Type -->
			<!-- :device - Pass device data to component  -->
			<div v-if="device['DEVICE_TYPE']=='co2_detector'"><CO2Detector :device="device"></CO2Detector></div>
			<div v-else-if="device['DEVICE_TYPE']=='curtain_1'"><Curtain :device="device"></Curtain></div>
			<div v-else-if="device['DEVICE_TYPE']=='gas_detector'"><GasDetector :device="device"></GasDetector></div>
			<div v-else-if="device['DEVICE_TYPE']=='rgb_light'"><RGBLight :device="device"></RGBLight></div>
			<div v-else-if="device['DEVICE_TYPE']=='motion_detector'"><MotionDetector :device="device"></MotionDetector></div>
			<div v-else-if="device['DEVICE_TYPE']=='smoke_detector'"><SmokeDetector :device="device"></SmokeDetector></div>
			<div v-else-if="device['DEVICE_TYPE']=='ir_remote'"><Remote :device="device"></Remote></div>
			<div v-else-if="device['DEVICE_TYPE']=='embedded_switch_1'"><EmbeddedSwitch1 :device="device"></EmbeddedSwitch1></div>
			<div v-else-if="device['DEVICE_TYPE']=='embedded_switch_2'"><EmbeddedSwitch2 :device="device"></EmbeddedSwitch2></div>
			<div v-else-if="device['DEVICE_TYPE']=='embedded_switch_3'"><EmbeddedSwitch3 :device="device"></EmbeddedSwitch3></div>
			<div v-else-if="device['DEVICE_TYPE']=='temp_hum'"><TemperatureHumidity :device="device"></TemperatureHumidity></div>
			<div v-else-if="device['DEVICE_TYPE']=='gas_valve'"><GasValve :device="device"></GasValve></div>
			<div v-else-if="device['DEVICE_TYPE']=='temp_light'"><WarmColdLight :device="device"></WarmColdLight></div>
			<div v-else-if="device['DEVICE_TYPE']=='wall_switch_1'"><WallSwitch1 :device="device"></WallSwitch1></div>
			<div v-else-if="device['DEVICE_TYPE']=='wall_switch_2'"><WallSwitch2 :device="device"></WallSwitch2></div>
			<div v-else-if="device['DEVICE_TYPE']=='wall_switch_3'"><WallSwitch3 :device="device"></WallSwitch3></div>
			<div v-else-if="device['DEVICE_TYPE']=='door_lock'"><DoorLock :device="device"></DoorLock></div>
			<div v-else-if="device['DEVICE_TYPE']=='water_valve'"><WaterValve :device="device"></WaterValve></div>
			<div v-else-if="device['DEVICE_TYPE']=='h2o_detector'"><H2ODetector :device="device"></H2ODetector></div>
			<div v-else-if="device['DEVICE_TYPE']=='panic_button'"><PanicButton :device="device"></PanicButton></div>
			<div v-else-if="device['DEVICE_TYPE']=='multi_detector'"><MultiDetector :device="device"></MultiDetector></div>
			<div v-else-if="device['DEVICE_TYPE']=='dust_detector'"><DustDetector :device="device"></DustDetector></div>
			<div v-else-if="device['DEVICE_TYPE']=='light_detector'"><LightDetector :device="device"></LightDetector></div>
			<div v-else-if="device['DEVICE_TYPE']=='electric_meter'"><ElectricMeter :device="device"></ElectricMeter></div>
			<div v-else="device['DEVICE_TYPE']==''">Please Register in DeviceList.vue</div>
	</div>
</template>
<script type="text/javascript">

// import vue file
import CO2Detector from "./CO2Detector.vue";
import Curtain from "./Curtain.vue";
import GasDetector from "./GasDetector.vue";
import SmokeDetector from "./SmokeDetector.vue";
import RGBLight from "./RGBLight.vue";
import MotionDetector from "./MotionDetector.vue";
import Remote from "./Remote.vue";
import EmbeddedSwitch1 from "./EmbeddedSwitch1.vue";
import EmbeddedSwitch2 from "./EmbeddedSwitch2.vue";
import EmbeddedSwitch3 from "./EmbeddedSwitch3.vue";
import TemperatureHumidity from "./TemperatureHumidity.vue";
import GasValve from "./GasValve.vue";
import WarmColdLight from "./WarmColdLight.vue";
import WallSwitch1 from "./WallSwitch1.vue";
import WallSwitch2 from "./WallSwitch2.vue";
import WallSwitch3 from "./WallSwitch3.vue";
import DoorLock from "./DoorLock.vue";
import WaterValve from "./WaterValve.vue";
import H2ODetector from "./H2ODetector.vue";
import PanicButton from "./PanicButton.vue";
import MultiDetector from "./MultiDetector.vue";
import DustDetector from "./DustDetector.vue";
import LightDetector from "./LightDetector.vue";
import ElectricMeter from "./ElectricMeter.vue";

export default{
	//include to components
	components:{
		MotionDetector,
		CO2Detector,
		GasDetector,
		RGBLight,
		Curtain,
		Remote,
		EmbeddedSwitch1,
		EmbeddedSwitch2,
		EmbeddedSwitch3,
		TemperatureHumidity,
		GasValve,
		WarmColdLight,
		WallSwitch1,
		WallSwitch2,
		WallSwitch3,
		SmokeDetector,
		DoorLock,
		WaterValve,
		H2ODetector,
		PanicButton,
		MultiDetector,
		DustDetector,
		LightDetector,
		ElectricMeter

	},
	props:{
		device:''
	},
	created(){

	},
	data(){
		return{

		}
	},
	methods:{
		closeModal(){
			this.$emit("closeModal");
		}
	}

};
</script>