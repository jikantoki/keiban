<template lang="pug">
.index-page
  commonMainContent
  .about-this(v-if="!userStore || !userStore.userId")
    .wrap
      v-card.content(elevation="4")
        .v-content
          h1.text-h4 こんにちは！UberUTLです！
          p UberUTLは、個人事業主のためのユーティリティアプリです。
          p 稼働履歴の登録、分析、ランキング表示などができます。
          hr
        .v-content
          h2.text-h5 何ができる？
          ul
            li 稼働履歴を登録
            li 日付別に経費をメモ
            li 時給の自動計算
            li グラフで実績を確認
            li カレンダーで実績を確認
            li 週、月、年、曜日ごとの統計を表示
          hr
        .v-content
          h2.text-h5 とりあえず使ってみよう！
          a(href="/login")
            v-btn(
              color="var(--accent-color)"
              size="x-large"
              style="border-radius: var(--border-radius);"
            ) ログイン
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
  name: 'index',
  mixins: [mixins],
  components: {
    commonMainContent,
  },
  setup() {
    //サーバーサイドで仮のタイトルを設定、mountedで言語ごとに再設定する
    Setup.setTitle('Top')
    Setup.setDescription('個人事業主のためのユーティリティ')
  },
  data() {
    return {}
  },
  async mounted() {
    this.setTitle(this.$t('index.title'))
  },
  methods: {},
  watch: {},
}
</script>

<style lang="scss" scoped></style>
