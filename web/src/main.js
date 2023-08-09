// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import router from './router'
// import ViewUI from 'iview'
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
import App from './App'
// import 'view-design/dist/styles/iview.css'
import VueResource from 'vue-resource'
import VueCookies from 'vue-cookies'
import md5 from 'js-md5'
import VueQuillEditor from 'vue-quill-editor'
// import 'quill/dist/quill.core.css'
// import 'quill/dist/quill.snow.css'
// import 'quill/dist/quill.bubble.css'
import globalApi from './plugin/globleApi'

Vue.config.productionTip = false
Vue.use(VueResource)
// Vue.use(ViewUI)
Vue.use(ElementUI)
Vue.use(VueCookies)
Vue.use(VueQuillEditor)
Vue.prototype.$md5 = md5
Vue.prototype.baseUrl = globalApi.baseURL

// 统一处理登录token
Vue.http.interceptors.push((request, next) => {
  request.headers.set('Authorization', localStorage.getItem('userToken'))
  next((response) => {
    if (response.data.status === 200) {
      if (response.data.data.code !== '0') {
        switch (response.data.data.code) {
          case '2008':
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
