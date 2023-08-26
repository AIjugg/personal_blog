<template>
  <div>
    <div style="margin: 30px 0 0;height: 330px">
      <div><Input v-model="username" maxlength="30" show-word-limit prefix="ios-contact" placeholder="用户名" style="width: 250px" /></div>
      <div style="margin: 30px"><Input v-model="password" type="password" password placeholder="密码" style="width: 250px" /></div>
      <div style="margin: 30px"><Input v-model="passwordRepeat" type="password" password placeholder="确认密码" style="width: 250px" /></div>
      <div style="margin-top: 30px; margin-left: 208px"><slide-verify
                ref="slideblock"
                @again="onAgain"
                @fulfilled="onFulfilled"
                @success="onSuccess"
                @fail="onFail"
                @refresh="onRefresh"
                :accuracy="accuracy"
                :slider-text="text"
        ></slide-verify></div>
    </div>
    <div style="margin: 80px auto 0;width: 200px;background-color: #ededed"><p :class="verify ? 'verify-word-success' : 'verify-word-fail'">{{ msg }}</p></div>
    <div style="margin:10px auto 0; width: 200px">
      <div style="background-color: #ededed">
        <Button type="text" @click="specialRegister('mobile')" ghost ><Avatar :src="method == 'mobile' ? '/static/setting/mobile_blue.png' : '/static/setting/mobile.png'"/></Button>
        <Button type="text" @click="specialRegister('email')" ghost ><Avatar :src="method == 'email' ? '/static/setting/email_blue.png' : '/static/setting/email.png'"/></Button>
        <Button type="text" @click="specialRegister('normal')" ghost ><Avatar :src="method == 'normal' ? '/static/setting/user_blue.png' : '/static/setting/user.png'"/></Button>
      </div>
    </div>
    <div style="margin: 30px" v-show="showCode">
      <Input v-model="code" password placeholder="验证码" style="width: 180px" />
      <Button type="primary" :loading="verifyCode" @click="sendCode">
        <span>{{ waitContent }}</span>
      </Button>
    </div>
    <div style="margin-top: 30px">
      <i-button type="error" style="width:50%" @click="register">注册</i-button>
    </div>
    <div style="width:50%;margin:10px auto">
      <p class="login-error">{{ error }}</p>
    </div>
  </div>
</template>

<script>
import { loginByAccount } from '@/plugin/login'
import SlideVerify from '@/components/component/SlideVerify'
export default {
  components: {
    SlideVerify
  },
  data () {
    return {
      method: 'normal',
      username: '',
      password: '',
      passwordRepeat: '',
      verify: false,
      code: '',
      showCode: false,
      verifyCode: false,
      verifyMsg: '',
      error: '',
      msg: '滑窗验证',
      text: '向右滑动',
      // 精确度小，可允许的误差范围小；为1时，则表示滑块要与凹槽完全重叠，才能验证成功。默认值为5
      accuracy: 2,
      timer: 60,
      waitContent: '发送验证码',
      canClick: true
    }
  },
  methods: {
    onSuccess (times) {
      this.msg = '耗时' + times + '毫秒'
      this.verify = true
    },
    onFail () {
      this.msg = '验证失败'
    },
    onRefresh () {
      console.log('点击了刷新小图标')
      this.msg = '刷新成功'
    },
    onFulfilled () {
      console.log('刷新成功啦！')
    },
    onAgain () {
      console.log('检测到非人为操作的哦！')
      this.msg = 'try again'
      // 刷新
      this.$refs.slideblock.reset()
    },
    specialRegister (method) {
      this.method = method
      if (method !== 'normal') {
        this.showCode = true
      } else {
        this.showCode = false
      }
    },
    register () {
      if (!this.verify) {
        this.error = '请完成划窗验证'
        return false
      }
      if (!this.usernameCheck()) {
        return false
      }
      if (this.password === '') {
        this.error = '密码为空'
        return false
      } else if (this.password !== this.passwordRepeat) {
        this.error = '两次密码不一致'
        return false
      }

      let registerUrl = ''
      let params = {}
      if (this.method === 'normal') {
        registerUrl = this.baseUrl + '/user/register'
        params = { 'username': this.username, 'password': this.password }
      } else if (this.method === 'email') {
        registerUrl = this.baseUrl + '/register/email-register'
        params = { 'email': this.username, 'password': this.password, 'code': this.code }
      } else if (this.method === 'mobile') {
        registerUrl = this.baseUrl + '/register/mobile-register'
        params = { 'mobile': this.username, 'password': this.password, 'code': this.code }
      }
      if (registerUrl === '') {
        return false
      }

      let _self = this
      this.$http.post(registerUrl, params, {
        emulateJSON: true
      }).then((res) => {
        if (res.data.status.code === 0) {
          loginByAccount(_self)
        } else {
          console.log(res.data)
          this.error = res.data.status.msg
        }
      }, (res) => {
        this.error = '服务器连接错误'
        console.log(res)
      }
      )
    },
    /**
     * 验证用户名格式是否正确
     */
    usernameCheck () {
      if (this.method === 'email') {
        let reg = /^[\w-]+(\.[\w-]+)*@[\w-]+(.[a-z]{2,})+$/i
        console.log(this.username)
        if (!reg.test(this.username)) {
          this.error = '邮箱号格式错误'
          return false
        }
      } else if (this.method === 'mobile') {
        let reg = /^1[34578]\d{9}$/
        if (!reg.test(this.username)) {
          this.error = '手机号错误'
          return false
        }
      } else if (this.method === 'normal') {
        let reg = /^[\w-]{2,30}$/
        if (!reg.test(this.username)) {
          this.error = '用户名请勿包含特殊符号'
          return false
        }
      }
      return true
    },
    /**
     * 发送手机验证码
     */
    sendCode () {
      if (!this.usernameCheck()) {
        return false
      }
      let validateUrl = ''
      let params = {}
      if (this.method === 'email') {
        validateUrl = this.baseUrl + '/validate/email-validate'
        params = {'email': this.username}
      } else if (this.method === 'mobile') {
        validateUrl = this.baseUrl + '/validate/mobile-validate'
        params = {'mobile': this.username}
      }
      if (validateUrl === '') {
        return false
      }
      this.getCode()

      this.$http.post(validateUrl, params, {
        emulateJSON: true
      }).then((res) => {
        if (res.data.code === '0') {
          this.verifyCode = true
          console.log('验证码发送成功')
          return true
        } else {
          console.log(res.data)
          this.error = res.data.msg
        }
      }, (res) => {
        this.error = '服务器连接错误'
        console.log(res)
      })
      return false
    },
    /**
     * 获取验证码倒计时
     * @returns {boolean}
     */
    getCode () {
      if (!this.canClick) {
        return false
      }
      this.canClick = false
      this.waitContent = this.timer + 's后重新发送'
      let clock = window.setInterval(() => {
        this.timer--
        this.waitContent = this.timer + 's后重新发送'
        if (this.timer < 0) {
          window.clearInterval(clock)
          this.canClick = true
          this.waitContent = '重新发送验证码'
          this.timer = 60
          this.verifyCode = false
        }
      }, 1000)
    }
  }
}
</script>
<style>
.login-error{
  color: #ff7418;
  font-size: 18px
}
.verify-word{
  color: #000000;
}
.verify-word-success{
  color: #3feb12;
}
.verify-word-fail{
  color: #ff4c00;
}
</style>
