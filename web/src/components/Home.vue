<template>
  <div>
    <el-container>
      <el-header style="height:150px">
        <el-row>
          <el-col :span="8"><div class="grid-content"></div></el-col>
          <el-col :span="8">
            <div class="grid-content">
              <span style="font-size: 20px;
    font-weight: 600;
    color: azure;
    font-family: STKaiti">
                {{ motto }}
              </span>
            </div>
          </el-col>
          <el-col :span="8"><div class="grid-content"></div></el-col>
        </el-row>
      </el-header>
      <el-main>
        <el-row :gutter="20">
          <el-col :span="14">
            <div class="grid-content">
              <el-image :src="src" style="max-width:800px"></el-image>
              <div style="margin:100px 0 0 0;">
                <el-card class="box-card" shadow="hover" @click.native="blogDetail()">
                  <div class="text item">
                    <span style="font-size: 15px;font-weight: 600;color:black;font-family: STKaiti">{{ title }}</span>
                  </div>
                  <div class="text item">
                    <span style="font-size: 14px;font-weight: 500;color:black;font-family: STKaiti">{{ description }}</span>
                  </div>
                </el-card>
              </div>
            </div>
          </el-col>
          <el-col :span="10">
            <div class="grid-content" style="margin: 200px 0 0 0">
              <span style="font-size: 40px;
    font-weight: 600;
    color:black;
    font-family: STKaiti">
                贺新郎·读史
              </span>
              <br />
              <span style="font-size: 20px;
    font-weight: 600;
    color:black;
    font-family: STKaiti">
                人猿相揖别。<br />
                只几个石头磨过，<br />
                小儿时节。<br />
                铜铁炉中翻火焰，<br />
                为问何时猜得？<br />
                不过几千寒热。<br />
                人世难逢开口笑，<br />
                上疆场彼此弯弓月。<br />
                流遍了，<br />
                郊原血。<br />
                一篇读罢头飞雪，<br />
                但记得斑斑点点，<br />
                几行陈迹。<br />
                五帝三皇神圣事，<br />
                骗了无涯过客。<br />
                有多少风流人物？<br />
                盗跖庄蹻流誉后，<br />
                更陈王奋起挥黄钺。<br />
                歌未竟，<br />
                东方白。
              </span>
            </div>
          </el-col>
        </el-row>
      </el-main>
    </el-container>
  </div>
</template>

<script>
export default {
  data () {
    return {
      speed: 0,
      year: '2023',
      month: 'May',
      day: '10',
      list: [],
      src: 'https://cube.elemecdn.com/6/94/4d3ea53c084bad6931a56d5158a48jpeg.jpeg',
      motto: '',
      title: '',
      description: '',
      blogId: 0
    }
  },
  mounted () {
    this.getDate()
    this.homeData()
  },
  methods: {
    getDate () {
      let date = new Date()
      this.year = date.getFullYear()
      this.day = date.getDate()
      this.month = date.toDateString().split(' ')[1]
    },
    homeData () {
      this.$http.get(this.baseUrl + '/home').then((res) => {
        this.motto = res.data.data.motto.motto
        this.src = res.data.data.motto.img
        this.title = res.data.data.blog.title
        this.description = res.data.data.blog.description
        this.blogId = res.data.data.blog.blog_id
      }, (res) =>
        console.log(res)
      )
    },
    blogDetail () {
      if (this.blogId === 0) {
        return 0
      }
      this.$router.push({name: 'blog-detail', params: {blog_id: this.blogId}})
    }
  }
}

</script>
<style scoped>

.home-style {
  background-color: #ffffff;
  background-color: rgba(256, 256, 256, 0.2);
}
.home-date {
  width: 200px;
  height: 200px;
  background-color: #ff9234;
}
.motto-word-board {
  background: #f2f2f2;
  margin-top:10px;
  padding: 20px 0 20px;
  width:100%
}
.motto-font {
  height: 120px;
  font-family: 楷体;
  font-size:15px;
  padding:10px 5%;
  vertical-align: middle;
  width: 100%;
  text-align:left
}
.total-box {
  float:left;
  margin-left: 30px;
  background-color: rgba(256, 256, 256, 0.2);
  width: 830px;
}
.motto-box {
  float:left;
  width: 600px
}
.date-box {
  float: left;
  margin-left: 30px
}
.date-month {
  font-weight: 900;
  font-size: 40px;
  color: #ff6500;
}
.month-style {
  float: left;
  margin-top: 40px;
  margin-left: 30px
}
.date-day {
  font-weight: 900;
  font-size: 25px;
  color: #fff54c;
}
.day-style {
  float: left;
  margin-top: 55px;
  margin-left:10px
}
.date-year {
  font-weight: 900;
  font-size: 25px;
  color: #fff54c;
}
.year-style {
  clear: both;
}
.home-carousel {
  width: 100%;
  height: 600px;
}
#catIframe {
  position: fixed;
  width: 400px;
  /* padding: 50px 0px; */
  bottom: 10%;
  right: 5%;
}
  .el-row {
    margin-bottom: 20px;
    &:last-child

  {
    margin-bottom: 0;
  }

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
</style>
