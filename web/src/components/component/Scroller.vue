<template>
  <div class="wrap">
    <div id="megaphone">
      <Icon type="md-megaphone" color="gray" size="20"/>
    </div>
    <div id="box">
      <div id="marquee">{{text}}</div>
    </div>
    <div id="node">{{text}}</div>
  </div>
</template>

<script type="text/ecmascript-6">
export default {
  name: 'Scroller',
  props: ['lists'], // 父组件传入数据， 数组形式 [ "连雨不知春去"，"一晴方觉夏深"]
  data () {
    return {
      text: '', // 数组文字转化后的字符串
      list: ['欢迎访问长夜个人博客！！！'] // 默认的滚动文字
    }
  },
  methods: {
    move () {
      // 获取文字text 的计算后宽度  （由于overflow的存在，直接获取不到，需要独立的node计算）
      let boxWidth = document.getElementById('box').getBoundingClientRect().width // box的宽度
      let nodeWidth = document.getElementById('node').getBoundingClientRect().width // 文字的宽度
      let box = document.getElementById('box')
      let distance = 0 // 初始化位置
      // 定时器设置位移
      setInterval(function () {
        distance = distance + 1
        // 文字从左到右滚动播放
        if (distance >= boxWidth) {
          distance = -nodeWidth
        }
        box.style.transform = 'translateX(' + distance + 'px)' // 沿x轴运动
      }, 40)
    }
  },
  // 把父组件传入的arr转化成字符串
  mounted: function () {
    this.list = this.lists
    for (let i = 0; i < this.list.length; i++) {
      this.text += '  ' + this.list[i]
    }
  },
  // 更新的时候运动
  updated: function () {
    this.move()
  }
}
</script>
<style scoped>
/*样式的话可以写*/
.wrap {
  overflow: hidden;
  color: #00a2ff;
}
#megaphone {
  float:left;
}
#box {
  /*width: 100%;*/
  width: 600px;
  float: left;
  height: 100%;
  white-space: nowrap;
}
#box div {
  float: left;
}
#marquee {
  margin: 0 16px 0 20px;
  font-size: 13px;
}
#node {
  display: none;
  position: absolute;
  z-index: -99;
  top: -99px;
}
</style>
