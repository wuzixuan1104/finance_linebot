<?php echo $search; ?>

<div class='panel'>
<?php echo $search->setTableClomuns (
  Restful\Column::create ('ID')->setWidth (20)->setSort ('id')->setTd (function ($obj) { return $obj->id; }),
  Restful\Column::create ('廣告標題')->setWidth (60)->setTd (function ($obj) { return $obj->adv->title; }),
  Restful\Column::create ('會員名稱')->setWidth (60)->setTd (function ($obj) { return $obj->user->name; }),
  Restful\Column::create ('會員信箱')->setWidth (60)->setTd (function ($obj) { return $obj->user->email; }),
  Restful\Column::create ('內容')->setWidth (150)->setTd (function ($obj) { return $obj->content; }),
  Restful\Column::create ('建立時間')->setWidth (200)->setTd (function ($obj) { return $obj->created_at; }),

  Restful\EditColumn::create ('編輯')->setTd (function ($obj, $column) {
    return $column->addDeleteLink (RestfulUrl::destroy ($obj))
                  ->addLink (RestfulUrl::show ($obj), array ('class' =>'icon-29'));
    })
  );
?>
</div>

<div class='pagination'><div><?php echo $pagination;?></div></div>
