<?php defined('MAPLE') || exit('此檔案不允許讀取！');

if (!function_exists('isEmail')) {
  function isEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
  }
}

if (!function_exists('isUrl')) {
  function isUrl($url) {
    return preg_match('/^https?:\/\/.*/', $url);
  }
}

if (!function_exists('isDate')) {
  function isDate($date) {
    return DateTime::createFromFormat('Y-m-d', $date) !== false;
  }
}

if (!function_exists('isDatetime')) {
  function isDatetime($date) {
    return DateTime::createFromFormat('Y-m-d H:i:s', $date) !== false;
  }
}

if (!function_exists('isTaxNum')) {
  function isTaxNum($taxNum) {

    // 共八位，全部為數字型態
    if (!preg_match("/^\d{8}$/", $taxNum))
      return false;

    // 各數字分別乘以 1,2,1,2,1,2,4,1
    // 例：統一編號為 53212539
    // Step1 將統編每位數乘以一個固定數字固定值
    //   5   3   2   1   2   5   3   9
    // x 1   2   1   2   1   2   4   1
    // ================================
    //   5   6   2   2   2  10  12   9
    $result = array_map(function($a, $b) { return $a * $b; }, str_split($taxNum), [1, 2, 1, 2, 1, 2, 4, 1]);

    // Step2 將所得值取出十位數及個位數
    // 十位數 個位數
    //   0      5
    //   0      6
    //   0      2
    //   0      2
    //   0      2
    //   1      0
    //   1      2
    //   0      9
    // 並將十位數與個位數全部結果值加總
    $sum = array_sum(array_map(function($elm) { return (int)($elm / 10) + $elm % 10; }, $result));

    // Step3 判斷結果
    // 第一種:加總值取10的餘數為0
    // 第二種:加總值取9的餘數等於9而且統編的第6碼為7
    return ($sum % 10 == 0) || ($sum % 9 == 9 && $taxNum[7] == 7);
  }
}

if (!function_exists('isUploadFile')) {
  function isUploadFile($file) {
    return isset($file['name'], $file['type'], $file['tmp_name'], $file['error'], $file['size']);
  }
}

if (!function_exists('uploadFileInFormats')) {
  function uploadFileInFormats($file, $formats) {
    static $extension;
    
    $formats = array_unique(array_map('trim', $formats));

    if (!$format = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION))) {
      $extension || $extension = config('extension');
      foreach ($extension as $ext => $mime)
        if (in_array($file['type'], $mime) && ($format = $ext))
          break;
    }

    return $format && in_array($format, $formats);
  }
}