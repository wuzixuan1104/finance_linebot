<?php echo $search; ?>

<div class='panel'>
<?php echo $search->setTableClomuns (
  Restful\Column::create ('ID')->setWidth (50)->setSort ('id')->setTd (function ($obj) { return $obj->id; }),
  Restful\Column::create ('品牌名稱')->setWidth (200)->setTd (function ($obj) { return $obj->brand->name; }),
  Restful\Column::create ('商品名稱')->setWidth (200)->setTd (function ($obj) { return $obj->name; }),
  Restful\Column::create ('拍攝規則')->setTd (function ($obj) { return $obj->rule; }),
  Restful\Column::create ('描述')->setWidth (150)->setTd (function ($obj) { return $obj->description; }),
  Restful\Column::create ('拍攝次數')->setWidth (80)->setTd (function ($obj) { return $obj->cnt_shoot; }),
  Restful\EditColumn::create ('編輯')->setTd (function ($obj, $column) {
    return $column->addLink (RestfulUrl::show ($obj), array ('class' =>'icon-29'));
    })
  );
?>
</div>

<div class='pagination'><div><?php echo $pagination;?></div></div>
