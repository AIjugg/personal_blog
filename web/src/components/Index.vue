<template>
  <div>
    <myHeader></myHeader>
    <state></state>
    <router-view></router-view>
    <myFooter></myFooter>
  </div>
</template>

<script>
import myHeader from './component/MyHeader'
import myFooter from './component/MyFooter'
import state from './component/State'
import '../plugin/background'

export default {
  components: {
    myHeader,
    myFooter,
    state
  }
}
</script>
