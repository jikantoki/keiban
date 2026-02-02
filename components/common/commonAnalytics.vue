<template lang="pug">
.center
  .commission
    .commission-item
        .text-h6 {{ title }}
        div.mb-2(style="display: flex; align-items: flex-end;")
          .text-h4.ma-0 {{ ignoreCost ? data.commission : data.commission - data.cost }}
          .text-h6(style="margin-left: 0.5em;") 円
    .hourly-and-operate-time(style="display:flex;")
      .hourly.commission-item.mr-2(style="width:50%;")
        .text-h7 時給
        .hourly-text.text-h6 {{ calcHourly((ignoreCost ? data.commission : data.commission - data.cost), data.time) }}円
      .operate-time.commission-item.ml-2(style="width:50%;")
        .text-h7 稼働時間
        .operate-time-text.text-h6 {{ Math.floor(data.time / 60) }}時間{{ Math.floor(data.time % 60) }}分
  .details.mt-4(v-if="mode != 'day'")
    .detail(v-for="detail in detailList")
      p {{ detail.title }}:
      p.right {{ detail.value }}{{ detail.unit }}
</template>

<script>
import mixins from '~/mixins/mixins'
export default {
  mixins: [mixins],
  data() {
    return {
      title: '収支',
      /** 詳細表示リスト */
      detailList: {
        /** 稼働日数 */
        daycount: {
          title: '稼働日数',
          value: this.data.days,
          unit: '日',
        },
        /** 日割の平均稼働時間 */
        averageTime: {
          title: '平均稼働時間',
          value: this.timeToHHMM(this.data.time),
          unit: '',
        },
        /** 日割の平均収支 */
        averageCommission: {
          title: '平均収支',
          value: Math.floor(
            (this.ignoreCost
              ? this.data.commission
              : this.data.commission - this.data.cost) / this.data.days,
          ),
          unit: '円',
        },
        /** 日割の最大収支 */
        maxCommission: {
          title: '最大収支',
          value: this.data.maxCommission,
          unit: '円',
        },
        /** 経費合計 */
        cost: {
          title: '経費合計',
          value: this.data.cost,
          unit: '円',
        },
      },
    }
  },
  props: {
    /** 稼働データ（1件のみ受け付け） */
    data: {
      type: Object,
      default: {
        days: 0,
        maxCommission: 0,
        commission: 0,
        cost: 0,
        dateUnixtime: 0,
        date: new Date(0),
        memo: '空のデータ',
        time: 0,
        workId: null,
        year: 1970,
        month: 0,
      },
      required: true,
    },
    mode: {
      type: String,
      default: null,
    },
    /** Trueは経費を無視する（売上表示） */
    ignoreCost: {
      type: Boolean,
      default: false,
    },
  },
  mounted() {
    switch (this.mode) {
      case 'all':
        this.title = '今までの総収入'
        break
      case 'month':
        this.title = `${this.data.year}年${this.data.month + 1}月`
        break
      case 'year':
        this.title = `${this.data.year}年`
        break
      case 'dayOfWeek':
        this.title = `${this.getJapaneseDayOfWeek(this.data.dayOfWeek)}曜日`
        break
      case 'day':
      default:
        this.title = `${this.data.year}年${this.dateToString(this.data.date)}`
        break
    }
  },
}
</script>

<style lang="scss" scoped>
.hitsuyou-mudana-space {
  height: 8em;
  width: 100%;
}
.details {
  .detail {
    display: flex;
    .right {
      margin-left: auto;
    }
  }
}
</style>
