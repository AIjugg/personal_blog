/**
 * 点赞
 */
import {tipWarning} from '@/plugin/login.js'
export function like (_self, type, id) {
  let action = 2
  if (_self.likeState === '1') {
    action = 1
  }
  _self.$http.post(_self.baseUrl + '/communicate/like-edit', {sourceId: id, sourceType: type, action: action}, {
    emulateJSON: true
  }).then((res) => {
    if (res.data.code !== '0') {
      tipWarning(_self, res.data.code, res.data.msg)
    }
  }, (res) =>
    console.log(res)
  )
}

export function commentLike (_self, type, id, index) {
  let action = 2
  if (_self.list[index].likeState === true) {
    action = 1
  }
  _self.$http.post(_self.baseUrl + '/appreciate/edit-like', {sourceId: id, sourceType: type, action: action, id: _self.list[index].likeId}, {
    emulateJSON: true
  }).then((res) => {
    if (res.data.code === '0') {
      _self.list[index].likeId = res.data.data.id
    } else {
      tipWarning(_self, res.data.code, res.data.msg)
    }
  }, (res) =>
    console.log(res)
  )
}
