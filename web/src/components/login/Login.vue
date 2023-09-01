<template>
  <div id="login">
    <div style="margin: 70px auto 0; height: 330px;width:500px">
      <div><Input v-model="username" prefix="ios-contact" placeholder="手机号/邮箱/用户名" style="width: 250px" /></div>
      <div style="margin: 30px"><Input v-model="password" type="password" password placeholder="密码" style="width: 250px" /></div>
      <Row>
        <Col span="12">记住密码</Col>
        <Col span="12"><router-link to="/forget">忘记密码？</router-link></Col>
      </Row>
      <div style="border-bottom: 1px solid #1f1f1f"></div>
      <div class="demo-avatar" style="padding:30px">
        <Avatar src="/static/setting/wechat.png" size="large" />
        <Avatar src="/static/setting/github.png" size="large" />
      </div>
    </div>
    <Button type="primary" style="width:50%" @click="login">登录</Button>
    <div style="width:50%;margin:10px auto">
      <p class="login-error">{{ error }}</p>
    </div>
  </div>
</template>

<script>
import { loginByAccount } from '@/plugin/login'

export default {
  data () {
    return {
      username: '',
      password: '',
      error: ''
    }
  },
  methods: {
    login () {
      if (this.username === '') {
        this.error = '用户名为空'
        return false
      } else if (this.password === '') {
        this.error = '密码为空'
        return false
      }
      let _self = this
      loginByAccount(_self)
    }
  }
}
</script>
<style>
.login-error{
  color: #ff7418;
  font-size: 18px
}
</style>
