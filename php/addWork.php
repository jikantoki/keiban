<?php
require_once '../env.php'; //環境変数読み込み
require_once './settings.php'; //ルートディレクトリ読み込み
require_once DIR_ROOT . '/php/myAutoLoad.php'; //自動読み込み
require_once DIR_ROOT . '/php/functions/authAPIforUse.php'; //APIが有効かどうか自動判定
require_once DIR_ROOT . '/php/functions/authAccountforUse.php'; //ログイン状態が有効かどうか判定

if (
  !isset($_SERVER['HTTP_ID']) ||
  !isset($_SERVER['HTTP_TOKEN']) ||
  $_SERVER['HTTP_TOKEN'] === '' ||
  !isset($_POST['work']) ||
  !isset($_POST['workDate'])
) {
  echo json_encode([
    'status' => 'invalid',
    'reason' => 'invalid authentication information',
    'errCode' => 10
  ]);
  exit;
}

$id = $_SERVER['HTTP_ID'];
$secretId = idToSecretId($id);
$token = $_SERVER['HTTP_TOKEN'];
$workData = json_decode($_POST['work'], true);
$workDate = $_POST['workDate'];
$commission = $workData['commission'];
$cost = $workData['cost'];
$memo = $workData['memo'];
$time = $workData['hour'] * 60 + $workData['min'];
/** 未使用なランダムID */
$workId = SQLmakeRandomId('work_list', 'workId');

$alreadyAddedFlag = SQLfindSome('work_list', [
  [
    'key' => 'secretId',
    'value' => $secretId,
    'func' => '='
  ],
  [
    'key' => 'dateUnixTime',
    'value' => $workDate,
    'func' => '='
  ]
]);

if ($alreadyAddedFlag && $alreadyAddedFlag['workId']) {
  SQLdelete('work_list', 'workId', $alreadyAddedFlag['workId']);
}

SQLinsert('work_list', [
  'workId' => $workId,
  'secretId' => $secretId,
  'dateUnixtime' => $workDate,
  'time' => $time,
  'commission' => $commission,
  'cost' => $cost,
  'memo' => $memo
]);

echo json_encode([
  'status' => 'ok',
  'id' => $id,
  'work' => json_encode($workData),
  'already' => $alreadyAddedFlag //上書きして消えたもの
]);
