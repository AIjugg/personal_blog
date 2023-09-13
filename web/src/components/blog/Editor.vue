<template>
  <div style="min-height: 600px">
    <Content>
      <Card>
        <div>
          <div>
            <el-row>
              <el-col :span="2"><div><h2>标题</h2></div></el-col>
              <el-col :span="10">
                <div class="title-input-style">
                  <el-input type="text" v-model="title" maxlength="50" show-word-limit placeholder="请输入标题"></el-input>
                </div>
              </el-col>
              <el-col :span="4"><div>
              <el-switch v-model="topSwitch" @change="changeTop" active-text="置顶" inactive-text="不置顶"></el-switch>
            </div></el-col>
              <el-col :span="4"><div>
              <el-switch v-model="stateSwitch" @change="changeState" active-text="展示" inactive-text="隐藏"></el-switch>
            </div></el-col>
            <el-col :span="4"><div>
              <div><el-button type="primary" @click="save">保存</el-button></div>
            </div></el-col>
            </el-row>
          </div>
          <div class="description-style">
            <el-row>
              <el-col :span="2">
                <div>
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
              </el-col>
              <el-col :span="2">
                <div class="description-title">
                  <h3>文章简介</h3>
                </div>
              </el-col>
              <el-col :span="14">
                <div class="description-input">
                  <el-input v-model="description" maxlength="200" show-word-limit type="textarea" :rows="6" placeholder="文章简介"/>
                </div>
              </el-col>
              <el-col :span="6">
                <div>
                  <div>
                    <h3>分类管理</h3>
                  </div>
                  <el-row>
                    <div style="padding-top: 10px;">
                      <div style="float: left;">
                          <el-select v-model="newTypeId" placeholder="添加分类" size="mini">
                          <el-option
                            v-for="type in showTypeLists"
                            :key="type.type_id"
                            :label="type.type_name"
                            :value="type.type_id">{{ type.type_name }}
                          </el-option>
                        </el-select>
                      </div>
                      <div>
                        <el-button type="primary" plain size="mini" @click="addType">新增</el-button>
                      </div>
                    </div>
                    <div style="padding-top: 10px;">
                      <div style="float: left;">
                        <el-tag
                          v-for="type in types"
                          :key="type.name"
                          closable
                          type="warning"
                          @close="removeType(type)">
                          {{type.type_name}}
                        </el-tag>
                      </div>
                    </div>
                  </el-row>
                </div>
              </el-col>
            </el-row>
          </div>
        </div>
        <Modal :loading=true v-model="tipShow">
          <p>{{ tipContent }}</p>
        </Modal>
        <div class="edit-container">
          <div>
            <el-upload
              v-show="false"
              class="avatar-uploader"
              :data='fileUpload'
              :show-file-list="false"
              :http-request="onUploadHandler"
            >
            </el-upload>
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
      blogId: this.$route.params.blog_id ? this.$route.params.blog_id : 0,
      state: 1,
      stateSwitch: true, // true 隐藏  false 正常
      top: 1,
      topSwitch: false, // true 置顶  false 正常
      title: '',
      description: '',
      content: `<p></p>`,
      draft: '',
      timer: '',
      image: '',
      oriImage: '',
      draftId: 0,
      newTypeId: '',
      tipShow: false,
      tipContent: '',
      types: [],
      type_ids: [],
      typeLists: [],
      showTypeLists: [],
      fileType: {
        accept: 'image/jpeg, image/png, image/jpg'
      },
      editorOption: {
        theme: 'snow',
        modules: {
          clipboard: {
            matchVisual: false
          },
          toolbar: {
            container: [
              ['bold', 'italic', 'underline', 'strike'], // 加粗，斜体，下划线，删除线
              ['blockquote', 'code-block'], // 引用，代码块
              [{ 'header': 1 }, { 'header': 2 }, { 'header': 3 }, { 'header': 4 }], // 标题，键值对的形式；1、2表示字体大小
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
              ['image', 'link']
            ],
            handlers: {
              'image': function (value) {
                if (value) {
                  document.querySelector('.avatar-uploader input').click()
                } else {
                  this.quill.format('image', false)
                }
              }
            }
          }
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
    // 加载博客还是草稿
    if (this.$route.params.type === 'blog') {
      this.blogDetail()
    } else if (this.$route.params.type === 'draft') {
      this.blogDraft()
    }

    // this.timer = setInterval(this.editDraft, 300000)
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
      this.$http.post(this.baseUrl + '/blog/edit-blog', {blog_id: this.blogId, title: this.title, description: this.description, image: this.image, state: this.state, content: this.content, top: this.top, type_ids: this.type_ids}, {
        emulateJSON: true
      }).then((response) => {
        if (response.data.code !== 0) {
          tipWarning(_self, response.data.code, response.data.msg)
        } else {
          // this.delDraft()
          tipWarning(_self, '-1', '编辑成功！')
          this.$router.push('/personal/blog-manager')
        }
      }, (response) => {
        console.log(response)
      })
    },
    blogDetail () {
      let _self = this
      if (parseInt(_self.blogId) === 0) {
        this.typeList()
        return 0
      }
      _self.$http.get(this.baseUrl + '/blog/manager-blog-detail', {params: {blog_id: _self.blogId}}).then((res) => {
        if (res.data.code === 0) {
          _self.title = res.data.data.detail.title
          _self.description = res.data.data.detail.description
          _self.image = res.data.data.detail.image
          _self.oriImage = _self.image
          _self.content = res.data.data.detail.content
          _self.top = res.data.data.detail.top
          _self.topSwitch = _self.top === 2
          _self.state = res.data.data.detail.state
          _self.stateSwitch = _self.state === 1
          _self.types = res.data.data.detail.types
          _self.types.forEach(element => {
            _self.type_ids.push(element.type_id)
          })
        } else {
          tipWarning(_self, res.data.code, res.data.msg)
        }
      }, (res) =>
        console.log(res)
      ).then(() => {
        // 获取分类列表
        this.typeList()
      })
    },
    // 草稿功能后面改
    blogDraft (callback) {
      let _self = this
      if (parseInt(this.blogId) === 0) {
        return 0
      }
      this.$http.get(this.baseUrl + '/blog/draft-detail', {params: {blogId: this.blogId}}).then((res) => {
        if (res.data.code === 0) {
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
      ).then(() => {
        // 获取分类列表
        this.typeList()
      })
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
      this.state = this.stateSwitch === true ? 1 : 2
    },
    changeTop () {
      this.top = this.topSwitch === true ? 2 : 1
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
      // console.log(this.image)

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
      this.$http.post(this.baseUrl + '/index/upload-img-base64', {image: this.image, type: 'cover'}, {
        emulateJSON: true
      }).then((response) => {
        if (response.data.code === 0) {
          this.image = response.data.data.imgPath
          this.oriImage = this.image
          this.editBlog()
        } else {
          console.log(response.data)
          tipWarning(_self, response.data.code, response.data.msg)
        }
      }, (response) => {
        console.log(response)
      })
    },
    removeType (type) {
      this.types.splice(this.types.indexOf(type), 1)
      this.type_ids.splice(this.type_ids.indexOf(type.type_id), 1)
    },
    typeList () {
      this.$http.get(this.baseUrl + '/blog/blog-type-list').then((res) => {
        if (res.data.code === 0) {
          if (res.data.data.list.length === 0) {
            return false
          }
          this.typeLists = res.data.data.list
          this.showTypeList()
        } else {
          console.log(res.data)
        }
      }, (res) =>
        console.log(res)
      )
    },
    // 展示分类列表，需要过滤掉已选择的分类
    showTypeList () {
      this.showTypeLists = this.typeLists
      let showTypeIds = []
      this.showTypeLists.forEach(type => {
        showTypeIds.push(type.type_id)
      })
      this.type_ids.forEach(typeId => {
        this.showTypeLists.splice(showTypeIds.indexOf(typeId), 1)
      })
    },
    addType () {
      this.type_ids.push(this.newTypeId)
      this.typeLists.forEach(type => {
        if (type.type_id === this.newTypeId) {
          this.types.push(type)
        }
      })
      this.showTypeList()
      this.newTypeId = ''
    },
    uploadBlogImg (img, imgType) {
      let _self = this
      const formData = new FormData()
      formData.append('file', img)
      formData.append('type', imgType)
      this.$http.post(this.baseUrl + '/index/upload-img', formData).then((response) => {
        if (response.data.code !== 0) {
          console.log(response.data)
          tipWarning(_self, response.data.code, response.data.msg)
        } else {
          let imageUrl = response.data.data.path
          // 获取光标所在位置
          let quill = this.$refs.myQuillEditor.quill
          let length = quill.getSelection().index
          // 插入图片
          quill.insertEmbed(length, 'image', imageUrl)
          // 调整光标到最后
          quill.setSelection(length + 1)
          // this.content += url
          return response.data.data.path
        }
      }, (response) => {
        console.log(response)
      })
    },
    async onUploadHandler (e) {
      this.uploadBlogImg(e.file, 'blog')
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
.title-input-style {
  width: 100%;
}
.description-style {
  padding-top: 20px;
}
.description-input {
  float: left;
  padding-left: 20px;
  padding-right: 10px;
  width: 100%;
}
.description-title {
  float: right;
}
.header-img-style {
  width:100px;
  height:100px;
}
.grid-content {
  border-radius: 4px;
  min-height: 36px;
}
.el-tag + .el-tag {
    margin-left: 10px;
  }
.avatar-uploader {
  width: 100%;
}
</style>
