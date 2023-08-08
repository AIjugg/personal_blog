<template>
  <div class="layout">
  <Layout>
      <Header>
          <Menu mode="horizontal" theme="dark" active-name="1">
              <div class="layout-logo"></div>
              <div class="layout-nav">
                  <MenuItem name="1">
                    <Icon type="md-home"></Icon>
                    <router-link to="/">首页</router-link>
                  </MenuItem>
                  <MenuItem name="2">
                    <Icon type="ios-paper-outline" /></Icon>
                    <router-link to="/blog">分享</router-link>
                  </MenuItem>
                  <MenuItem name="4">
                    <Icon type="ios-paper"></Icon>
                    <router-link to="/message">留言板</router-link>
                  </MenuItem>
              </div>
          </Menu>
      </Header>
   </Layout>
   </div>
</template>

<script>
export default {
  name: 'myHeader',
  data () {
    return {

    }
  }
}
</script>
<style scoped>
.layout{
    border: 1px solid #d7dde4;
    background: #f5f7f9;
    position: relative;
    border-radius: 4px;
    overflow: hidden;
}
.layout-logo{
    width: 100px;
    height: 30px;
    background: #515a6e;
    background-image: url('../../assets/webname.png');
    background-repeat: no-repeat;
    border-radius: 3px;
    float: left;
    position: relative;
    top: 15px;
    left: 20px;
}
.layout-nav{
    width: 500px;
    margin: 0 auto;
}
a{
  color: #f5f7f9;
}
</style>
