<template>
  <Content :style="{padding: '20px 40px', margin: '0'}">
    <Card>
      <div>
        <div class="type-title">
          <h3>类型</h3>
        </div>
        <div style="clear: both"></div>
      </div>
      <Modal :loading=true v-model="tipShow">
        <p>{{ tipContent }}</p>
      </Modal>
      <Divider />
      <div>
        <div class="type-style">
          <div class="type-input">
            <el-input v-model="newType" placeholder="新增类型名"></el-input>
          </div>
          <div class="type-btn">
            <el-button type="primary" @click="addType(newType)">新增</el-button>
          </div>
        </div>
        <Divider />
        <List item-layout="vertical">
          <ListItem v-for="type in list" :key="type.type_id">
            <div class="type-style">
              <div class="type-input">
                <el-input v-model="type.type_name" placeholder="类型名"></el-input>
              </div>
              <div class="type-btn">
                <el-button type="primary" @click="editType(type.type_id, type.type_name)">保存</el-button>
              </div>
              <div class="type-btn">
                <el-button type="warning" @click="delType(type.type_id)">删除</el-button>
              </div>
              <div class="type-btn">
                <el-button type="danger" @click="forceDelType(type.type_id)">强制删除</el-button>
              </div>
            </div>
          </ListItem>
        </List>
      </div>
      <div style="clear: both"></div>
    </Card>
  </Content>
</template>

<script>
import {tipWarning} from '@/plugin/login.js'
export default {
  inject: ['reload'],
  data () {
    return {
      newType: '',
      tipShow: false,
      tipContent: '',
      list: []
    }
  },
  mounted: function () {
    this.typeList()
  },
  methods: {
    addType (type) {
      let _self = this
      this.$http.post(this.baseUrl + '/blog/add-blog-type', { type_name: type }, {
        emulateJSON: true
      }).then((result) => {
        if (result.data.code === 0) {
          // tipWarning(_self, '-1', '编辑成功！')
          this.reload()
        } else {
          tipWarning(_self, result.data.code, result.data.msg)
          console.log(result.data)
        }
      }, (result) => {
        console.log(result)
      })
    },
    editType (id, type) {
      let _self = this
      this.$http.post(this.baseUrl + '/blog/edit-blog-type', {type_name: type, type_id: id}, {
        emulateJSON: true
      }).then((result) => {
        if (result.data.code === 0) {
          // tipWarning(_self, '-1', '编辑成功！')
          this.reload()
        } else {
          tipWarning(_self, result.data.code, result.data.msg)
          console.log(result.data)
        }
      }, (result) => {
        console.log(result)
      })
    },
    delType (id) {
      let _self = this
      this.$http.post(this.baseUrl + '/blog/del-blog-type', { type_id: id }, {
        emulateJSON: true
      }).then((result) => {
        if (result.data.code === 0) {
          // tipWarning(_self, '-1', '编辑成功！')
          this.reload()
        } else {
          tipWarning(_self, result.data.code, result.data.msg)
          console.log(result.data)
        }
      }, (result) => {
        console.log(result)
      })
    },
    forceDelType (id) {
      let _self = this
      this.$http.post(this.baseUrl + '/blog/del-blog-type', { type_id: id, if_force: true }, {
        emulateJSON: true
      }).then((result) => {
        if (result.data.code === 0) {
          // tipWarning(_self, '-1', '编辑成功！')
          this.reload()
        } else {
          tipWarning(_self, result.data.code, result.data.msg)
          console.log(result.data)
        }
      }, (result) => {
        console.log(result)
      })
    },
    typeList () {
      this.$http.get(this.baseUrl + '/blog/blog-type-list').then((res) => {
        if (res.data.code === 0) {
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
@import "../../assets/css/button.css";
.type-title {
  float: left;
}
.type-style {
  height: 50px;
}
.type-input {
  float: left;
}
.type-btn {
  padding-left: 20px;
  float: left;
}
.demo-split{
  height: 200px;
  border: 1px solid #dcdee2;
}
.demo-split-pane{
  padding: 10px;
}
</style>
