<template lang="pug">
.commonMainContent
  .wrap
    .content(style="padding: 0px 0;")
      .commission
        .thisweek-commission.commission-item
          .text-h6 {{ allHistory ? '今までの収益' : '今週の収益'}}
          div(style="display: flex; align-items: flex-end; margin: 8px 0;")
            .text-h2.ma-0(v-show="!loading") {{ thisWeekCommission - thisWeekCost }}
            .text-h6(style="margin-left: 0.5em;" v-show="!loading") 円
            ContentLoader.text-h2.ma-0(v-show="loading" style="width: 5em;")
      .hourly-and-operate-time(style="display:flex;")
        .hourly.commission-item.mr-2(style="width:40%;")
          .text-h6 時給
          .hourly-text.text-h5(v-show="!loading") {{ thisWeekHourly }}円
          ContentLoader.text-h5(v-show="loading" style="width: 5em;")
        .operate-time.commission-item.ml-2(style="width:60%;")
          .text-h6 稼働時間
          .operate-time-text.text-h5(v-show="!loading") {{ timeToHHMM(thisWeekOperateTime) }}
          ContentLoader.text-h5(v-show="loading" style="width: 5em;")
      .edit-work-button(style="display: flex;")
        a.mt-2(
          v-if="userStore && userStore.userId"
          href="/edit"
          style="width: 100%;"
        )
          v-btn.text-h6.ma-0.pa-2(
            style="height: auto; width: 100%; padding: 8px; margin: 8px 0; border-radius: var(--border-radius);color: white;"
            prepend-icon="mdi-pencil"
            color="var(--accent-color)"
          ) 編集
        a.mt-2(
          v-if="!userStore || !userStore.userId"
          href="/login"
          style="width: 100%;"
        )
          v-btn.text-h6.ma-0.pa-2(
            style="height: auto; width: 100%; border-radius: var(--border-radius);color: white;"
            prepend-icon="mdi-account-circle"
            color="var(--accent-color)"
          ) ログインして入力
  .wrap(v-if="userStore && userStore.userId")
    v-card.content(v-if="!commission[0]")
      p.text-h5.ma-8(
        v-show="!loading"
        style="text-align: center;"
      ) 稼働履歴はありません！
      v-progress-circular(
        v-show="loading"
        indeterminate
        size=100
        style="margin: 4em auto;"
      )
      .edit-work-button(style="display: flex; align-self: center;")
        a.ma-2(
          href="/history"
          v-if="!allHistory"
        )
          v-btn.ma-0(
            style="border-radius: var(--border-radius);color: white;"
            size="large"
            prepend-icon="mdi-history"
            color="var(--accent-color)"
          ) 全期間
        a.ma-2(
          href="/"
          v-if="allHistory"
        )
          v-btn.ma-0(
            style="border-radius: var(--border-radius);color: white;"
            size="large"
            prepend-icon="mdi-calendar-today"
            color="var(--accent-color)"
          ) 今週
    v-card.content(v-else)
      p.text-h6.mb-4 稼働グラフ
      v-tabs(active-color="red" v-model="tab")
        v-tab.text-h7.v-tab-mainscreen(value='dafault') 全表示
        v-tab.text-h7.v-tab-mainscreen(value='date') 日付
        v-tab.text-h7.v-tab-mainscreen(value='commission') 収益
        v-tab.text-h7.v-tab-mainscreen(value='hourly') 時給
        v-tab.text-h7.v-tab-mainscreen(value='operateTime') 稼働時間
      v-window.tab-item-wrap(v-model="tab")
        v-window-item.tab-item(value='dafault')
          v-card.content
            v-sparkline(
              :model-value="commissionHistory"
              line-width=1
              padding=8
              stroke-linecap="round"
              smooth
              auto-draw
              show-labels
              :labels="dateHistory"
              )
              template(v-slot:label="item") {{ item.value }}
          table(style="width: 100%;")
            thead
              tr
                th 日付
                th 稼働時間
                th 獲得報酬
                th 時給
            tbody
              tr(v-for="(work, cnt) in commission" :style="!work.time ? 'opacity: 0.5;' : ''")
                td(style="font-weight: unset;") {{ dateToString(work.date) }}
                td(style="font-weight: unset;") {{ timeToHHMM(work.time) }}
                td(style="font-weight: unset;") {{ work.commission - work.cost }}円
                td(style="font-weight: unset;") {{ calcHourly(work.commission - work.cost, work.time) }}円
        v-window-item.tab-item(value='date')
          v-card.content
            v-sparkline(
              :model-value="commissionHistory"
              line-width=1
              padding=8
              stroke-linecap="round"
              smooth
              auto-draw
              show-labels
              :labels="dateHistory"
              )
              template(v-slot:label="item") {{ item.value }}
          table(style="width: 100%;")
            thead
              tr
                th 日付
                th 稼働時間
                th 獲得報酬
            tbody
              tr(v-for="(work, cnt) in commission" :style="!work.time ? 'opacity: 0.5;' : ''")
                td(style="font-weight: unset;") {{ dateToString(work.date) }}
                td(style="font-weight: unset;") {{ timeToHHMM(work.time) }}
                td(style="font-weight: unset;") {{ work.commission - work.cost }}円
        v-window-item.tab-item(value='commission')
          v-card.content
            v-sparkline(
              :model-value="commissionHistory"
              line-width=1
              padding=8
              stroke-linecap="round"
              smooth
              auto-draw
              show-labels
              :labels="dateHistory"
              )
              template(v-slot:label="item") {{ item.value }}
          table(style="width: 100%;")
            thead
              tr
                th 順位
                th 日付
                th 獲得報酬
            tbody
              tr(v-for="(work, cnt) in commission" :style="!work.time ? 'opacity: 0.5;' : ''")
                td(style="font-weight: unset;") {{ cnt + 1 }}
                td(style="font-weight: unset;") {{ dateToString(work.date) }}
                td(style="font-weight: unset;") {{ work.commission - work.cost }}円
        v-window-item.tab-item(value='hourly')
          v-card.content
            v-sparkline(
              :model-value="hourlyHistory"
              line-width=1
              padding=8
              stroke-linecap="round"
              smooth
              auto-draw
              show-labels
              :labels="dateHistory"
              )
              template(v-slot:label="item") {{ item.value }}
          table(style="width: 100%;")
            thead
              tr
                th 順位
                th 日付
                th 時給
            tbody
              tr(v-for="(work, cnt) in commission" :style="!work.time ? 'opacity: 0.5;' : ''")
                td(style="font-weight: unset;") {{ cnt + 1 }}
                td(style="font-weight: unset;") {{ dateToString(work.date) }}
                td(style="font-weight: unset;") {{ calcHourly(work.commission - work.cost, work.time) }}円
        v-window-item.tab-item(value='operateTime')
          v-card.content
            v-sparkline(
              :model-value="timeHistory"
              line-width=1
              padding=8
              stroke-linecap="round"
              smooth
              auto-draw
              show-labels
              :labels="dateHistory"
              )
              //template(v-slot:label="item") {{ timeToHHMM(item.value) }}
          table(style="width: 100%;")
            thead
              tr
                th 順位
                th 日付
                th 稼働時間
            tbody
              tr(v-for="(work, cnt) in commission" :style="!work.time ? 'opacity: 0.5;' : ''")
                td(style="font-weight: unset;") {{ cnt + 1 }}
                td(style="font-weight: unset;") {{ dateToString(work.date) }}
                td(style="font-weight: unset;") {{ timeToHHMM(work.time) }}
      .edit-work-button(style="display: flex; align-self: center;")
        .ma-2(v-if="userStore && userStore.userId")
          v-btn.ma-0(
            v-if="!allHistory"
            style="border-radius: var(--border-radius);color: white;"
            size="large"
            prepend-icon="mdi-history"
            color="var(--accent-color)"
            @click="a('/history')"
          ) 全期間
          v-btn.ma-0(
            v-if="allHistory"
            style="border-radius: var(--border-radius);color: white;"
            size="large"
            prepend-icon="mdi-calendar-today"
            color="var(--accent-color)"
            @click="a('/')"
          ) 今週
  </template>

