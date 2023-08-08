<template>
  <Content :style="{padding: '20px 40px', margin: '0'}">
    <div>
      <div>
        <div>
          <Row>
            <div class="title">
              总博客数
            </div>
            <div class="word">
                {{ blogs }}
            </div>
          </Row>
        </div>
        <div class="gap">
          <Row>
            <div class="title">
              总浏览量
            </div>
            <div class="word">
                {{ pageviews }}
            </div>
          </Row>
        </div>
        <div class="gap">
          <Row>
            <div class="title">
              总点赞数
            </div>
            <div class="word">
                {{ likes }}
            </div>
          </Row>
        </div>
        <div class="gap">
          <Row>
            <div class="title">
              总评论数
            </div>
            <div class="word">
                {{ comments }}
            </div>
          </Row>
        </div>
        <div class="gap">
          <Row>
            <div class="title">
              今日访问数
            </div>
            <div class="word">
              {{ todayPageviews }}
            </div>
          </Row>
        </div>
      </div>
      <Modal :loading=true v-model="tipShow">
        <p>{{ tipContent }}</p>
      </Modal>
    </div>
    </Card>
  </Content>
</template>

<script>
import {tipWarning} from '@/plugin/login.js'
export default {
  data () {
    return {
      likes: 0,
      comments: 0,
      blogs: 0,
      pageviews: 0,
      todayPageviews: 0,
      tipShow: false,
      tipContent: ''
    }
  },
  mounted: function () {
    // this.totalStatistic()
  },
  methods: {
    totalStatistic () {
      this.$http.get(this.baseUrl + '/appreciate/total-statistic').then((res) => {
        if (res.data.code === '0') {
          this.likes = res.data.data.likes
          this.comments = res.data.data.comments
          this.blogs = res.data.data.blogs
          this.pageviews = res.data.data.pageviews
          this.todayPageviews = res.data.data.todayPageviews
        } else {
          let _self = this
          tipWarning(_self, res.data.code, res.data.msg)
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
.gap {
  margin-top: 10px;
}
.title {
  padding-right: 10px;
  float: left;
  color: #636363;
  font-weight: 700;
  font-family: arial;
  font-size: 14px;
  line-height: 22px;
}
.word {
  float: left;
  color: #F40;
  font-weight: 700;
  font-family: arial;
  font-size: 18px;
  line-height: 22px;
}
</style>
