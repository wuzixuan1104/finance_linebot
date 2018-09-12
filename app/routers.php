<?php defined('MAPLE') || exit('此檔案不允許讀取！');

Router::get('')->controller('Main@index');
Router::file('api.php') || gg('載入 Router「api.php」失敗！');