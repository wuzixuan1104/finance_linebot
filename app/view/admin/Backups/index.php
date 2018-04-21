<?php echo $search; ?>

<div class='panel'>
<?php echo $search->setTableClomuns (
  Restful\Column::create ('已讀')->setWidth (60)->setClass ('center')->setTd (function ($obj, $column) { return $column->setSwitch ($obj->read == Backup::READ_YES, array ('class' => 'switch ajax', 'data-column' => 'read', 'data-url' => RestfulURL::url ('admin/Backups@read', $obj), 'data-true' => Backup::READ_YES, 'data-false' => Backup::READ_NO, 'data-cntlabel' => 'backup')); }),
  Restful\Column::create ('ID')->setWidth (60)->setSort ('id')->setTd (function ($obj) { return $obj->id; }),
  Restful\Column::create ('狀態')->setWidth (100)->setTd (function ($obj) { return $obj->status != Backup::STATUS_SUCCESS ? '<b style="color: red">' . Backup::$statusNames[$obj->status] . '</b>' : Backup::$statusNames[$obj->status]; }),
  Restful\Column::create ('類型')->setWidth (100)->setTd (function ($obj) { return Backup::$typeNames[$obj->type]; }),
  Restful\Column::create ('大小')->setWidth (100)->setTd (function ($obj) { return byte_format ($obj->size); }),
  Restful\Column::create ('檔案')->setTd (function ($obj) { return $obj->file->link ('備份檔', array ('target' => '_blank')); }),
  Restful\Column::create ('時間')->setWidth (155)->setTd (function ($obj) { return $obj->time_at->format ('Y-m-d H:i:s'); }),
  Restful\Column::create ('建立時間')->setWidth (155)->setClass ('right')->setTd (function ($obj) { return $obj->created_at->format ('Y-m-d H:i:s'); })
  );
?>
</div>

<div class='pagination'><div><?php echo $pagination;?></div></div>
