<template lang="pug">
ContentLoader.loading-text(
  :width="width"
  primaryColor="#777777"
  secondaryColor="#bbbbbb"
  :primaryOpacity="Number('0.3')"
  :secondaryOpacity="Number('0.4')"
  :speed="Number('3')"
  )
</template>

<script>
import { ContentLoader } from 'vue-content-loader'
export default {
  props: {
    width: {
      type: String,
      require: false,
      default: '10em',
    },
  },
  components: {
    ContentLoader,
  },
}
</script>

<style lang="scss" scoped>
.loading-text {
  border-radius: 0.3em;
  max-width: 100%;
  display: block;
  opacity: 0.4;
}
</style>
