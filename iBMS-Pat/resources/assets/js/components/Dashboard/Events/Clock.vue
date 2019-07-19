<template>
  <!-- <Clock :displaySeconds="true"/> -->
  <div class="text-right p-0">
    <div :class="textSize">
      {{ dateTimeString }} &nbsp;|&nbsp; {{ timeString }}
    </div>
  </div>
</template>

<script>
  import moment from 'moment';

  export default {
    props:{
      size:"",
      locale:''
    },
    data () {
      return {
        dateTimeString: '',
        timeString: '',
        textSize:'small',
      }
    },

    created () {
      //Size of text
      if(this.size!==undefined){
        this.textSize = this.size;
      }
      //change date format according to locale
      if (this.locale !== undefined) {
        this.dateTimeString = moment().locale(this.locale).format('dddd, MMMM Do YYYY');
      }
      //fallback locale is en
      else{
        this.dateTimeString = moment().locale('en').format('dddd, MMMM Do YYYY');
      }
      this.timeString = moment().format('LTS');
      //continuously update clock
      setInterval(() => {
        this.timeString = moment().format('LTS');
      })
    },
    watch:{
      //watch change for locale
      locale: function(){
        //change date format according to locale
        if (this.locale !==undefined) {
          this.dateTimeString = moment().locale(this.locale).format('dddd, MMMM Do YYYY');
        }else{
          this.dateTimeString = moment('en').format('dddd, MMMM Do YYYY');
        }
      }
    },

  }
</script>