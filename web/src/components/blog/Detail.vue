<template>
  <Content class="card-detail-style">
    <Card>
      <div class="blog-style">
        <div>
          <h1>{{ title }}</h1>
        </div>
        <div class="blog-info">
          <Avatar :src="imgUrl(profilePhoto)" />{{ nickname }}
          <Icon type="ios-clock-outline" /> 发表于 {{ created_at }}
          <Icon type="ios-eye-outline" /> 阅读次数：{{ pageviews }}
          <Icon type="ios-thumbs-up-outline"/> 点赞：{{ likeNum }}
          <Icon type="ios-chatbubbles-outline" /> 评论：{{ commentNum }}
        </div>
        <div class="type-list">
          <div v-for="type in types" :key="type" class="blog-type">
            <Tag color="error"> {{ type.type_name }} </Tag>
          </div>
        </div>
        <div class="blog-content">
          <div id="blogContent"></div>
        </div>
        <Modal :loading=true v-model="tipShow">
          <p>{{ tipContent }}</p>
        </Modal>
        <div class="like-state">
          <Button type="text" @click="likeAction">
            <Icon type="ios-thumbs-up-outline" color="#FF6633" v-show="likeState === '2'" size="30"/>
            <Icon type="ios-thumbs-up" color="#FF6633" v-show="likeState === '1'" size="30"/> {{ likeWord }}
          </Button>
        </div>
        <Divider> END </Divider>
        <Comment sourceType="blog" :sourceId="id" typename="评论区"></Comment>
      </div>
    </Card>
  </Content>
</template>
<script>
import {tipWarning} from '@/plugin/login.js'
import {like} from '@/plugin/appreciate.js'
import Comment from '@/components/component/Comment'
export default {
  components: {
    Comment
  },
  data () {
    return {
      blog_id: this.$route.params.blog_id,
      title: '',
      content: '',
      types: [],
      likeNum: 0,
      commentNum: 0,
      pageviews: 0,
      created_at: '',
      nickname: '',
      likeState: false,
      likeWord: '推荐',
      // 用户是否登录
      login: false,
      profilePhoto: '/static/setting/defaultheadimg.jpg',
      tipShow: false,
      tipContent: ''
    }
  },
  mounted: function () {
    this.blogDetail()
    this.userStatus()
  },
  methods: {
    imgUrl (url) {
      return this.baseUrl + '/' + url
    },
    blogDetail () {
      let _self = this
      this.$http.get(this.baseUrl + '/blog/get-blog-detail', {params: {blog_id: this.blog_id}}).then((res) => {
        if (res.data.code === 0) {
          this.title = res.data.data.detail.title
          this.likeNum = res.data.data.detail.like
          this.commentNum = res.data.data.detail.comment
          this.pageviews = res.data.data.detail.pageviews
          this.created_at = res.data.data.detail.created_at
          this.content = res.data.data.detail.content
          this.nickname = res.data.data.detail.nickname
          this.profilePhoto = res.data.data.detail.profile_photo
          this.types = res.data.data.detail.types
          document.getElementById('blogContent').innerHTML = this.content
        } else {
          tipWarning(_self, res.data.status.code, res.data.status.msg)
        }
      }, (res) =>
        console.log(res)
      )
    },
    userStatus () {
      let _self = this
      this.$http.get(this.baseUrl + '/communicate/like-status', {params: {sourceId: this.id, sourceType: 'blog'}}).then((res) => {
        if (res.data.code === '0') {
          this.likeState = res.data.data.likeState
          this.likeWord = this.likeState === '1' ? '已推荐' : '推荐'
        } else {
          tipWarning(_self, res.data.code, res.data.msg)
        }
      }, (res) =>
        console.log(res)
      )
    },
    likeAction () {
      this.likeState = this.likeState === '1' ? '2' : '1'
      if (this.likeState === '1') {
        this.likeWord = '已推荐'
      } else {
        this.likeWord = '推荐'
      }
      let _self = this
      like(_self, 'blog', this.id)
    }
  }
}
</script>
<style>
.card-detail-style {
  padding: 0 5%;
  margin: 1% 5%
}
.blog-style {
  text-align: left;
  padding: 10%;
}
#blogContent pre {
  background-color: #23241f;
  color: #f8f8f2;
  overflow: visible;
  white-space: pre-wrap;
  margin-bottom: 5px;
  margin-top: 5px;
  padding: 5px 10px;
  border-radius: 3px;
}
#blogContent img {
  max-width: 100%;
}
.blog-info {
  padding-top: 50px
}
.type-list {
  padding-top: 30px
}
.blog-type {
  float: left;
}
.blog-content {
  clear: both;
  padding-top: 50px;
  min-height: 200px;
}
.like-state {
  float: right;
  padding-right: 20px;
}
</style>
