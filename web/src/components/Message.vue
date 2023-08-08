<template>
  <div class="message-style">
    <div>
      <comment sourceType="message" sourceId="-1" typename="留言板"></comment>
    </div>
  </div>
</template>

<script>
import Comment from '@/components/component/Comment'
export default {
  inject: ['reload'],
  components: {
    Comment
  },
  data () {
    return {

    }
  },
  mounted: function () {

  },
  methods: {
    imgUrl (url) {
      return this.baseUrl + '/' + url
    }
  }
}
</script>
<style scoped>
a{
  color: #1f1f1f;
}
.message-style {
  margin: 1% 10%
}
</style>
