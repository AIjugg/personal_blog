<template>
  <div>
    <el-container>
      <el-header style="padding:0; height:400px"><myHeader></myHeader></el-header>
      <el-main><state></state></el-main>
      <router-view></router-view>
      <el-footer><myFooter></myFooter></el-footer>
    </el-container>
  </div>
</template>

<script>
import myHeader from './component/MyHeader'
import myFooter from './component/MyFooter'
import state from './component/State'
import '../plugin/background'

export default {
  components: {
    myHeader,
    myFooter,
    state
  }
}
</script>
