<template lang="pug">
.index-page
  commonMainContent(allHistory)
</template>

<script>
/**
 * ページ推移ごとにmountedを実行する必要があるため、どのviewsでも読み込むこと
 */
import mixins from '~/mixins/mixins'
import webpush from '~/js/webpush'
import metaFunctions from '~/js/metaFunctions'
import Setup from '~/js/setup'
import commonMainContent from '~/components/common/commonMainContent.vue'

export default {
  name: 'history',
  mixins: [mixins],
  components: {
    commonMainContent,
  },
  setup() {
    //サーバーサイドで仮のタイトルを設定、mountedで言語ごとに再設定する
    Setup.setTitle('History')
    Setup.setDescription('報酬の履歴を確認')
  },
  data() {
    return {}
  },
  async mounted() {
    this.setTitle('報酬履歴')
  },
  methods: {},
  watch: {},
}
</script>

<style lang="scss" scoped></style>
