<template>
    <!-- containter -->
    <div class="text-center">
        <div class="login-container">
            <div class="logo-img">
                <img src="/img/pat/tplogo.png">
            </div>
            <!-- card -->
            <div class="card card-login mx-auto mt-5 login-content-bg">
                <div class="card-body">
                    <form method="POST" @submit="loginFunction">
                        <div class="form-group">
                            <h4>
                                <small v-if="errors" class="text-orange">{{ errors }}</small>
                            </h4>
                        </div>
                        <div class="input-group mb-4">
                            <div class="input-group-prepend">
                                <span class="login-input-group-text">
                                    <i aria-hidden="true" class="fa fa-user fa-2x login-icon-color"></i>
                                </span>
                            </div>
                            <input v-model="login.username" type="text" placeholder="Username" class="form-control login-form-control" required autofocus>
                        </div>
                        <div class="input-group mb-4">
                            <div class="input-group-prepend">
                                <span class="login-input-group-text">
                                    <i aria-hidden="true" class="fa fa-lock fa-2x login-icon-color"></i>
                                </span>
                            </div>
                            <input v-model="login.password" placeholder="Password" class="form-control login-form-control" type="password" minlength = 8 required autofocus>
                        </div>
                        <button type="submit" class="btn btn-orange">LOGIN</button>
                    </form>
                </div>
            </div>
            <!-- card end -->
        </div>
        <footer class="login-footer sticky-footer">
            <div class="login-footer-container">
                <div class="footer-content">
                    <img src="img/pat/tplogo.png" class="footer-client-logo mr-1">
                    <small>Copyright © iBMS 2018 | Powered by: GTI </small>
                    <img src="img/pat/tplogo.png" class="footer-gti-logo ml-1">
                </div>
            </div>
        </footer>
    </div>
    <!-- containter end -->
</template>
<script>
    export default {
        data() {
            return {
                login: {
                    username: '',
                    password: '',
                    min_pass: 8
                },
                errors: '',
                visibility: 'd-none',
            }
        },
        methods: {
            //Function Name: loginFunction
            //Function Description: login function
            //Param: e
            loginFunction(e){
                let self = this;
                var error;
                e.preventDefault();
                axios.post('login', this.login)
                .then(function(response) {
                    location.reload()
                })
                .catch(function(error) {
                    console.log(error.response.status == 419);
                    console.log(error.response.status == '419');
                    if (error.response.status == 419) {
                        console.log(error.response.status + ': this page should have been refreshed something went wrong');
                        location.reload();
                        console.log('this means location reload didnt work');
                        return Promise.reject(error);
                        console.log('last line of 419 catch fml');
                    }
                    if (error.response.data.errors.username[0]) {
                        self.errors = error.response.data.errors.username[0];
                    }
                    self.login.username ="";
                    self.login.password ="";
                });
            },
            //Function Name: checkTimeout
            //Function Description: display notice of session expiration/timeout if a cookie is detected
            checkTimeout(){
                if (this.$cookies.isKey('test')) {
                    this.$toast.warning("You have been logged out!","Warning",{position:"topCenter"});
                }else{
                    console.log(this.$cookies.isKey('test'));
                }
                //delete cookie for recently logged out user
            }
        },
        mounted(){
            this.checkTimeout();
        }
    };
</script>