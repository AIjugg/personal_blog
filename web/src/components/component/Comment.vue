<template>
  <div>
    <Content class="comment-style">
      <Card>
        <div>
          <div class="comment-info" v-show="!login">
            <div>
              <router-link to="/login/login">请登录</router-link>
            </div>
          </div>
          <div class="comment-info" v-show="login">
            <div>
              <Avatar :src="imgUrl(profilePhoto)" />
            </div>
          </div>
          <div class="comment-info" v-show="login">
            <div>
              <p>{{ nickname }}</p>
            </div>
          </div>
          <div class="comment-input">
            <div>
              <Input v-model="content" maxlength="100" show-word-limit type="textarea" placeholder="想说点什么..." :rows="4" style="width: 100%;height: 100px" />
            </div>
          </div>
          <div class="comment-button">
            <div>
              <Button type="error" @click="comment(sourceType, sourceId, content)">发表评论</Button>
            </div>
          </div>
          <Modal :loading=true v-model="tipShow">
            <p>{{ tipContent }}</p>
          </Modal>
        </div>
      </Card>
    </Content>
    <div class="comment-title">
      <Affix>
        <Tag color="volcano" size="large">{{ typename }}</Tag>
      </Affix>
    </div>
    <Content class="comment-board">
      <Card style="background-color:rgba(0,0,0,0)">
        <div style="min-height: 200px;">
          <List item-layout="vertical">
            <ListItem v-for="(item, index) in commentlist" :key="item.id">
              <div class="comment-item-gap">
                <div class="comment-item-user">
                  <div class="comment-item-photo">
                    <Avatar :src="imgUrl(item.profile_photo)" size="small" />
                  </div>
                  <div class="comment-item-name">
                    <p>{{ item.nickname }}</p>
                  </div>
                </div>
                <div class="comment-item-time">
                  <p>评论于 {{ item.created_at }}</p>
                </div>
                <div class="comment-item-content">
                  <div class="comment-item-font">
                    {{ item.content }}
                  </div>
                  <div class="comment-item-comment" v-show="item.comments > 0">
                    <Button type="text" size="small" @click="getChildComment(item.id)">查看回复({{ item.comments }})</Button>
                  </div>
                  <!--<div class="comment-del">
                    <Button type="text" size="small" v-show="item.delState" @click="delComment(item.id)">删除</Button>
                    <Button type="text" @click="likeAction(index)">
                  <Icon type="ios-thumbs-up-outline" color="#FF6633" v-show="!item.likeState" size="20"/>
                    <Icon type="ios-thumbs-up" color="#FF6633" v-show="item.likeState" size="20"/> {{ item.like }}
                  </Button>
                  </div>-->
                  <div class="comment-del">
                    <Button type="text" ghost @click="childComment(0, index)">
                      <Icon type="ios-send" size="20" color="gray"/>
                    </Button>
                  </div>
                  <div v-if="childCommentList[item.id] !== undefined" class="comment-child-list">
                    <div style="  background-color: #f9f3f0;">
                      <List border size="small" item-layout="vertical">
                        <ListItem v-for="child in childCommentList[item.id].list" :key="child.id">
                          <div class="comment-child-user">
                            <p>{{ child.title }}:</p>
                          </div>
                          <div class="comment-child-time">
                            <p>{{ child.created_at }}</p>
                          </div>
                          <div class="comment-child-font">
                            <p>{{ child.content }}</p>
                          </div>
                          <div class="comment-child-button">
                            <Button type="text" ghost @click="childComment(child.id, index)">
                              <Icon type="ios-send" size="20" color="gray"/>
                            </Button>
                          </div>
                        </ListItem>
                      </List>
                    </div>
                  </div>
                </div>
                <div class="comment-child">
                  <div class="comment-child-comment">
                    <Input v-model="item.msg" :placeholder="item.defaultMsg === undefined ? '想说啥呢' : item.defaultMsg" style="width: 100%" />
                  </div>
                  <div class="comment-child-comment-button">
                    <Button type="info" size="small" @click="comment('comment', item.id, item.msg, item.pid)">评论</Button>
                  </div>
                </div>
                <div style="clear:both; padding-bottom: 10px"></div>
              </div>
            </ListItem>
          </List>
          <div class="page-style">
            <Page :total=total size="small" :style="{padding: '30px'}" @on-change="changePage"/>
          </div>
        </div>
      </Card>
    </Content>
  </div>
</template>

