<template lang="pug">
.index-page
  .wrap
    v-card.content(elevation="4")
      .text-h2 編集
      hr
      .editor
        .text-h6 稼働履歴を確認して、編集できます
        v-btn.text-h5.mx-0.py-4(
          size="large"
          color="var(--accent-color)"
          style="height: auto; width: 100%; border-radius: var(--border-radius); color: white;"
          @click="clearWorkData();editDialog=true;editMode=false"
        ) 新規追加
        table(style="width: 100%; white-space: nowrap;" v-show="!loading && commission[0]")
          thead
            tr
              th 日付
              th 稼働時間
              th 獲得報酬
              th 時給
              th 操作
          tbody(style="text-align: center;")
            tr(v-for="(work, cnt) in commission")
              td(style="font-weight: unset;") {{ dateToString(work.date) }}
              td(style="font-weight: unset;") {{ timeToHHMM(work.time) }}
              td(style="font-weight: unset;") {{ work.commission - work.cost }}円
              td(style="font-weight: unset;") {{ calcHourly(work.commission - work.cost, work.time) }}円
              td(style="font-weight: unset; max-width: 5em;")
                v-btn.my-0(color="var(--accent-color)" style="color: white;" @click="editWorkData(cnt)" icon="mdi-pencil" size="x-small")
                v-btn.my-0(color="var(--color-error)" style="color: white;" @click="viewDeleteDialog(cnt)" icon="mdi-delete" size="x-small")
        p.text-h5.ma-4(
          v-if="!commission[0] && !loading"
          style="text-align: center;"
        ) データが登録されていません！
        v-progress-circular(
          v-if="loading"
          indeterminate
          size=100
          style="display: block; margin: 64px auto;"
        )
  v-dialog(v-model="deleteDialog" max-width="480px")
    v-card(title="削除の確認")
      .delete-message.mx-6
        p 以下の稼働データを削除します。よろしいですか？
        .delete-detail
          p 日付: {{ dateToString(commission[deleteIndex].date) }}
          p 稼働時間: {{ timeToHHMM(commission[deleteIndex].time) }}
          p 報酬: {{ commission[deleteIndex].commission }}円
          p 経費: {{ commission[deleteIndex].cost }}円
          p メモ: {{ commission[deleteIndex].memo }}
      v-card-actions
        v-btn(@click="deleteDialog=false" variant="elevated") いいえ
        v-btn(
          @click="deleteWorkData(deleteIndex);deleteDialog=false;"
          variant="elevated"
          color="var(--accent-color)"
          style="color: white;"
        ) はい
  v-dialog(v-model="editDialog" persistent max-width="640px")
    v-card(:title="editMode ? '編集' : '新規追加'")
      .editor-form.mx-4
        .form-cell(style="display: flex;")
          p.th 日付
          .td(style="display: flex; align-items: center;")
            v-date-input(
              placeholder="稼働日を選択"
              v-model="editForm.date"
              :disabled="editMode"
            )
        .form-cell(style="display: flex;")
          p.th 稼働時間
          .td(style="display: flex; align-items: center;")
            v-number-input(
              control-variant="stacked"
              v-model="editForm.hour"
              :max="23"
              :min="0"
            )
            span.mx-2 時間
            v-number-input(
              control-variant="stacked"
              v-model="editForm.min"
              :max="59"
              :min="0"
            )
            span.mx-2 分
        .form-cell(style="display: flex;")
          p.th 獲得報酬
          .td(style="display: flex; align-items: center;")
            v-number-input(
              control-variant="stacked"
              v-model="editForm.commission"
              :min="0"
            )
            span.mx-2 円
        .form-cell(style="display: flex;")
          p.th 経費
          .td(style="display: flex; align-items: center;")
            v-number-input(
              control-variant="stacked"
              v-model="editForm.cost"
              :min="0"
            )
            span.mx-2 円
        .form-cell(style="display: flex;")
          p.th メモ
          .td(style="display: flex; align-items: center;")
            v-text-field(
              placeholder="現在実装中"
              v-model="editForm.memo"
            )
      .error-message.ma-4.py-2.px-4(
        v-show="dialogErrorMessage"
        style=`
          background-color: var(--color-error);
          color: white;
          border-radius: var(--border-radius);
          display: flex;
          align-items: center;
        `
      )
        v-icon mdi-alert-circle-outline
        p.mx-4 {{ dialogErrorMessage }}
        v-icon(
          style="position: absolute; right: 1em;cursor: pointer;"
          @click="dialogErrorMessage = null"
        ) mdi-close
      v-card-actions
        v-btn(@click="editDialog=false" variant="elevated") キャンセル
        v-btn(
          @click="addWorkData(editForm)"
          variant="elevated"
          color="var(--accent-color)"
          style="color: white;"
        ) 保存
  component-button(
    v-if="userStore && userStore.userId"
    icon="mdi-plus"
    @clicked="clearWorkData();editDialog=true;editMode=false"
    )
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
  name: 'edit',
  components: {},
  mixins: [mixins],
  setup() {
    //サーバーサイドで仮のタイトルを設定、mountedで言語ごとに再設定する
    Setup.setTitle('Edit')
    Setup.setDescription('ケイバン！の編集画面')
  },
  data() {
    return {
      /** ページ読み込み中フラグ */
      loading: true,
      /** 編集ダイアログ開いているか？ */
      editDialog: false,
      /** 削除確認ダイアログ開いているか？ */
      deleteDialog: false,
      /** 何番目のデータを削除？ */
      deleteIndex: 0,
      editForm: {
        date: null,
        hour: null,
        min: null,
        commission: null,
        cost: null,
        memo: null,
      },
      /** エラーメッセージ（nullで非表示） */
      dialogErrorMessage: null,
      /** 編集モード（trueは上書きを許可、falseは新規登録） */
      editMode: false,
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
    }
  },
  async mounted() {
    this.setTitle('編集')

    //commissionリスト取得
    const getWork = await this.sendAjaxWithAuth('/getWork.php', {
      id: this.userStore.userId,
      token: this.userStore.userToken,
    })
    const worklist = JSON.parse(getWork.body.work)
    worklist.forEach((workData) => {
      this.commission.push({
        date: new Date(workData.dateUnixtime * 1000),
        workId: workData.workId,
        commission: workData.commission,
        cost: workData.cost,
        time: workData.time,
        memo: workData.memo,
      })
    })

    this.loading = false
  },
  methods: {
    clearWorkData: function () {
      this.editForm = {
        date: this.getToday(),
        hour: null,
        min: null,
        commission: null,
        cost: null,
        memo: null,
      }
      this.dialogErrorMessage = null
    },
    /** 稼働日の追加 */
    addWorkData: function (workData) {
      if (workData.date && workData.commission !== null) {
        const dates = this.commission.map((work) => {
          return work.date
        })
        let duplicationFlag = false
        let count = 0
        dates.forEach((date) => {
          if (workData.date - date == 0) {
            duplicationFlag = true
          }
          if (!duplicationFlag) count++ //breakできないためこのような記述
        })
        if (duplicationFlag) {
          if (this.editMode) {
            this.commission.splice(count, 1)
          } else {
            this.dialogErrorMessage = '既に登録されている日付です'
            return false
          }
        }

        this.dialogErrorMessage = null
        if (!workData.commission) workData.commission = 0
        if (!workData.cost) workData.cost = 0
        if (!workData.hour) workData.hour = 0
        if (!workData.min) workData.min = 0

        this.sendAjaxWithAuth(
          '/addWork.php',
          {
            id: this.userStore.userId,
            token: this.userStore.userToken,
          },
          {
            work: JSON.stringify(workData),
            workDate: workData.date.getTime() / 1000,
          }
        )
          .then((e) => {
            if (e.body.status === 'ok') {
              //登録成功
            } else {
              //登録失敗
            }
          })
          .catch((e) => {
            console.log(e)
            this.dialogErrorMessage = 'ネットワークエラー'
          })

        this.commission.push({
          date: workData.date,
          commission: workData.commission,
          cost: workData.cost,
          time: workData.hour * 60 + workData.min,
          memo: workData.memo,
        })

        this.editDialog = false
      } else {
        this.dialogErrorMessage = '日付と報酬のnullは受け付けられません'
      }
    },
    /** 指定された場所のデータを編集する */
    editWorkData: function (indexNumber) {
      this.editForm.date = this.commission[indexNumber].date
      this.editForm.hour = Math.floor(this.commission[indexNumber].time / 60)
      this.editForm.min = this.commission[indexNumber].time % 60
      this.editForm.commission = this.commission[indexNumber].commission
      this.editForm.cost = this.commission[indexNumber].cost
      this.editForm.memo = this.commission[indexNumber].memo
      this.dialogErrorMessage = null
      this.editMode = true
      this.editDialog = true
    },
    viewDeleteDialog: function (index) {
      this.deleteIndex = index
      this.deleteDialog = true
    },
    deleteWorkData: async function (indexNumber) {
      const ans = await this.sendAjaxWithAuth(
        '/deleteWork.php',
        {
          id: this.userStore.userId,
          token: this.userStore.userToken,
        },
        {
          workId: this.commission[indexNumber].workId,
        }
      )
      this.commission.splice(indexNumber, 1)
    },
  },
  watch: {
    commission: {
      handler: function () {
        this.commissionSort('date')
      },
      immediate: true,
      deep: true,
    },
  },
}
</script>

<style lang="scss" scoped>
.th {
  text-align: right;
  align-content: center;
  width: 6em;
  padding-right: 1em;
}
.td {
  width: -webkit-fill-available;
  width: -moz-available;
}
</style>
