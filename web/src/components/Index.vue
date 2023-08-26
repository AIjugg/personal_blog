<template>
  <div>
    <el-container>
      <el-header style="padding:0; height:400px"><myHeader></myHeader></el-header>
      <el-main><router-view></router-view></el-main>
      <el-footer><myFooter></myFooter></el-footer>
    </el-container>
  </div>
</template>

<script>
import myHeader from './component/MyHeader'
import myFooter from './component/MyFooter'
import '../plugin/background'

export default {
  components: {
    myHeader,
    myFooter
  }
}
</script>
