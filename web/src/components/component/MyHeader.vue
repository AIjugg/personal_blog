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
             <el-col :span="6"><el-button type="text"><router-link to="/message">LOGIN</router-link></el-button></el-col>
          </el-col>
            <el-col :span="6"><div class="grid-content"></div></el-col>
        </el-row>
      </el-main>
    </el-container>
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
