<template>
  <div class="canvas-page">
    <canvas></canvas>
    <Content style="position: absolute;width: 700px;min-height: 800px;left:50%;margin-left: -350px;top:10%;">
      <Card style="min-height: 600px;background: url('/static/setting/loginbg.jpg');background-repeat:no-repeat;background-size:100% 100%">
        <Menu mode="horizontal" theme="dark" active-name="1"  style="background-color:rgba(134,134,134,0.77)">
          <Row>
            <Col span="12"><MenuItem name="1" style="left:40%">
              <router-link to="/login/login">登录</router-link>
            </MenuItem></Col>
            <Col span="12"><MenuItem name="2" style="left:40%">
              <router-link to="/login/register">注册</router-link>
            </MenuItem></Col>
          </Row>
        </Menu>
        <div>
          <router-view></router-view>
        </div>
        <Row>
          <div style="float: right">
            <Button type="text" @click="home" ghost ><Icon type="md-home" color="black" size="30"/></Button>
          </div>
        </Row>
      </Card>
    </Content>
  </div>
</template>

<script>
import { canvas } from '../../plugin/canvas'
export default {
  mounted () {
    canvas(document.getElementsByTagName('canvas')[0])
  },
  methods: {
    home () {
      this.$router.push('/home')
    }
  }
}
</script>
<style scoped>
a {
  color: white;
  font-size: 18px;
  font-family: 楷体;
}
.canvas-page {
  height: 100vh;
  width: 100vw;
  background-size: cover;
  position: relative;
  background-color: #ffffff;
  background-image: radial-gradient(circle at 2% 60%, #cccccc, transparent 100%),
  radial-gradient(circle at 98% 70%, #d9d9d9, transparent 100%),
  radial-gradient(circle at 50% 50%, #e4e4e4, transparent 100%);
}
.canvas {
  position: absolute;
  left: 0;
  top: 0;
  background: transparent;
  z-index: 15;
}
</style>
