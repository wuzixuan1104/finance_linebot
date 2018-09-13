<?php defined('MAPLE') || exit('此檔案不允許讀取！');

Router::dir('cli', function() {
  Router::cli('crontab/updateRecord')->controller('Crontab@updateRecord');
  Router::cli('crontab/currency')->controller('Crontab@currency');
  Router::cli('crontab/updateDaily')->controller('Crontab@forexRecordJob');
});
