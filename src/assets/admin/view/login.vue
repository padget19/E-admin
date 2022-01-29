<template>
  <div class="login-container">
    <div class="title-container">
      <h3 class="title">
        <el-avatar :size="100" fit="fill" :src="webLogo"/>
        <br>
        <span>{{webName}}</span>
      </h3>
    </div>
    <el-form ref="loginForm" :model="loginForm" :rules="loginRules" class="login-form" auto-complete="on"
             label-position="left">
      <div>
        <el-form-item prop="username" :style="inputFocusIndex == 'username' ? inputFocusCss : ''">
          <span class="svg-container">
            <i class="el-icon-user"/>
          </span>
          <el-input
              ref="username"
              v-model="loginForm.username"
              placeholder="{:admin_trans('admin.please_account')}"
              name="username"
              type="text"
              tabindex="1"
              auto-complete="on"
              @focus="inputFocus('username')"
              @blur="inputFocusIndex = ''"
          />
        </el-form-item>
        <el-form-item prop="password" :style="inputFocusIndex == 'password' ? inputFocusCss : ''">
          <span class="svg-container">
            <i class="el-icon-lock"/>
          </span>
          <el-input
              :key="passwordType"
              ref="password"
              v-model="loginForm.password"
              :type="passwordType"
              placeholder="{:admin_trans('admin.please_password')}"
              name="password"
              tabindex="2"
              auto-complete="on"
              @focus="inputFocus('password')"
              @blur="inputFocusIndex = ''"
              @keyup.enter.native="handleLogin"
          />
          <span class="show-pwd" @click="showPwd">
            <i :class="passwordType === 'password' ? 'el-icon-key' : 'el-icon-view'"/>
          </span>
        </el-form-item>
        <div v-if="verifyMode == 2" style="display: flex;justify-content: space-between;">
          <el-form-item prop="verify" :style="inputFocusIndex == 'verify' ? inputFocusCss : 'width:190px'">
            <span class="svg-container">
              <i class="el-icon-circle-check"/>
            </span>
            <el-input
                ref="verify"
                v-model="loginForm.verify"
                placeholder="{:admin_trans('admin.please_verify')}"
                name="verify"
                type="text"
                tabindex="3"
                auto-complete="on"
                style="width: 150px"
                maxlength="4"
                @keyup.enter.native="handleLogin"
            />
          </el-form-item>
          <el-image :src="verifyImage" style="height: 52px;cursor: pointer;border-radius: 5px"
                    @click="getVerify"/>
        </div>
        <el-button :loading="loading" type="primary" style="width:100%;margin-bottom:30px;height: 50px"
                   @click.native.prevent="handleLogin">{{loginBtnText}}
        </el-button>
      </div>
    </el-form>
    <div class="icp"><a href="http://beian.miit.gov.cn" target="_blank">{{webMiitbeian}}</a> | {{webCopyright}}
    </div>
  </div>
</template>
<script>
export default {
  name: 'Login',
  props:{
    webLogo:String,
    webName:String,
    webCopyright:String,
    webMiitbeian:String,
    deBug: Boolean,
  },
  data() {
    const validatePassword = (rule, value, callback) => {
      if (value.length < 5) {
        callback(new Error('{:admin_trans("admin.password_min_length")}'))
      } else {
        callback()
      }
    }
    return {
      verifyMode: 1,
      buttonShow: false,
      loginForm: {
        debug: false,
        username: '',
        password: '',
        verify: '',
        hash: '',
      },
      loginRules: {
        username: [{required: true, trigger: 'change', message: '{:admin_trans("admin.please_account")}'}],
        verify: [{required: true, trigger: 'change', message: '{:admin_trans("admin.please_verify")}'}],
        password: [{required: true, trigger: 'change', validator: validatePassword}]
      },
      loading: false,
      passwordType: 'password',
      inputFocusCss: '',
      inputFocusIndex: '',
      verifyImage: '',
      loginBtnText: '{:admin_trans("admin.login")}',
      redirect: null,
    }
  },
  watch: {
    $route: {
      handler: function(route) {
        if(route.query && route.query.redirect){
          const index = route.fullPath.indexOf('?redirect=')
          this.redirect = route.fullPath.substr(index+10)
        }
      },
      immediate: true
    }
  },
  created(){
    if(this.deBug){
      this.loginForm.username = 'admin';
      this.loginForm.password = 'admin';
    }
    this.getVerify()
  },
  methods: {
    getVerify() {
      this.$request('admin/system/verify').then(res => {
        this.verifyImage = res.data.image
        this.loginForm.hash = res.data.hash
        this.verifyMode = res.data.mode
      })
    },
    inputFocus(mark) {
      this.inputFocusIndex = mark
      this.inputFocusCss = 'box-shadow: 0px 1px 6px #d3dce6;'
    },
    showPwd() {
      if (this.passwordType === 'password') {
        this.passwordType = ''
      } else {
        this.passwordType = 'password'
      }
      this.$nextTick(() => {
        this.$refs.password.focus()
      })
    },
    handleLogin() {
      this.loading = true
      this.$action.login(this.loginForm).then(res => {
        this.$action.getInfo().then(response=>{
          this.$router.push(this.redirect || '/' )
        })
      }).catch(res=>{
        this.getVerify()
      }).finally(() => {
        this.loading = false
      })
    }
  }
}
</script>
<style scoped>
@supports (-webkit-mask: none) and (not (cater-color: #999)) {
  .login-container .el-input input {
    color: #999;
    height: 47px;
  }
}

/* reset element-ui css */
.login-container .el-input {
  display: inline-block;
  height: 47px;
  width: 85%;
}

.login-container input {
  background: transparent;
  border: 0px;
  -webkit-appearance: none;
  border-radius: 0px;
  padding: 12px 5px 12px 15px;
  color: #000;
  height: 47px;
  caret-color: #999;
}

.login-container .el-form-item {
  border: 1px solid rgba(255, 255, 255, 0.1);
  background: #f5f7fa;
  border-radius: 5px;
  color: #454545;
}

.icp {
  text-align: center;
  width: 100%;
  color: #fff;
  opacity: .5;
  font-size: 12px;

}

.icp a {
  color: #fff;
  text-decoration: none;
}

.login-container {
  min-height: 100%;
  width: 100%;
  background: url("{:request()->domain()}/eadmin/login.png") no-repeat;
  overflow: hidden;
  display: flex;
  align-items: center;
  flex-direction: column;
}

.login-container .login-form {
  border-radius: 5px;
  width: 400px;
  max-width: 100%;
  padding: 30px 35px 0;
  margin-bottom: 20px;

}

.login-container .tips {
  font-size: 14px;
  color: #fff;
}

.login-container .svg-container {
  padding: 6px 5px 6px 15px;
  color: #889aa4;
  vertical-align: middle;
  display: inline-block;
}

.login-container .title-container {
  margin-top: 100px;
}

.login-container .title-container .title {
  font-size: 26px;
  color: #eee;
  text-align: center;
  font-weight: bold;
}

.login-container .show-pwd {
  position: absolute;
  right: 10px;
  top: 7px;
  font-size: 16px;
  color: #889aa4;
  cursor: pointer;
  user-select: none;
}
</style>
