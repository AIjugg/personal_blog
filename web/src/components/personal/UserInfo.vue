<template>
  <Content :style="{padding: '20px 40px', margin: '0'}">
    <div>
      <div v-for="user in list" :key="user.id">
        <div style="margin-top: 30px">
          <Row>
            <div style="float: left; padding-right: 30px">
              <Avatar shape="square" :src="imgUrl(user.profilePhoto)" size="large" />
            </div>
            <div style="float: left; padding-right: 30px">
                  {{ user.nickname }}
            </div>
            <div style="float: left">
                上次登录：{{ user.lastLogin }}
            </div>
          </Row>
        </div>
        <Divider />
      </div>
      <Page :total="total" size="small" show-elevator :style="{padding: '30px'}" @on-change="changePage"/>
    </div>
    </Card>
  </Content>
</template>

<script>
export default {
  data () {
    return {
      page: 1,
      total: 0,
      list: []
    }
  },
  mounted: function () {
    this.userList()
  },
  methods: {
    imgUrl (url) {
      return this.baseUrl + '/' + url
    },
    userList () {
      this.$http.get(this.baseUrl + '/user/user-list', {params: {page: this.page}}).then((res) => {
        if (res.data.code === '0') {
          this.list = res.data.data.list
          this.total = res.data.data.total
        }
      }, (res) =>
        console.log(res)
      )
    },
    changePage (val) {
      this.page = val
      this.list = this.userList()
    }
  }
}
</script>

<style scoped>

</style>
