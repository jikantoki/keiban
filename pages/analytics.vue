<template lang="pug">
.other-page
  .wrap
    v-card.content(elevation="4")
      .text-h5 収支
      .contents
        .other-content(v-for="button of shushiButtons")
          .a-cover(v-ripple)
            a.content-a(@click="ignoreCost = false;openData(button.mode)")
              .button-inner.pl-4.py-4
                v-icon {{ button.icon }}
                p.text-h7.ml-2 {{ button.name }}
                p.text-h7.right.pr-4 >
          hr.ma-0
      .text-h5 売上
      .contents
        .other-content(v-for="button of riekiButtons")
          .a-cover(v-ripple)
            a.content-a(@click="ignoreCost = true;openData(button.mode)")
              .button-inner.pl-4.py-4
                v-icon {{ button.icon }}
                p.text-h7.ml-2 {{ button.name }}
                p.text-h7.right.pr-4 >
          hr.ma-0
  v-dialog(v-model="activeDialog" max-width="640px")
    v-card(
      :title="`${dialogTitle}${ignoreCost ? '売上' : '収支'}`"
      style="position: relative;"
      )
      .analytics-item.my-4.mx-6
        .vfor-analytics(v-for="data of workData")
          commonAnalytics(:data="data" :mode="mode" :ignoreCost="ignoreCost")
          hr.my-2(style="opacity: 0.3")
      v-card-actions(style=`
        position: sticky;
        bottom: 0;
        right: 1em;
        background: rgb(var(--v-theme-surface));
        width: -webkit-fill-available;
        width: -moz-available;
        border-top: solid 1px rgb(var(--v-theme-surface-variant));
      `)
        v-btn(
          @click="activeDialog=false"
          variant="elevated"
          color="var(--accent-color)"
          style="color: white;"
        ) 閉じる
</template>

<script>
import metaFunctions from '~/js/metaFunctions'
import mixins from '~/mixins/mixins'
import Setup from '~/js/setup'
import commonAnalytics from '~/components/common/commonAnalytics.vue'

