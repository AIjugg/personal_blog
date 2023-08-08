<template>
  <Row>
    <div style="margin-left: 40px; float: left">
      <Scroller :lists="list" v-if="list.length"></Scroller>
    </div>
    <div style="margin-right: 40px; float: right" v-show="!login">
      <router-link to="/login/login">登录</router-link>
      <router-link to="/login/register">注册</router-link>
    </div>
    <div style="margin-right: 40px; float: right" v-show="login">
      <router-link to="/personal"><Avatar :src="imgUrl(profilePhoto)" />{{ nickname }}</router-link>
      <Button type="text" @click="logout">登出</Button>
    </div>
  </Row>
</template>

<script>
import {userInfo} from '@/plugin/login'
import Scroller from '@/components/component/Scroller'
export default {
  components: {
    Scroller
  },
  inject: ['reload'],
  name: 'state',
  data () {
    return {
      list: [],
      login: localStorage.getItem('isLogin'),
      nickname: localStorage.getItem('nickname'),
      profilePhoto: localStorage.getItem('profilePhoto')
    }
  },
  mounted: function () {
    let _self = this
    userInfo(_self, this.start)
    this.notice()
  },
  methods: {
    imgUrl (url) {
      if (url !== null && url.indexOf('/static/setting', 0) === -1) {
        return this.baseUrl + '/' + url
      }
      return url
    },
    start (obj) {
      obj.login = localStorage.getItem('isLogin')
      obj.profilePhoto = localStorage.getItem('profilePhoto') === '' ? '/static/setting/defaultheadimg.jpg' : localStorage.getItem('profilePhoto')
      obj.nickname = localStorage.getItem('nickname') === '' ? '来修改昵称吧' : localStorage.getItem('nickname')
    },
    logout () {
      localStorage.removeItem('userToken')
      localStorage.removeItem('isLogin')
      localStorage.removeItem('profilePhoto')
      localStorage.removeItem('nickname')
      this.login = false
      this.nickname = ''
      this.profilePhoto = ''
      this.reload()
    },
    notice () {
      this.$http.get(this.baseUrl + '/index/notice').then((res) => {
        if (res.data.code === '0') {
          this.list = res.data.data.list
        }
      }, (res) =>
        console.log(res)
      )
    }
  }
}
</script>
<style scoped>
a{
  color:#1f1f1f;
}
</style>
