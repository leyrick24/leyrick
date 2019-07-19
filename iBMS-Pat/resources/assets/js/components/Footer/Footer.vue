<template>
    <div class="content-wrapper">
        <div class="footer-bg">
            <footer class="sticky-footer">
                <div class="footer-container">
                    <div class="footer-content">
                        <img src="img/client-logo.jpeg" class="footer-client-logo mr-1">
                        <small>{{$t('footer')}}</small>
                        <img src="img/gti-logo.png" class="footer-gti-logo ml-1">
                    </div>
                </div>
            </footer>
        </div>
    </div>
</template>
<script type="text/javascript">
    export default{
        mounted(){
            this.$i18n.locale = this.$parent.locale
        }
    }
</script>