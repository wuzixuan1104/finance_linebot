<?php defined('MAPLE') || exit('此檔案不允許讀取！');

return [
  [
    'stage' => 'testing',
    'name' => '測試',
    'host' => 'testing.___.com.tw',
    'user' => 'ubuntu',
    'port' => 22,
    'path' => '~/www/',
    'remote' => 'origin',
    'branch' => 'master',
    // 'migration' => [
    //   'remote' => 'origin',
    //   'branch' => 'migration',
    // ],
  ],
  [
    'stage' => 'production',
    'name' => '正式',
    'host' => 'www.___.com.tw',
    'user' => 'ubuntu',
    'port' => 22,
    'path' => '~/www/',
    'remote' => 'origin',
    'branch' => 'master',
    // 'migration' => [
    //   'remote' => 'origin',
    //   'branch' => 'migration',
    // ],
  ]
];