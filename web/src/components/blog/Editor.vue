<template>
  <div style="min-height: 600px">
    <Content :style="{margin: '1% 5%'}">
      <Card>
        <div>
          <div>
            <div class="title-style">
              <h3>标题</h3>
            </div>
            <div class="title-input-style">
              <Input v-model="title" maxlength="30" show-word-limit placeholder="输入标题"/>
            </div>
            <div style="float:left;margin-left: 10px">
              <i-switch v-model="topSwitch" @on-change="changeTop" size="large">
                <span slot="open">置顶</span>
                <span slot="close">正常</span>
              </i-switch>
            </div>
            <div style="float:left;margin-left: 10px">
              <i-switch v-model="stateSwitch" @on-change="changeState" size="large">
                <span slot="open">隐藏</span>
                <span slot="close">正常</span>
              </i-switch>
            </div>
            <div style="float:right">
              <Button type="primary" @click="save">保存</Button>
            </div>
          </div>
          <div class="description-style">
            <div class="header-style">
              <div>
                <img :src="imgUrl(image)" class="header-img-style"/>
              </div>
              <br>
              <div>
                <a href="javascript:" class="file">修改封面
                  <input @change="chooseImg" type="file" accept="image/png, image/jpeg" class="file">
                </a>
              </div>
            </div>
            <div class="description-title">
              <h3>文章简介</h3>
            </div>
            <div class="description-input">
              <Input v-model="description" maxlength="200" show-word-limit type="textarea" placeholder="文章简介"/>
            </div>
          </div>
        </div>
        <Modal :loading=true v-model="tipShow">
          <p>{{ tipContent }}</p>
        </Modal>
        <div class="edit-container">
          <div>
            <quill-editor style="height: 800px" v-model="content" ref="myQuillEditor" :options="editorOption" @blur="onEditorBlur($event)" @focus="onEditorFocus($event)" @change="onEditorChange($event)"></quill-editor>
          </div>
        </div>
      </Card>
    </Content>
  </div>
</template>

