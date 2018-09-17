<?php defined('MAPLE') || exit('此檔案不允許讀取！');

Load::sysLib('Html.php');

spl_autoload_register(function($className) {
  if (!preg_match("/^Admin/", $className))
    return false;

  $file = implode(DIRECTORY_SEPARATOR, (array_filter(preg_split('/(?=[A-Z])/', $className)))) . '.php';

  Load::sysLib($file);

  class_exists($className) || gg('找不到名稱為「' . $className . '」的 Model 物件！');
}, false, true);
