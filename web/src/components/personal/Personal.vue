<template>
  <div class="profile-style">
    <Layout id="personal">
      <Header>
        <div>
          <div class="home-site">
            <Button type="text" @click="home" ghost><Icon type="md-home" color="white" size="30"/></Button>
          </div>
        </div>
      </Header>
      <Content class="card-style">
        <Row :gutter="10">
          <Col span="5">
            <div style="width: 100%">
              <Card style="background: #515a6e">
                <Menu theme="dark" active-name="1" :style="{width:'100%'}">
                  <MenuItem name="1" v-show="authList['user-edit']">
                    <Icon type="logo-octocat" />
                    <router-link to="/personal/profile">个人资料</router-link>
                  </MenuItem>
                  <MenuItem name="2"  v-show="authList['blog-edit']">
                    <Icon type="md-chatbubbles" />
                    <router-link to="/personal/blog-manager">博客管理</router-link>
                  </MenuItem>
                  <MenuItem name="3"  v-show="authList['user-list']">
                    <Icon type="ios-stats-outline" />
                    <router-link to="/personal/statistic">用户流量</router-link>
                  </MenuItem>
                  <MenuItem name="4"  v-show="authList['setting']">
                    <Icon type="ios-flame" />
                    <router-link to="/personal/type">分类管理</router-link>
                  </MenuItem>
                </Menu>
              </Card>
            </div>
          </Col>
          <Col span="19">
            <Card>
              <router-view></router-view>
            </Card>
          </Col>
        </Row>
      </Content>
    </Layout>
  </div>
</template>

<script>
export default {
  data () {
    return {
      authList: {
        'user-edit': 1,
        'setting': 0,
        'blog-edit': 0,
        'user-list': 0
      }
    }
  },
  mounted () {
    this.auth()
  },
  methods: {
    auth () {
      this.$http.get(this.baseUrl + '/user/auth-list').then((res) => {
        if (res.data.code === '0') {
          this.authList = res.data.data.authList
        } else {
          console.log(res.data)
        }
      }, (res) =>
        console.log(res)
      )
    },
    home () {
      this.$router.push('/home')
    }
  }
}
</script>
<style scoped>
a{
  color: #ffffff;
}
.profile-style {
  margin: 0 2%
}
.home-site {
  float: right;
  padding-top: 150px
}
.card-style {
  margin-top: 20px;
  min-width: 800px
}
#personal .ivu-layout-header {
  background: #1f1f1f;
  background-image: url("/static/setting/stars.jpg");
  background-repeat: no-repeat;
  background-size: cover;
  padding: 0;
  height: 200px;
}
#personal .ivu-menu-item-active {
  color: #ffffff;
}

</style>
