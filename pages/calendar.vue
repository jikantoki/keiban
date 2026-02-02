<template lang="pug">
  .other-page
    .wrap
      v-card.content(elevation="4")
        .text-h4 稼働実績カレンダー
        .calendar.my-4
          v-sheet
            v-toolbar
              v-btn(
                small
                icon="mdi-calendar-today"
                @click="focus = ''"
              ) 今日
              v-btn.ma-2(
                small
                icon="mdi-chevron-left"
                @click=" $refs.calendar.prev() "
              )
              v-btn.ma-2(
                small
                icon="mdi-chevron-right"
                @click=" $refs.calendar.next() "
              )
              v-toolbar-title.text-h6 {{ $refs.calendar ? $refs.calendar.title : '' }}
          v-sheet
            VCalendar(
              ref="calendar"
              :events="attributes"
              color="var(--accent-color)"
              v-model="focus"
              @click:event="showEvent"
            )
            v-menu(
              v-model="selectedOpen"
              :activator="selectedElement"
              max-width="300"
            )
              v-card
                v-card-title 稼働詳細
                v-card-text
                  p 日付: {{ selectedEvent.start.getFullYear() }}年{{ selectedEvent.start.getMonth() + 1 }}月{{ selectedEvent.start.getDate() }}日
                  //p 拠点: {{ selectedEvent.name }}
                  //p 獲得ポイント: {{ selectedEvent.productionPoint }}ポイント
                  p 売上: {{ selectedEvent.commission.toLocaleString() }}円
                  //p 進捗: {{ selectedEvent.progress }}%
</template>

<script>
import metaFunctions from '~/js/metaFunctions'
import mixins from '~/mixins/mixins'
import Setup from '~/js/setup'

export default {
  mixins: [mixins],
  setup() {
    //サーバーサイドで仮のタイトルを設定、mountedで言語ごとに再設定する
    Setup.setTitle('Calendar')
    Setup.setDescription('稼働履歴をカレンダーで見る')
  },
  data() {
    return {
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
      /** カレンダーに表示するイベントリスト */
      attributes: [],
      focus: '',
      selectedEvent: {},
      selectedElement: null,
      selectedOpen: false,
    }
  },
  async mounted() {
    this.setTitle('カレンダー')
    console.log(this.$refs.calendar.title)

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

      worklist.forEach((workData, index) => {
        const date = new Date(workData.dateUnixtime * 1000)
        this.commission.push({
          date: date,
          workId: workData.workId,
          commission: workData.commission,
          cost: workData.cost,
          time: workData.time,
          memo: workData.memo,
        })
        let highlight = {}
        if (workData.commission > 0) {
          highlight = {
            color: 'var(--accent-color)',
            fillMode: 'solid',
          }
        } else {
          highlight = {
            color: '#666666',
            fillMode: 'outline',
          }
        }
        this.attributes.push({
          /*
          key: `workdata${index}`,
          highlight: highlight,
          dates: date,
          popover: {
            label: `${workData.place}、${productionPoint}ポイント、${
              workData.commission - workData.cost
            }`,
          },
          */
          start: date,
          end: date,
          name: workData.place,
          commission: workData.commission,
          color: highlight.color,
        })
      })
    }
  },
  methods: {
    showEvent(nativeEvent, { event }) {
      const open = () => {
        this.selectedEvent = event
        this.selectedElement = nativeEvent.target
        requestAnimationFrame(() =>
          requestAnimationFrame(() => (this.selectedOpen = true)),
        )
      }

      if (this.selectedOpen) {
        this.selectedOpen = false
        requestAnimationFrame(() => requestAnimationFrame(() => open()))
      } else {
        open()
      }

      nativeEvent.stopPropagation()
    },
  },
}
</script>

<style lang="scss">
.vc-container {
  .vc-weekday-1,
  .vc-weekday-7 {
    color: red;
  }
  .vc-day {
    height: 4em;
    .vc-day-content,
    .vc-highlight {
      height: 90%;
      width: 90%;
    }
  }
}
</style>
