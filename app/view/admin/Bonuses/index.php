<?php echo $search; ?>

<div class='panel'>
<?php echo $search->setTableClomuns (
  Restful\Column::create ('ID')->setWidth (50)->setSort ('id')->setTd (function ($obj) { return $obj->id; }),
  Restful\Column::create ('廣告標題')->setWidth (60)->setTd (function ($obj) { return '<a href="' . RestfulUrl::url ('admin/Advs@index', $obj) . '?_q0=' . ($obj->adv ? $obj->adv->id : '') . '">' . ($obj->adv ? $obj->adv->title : '') . '</a>'; }),
  Restful\Column::create ('會員名稱')->setWidth (150)->setTd (function ($obj) { return $obj->user->name; }),
  Restful\Column::create ('會員信箱')->setWidth (150)->setTd (function ($obj) { return $obj->user->email; }),
  Restful\Column::create ('金額')->setWidth (100)->setTd (function ($obj) { return $obj->price; }),
  Restful\Column::create ('剩餘金額')->setWidth (200)->setTd (function ($obj) { return $obj->remain_price; })
  );
?>
</div>

<div class='pagination'><div><?php echo $pagination;?></div></div>
