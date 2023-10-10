import Test from '@/components/Test'
import Home from '@/components/Home'
import Message from '@/components/Message'
import Index from '@/components/Index'
import Blog from '@/components/Blog'
import LoginIndex from '@/components/login/LoginIndex'
import Login from '@/components/login/Login'
import Register from '@/components/login/Register'
import Forget from '@/components/login/Forget'
import Personal from '@/components/personal/Personal'
import Editor from '@/components/blog/Editor'
import Detail from '@/components/blog/Detail'
import Profile from '@/components/personal/Profile'
import blogManager from '@/components/personal/BlogManager'
import type from '@/components/personal/Type'
import userInfo from '@/components/personal/UserInfo'
import statistic from '@/components/personal/Statistic'
const Router = require('vue-router')

let myrouter = new Router({
  routes: [
    {
      path: '/',
      redirect: 'home',
      component: Index,
      children: [
        {path: 'home', name: 'home', component: Home},
        {path: 'message', name: 'message', component: Message},
        {path: 'blog', name: 'blog', component: Blog},
        {path: 'blog-edit', name: 'editor', component: Editor, meta: {auth: true}},
        {path: 'blog-edit/:id', name: 'blog-edit', component: Editor, meta: {auth: true}},
        {path: 'blog-detail/:id', name: 'blog-detail', component: Detail, meta: {isLogin: false}}
      ]
    },
    {
      path: '/test',
      name: 'Test',
      component: Test
    },
    {
      path: '/login',
      redirect: '/login/login',
      name: 'loginIndex',
      component: LoginIndex,
      children: [
        {path: 'login', name: 'login', component: Login},
        {path: 'register', name: 'register', component: Register}
      ]
    },
    {
      path: '/forget',
      name: 'Forget',
      component: Forget
    },
    {
      path: '/personal',
      name: 'personal',
      redirect: '/personal/profile',
      component: Personal,
      children: [
        {path: 'profile', name: 'profile', component: Profile},
        {path: 'blog-manager', name: 'blog-manager', component: blogManager},
        {path: 'type', name: 'type', component: type},
        {path: 'userinfo', name: 'userinfo', component: userInfo},
        {path: 'statistic', name: 'statistic', component: statistic}
      ]
    }
  ]
})
/*
// 可以利用vue-router的路由拦截，达到登录控制
const whiteList = ['/home', '/message', '/login/login', '/login/register', '/forget', '/blog']
myrouter.beforeEach((to, from, next) => {
  let isLogin = localStorage.getItem('isLogin')
  if (isLogin) {
    if (to.path === '/login/login' || to.path === '/login/register') {
      next({path: '/home'})
    } else {
      next()
    }
  } else {
    // 博客详情的网页只能通过meta标签来判断不需要登录
    if (to.meta.isLogin === false) {
      next()
    } else {
      if (whiteList.indexOf(to.path) !== -1) {
        next()
      } else {
        next({path: '/login/login'})
      }
    }
  }
})
*/
export default myrouter
