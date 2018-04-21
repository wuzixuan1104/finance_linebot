<?php echo $search; ?>

<div class='panel'>
<?php echo $search->setTableClomuns (
  Restful\Column::create ('領取')->setWidth (60)->setClass ('center')->setTd (function ($obj, $column) { return $column->setSwitch ($obj->is_receive == BonusReceive::RECEIVE_YES, array ('class' => 'switch ajax', 'data-column' => 'is_receive', 'data-url' => RestfulURL::url ('admin/BonusReceives@receive', $obj), 'data-true' => BonusReceive::RECEIVE_YES, 'data-false' => BonusReceive::RECEIVE_NO)); }),
  Restful\Column::create ('ID')->setWidth (50)->setSort ('id')->setTd (function ($obj) { return $obj->id; }),
  Restful\Column::create ('會員名稱')->setWidth (60)->setTd (function ($obj) { return $obj->user->name; }),
  Restful\Column::create ('會員信箱')->setWidth (150)->setTd (function ($obj) { return $obj->user->email; }),
  Restful\Column::create ('帳戶名稱')->setWidth (100)->setTd (function ($obj) { return $obj->account->name; }),
  Restful\Column::create ('付款類型')->setWidth (200)->setTd (function ($obj) { return BonusReceive::$typeTexts[$obj->type]; }),
  Restful\Column::create ('金額')->setWidth (200)->setTd (function ($obj) { return $obj->price; }),
  Restful\EditColumn::create ('編輯')->setTd (function ($obj, $column) {
    return $column->addLink (RestfulUrl::show ($obj), array ('class' =>'icon-29'));
    })
  );
?>
</div>

<div class='pagination'><div><?php echo $pagination;?></div></div>
