<?php echo $search; ?>

<div class='panel'>
<?php echo $search->setTableClomuns (
  Restful\Column::create ('ID')->setWidth (50)->setSort ('id')->setTd (function ($obj) { return $obj->id; }),
  Restful\Column::create ('廣告名稱')->setWidth (100)->setTd (function ($obj) { return ($obj->bonus->adv) ? $obj->bonus->adv->title : ''; }),
  Restful\Column::create ('會員名稱')->setWidth (100)->setTd (function ($obj) { return ($obj->bonus_receive->user) ? $obj->bonus_receive->user->name : ''; }),
  Restful\Column::create ('會員信箱')->setWidth (100)->setTd (function ($obj) { return ($obj->bonus_receive->user) ? $obj->bonus_receive->user->email : ''; }),
  Restful\Column::create ('帳戶名稱')->setWidth (100)->setTd (function ($obj) { return ($obj->bonus_receive->account) ? $obj->bonus_receive->account->name : ''; }),
  Restful\Column::create ('剩餘可領取')->setWidth (50)->setTd (function ($obj) { return ($obj->bonus) ? $obj->bonus->remain_price : ''; }),
  Restful\Column::create ('領取方式')->setWidth (50)->setTd (function ($obj) { return ($obj->bonus_receive) ? BonusReceive::$typeTexts[$obj->bonus_receive->type] : ''; }),
  Restful\Column::create ('領取金額')->setWidth (50)->setTd (function ($obj) { return $obj->price; })
  );
?>
</div>

<div class='pagination'><div><?php echo $pagination;?></div></div>
