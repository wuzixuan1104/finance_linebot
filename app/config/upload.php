<?php defined('MAPLE') || exit('此檔案不允許讀取！');

return [
  'picture' => [
    'formats' => ['jpg', 'gif', 'png'],
    'maxSize' => 10 * 1024 * 1024 // 10MB
  ],
  'video' => [
    'formats' => ['mp4', 'mov'],
    'maxSize' => 100 * 1024 * 1024 // 100MB
  ],
];