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
            <Input v-model="newType" placeholder="新增类型名" size="small" maxlength="30" style="width: 300px" />
          </div>
          <div class="type-btn">
            <Button type="info" size="small" @click="editType(0, newType)">新增</Button>
          </div>
        </div>
        <Divider />
        <List item-layout="vertical">
          <ListItem v-for="type in list" :key="type.id">
            <div class="type-style">
              <div class="type-input">
                <Input v-model="type.type" placeholder="类型名" size="small" maxlength="30" style="width: 300px" />
              </div>
              <div class="type-btn">
                <Button type="warning" size="small" @click="editType(type.id, type.type)">编辑</Button>
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
    editType (id, type) {
      let _self = this
      this.$http.post(this.baseUrl + '/blog/type-edit', {type: type, id: id}, {
        emulateJSON: true
      }).then((result) => {
        if (result.data.code === '0') {
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
      this.$http.get(this.baseUrl + '/blog/type-list').then((res) => {
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
