<template lang="pug">
.center
  .copyRight.my-16 &copy; 2019 - {{ new Date().getFullYear() }} ratech &amp; エノキ電気
.hitsuyou-mudana-space
</template>

<script>
import mixins from '~/mixins/mixins'
export default {
  mixins: [mixins],
}
</script>

<style lang="scss" scoped>
.hitsuyou-mudana-space {
  height: 8em;
  width: 100%;
}
</style>
