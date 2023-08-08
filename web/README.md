# vue-try

> A Vue.js project

## Build Setup

``` bash
# install dependencies
npm install

# serve with hot reload at localhost:8080
npm run dev

# build for production with minification
npm run build

# build for production and view the bundle analyzer report
npm run build --report

# run unit tests
npm run unit

# run e2e tests
npm run e2e

# run all tests
npm test
```

For a detailed explanation on how things work, check out the [guide](http://vuejs-templates.github.io/webpack/) and [docs for vue-loader](http://vuejs.github.io/vue-loader).



网页目录

|-主页

|-博客页





```
<Content style="position: absolute;width: 700px;min-height: 800px;left:50%;margin-left: -350px;top:10%">
  <Card style="min-height: 600px">
    <Menu mode="horizontal" theme="light" active-name="1">
      <Row>
        <Col span="12"><MenuItem name="1" style="left:40%">
          <router-link to="/logina">登录</router-link>
        </MenuItem></Col>
        <Col span="12"><MenuItem name="2" style="left:40%">
          <router-link to="/loginb">注册</router-link>
        </MenuItem></Col>
      </Row>
    </Menu>
    <div>
      <p>hahaha</p>
      <router-view></router-view>
    </div>
  </Card>
</Content>
```



```

```