export default {
  mixins: [mixins],
  components: {
    commonAnalytics,
  },
  setup() {
    //サーバーサイドで仮のタイトルを設定、mountedで言語ごとに再設定する
    Setup.setTitle('Analytics')
    Setup.setDescription('収支分析ページ')
  },
  data: () => {
    return {
      shushiButtons: [
        {
          name: '生涯収支',
          icon: 'mdi-heart',
          mode: 'all',
        },
        {
          name: '年別収支',
          icon: 'mdi-calendar-expand-horizontal',
          mode: 'year',
        },
        {
          name: '月別収支',
          icon: 'mdi-moon-waning-crescent',
          mode: 'month',
        },
        {
          name: '日別収支',
          icon: 'mdi-sun-clock',
          mode: 'day',
        },
        {
          name: '曜日別収支',
          icon: 'mdi-calendar-week-begin',
          mode: 'dayOfWeek',
        },
      ],
      riekiButtons: [
        {
          name: '生涯売上',
          icon: 'mdi-heart',
          mode: 'all',
        },
        {
          name: '年別売上',
          icon: 'mdi-calendar-expand-horizontal',
          mode: 'year',
        },
        {
          name: '月別売上',
          icon: 'mdi-moon-waning-crescent',
          mode: 'month',
        },
      ],
      /** trueは経費を無視する（売上表示） */
      ignoreCost: false,
      /** propで渡す用データ */
      workData: [],
      /** 稼働実績リスト */
      commission: [],
      /** ダイアログ開くフラグ */
      activeDialog: false,
      /** ダイアログのタイトル */
      dialogTitle: '',
      /** 統計の表示モード */
      mode: null,
    }
  },
  async mounted() {
    this.setTitle('収支分析')

    //commissionリスト取得
    if (this.userStore && this.userStore.userId) {
      const getWork = await this.sendAjaxWithAuth('/getWork.php', {
        id: this.userStore.userId,
        token: this.userStore.userToken,
      })
      const worklist = JSON.parse(getWork.body.work)

      worklist.sort((a, b) => {
        return a.dateUnixtime - b.dateUnixtime
      })
      worklist.forEach((work, index) => {
        worklist[index].date = new Date(work.dateUnixtime * 1000)
        worklist[index].year = work.date.getFullYear()
        worklist[index].month = work.date.getMonth()
        worklist[index].dayOfWeek = work.date.getDay()
      })

      this.commission = worklist
    }
  },
  methods: {
    /**
     * データを開く
     */
    openData(mode) {
      this.workData = []
      this.mode = mode
      switch (mode) {
        case 'all':
          this.dialogTitle = '生涯'
          this.workData[0] = {
            commission: 0,
            maxCommission: 0,
            cost: 0,
            time: 0,
            days: 0,
          }
          this.commission.forEach((data) => {
            this.workData[0].commission += data.commission
            this.workData[0].cost += data.cost
            this.workData[0].time += data.time
            this.workData[0].days += 1
            if (this.workData[0].maxCommission < data.commission - data.cost) {
              this.workData[0].maxCommission = data.commission - data.cost
            }
          })
          break
        case 'month':
          this.dialogTitle = '月別'
          this.commission.forEach((data) => {
            let findFlag = false
            this.workData.forEach((work, index) => {
              //年と月が同じなら統合
              if (
                work.year == data.year &&
                work.month == data.month &&
                !findFlag
              ) {
                this.workData[index] = {
                  commission: work.commission + data.commission,
                  cost: work.cost + data.cost,
                  date: work.date,
                  dateUnixtime: work.dateUnixtime,
                  year: work.year,
                  month: work.month,
                  time: work.time + data.time,
                  days: this.workData[index].days + 1,
                  maxCommission:
                    this.workData[index].maxCommission < work.commission
                      ? work.commission
                      : this.workData[index].maxCommission,
                }
                findFlag = true
              }
            })
            if (!findFlag) {
              this.workData.push({
                ...data,
                days: 1,
                maxCommission: data.commission,
              })
            }
          })
          break
        case 'year':
          this.dialogTitle = '年別'
          this.commission.forEach((data) => {
            let findFlag = false
            this.workData.forEach((work, index) => {
              //年が同じなら統合
              if (work.year == data.year && !findFlag) {
                this.workData[index] = {
                  commission: work.commission + data.commission,
                  cost: work.cost + data.cost,
                  date: work.date,
                  dateUnixtime: work.dateUnixtime,
                  year: work.year,
                  month: work.month,
                  time: work.time + data.time,
                  days: this.workData[index].days + 1,
                  maxCommission:
                    this.workData[index].maxCommission < work.commission
                      ? work.commission
                      : this.workData[index].maxCommission,
                }
                findFlag = true
              }
            })
            if (!findFlag) {
              this.workData.push({
                ...data,
                days: 1,
                maxCommission: data.commission,
              })
            }
          })
          break
        case 'dayOfWeek':
          this.dialogTitle = '曜日別'
          this.commission.forEach((data) => {
            let findFlag = false
            this.workData.forEach((work, index) => {
              //曜日が同じなら統合
              if (work.dayOfWeek == data.dayOfWeek && !findFlag) {
                this.workData[index] = {
                  commission: work.commission + data.commission,
                  cost: work.cost + data.cost,
                  date: work.date,
                  dateUnixtime: work.dateUnixtime,
                  year: work.year,
                  month: work.month,
                  time: work.time + data.time,
                  days: this.workData[index].days + 1,
                  dayOfWeek: work.dayOfWeek,
                  maxCommission:
                    this.workData[index].maxCommission < work.commission
                      ? work.commission
                      : this.workData[index].maxCommission,
                }
                findFlag = true
              }
            })
            if (!findFlag) {
              this.workData.push({
                ...data,
                days: 1,
                maxCommission: data.commission,
              })
            }
          })
          break
        case 'day':
        default:
          this.dialogTitle = '日付別'
          this.workData = this.commission
          this.workData.forEach((data, index) => {
            this.workData[index].days = 1
            this.workData[index].maxCommission = data.commission
          })
          break
      }
      if (mode == 'dayOfWeek') {
        this.workData.sort((a, b) => {
          if (a.dayOfWeek < b.dayOfWeek) {
            return -1
          } else {
            return 1
          }
        })
      } else {
        this.workData.reverse()
      }
      this.activeDialog = true
    },
  },
}
</script>

<style lang="scss" scoped>
.content-a {
  text-decoration: none;
  color: unset;
  cursor: pointer;
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
