<template>
  <div>
    <el-container>
      <el-main>
        <el-row>
          <el-col :span="6"><div class="grid-content"></div></el-col>
          <el-col :span="12">
            <el-col :span="6"><span style="font-size: 30px; font-weight: 600; color:azure">changye博客</span></el-col>
            <el-col :span="12">
              <el-row :gutter="20">
                <el-col :span="4"><div class="grid-content"></div></el-col>
                <el-col :span="4"><el-button type="text"><router-link to="/">HOME</router-link></el-button></el-col>
                <el-col :span="4"><el-button type="text"><router-link to="/blog">BLOG</router-link></el-button></el-col>
                <el-col :span="5"><el-button type="text"><router-link to="/message">RECORD</router-link></el-button></el-col>
                <el-col :span="4"><el-button type="text"><router-link to="/message">NOTES</router-link></el-button></el-col>
              </el-row>
            </el-col>
            <el-col :span="6" v-show="!login"><el-button type="text"><router-link to="/login">LOGIN</router-link></el-button></el-col>
            <el-col :span="6" v-show="login">
              <router-link to="/personal"><Avatar :src="imgUrl(profilePhoto)" />{{ nickname }}</router-link>
              <el-button type="text" @click="logout" style="color: #F2F6FC;font-size:10px;padding-left:5px">登出</el-button>
            </el-col>
          </el-col>
            <el-col :span="6"><div class="grid-content"></div></el-col>
        </el-row>
      </el-main>
    </el-container>
  </div>
</template>

<script>

import { userInfo } from '@/plugin/login'

export default {
  name: 'myHeader',
  data () {
  return {
    login: localStorage.getItem('isLogin'),
    nickname: localStorage.getItem('nickname'),
    profilePhoto: localStorage.getItem('profilePhoto')
  }
  },
  mounted: function () {
    let _self = this
    userInfo(_self, this.start)
  },
  methods: {
    imgUrl(url) {
      if (url !== null && url.indexOf('/static/setting', 0) === -1) {
        return this.baseUrl + '/' + url
      }
      return url
    },
    start(obj) {
      obj.login = localStorage.getItem('isLogin')
      obj.profilePhoto = localStorage.getItem('profilePhoto') === '' ? '/static/setting/defaultheadimg.jpg' : localStorage.getItem('profilePhoto')
      obj.nickname = localStorage.getItem('nickname') === '' ? '无名' : localStorage.getItem('nickname')
    },
    logout() {
      localStorage.removeItem('accessToken')
      localStorage.removeItem('isLogin')
      localStorage.removeItem('profilePhoto')
      localStorage.removeItem('nickname')
      this.login = false
      this.nickname = ''
      this.profilePhoto = ''
      this.reload()
    }
  }
}
</script>
<style scoped>
  .el-container {
    background-image: url('../../assets/sun.jpg');
    background-repeat: no-repeat;
    background-size: 100%;
    height: 800px;
  }


a{
  color: #f5f7f9;
}
.el-col {
  border-radius: 4px;
}

.bg-purple-dark {
  background: #99a9bf;
}

.bg-purple {
  background: #d3dce6;
}

.bg-purple-light {
  background: #e5e9f2;
}

.grid-content {
  border-radius: 4px;
  min-height: 36px;
}

.row-bg {
  padding: 10px 0;
  background-color: #f9fafc;
}

.el-button {
  font-size: 14px;
  font-weight: 600
}
</style>
