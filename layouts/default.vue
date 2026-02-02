<template lang="pug">
.v-app-main-application#nuxt
  splash(v-show="splash")
  v-app.wrap100svh(style="min-height: 100svh!important;width:100svw")
    header
      common-header
    v-main#main
      .center.main-content
        nuxt-page
      ClientOnly
        common-bar(
          v-if="!userStore.userId && !loginPageFlag"
          title="ログインして、使ってみよう"
          subTitle="ケイバン！で、稼働履歴や統計を見てみよう"
          :buttons="commonBarButtons"
          )
        //- common-bar(
          v-if="userStore.userId && isDisplayCommonPushButtons"
          title="最新の情報を入手しよう"
          subTitle="通知の送信を許可することで、最新情報を入手できます。"
          :buttons="commonBarPushButtons"
          @clicked="pushFlow()"
          )
      footer#footer
        common-footer
    commonBottomBar(v-if="userStore && userStore.userId")
  v-dialog(v-model="dialog" max-width="500")
    v-card
      v-card-title {{ dialogTitle }}
      v-card-text(v-html="dialogText")
      v-card-actions(v-if="dialogActions")
        v-spacer
        v-btn(
          v-for="btn, key in dialogActions"
          :key="key"
          @click="btn.action()"
          v-bind:class="[key === dialogActions.length - 1 ? 'btn-default' : 'btn-other']"
          ) {{ btn.value }}
  .right-space(style="min-height: 100svh")
</template>

<script>
import PackageJson from '/package.json'
import Functions from '~/js/Functions'
import commonHeader from '~/components/common/commonHeader'
import commonFooter from '~/components/common/commonFooter'
import mixins from '~/mixins/mixins'
import webpush from '~/js/webpush'
import splash from '~/components/common/commonSplash'
import commonBar from '~/components/common/commonBar.vue'
import commonBottomBar from '~/components/common/commonBottomBar.vue'
import commonAdsense from '~/components/common/commonAdsense.vue'

export default {
  /**
   * Appが定番
   */
  name: 'App',
  /**
   * 使いたいvueファイルを記述
   */
  components: {
    commonHeader,
    commonFooter,
    splash,
    commonBar,
    commonBottomBar,
    commonAdsense,
  },
  mixins: [mixins],
  /**
   * このファイル内で使うthisで始まる変数
   */
  data() {
    /**
     * この中に書かないと使えない
     */
    return {
      splash: true,
      isDisplayCommonPushButtons: false,
      commonBarButtons: [
        {
          title: 'ログイン',
          href: '/login',
        },
        {
          title: 'アカウント作成',
          href: '/registar',
        },
      ],
      commonBarPushButtons: [
        {
          title: '通知を許可',
          return: 'allowPush',
        },
      ],
      dialog: false,
      dialogTitle: null,
      dialogText: null,
      dialogActions: null,
      loginPageFlag: false,
    }
  },
  /**
   * 監視したい変数を記述
   */
  watch: {
    $route: {
      handler: function (to) {
        if (to.path == '/login' || to.path == '/registar') {
          this.loginPageFlag = true
        } else {
          this.loginPageFlag = false
        }
      },
    },
  },
  /**
   * ページ生成時にやりたい事
   */
  created() {
    this.$vuetify.theme.global.name = 'dark'
  },
  mounted() {
    PackageJson.name = Functions.ifEnglishStartUpper(PackageJson.name)
    setTimeout(() => {
      webpush
        .set()
        .then((e) => {})
        .catch((e) => {
          this.isDisplayCommonPushButtons = true
        })
    }, 1000)

    /**
     * mountedの最後に記述
     */
    window.setTimeout(() => {
      this.splash = false
    }, 1000)
  },
  /**
   * ページ離脱時にやりたい事
   */
  unmouonted() {},
  /**
   * このファイル内で使いたい変数
   */
  methods: {
    async pushFlow() {
      const res = await this.getRequest()
      if (res) {
        this.isDisplayCommonPushButtons = false
      }
      return res
    },
    async getRequest() {
      const webPush = await webpush.get(true)
      if (webPush) {
        this.dialogTitle = 'ありがとうございます！'
        this.dialogText = 'プッシュ通知の許可に成功しました。'
        this.dialogActions = [
          {
            value: '閉じる',
            action: () => {
              this.dialog = false
            },
          },
        ]
        this.dialog = true
        this.sendAjaxWithAuth(
          '/sendPushForMe.php',
          {
            endpoint: webPush.endpoint,
            publickey: webPush.publicKey,
            authtoken: webPush.authToken,
          },
          {
            title: `ケイバン！`,
            message: 'プッシュ通知の許可に成功しました！',
          }
        )
        return webPush
      } else {
        if (webPush === undefined) {
          this.dialogTitle = 'リクエスト失敗'
          this.dialogText =
            'ブラウザによって通知へのリクエストが拒否されています。'
          this.dialog = true
          this.dialogActions = [
            {
              value: '閉じる',
              action: () => {
                this.dialog = false
              },
            },
          ]
        } else {
          this.dialogTitle = 'リクエスト失敗'
          this.dialogText = `プッシュ通知の許可は、ブラウザから行う必要があります。\nこの端末で <span class="allow-select-all underline">https://${location.host}</span> にアクセスしてください。`
          this.dialog = true
          this.dialogActions = [
            {
              value: '閉じる',
              action: () => {
                this.dialog = false
              },
            },
          ]
        }
        return null
      }
    },
  },
}
</script>

