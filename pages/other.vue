<template lang="pug">
.other-page
  .wrap
    v-card.content(elevation="4")
      .text-h4 その他
      .other-props
        .other-content(v-for="button of buttons")
          .a-cover(v-ripple)
            a.content-a(:href="button.href")
              .button-inner.pl-4.py-4
                v-icon(size="large") {{ button.icon }}
                p.text-h6.ml-2 {{ button.name }}
                p.text-h6.right.pr-4 >
          hr.ma-0
</template>

<script>
import metaFunctions from '~/js/metaFunctions'
import mixins from '~/mixins/mixins'
import Setup from '~/js/setup'

export default {
  mixins: [mixins],
  setup() {
    //サーバーサイドで仮のタイトルを設定、mountedで言語ごとに再設定する
    Setup.setTitle('Other')
    Setup.setDescription('その他')
  },
  data: () => {
    return {
      buttons: [
        {
          name: '設定',
          icon: 'mdi-cog',
          href: '/settings',
        },
        {
          name: 'About',
          icon: 'mdi-information',
          href: '/about',
        },
      ],
    }
  },
  mounted() {
    this.setTitle('その他')
  },
}
</script>

<style lang="scss" scoped>
.content-a {
  text-decoration: none;
  color: unset;
  .button-inner {
    display: flex;
    position: relative;
    align-items: center;
    .right {
      position: absolute;
      right: 0;
    }
  }
}
hr {
  width: 100% !important;
}
</style>
