<template>
  
    <div>
      <el-row>
        <el-col :span="4"><div class="grid-content"></div></el-col>
        <el-col :span="16">
          <div class="grid-content">
            <el-container class="container-bg" style="box-shadow: 0 2px 4px rgba(0, 0, 0, .12), 0 0 6px rgba(0, 0, 0, .04)">
              <el-header style="height:120px">
                <div style="margin: 20px 0 20px 0">
                  <el-col :span="4"><div class="grid-content"></div></el-col>
                  <el-col :span="16">
                    <div>
                      <Input v-model="word" @on-enter="blogList" @on-click="blogList" icon="md-paper-plane" style="width: 100%" enter-button placeholder="关键词搜索..." on-enter="blogList" />
                    </div>
                  </el-col>
                  <el-col :span="4"><div class="grid-content"></div></el-col>
                </div>
                <el-col :span="4"><div class="grid-content"></div></el-col>
                <el-col :span="16">
                  <div class="select-gap">
                    <div class="select-style">
                      <p>排序</p>
                    </div>
                    <div class="select-style">
                      <Select v-model="sortBy" size="small" style="width:100px">
                        <Option v-for="sort in sortList" :value="sort.key" :key="sort.key">{{ sort.label }}</Option>
                      </Select>
                    </div>
                    <div class="select-style">
                      <p>分类</p>
                    </div>
                    <div class="select-style">
                      <Select v-model="typeId" size="small" style="width:100px">
                        <Option v-for="type in typeLists" :value="type.id" :key="type.id">{{ type.label }}</Option>
                      </Select>
                    </div>
                  </div>
                </el-col>
                <el-col :span="4"><div class="grid-content"></div></el-col>
              </el-header>
              <el-main>
                <List item-layout="vertical">
                  <ListItem v-for="item in list" :key="item.title">
                    <div class="blog-item">
                      <div @click="blogDetail(item.blog_id)">
                        <div style="padding-bottom: 40px">
                          <div class="blog-top" v-show="item.top == '2'">
                            <Tag color="gold">置顶</Tag>
                          </div>
                          <div class="blog-title">
                            <h3>{{ item.title }}</h3>
                          </div>
                          <div>
                            <div v-for="type_id in item.types" :key="type_id" class="blog-type">
                              <Tag color="error"> {{ types[type_id] }} </Tag>
                            </div>
                          </div>
                        </div>
                        <div style="clear:both">
                          <div class="blog-image">
                            <img :src="imgUrl(item.image)" height="125" width="200">
                          </div>
                          <div class="blog-description">
                            <p align="left">{{ item.description }}</p>
                          </div>
                        </div>
                      </div>
                      <br>
                      <div class="blog-messagebox">
                        <div class="blog-message">
                          <Icon type="ios-chatbubbles-outline" /> {{ item.comment }}
                        </div>
                        <div class="blog-message">
                          <Icon type="ios-thumbs-up-outline" /> {{ item.like }}
                        </div>
                        <div class="blog-message">
                          <Icon type="ios-eye-outline" /> {{ item.pageviews }}
                        </div>
                        <div class="blog-message">
                          <Icon type="ios-clock-outline" /> {{ item.created_at }}
                        </div>
                        <div class="blog-message">
                          <Avatar :src="imgUrl(item.profile_photo)" size="small" />{{ item.nickname }}
                        </div>
                      </div>
                    </div>
                  </ListItem>
                  <Page :current="page" :total="total" size="small" show-elevator :style="{padding: '30px'}" @on-change="changePage" />
                </List>
              </el-main>
            </el-container>
          </div>
        </el-col>
        <el-col :span="4"><div class="grid-content"></div></el-col>
      </el-row>
    </div>
</template>
<script>
export default {
  data () {
    return {
      typeId: sessionStorage.getItem('blogTypeId') ? sessionStorage.getItem('blogTypeId') : 0,
      sortBy: sessionStorage.getItem('blogSortBy') ? sessionStorage.getItem('blogSortBy') : 'created_at',
      sortList: [
        {
          'key': 'created_at',
          'label': '最新日志'
        },
        {
          'key': 'updated_at',
          'label': '最近更新日志'
        },
        {
          'key': 'like',
          'label': '点赞最多'
        },
        {
          'key': 'pageview',
          'label': '浏览量最多'
        }
      ],
      word: sessionStorage.getItem('blogWord') ? sessionStorage.getItem('blogWord') : '',
      total: 0,
      list: [],
      page: sessionStorage.getItem('blogPage') ? parseInt(sessionStorage.getItem('blogPage')) : 1,
      types: [],
      typeLists: []
    }
  },
  mounted: function () {
    this.typeList()
    this.blogList()
  },
  methods: {
    imgUrl (url) {
      if (url === '') {
        return 'static/setting/defaultcover.jpg'
      }
      return this.baseUrl + '/' + url
    },
    blogList () {
      sessionStorage.setItem('blogTypeId', this.typeId)
      sessionStorage.setItem('blogWord', this.word)
      sessionStorage.setItem('blogSortBy', this.sortBy)
      sessionStorage.setItem('blogPage', this.page)
      this.$http.get(this.baseUrl + '/blog/blog-list', {params: { type_id: this.typeId,
        word: this.word,
        sort_filed: this.sortBy,
        sort_direction: 'desc',
        page: this.page,
        pagesize: 10
      }}).then((res) => {
        this.list = res.data.data.list
        this.list.forEach(function (ele) {
          if (ele.typeIds !== null) {
            ele.types = ele.typeIds.split(',')
          } else {
            ele.types = []
          }
        })
        this.total = parseInt(res.data.data.total)
      }, (res) =>
        console.log(res)
      )
    },
    typeList () {
      this.$http.get(this.baseUrl + '/blog/blog-type-list').then((res) => {
        if (res.data.code === 0) {
          this.types = []
          this.typeLists = []
          if (res.data.data.list.length === 0) {
            return false
          }
          for (let i = 0; i < res.data.data.list.length; i++) {
            this.types[res.data.data.list[i].type_id] = res.data.data.list[i].type_name
            this.typeLists.push({
              'id': res.data.data.list[i].type_id,
              'label': res.data.data.list[i].type_name
            })
          }
          this.typeLists.unshift({
            'id': 0,
            'label': '全部'
          })
        } else {
          console.log(res.data)
        }
      }, (res) =>
        console.log(res)
      )
    },
    changePage (val) {
      this.page = val
      this.list = this.blogList()
    },
    blogDetail (blogId) {
      this.$router.push({name: 'blog-detail', params: {blog_id: blogId}})
    }
  }
}
</script>
<style scoped>
  .select-gap {
    
  }
.select-style {
  float: left;
  padding-right: 10px;
}
.blog-item {
  height: 200px;
  position: relative
}
.blog-top {
  float: left;
  padding-right: 5px
}
.blog-title {
  float: left;
  padding-right: 10px
}
.blog-type {
  float: left;
}
.blog-image {
  float: left;
  padding: 0 30px 0 0
}
.blog-description {
  float: left;
  width: 70%
}
.blog-message {
  float: right;
  padding: 0 0 0 10px
}
.blog-messagebox {
  position:absolute;
  right:0;
  bottom:0;
}
  .grid-content {
    border-radius: 4px;
    min-height: 36px;
  }
  .container-bg {
    background-color: rgba(255,255,255,1);
    opacity: 1;
  }
</style>
