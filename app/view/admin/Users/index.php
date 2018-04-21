<?php echo $search; ?>

<div class='panel'>
<?php echo $search->setTableClomuns (
  Restful\Column::create ('驗證')->setWidth (15)->setClass ('center')->setTd (function ($obj, $column) { return $column->setSwitch ($obj->active == User::ACTIVE_ON, array ('class' => 'switch ajax', 'data-column' => 'active', 'data-url' => RestfulURL::url ('admin/Users@active', $obj), 'data-true' => User::ACTIVE_ON, 'data-false' => User::ACTIVE_OFF)); }),
  //
  Restful\Column::create ('ID')->setWidth (15)->setSort ('id')->setTd (function ($obj) { return $obj->id; }),
  Restful\Column::create ('名稱')->setWidth (30)->setTd (function ($obj) { return $obj->name; }),
  Restful\Column::create ('信箱')->setWidth (80)->setSort ('id')->setTd (function ($obj) { return $obj->email; }),
  Restful\Column::create ('電話')->setWidth (50)->setTd (function ($obj) { return $obj->phone; }),

  Restful\EditColumn::create ('編輯')->setTd (function ($obj, $column) {
    return $column->addLink (RestfulUrl::show ($obj), array ('class' =>'icon-29')); })
);
?>
</div>

<div class='pagination'><div><?php echo $pagination;?></div></div>