<script>
import {tipWarning} from '@/plugin/login.js'
export default {
  name: 'App',
  data () {
    return {
      blogId: this.$route.params.id ? this.$route.params.id : 0,
      state: '1',
      stateSwitch: false, // true 隐藏  false 正常
      top: '1',
      topSwitch: false, // true 置顶  false 正常
      title: '',
      description: '',
      content: `<p></p>`,
      draft: '',
      timer: '',
      image: '',
      oriImage: '',
      draftId: 0,
      tipShow: false,
      tipContent: '',
      fileType: {
        accept: 'image/jpeg, image/png, image/jpg'
      },
      editorOption: {
        theme: 'snow',
        modules: {
          clipboard: {
            matchVisual: false
          },
          toolbar: [
            ['bold', 'italic', 'underline', 'strike'], // 加粗，斜体，下划线，删除线
            ['blockquote', 'code-block'], // 引用，代码块
            [{ 'header': 1 }, { 'header': 2 }], // 标题，键值对的形式；1、2表示字体大小
            [{'list': 'ordered'}, { 'list': 'bullet' }], // 列表
            [{'script': 'sub'}, { 'script': 'super' }], // 上下标
            [{'indent': '-1'}, { 'indent': '+1' }], // 缩进
            // [{ 'direction': 'rtl' }],     文本方向
            [{ 'size': ['small', false, 'large', 'huge'] }], // 字体大小
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }], // 几级标题
            [{ 'color': [] }, { 'background': [] }], // 字体颜色，字体背景颜色
            [{ 'font': [] }], // 字体
            [{ 'align': [] }], // 对齐方式
            ['clean'], // 清除字体样式
            // ['image','video']     上传图片、上传视频
            ['image']
          ]
        }
      }
    }
  },
  computed: {
    editor () {
      return this.$refs.myQuillEditor.quill
    }
  },
  mounted: function () {
    this.blogDraft(this.blogDetail)
    this.timer = setInterval(this.editDraft, 300000)
  },
  beforeDestroy () {
    clearInterval(this.timer)
  },
  methods: {
    onEditorReady (editor) { // 准备编辑器
    },
    onEditorBlur () {}, // 失去焦点事件
    onEditorFocus () {}, // 获得焦点事件
    onEditorChange () {}, // 内容改变事件
    saveHtml: function (event) {
      alert(this.content)
    },
    save () {
      if (this.oriImage !== this.image) {
        this.uploadImg()
      } else {
        this.editBlog()
      }
    },
    editBlog () {
      let _self = this
      this.$http.post(this.baseUrl + '/blog/blog-edit', {id: this.blogId, title: this.title, description: this.description, image: this.image, state: this.state, content: this.content, top: this.top}, {
        emulateJSON: true
      }).then((response) => {
        if (response.data.code !== '0') {
          tipWarning(_self, response.data.code, response.data.msg)
        } else {
          this.oriImage = this.image
          this.delDraft()
          tipWarning(_self, '-1', '编辑成功！')
          this.$router.push('/personal/blog-manager')
        }
      }, (response) => {
        console.log(response)
      })
    },
    blogDetail (_self) {
      if (parseInt(_self.blogId) === 0) {
        return 0
      }
      _self.$http.get(this.baseUrl + '/blog/admin-blog-detail', {params: {blogId: this.blogId}}).then((res) => {
        if (res.data.code === '0') {
          _self.title = res.data.data.detail.title
          _self.description = res.data.data.detail.description
          _self.image = res.data.data.detail.image
          _self.oriImage = _self.image
          _self.content = res.data.data.detail.content
          _self.top = res.data.data.detail.top
          _self.topSwitch = _self.top === '2'
          _self.state = res.data.data.detail.state
          _self.stateSwitch = _self.state === '2'
          if (_self.draft !== '') {
            _self.content = _self.draft
            tipWarning(_self, '-1', '已加载草稿')
          } else {
            _self.draft = _self.content
          }
        } else {
          tipWarning(_self, res.data.code, res.data.msg)
        }
      }, (res) =>
        console.log(res)
      )
    },
    blogDraft (callback) {
      let _self = this
      if (parseInt(this.blogId) === 0) {
        return 0
      }
      this.$http.get(this.baseUrl + '/blog/draft-detail', {params: {blogId: this.blogId}}).then((res) => {
        if (res.data.code === '0') {
          this.draftId = res.data.data.id
          if (this.draftId !== 0) {
            this.draft = res.data.data.draft
          }
        } else {
          console.log(res.data)
        }
        callback(_self)
      }, (res) =>
        console.log(res)
      )
    },
    getItemVal (val) {
      this.typeId = val
    },
    editDraft () {
      if (this.draft !== this.content && this.blogId !== 0) {
        let _self = this
        this.$http.post(this.baseUrl + '/blog/draft-edit', {blogId: this.blogId, draft: this.content, id: this.draftId}, {
          emulateJSON: true
        }).then((response) => {
          if (response.data.code !== '0') {
            tipWarning(_self, response.data.code, response.data.msg)
          } else {
            this.draftId = response.data.data.id
            this.draft = this.content
            tipWarning(_self, '-1', '自动保存草稿成功！')
          }
        }, (response) => {
          console.log(response)
        })
      }
    },
    delDraft () {
      this.$http.post(this.baseUrl + '/blog/draft-del', {id: this.draftId}, {
        emulateJSON: true
      }).then((response) => {
        if (response.data.code !== '0') {
          console.log(response)
        }
      }, (response) => {
        console.log(response)
      })
    },
    changeState () {
      this.state = this.stateSwitch === true ? '2' : '1'
      console.log(this.state)
    },
    changeTop () {
      this.top = this.topSwitch === true ? '2' : '1'
    },
    imgUrl (url) {
      if (url === '') {
        return ''
      }
      if (url.indexOf('data:image/') === -1) {
        return this.baseUrl + '/' + url
      }
      return url
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
          _self.compress(img, 0.9, 200, 200)
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
      this.image = base64
      console.log(this.image)

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
    uploadImg () {
      let _self = this
      this.$http.post(this.baseUrl + '/index/upload-img', {image: this.image, type: 'cover'}, {
        emulateJSON: true
      }).then((result) => {
        if (result.data.code === '0') {
          this.image = result.data.data.imgPath
          this.editBlog()
        } else {
          console.log(result.data)
          tipWarning(_self, result.data.code, result.data.msg)
        }
      }, (result) => {
        console.log(result)
      })
    }
  }
}
</script>

<style>
@import "../../assets/css/button.css";
.edit-container {
  min-height: 850px;
  padding-top: 20px;
  clear: both;
}
.title-style {
  float: left;
}
.title-input-style {
  padding-left: 20px;
  width: 40%;
  float: left;
}
.description-style {
  clear: both;
  padding-top: 20px;
}
.description-input {
  float: left;
  padding-left: 20px;
  width: 70%;
}
.description-title {
  float: left;
}
.header-style {
  float: left;
  padding-left: 10px;
  padding-right: 50px;
}
.header-img-style {
  width:100px;
  height:100px;
}
</style>
