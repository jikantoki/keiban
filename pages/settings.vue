<template lang="pug">
.about
  h1 設定…やってっちゃう？w
  adsense
</template>

<script>
import metaFunctions from '~/js/metaFunctions'
import mixins from '~/mixins/mixins'
import Setup from '~/js/setup'

export default {
  mixins: [mixins],
  setup() {
    //サーバーサイドで仮のタイトルを設定、mountedで言語ごとに再設定する
    Setup.setTitle('Settings')
    Setup.setDescription('設定')
  },
  mounted() {
    this.setTitle('Settings')
  },
}
</script>
