<template>
  <Content class="profile-card">
    <Card>
      <div class="info-gap">
        <div class="header-style">
          <div>
            <img :src="imgUrl(profilePhoto)" class="header-img-style"/>
          </div>
          <br>
          <div>
            <a href="javascript:" class="file">修改头像
              <input @change="chooseImg" type="file" accept="image/png, image/jpeg" class="file">
            </a>
          </div>
        </div>
        <div class="user-info">
          <List item-layout="vertical">
            <div class="item-style">
              <div class="item-title">
                <p>昵称</p>
              </div>
              <div class="item-input">
                <Input v-model="nickname" size="large" placeholder="快取个昵称" style="width: 400px" show-word-limit maxlength="20">
                <Icon type="ios-contact" slot="prefix" />
                </Input>
              </div>
            </div>
            <div class="item-style">
              <div class="item-title">
                <p>个签</p>
              </div>
              <div class="item-input">
                <Input v-model="signature" size="large" maxlength="100" show-word-limit placeholder="个性签名……" style="width: 400px" type="textarea" >
                <Icon type="md-chatboxes" slot="prefix" />
                </Input>
              </div>
            </div>
            <div class="item-style">
              <div class="item-title">
                <p>性别</p>
              </div>
              <div class="item-input">
                <Select v-model="sex" size="small" style="width:80px">
                  <Option v-for="item in sexList" :value="item.value" :key="item.value">{{ item.label }}</Option>
                </Select>
              </div>
            </div>
            <div class="item-style">
              <div class="item-title">
                <p>生日</p>
              </div>
              <div class="item-input">
                <DatePicker type="date" :value="birthday" placeholder="生日" format="yyyy-MM-dd" style="width: 150px" @on-change="birthday=$event"></DatePicker>
              </div>
            </div>
          </List>
        </div>
        <Modal :loading=true v-model="tipShow">
          <p>{{ tipContent }}</p>
        </Modal>
        <div class="save-btn">
          <Button type="primary" @click="save">保存</Button>
        </div>
      </div>
    </Card>
  </Content>
</template>

<script>
import {tipWarning} from '@/plugin/login.js'
export default {
  data () {
    return {
      nickname: '',
      profilePhoto: '',
      oriProfilePhoto: '',
      sex: '',
      birthday: '1970-01-01',
      signature: '',
      sexList: [
        {
          label: '男',
          value: 1
        },
        {
          label: '女',
          value: 2
        },
        {
          label: '未知',
          value: 3
        }
      ],
      fileType: {
        accept: 'image/jpeg, image/png, image/jpg'
      },
      tipShow: false,
      tipContent: ''
    }
  },
  mounted () {
    this.userInfo()
  },
  methods: {
    imgUrl (url) {
      if (url === '') {
        return '/static/setting/defaultheadimg.jpg'
      }
      if (url.indexOf('data:image/') === -1) {
        return this.baseUrl + '/' + url
      }
      return url
    },
    userInfo () {
      this.$http.get(this.baseUrl + '/user/get-userinfo').then((res) => {
        if (res.data.code === 0) {
          this.nickname = res.data.data.userinfo.nickname
          this.oriProfilePhoto = res.data.data.userinfo.profile_photo
          this.profilePhoto = this.oriProfilePhoto
          this.sex = res.data.data.userinfo.sex
          this.birthday = res.data.data.userinfo.birthday === '0000-00-00' ? '1970-01-01' : res.data.data.userinfo.birthday
          this.signature = res.data.data.userinfo.signature
        } else {
          let _self = this
          tipWarning(_self, res.data.code, res.data.msg)
        }
      }, (res) =>
        console.log(res)
      )
    },
    chooseImg (event) {
      let _self = this
      let file = event.target.files[0]
      let type = file.type
      let size = file.size
      if (this.fileType.accept.indexOf(type) === -1) {
        alert('请选择jpg或png图片')
        return false
      }
      // 2MB
      if (size > 2097152) {
        alert('图片过大')
        return false
      }
      let reader = new FileReader()
      reader.readAsDataURL(file)
      reader.onload = () => {
        // console.log(reader.result)
        let img = new Image()
        img.src = reader.result
        img.onload = function () {
          _self.compress(img, 0.75, 100, 100)
        }
      }
      reader.onerror = function (error) {
        console.log(error)
        return 0
      }
    },
    compress (img, size, w, h) {
      let canvas = document.createElement('canvas')
      let ctx = canvas.getContext('2d')
      var anw = document.createAttribute('width')
      anw.nodeValue = w
      var anh = document.createAttribute('height')
      anh.nodeValue = h
      canvas.setAttributeNode(anw)
      canvas.setAttributeNode(anh)

      ctx.fillRect(0, 0, w, h)
      ctx.drawImage(img, 0, 0, w, h)

      // 预览压缩后的图片
      let base64 = canvas.toDataURL('image/jpeg', size) // 压缩后质量
      this.profilePhoto = base64

      // let bytes = window.atob(base64.split(',')[1]);
      // let ab = new ArrayBuffer(bytes.length);
      // let ia = new Uint8Array(ab);
      // for (let i = 0; i < bytes.length; i++) {
      //   ia[i] = bytes.charCodeAt(i);
      // }
      // let blob = new Blob([ab], {type: 'image/jpeg'});
      //
      // console.log('压缩后的图片大小', blob.size);
    },
    save () {
      if (this.oriProfilePhoto !== this.profilePhoto) {
        this.uploadImg()
      } else {
        this.editUserInfo()
      }
    },
    uploadImg () {
      let _self = this
      this.$http.post(this.baseUrl + '/index/upload-img', {image: this.profilePhoto, type: 'profilePhoto'}, {
        emulateJSON: true
      }).then((result) => {
        if (result.data.code === 0) {
          this.profilePhoto = result.data.data.imgPath
          this.editUserInfo()
        } else {
          console.log(result.data)
          tipWarning(_self, result.data.code, result.data.msg)
        }
      }, (result) => {
        console.log(result)
      })
    },
    editUserInfo () {
      let _self = this
      this.$http.post(this.baseUrl + '/user/edit-userinfo', {
        nickname: this.nickname,
        profile_photo: this.profilePhoto,
        sex: this.sex,
        birthday: this.birthday,
        signature: this.signature
      }, {
        emulateJSON: true
      }).then((result) => {
        if (result.data.code === 0) {
          this.oriProfilePhoto = this.profilePhoto
          localStorage.setItem('nickname', this.nickname)
          localStorage.setItem('profilePhoto', this.profilePhoto)
          tipWarning(_self, '-1', '修改成功！')
        } else {
          tipWarning(_self, result.data.code, result.data.msg)
          console.log(result.data)
        }
      }, (result) => {
        console.log(result)
      })
    }
  }
}
</script>

<style scoped>
@import "../../assets/css/button.css";
.profile-card {
  padding: 10px;
}
.info-gap {
  min-height: 200px;
  padding-top: 30px;
}
.header-style {
  float: left;
  padding-left: 10px;
  padding-right: 50px;
}
.header-img-style {
  width:100px;
  height:100px;
  border-radius:50%;
}
.user-info {
  float: left;
}
.save-btn {
  padding-top: 30px;
  clear: both;
}
.item-style {
  clear: both;
  padding-top: 10px;
}
.item-title {
  float: left;
  padding-top: 10px;
}
.item-input {
  float: left;
  padding-left: 10px;
}
.demo-split{
  height: 200px;
  border: 1px solid #dcdee2;
}
.demo-split-pane{
  padding: 10px;
}
</style>