<script>
/**
 * ページ推移ごとにmountedを実行する必要があるため、どのviewsでも読み込むこと
 */
import mixins from '~/mixins/mixins'
import webpush from '~/js/webpush'
import metaFunctions from '~/js/metaFunctions'
import Setup from '~/js/setup'

export default {
  mixins: [mixins],
  props: {
    /** 全ての履歴を表示するか？ */
    allHistory: {
      type: Boolean,
      default: false,
      required: false,
    },
  },
  data() {
    return {
      tab: null,
      loading: true, //ページ読み込み中フラグ
      thisWeekCommission: 0, //今週の収益
      thisWeekCost: 0, //今週の経費
      thisWeekHourly: 0, //今週の時給
      thisWeekOperateTime: 0, //今週の稼働時間
      /** 報酬リスト
         * サンプル
          {
            workId: 'hogefuga', //ランダムID
            date: new Date(2024, 8, 29), //稼働日時
            commission: 11136, //報酬（円）
            cost: 810, //経費（円）
            time: 60 * 4.7, //稼働時間（分）
            memo: 'piyo', //メモ
          },
         */
      commission: [],
      commissionHistory: [], //日付順の報酬額リスト
      costHistory: [], //日付順の経費リスト
      hourlyHistory: [], //日付順の時給リスト
      timeHistory: [], //日付順の稼働時間リスト
      dateHistory: [], //historyで使う日付リスト
    }
  },
  async mounted() {
    //commissionリスト取得
    if (this.userStore && this.userStore.userId) {
      const getWork = await this.sendAjaxWithAuth('/getWork.php', {
        id: this.userStore.userId,
        token: this.userStore.userToken,
      })
      const worklist = JSON.parse(getWork.body.work)

      worklist.sort((a, b) => {
        return b.dateUnixtime - a.dateUnixtime
      })

      if (this.allHistory) {
        worklist.forEach((workData) => {
          const date = new Date(workData.dateUnixtime * 1000)
          this.commission.push({
            date: date,
            workId: workData.workId,
            commission: workData.commission,
            cost: workData.cost,
            time: workData.time,
            memo: workData.memo,
          })
        })
      } else {
        ;[...Array(7)].map((_, i) => {
          let addFlag = false
          const now = new Date()
          worklist.forEach((workData) => {
            if (!addFlag) {
              const date = new Date(workData.dateUnixtime * 1000)
              //commissionリストにpushするかどうか？
              let flag = false
              if (now.getDay() != 0) {
                if (date - this.getDayOfWeek(7 - i) == 0) flag = true
              } else {
                if (date - this.getDayOfWeek(-7 + (7 - i)) == 0) flag = true
              }
              if (flag) {
                addFlag = true
                this.commission.push({
                  date: date,
                  workId: workData.workId,
                  commission: workData.commission,
                  cost: workData.cost,
                  time: workData.time,
                  memo: workData.memo,
                })
              }
            }
          })
          if (!addFlag) {
            if (now.getDay() != 0) {
              this.commission.push({
                date: this.getDayOfWeek(7 - i),
                workId: null,
                commission: 0,
                cost: 0,
                time: 0,
                memo: null,
              })
            } else {
              this.commission.push({
                date: this.getDayOfWeek(-7 + (7 - i)),
                workId: null,
                commission: 0,
                cost: 0,
                time: 0,
                memo: null,
              })
            }
          }
        })
      }
    }

    for (const work of this.commission.reverse()) {
      const hourly = this.calcHourly(work.commission - work.cost, work.time)

      this.commissionHistory.push(work.commission)
      this.costHistory.push(work.cost)
      this.hourlyHistory.push(hourly)
      this.timeHistory.push(work.time)
      this.dateHistory.push(
        `${('0' + (work.date.getMonth() + 1)).slice(-2)}/${(
          '0' + work.date.getDate()
        ).slice(-2)}`,
      )
      this.thisWeekCommission += work.commission
      this.thisWeekCost += work.cost
      this.thisWeekOperateTime += work.time
    }
    this.thisWeekHourly = this.calcHourly(
      this.thisWeekCommission - this.thisWeekCost,
      this.thisWeekOperateTime,
    )
    this.commission.reverse()

    this.loading = false
  },
  methods: {},
  watch: {
    tab: {
      handler: function (newValue) {
        this.commissionSort(newValue)
      },
      immediate: true,
    },
  },
}
</script>

<style lang="scss" scoped>
.commission-item {
  margin: 8px 0;
  padding: 8px 16px;
  background: rgb(var(--v-theme-surface));
  border-radius: var(--border-radius);
}
.v-tab-mainscreen {
  margin: 0 !important;
  padding: 4px;
  height: auto !important;
}
.v-tab-item--selected {
  background: var(--accent-color);
  color: white;
}
.tab-item {
  padding: 8px;
}
td {
  text-align: center;
}
</style>
