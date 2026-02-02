<template lang="pug">
.about
  h1 燃費計算！
  p 開発中
</template>

<script>
import metaFunctions from '~/js/metaFunctions'
import mixins from '~/mixins/mixins'
import Setup from '~/js/setup'

export default {
  mixins: [mixins],
  setup() {
    //サーバーサイドで仮のタイトルを設定、mountedで言語ごとに再設定する
    Setup.setTitle('Fuel')
    Setup.setDescription('燃費計算')
  },
  mounted() {
    this.setTitle('燃費')
  },
}
</script>
