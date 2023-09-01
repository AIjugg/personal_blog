<template>
  <div>
    <el-container>
      <el-header>
        <div id="jsi-flying-fish-container"></div>
      </el-header>
      <el-main>
        <span class="layout-footer-center">我是底线@2023</span>
      </el-main>
    </el-container>
  </div>
</template>

<script>
import '../../plugin/fish'

export default {
  name: 'myFooter'
}
</script>
<style>
  .layout-footer-center {
    font-size: 14px;
    font-weight: 600;
    text-align: center;
  }

#jsi-flying-fish-container {
  width: 100%;
  height: 200px;
  z-index: 1;
  left: 0;
}
</style>
