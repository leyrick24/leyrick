<template>

<div class="row">
Device Name: {{parentData.DEVICE_NAME}}    <br>
Device Type: {{parentData.DEVICE_TYPE}}    
 <input type="button" @click="Shaw(parentData.DEVICE_NAME)"  class="btn btn-primary" :value="Showbutton"/> 
</div>  
</template>
<script>
export default {

    props: {
        parentData: Object,
    },
    data() {
        return {
            deviceData: {},
            loggedUser: this.user,
            chosenModal:'',
            modalData:'',
            Showbutton:''
        }
    },
    methods: {
    onChildClick (newDevs) {
        this.fromChild = value;
    },
    Shaw (show) {
            this.Showbutton = show;
        
        }
}
}
   
</script>