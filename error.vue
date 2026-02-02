<template lang="pug">
.error-page(style="justify-content: center; height: 100svh; align-items: center;")
  .center
    .card(style="text-align: center;")
      img.logo-img(src="/icon.png" style="height: 120px;")
      v-card-title.title Error {{ error.statusCode }}
      v-card-subtitle {{ error.message }}
      v-card-text
        span ごめんエラーページ出ちった
        .detail.ma-4
          p エラー詳細:
          pre.detail-pre {{ error }}
      v-card-actions(style="justify-content: center;")
        a(href="/" style="color: unset;")
          v-btn Top
        v-btn(@click="$router.go(-1)") Undo
</template>

<script>
export default {
  layout: 'default',
  data() {
    return {
      error: useError(),
    }
  },
}
</script>

<style lang="scss" scoped>
@use 'sass:map';
$breakpoints: (
  'smartPhone': 'screen and (max-width:900px)',
  'tablet': 'screen and (max-width:1400px)',
  'pwa': '(display-mode: standalone)',
) !default;

/* フォント設定 */
$font: 'Zen Maru Gothic', sans-serif;
@import url('https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic&display=swap');
/* フォント設定ここまで */

@mixin mq($breakpoint) {
  @media #{map.get($breakpoints, $breakpoint)} {
    @content;
  }
}
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
  /** アプリの色 */
  --accent-color: #00bbee;
  /** アプリの色に合わせた文字色 */
  --accent-text-color: #ffffff;
  /** デフォルトのボーダー角の大きさ */
  --border-radius: 16px;
}
* {
  user-select: none;
  list-style: none;
  font-family: $font !important;
}
.center {
  width: 60%;
  margin: auto;
  height: 100svh;
  display: flex;
  justify-content: center;
  align-items: center;
  @include mq('tablet') {
    width: 80%;
  }
  @include mq('smartPhone') {
    width: 100%;
  }
}
.card {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}
.title {
  font-size: 2em;
}
.logo-img {
  width: 8em;
}
.detail {
  text-align: left;
  .detail-pre {
    max-height: 40vh;
    max-width: 100%;
    overflow: auto;
    white-space: break-spaces;
  }
}
</style>
