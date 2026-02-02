<template lang="pug">
.index-page
  .wrap
    v-card.content(elevation="4")
      .text-h3
        span ケイバン！
        //img.ontext(src="/img/icon192.png")
      hr
      img(
        src="/img/thumbnail.png"
        style="border-radius: var(--border-radius);"
        )
      hr
      .text-h6
        span 作成者: エノキ電気 (
        a(
          href="https://enoki.xyz"
          target="_blank"
          style="color: unset;"
          ) ホームページ
        span )
      .text-h6
        span.mr-4 お問い合わせ:
        a(href="mailto:info@enoki.xyz" style="color: unset;") info@enoki.xyz
.index-page
  .wrap
    v-card.content(elevation="4")
      .text-h3
        span プライバシーポリシー
        v-icon(size="x-small" style="vertical-align: baseline;") mdi-security
      hr
      .text-h6 ・パスワードの暗号化
      p ケイバン！では、アカウントのパスワードを暗号化して保存しています。サーバー管理者でもパスワードの閲覧はできないようになっているので、安心してご利用ください。
      hr
      .text-h6 ・データの取り扱い
      p ケイバン！では、ユーザーのデータを第三者に販売・提供することは一切ありません。データの安全性とプライバシーを最優先に考えています。
</template>

<script>
import metaFunctions from '~/js/metaFunctions'
import mixins from '~/mixins/mixins'
import Setup from '~/js/setup'

export default {
  mixins: [mixins],
  setup() {
    //サーバーサイドで仮のタイトルを設定、mountedで言語ごとに再設定する
    Setup.setTitle('About')
    Setup.setDescription('Juice Recordについて')
  },
  mounted() {
    this.setTitle('このアプリについて')
  },
}
</script>