<style lang="scss">
@use 'sass:map';
$breakpoints: (
  'smartPhone': 'screen and (max-width:700px)',
  'tablet': 'screen and (max-width:1100px)',
  'pwa': '(display-mode: standalone)',
) !default;

@mixin mq($breakpoint) {
  @media #{map.get($breakpoints, $breakpoint)} {
    @content;
  }
}

/* フォント設定 */
$font: 'Zen Maru Gothic', sans-serif;
@import url('https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic&display=swap');
/* フォント設定ここまで */

$body-font-family: $font;
html,
body {
  height: 100svh !important;
}
:root {
  font-size: 16px;
  --color-allow: #cceeff;
  --color-error: #ffcccc;
  --color-error: #cc2222;
  --color-success: #338833;
  /** アプリの色 */
  --accent-color: #008f3b;
  /** アプリの色に合わせた文字色 */
  --accent-text-color: #ffffff;
  /** デフォルトのボーダー角の大きさ */
  --border-radius: 16px;

  /* システムバーの領域に応じた余白を自動で取得 */
  --safe-area-inset-top: env(safe-area-inset-top);
  --safe-area-inset-bottom: env(safe-area-inset-bottom);
  --safe-area-inset-left: env(safe-area-inset-left);
  --safe-area-inset-right: env(safe-area-inset-right);
  //--safe-area-inset-top: 12px;
  //--safe-area-inset-bottom: 8px;
  //--safe-area-inset-left: 0px;
  //--safe-area-inset-right: 0px;
}
* {
  user-select: none;
  list-style: none;
  font-family: $font !important;
}
#nuxt {
  font-family: $font !important;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  padding-top: var(--safe-area-inset-top);
  /* 必要に応じて下部や左右の余白も設定 */
  padding-bottom: var(--safe-area-inset-bottom);
  nav {
    padding: 30px;
  }
  .center {
    width: 60%;
    margin: auto;
    @include mq('tablet') {
      width: 80%;
    }
    @include mq('smartPhone') {
      width: 100%;
    }
  }
  .text-h0 {
    font-size: 4em !important;
    height: 5.8rem;
    line-height: 5.8rem;
  }
  .text-h1 {
    font-size: 3em !important;
    line-height: 4rem;
  }
  svg.text-h1 {
    height: 4rem;
  }
  .text-h2 {
    font-size: 2.8em !important;
    line-height: 3.75rem;
  }
  svg.text-h2 {
    height: 3.75rem;
  }
  .text-h3 {
    font-size: 2.4em !important;
    line-height: 3.125rem;
  }
  svg.text-h3 {
    height: 3.125rem !important;
  }
  .text-h4 {
    font-size: 2.2em !important;
    line-height: 2.5rem;
  }
  svg.text-h4 {
    height: 2.5rem;
  }
  .text-h5 {
    font-size: 2em !important;
    line-height: 2rem;
  }
  svg.text-h5 {
    height: 2rem;
  }
  .text-h6,
  .text {
    font-size: 1.5em !important;
    line-height: 1.5rem;
  }
  svg.text-h6 {
    height: 1.5rem;
  }
  .text-h7,
  .text-small {
    font-size: 1em !important;
    line-height: 1.45rem;
  }
  svg.text-h7 {
    height: 1.45rem;
  }
  .text-h0,
  .text-h1,
  .text-h2,
  .text-h3 {
    white-space: nowrap;
    overflow: hidden;
  }
  .text-h0,
  .text-h1,
  .text-h2,
  .text-h3,
  .text-h4,
  .text-h5,
  .text-h6,
  .text-h7,
  .text-small {
    font-family: $font !important;
    margin-top: 0.2em;
    margin-bottom: 0.2em;
  }
  .splash-text {
    font-family: $font !important;
  }
  .allow-select {
    user-select: auto;
  }
  .allow-select-all {
    user-select: all;
  }
  .underline {
    text-decoration: underline;
  }
  .v-btn {
    text-transform: none;
  }
  button {
    margin: 0.5em;
  }
  .content {
    width: 95%;
    display: flex;
    flex-direction: column;
    padding: 8px 16px;
    border-radius: var(--border-radius);
    overflow: hidden;
  }
  .wrap {
    display: flex;
    justify-content: center;
    padding: 8px 0px;
    flex-direction: column;
    align-items: center;
  }
  .wrap100svh {
    min-height: 100svh !important;
  }
  .ontext {
    height: 1em;
    vertical-align: middle;
    object-fit: cover;
  }
  hr {
    margin: 8px auto;
    width: 98%;
    align-items: center;
    border: 1px solid;
    opacity: 0.3;
  }
  /** モバイル用表示 */
  .is-mobile {
    display: none;
    @include mq('smartPhone') {
      display: initial;
    }
  }
  /** デスクトップ用表示 */
  .is-not-mobile {
    display: initial;
    @include mq('smartPhone') {
      display: none;
    }
  }
  /** モバイル用表示 */
  .is-pwa {
    display: none;
    @include mq('pwa') {
      display: initial;
    }
  }
  /** PWAじゃない場合に表示 */
  .is-not-pwa {
    display: initial;
    @include mq('pwa') {
      display: none;
    }
  }
  .big-img {
    max-height: 300px;
    object-fit: cover;
    width: 100%;
  }
  .img-wrap {
    position: relative;
    background-color: #000000;
    p {
      position: absolute;
      top: 50%;
      left: 50%;
      -ms-transform: translate(-50%, -50%);
      -webkit-transform: translate(-50%, -50%);
      transform: translate(-50%, -50%);
      margin: 0;
      padding: 0;
      color: white;
      white-space: nowrap;
    }
    img {
      opacity: 0.7;
    }
  }
  main.v-main {
    display: flex;
    flex-direction: column;
    .center.main-content {
      flex: 1;
    }
    #footer {
      bottom: 0;
      width: 100%;
      min-height: 100px !important;
      background-color: rgb(var(--v-theme-surface));
    }
  }
  .relative {
    position: relative;
  }
  .absolute {
    position: absolute;
  }
  .flex {
    display: flex;
    @include mq('smartPhone') {
      flex-direction: column;
    }
  }
  .flex-child {
    flex: 1;
    overflow: hidden;
    padding: 8px;
    @include mq('smartPhone') {
      padding: 0;
    }
  }
}
.btn-default {
  background-color: var(--accent-color);
  color: white;
}
</style>
