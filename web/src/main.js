// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
// import Vue from 'vue'
import router from './router'
import App from './App'
// import 'view-design/dist/styles/iview.css'
// import VueQuillEditor from 'vue-quill-editor'
// import 'quill/dist/quill.core.css'
// import 'quill/dist/quill.snow.css'
// import 'quill/dist/quill.bubble.css'
import globalApi from './plugin/globleApi'
import hljs from 'highlight.js'
// 使用样式，有多种样式可选
import 'highlight.js/styles/agate.css'

const Vue = require('vue')
const ElementUI = require('element-ui')
const ViewUI = require('iview')
const VueResource = require('vue-resource')
const VueQuillEditor = require('vue-quill-editor')
// const hljs = require('highlight.js')

// Vue.use(ElementUI)
// Vue.use(VueResource)
// Vue.use(VueQuillEditor)
// Vue.use(ViewUI)

Vue.config.productionTip = false

Vue.prototype.baseUrl = globalApi.baseURL
Vue.directive('highlight', function (el) {
  let blocks = el.querySelectorAll('.ql-syntax')
  blocks.forEach(block => {
    hljs.highlightBlock(block)
  })
})
hljs.configure({ // optionally configure hljs
  languages: ['javascript']
})
// 增加组定义属性，用于在代码中预处理代码格式
Vue.prototype.hljs = hljs

// 统一处理登录token
Vue.http.interceptors.push((request, next) => {
  let accessToken = localStorage.getItem('accessToken')
  request.headers.set('Authorization', 'Bearer ' + accessToken)
  next((response) => {
    if (response.status === 200) {
      if (response.data.code !== 0) {
        switch (response.data.code) {
          case 13008:
          {
            router.push('/login/login')
            break
          }
        }
      }
    }
    return response
  })
})
/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  components: { App },
  template: '<App/>'
})
