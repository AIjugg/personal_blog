<template>
  <Content class="card-style">
    <Card style="background-color:rgba(0,0,0,0)">
      <div class="home-style">
        <div class="total-box">
          <div class="motto-box">
            <Carousel autoplay v-model="speed" loop autoplay-speed="4000">
              <CarouselItem v-for="item in list" :key="item.id">
                <div class="home-carousel">
                  <img :src="item.img" height="400" width="600">
                  <div class="motto-word-board">
                    <div class="motto-font">
                        {{ item.motto }}
                    </div>
                  </div>
                </div>
              </CarouselItem>
            </Carousel>
          </div>
          <div class="date-box">
            <div class="home-date">
              <div class="month-style">
                <span class="date-month">{{ month }}</span>
              </div>
              <div class="day-style">
                <span class="date-day">{{ day }}</span>
              </div>
              <div class="year-style">
                <span class="date-year">{{ year }}</span>
              </div>
            </div>
          </div>
        </div>
        <div style="clear: both"></div>
        <iframe src="https://zhanyuzhang.github.io/lovely-cat/cat.html" frameborder="0" id="catIframe"></iframe>
      </div>
      <div id="jsi-flying-fish-container"></div>
    </Card>
  </Content>
</template>


<script>
export default {
  data () {
    return {
      speed: 0,
      year: '2020',
      month: 'May',
      day: '10',
      list: []
    }
  },
  mounted () {
    this.getDate()
    this.getMotto()
  },
  methods: {
    getDate () {
      let date = new Date()
      this.year = date.getFullYear()
      this.day = date.getDate()
      this.month = date.toDateString().split(' ')[1]
    },
    getMotto () {
      this.$http.get(this.baseUrl + '/index/motto').then((res) => {
        this.list = res.data.data.list
      }, (res) =>
        console.log(res)
      )
    }
  }
}
import '../plugin/fish'
</script>
<style scoped>
.card-style {
  margin: 0 auto;
  width:80%;
  min-width: 900px;
  background-color:rgba(0,0,0,0);
}
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
#jsi-flying-fish-container {
  width: 100%;
  height: 200px;
  z-index: 1;
  bottom: 145px;
  left: 0;
}
</style>
