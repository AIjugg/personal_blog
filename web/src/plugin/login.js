/**
 * 获取登录token
 */
export function loginByAccount(_self) {
  _self.$http.post(_self.baseUrl + '/user/login-token', { username: _self.username, password: _self.password}, {
    emulateJSON: true
  }).then((result) => {
    if (result.data.status.code === 0) {
      console.log(result.data.data)
      localStorage.setItem('isLogin', 1)
      localStorage.setItem('accessToken', result.data.data.access_token)
      localStorage.removeItem('nickname')
      localStorage.removeItem('profilePhoto')
      _self.$router.push('/home')
    } else {
      console.log(result.data)
      _self.error = result.data.status.msg
    }
  }, (result) => {
    _self.error = '登录失败'
    console.log('login error')
    console.log(result)
  })
}

export function tipWarning (_self, code, msg) {
  if (code === '4001') {
    _self.tipContent = '您尚未登录，2秒后跳转登录页面'
  } else {
    _self.tipContent = msg
  }
  _self.tipShow = true
  switch (code) {
    case ('4001'): {
      setTimeout(() => {
        localStorage.removeItem('isLogin')
        _self.$router.push('/login/login')
      }, 2000)
      break
    }
    case ('2202'): {
      setTimeout(() => {
        _self.$router.push('/blog')
      }, 2000)
      break
    }
    case ('-1'): {
      setTimeout(() => {
        _self.tipShow = false
      }, 1000)
      break
    }
    default: {
      setTimeout(() => {
        _self.tipShow = false
      }, 2000)
    }
  }
}

export function userInfo (_self, callback) {
  let islogin = localStorage.getItem('isLogin')
  if (islogin) {
    if (!localStorage.getItem('profilePhoto')) {
      _self.$http.get(_self.baseUrl + '/user/get-userinfo').then((res) => {
        if (res.data.status.code === 0) {
          localStorage.setItem('nickname', res.data.data.userinfo.nickname)
          localStorage.setItem('profilePhoto', res.data.data.userinfo.profile_photo)
          callback(_self)
        } else {
          localStorage.removeItem('isLogin')
          localStorage.removeItem('nickname')
          localStorage.removeItem('profilePhoto')
          console.log(res.data)
        }
      }, (res) =>
        console.log(res)
      )
    }
  }
}