<script>
import {tipWarning} from '@/plugin/login.js'
export default {
  props: ['sourceType', 'sourceId', 'typename'],
  inject: ['reload'],
  data () {
    return {
      total: 0,
      page: 1,
      commentlist: [],
      login: localStorage.getItem('isLogin'),
      profilePhoto: localStorage.getItem('profilePhoto') ? localStorage.getItem('profilePhoto') : '/static/setting/defaultheadimg.jpg',
      nickname: localStorage.getItem('nickname') ? localStorage.getItem('nickname') : '快改个昵称吧',
      content: '',
      tipShow: false,
      tipContent: '',
      childCommentList: {}
    }
  },
  mounted: function () {
    this.getComment()
  },
  methods: {
    imgUrl (url) {
      if (url !== '' && url.indexOf('/static/setting', 0) !== -1) {
        return url
      } else if (url === '') {
        return '/static/setting/defaultheadimg.jpg'
      }
      return this.baseUrl + '/' + url
    },
    getComment () {
      this.$http.get(this.baseUrl + '/communicate/comment-list', {params: {page: this.page, sourceType: this.sourceType, sourceId: this.sourceId}}).then((res) => {
        this.commentlist = res.data.data.list
        this.total = parseInt(res.data.data.total)
      }, (res) =>
        console.log(res)
      )
    },
    getChildComment (id) {
      this.$http.get(this.baseUrl + '/communicate/comment-list', {params: {page: this.childCommentList[id] === undefined ? 1 : this.childCommentList[id].page, sourceType: 'comment', sourceId: id}}).then((res) => {
        let childList = {}
        for (let i = 0; i < res.data.data.list.length; i++) {
          res.data.data.list[i].title = res.data.data.list[i].nickname
          childList[res.data.data.list[i].id] = res.data.data.list[i]
        }
        for (let i in childList) {
          if (childList[i].pid !== '0' && childList[childList[i].pid] !== undefined) {
            childList[i].title = childList[i].nickname + ' 对 ' + childList[childList[i].pid].nickname + ' 说'
          }
        }
        this.childCommentList[id] = {page: 1, list: childList, total: parseInt(res.data.data.total)}
        this.$forceUpdate()
      }, (res) =>
        console.log(res)
      )
    },
    changePage (val) {
      this.page = val
      this.list = this.getComment()
    },
    comment (sourceType, sourceId, content, pid = 0) {
      let _self = this
      if (content === '' || content === undefined) {
        tipWarning(_self, 1, '好歹写点呗')
        return false
      }
      this.$http.post(this.baseUrl + '/communicate/comment-edit', {content: content, sourceType: sourceType, sourceId: sourceId, pid: pid}, {
        emulateJSON: true
      }).then((res) => {
        if (res.data.code === '0') {
          this.reload()
        } else {
          tipWarning(_self, res.data.code, res.data.msg)
        }
      }, (res) =>
        console.log(res)
      )
    },
    childComment (pid, index) {
      this.commentlist[index].pid = pid
      if (pid === 0) {
        this.commentlist[index].defaultMsg = '想说啥呢'
      } else {
        this.commentlist[index].defaultMsg = '对 ' + this.childCommentList[this.commentlist[index].id].list[pid].nickname + ' 说: '
      }
      this.$set(this.commentlist, index, this.commentlist[index])
    }
    // likeAction (index) {
    //   this.list[index].likeState = !this.list[index].likeState
    //   if (this.list[index].likeState === true) {
    //     this.list[index].like = parseInt(this.list[index].like) + 1
    //   } else {
    //     this.list[index].like = parseInt(this.list[index].like) - 1
    //   }
    //   let _self = this
    //   commentLike(_self, 'comment', this.list[index].id, index)
    // },
    // delComment (index) {
    //   let _self = this
    //   this.$http.post(this.baseUrl + '/appreciate/del-comment', {id: this.list[index].id}, {
    //     emulateJSON: true
    //   }).then((res) => {
    //     if (res.data.code === '0') {
    //       this.reload()
    //     } else {
    //       tipWarning(_self, res.data.code, res.data.msg)
    //     }
    //   }, (res) =>
    //     console.log(res)
    //   )
    // },
  }
}
</script>
<style scoped>
a{
  color: #1f1f1f;
}
.comment-style {
  margin-top: 20px;
}
.comment-info {
  float: left;
  padding-right: 10px;
}
.comment-input {
  clear: both;
  padding-top: 10px;
}
.comment-button {
  text-align: right
}
.comment-title {
  text-align: center;
  margin-top: 20px;
  margin-bottom: 20px;
}
.comment-item-gap {
  padding-left: 30px;
}
.comment-item-photo {
  float: left;
}
.comment-item-name {
  float: left;
  padding-left: 10px;
}
.comment-item-user {
  float: left;
}
.comment-item-time {
  float: right;
}
.comment-item-content {
  clear: both;
  padding-top: 20px;
}
.comment-item-font {
  font-size: 17px;
  font-family: 楷体;
  text-align: left
}
.comment-item-comment {
  float: left;
  margin-left: -10px;
  padding-top: 30px;
}
.comment-del {
  float: right;
  padding-top: 30px;
}
.comment-child {
  clear: both;
  padding-top: 10px;
  text-align: left
}
.comment-child-comment {
  width:90%;
  float: left;
}
.comment-child-comment-button {
  padding-top: 3px;
  padding-left: 10px;
  float: left;
}
.comment-child-list {
  clear: both;
  padding-top: 10px;
}
.comment-child-font {
  clear: both;
  font-size: 15px;
  font-family: 楷体;
  text-align: left
}
.comment-child-user {
  font-size: 15px;
  float: left;
}
.comment-child-time {
  font-size: 12px;
  float: right;
}
.comment-child-button {
  text-align: right
}
.page-style {
  text-align: center;
}
</style>
