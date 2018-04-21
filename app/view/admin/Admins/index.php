<?php echo $search; ?>

<div class='panel'>
<?php echo $search->setTableClomuns (

  Restful\Column::create ('ID')->setWidth (15)->setSort ('id')->setTd (function ($obj) { return $obj->id; }),
  Restful\Column::create ('名稱')->setWidth (30)->setTd (function ($obj) { return $obj->name; }),
  Restful\Column::create ('帳號')->setWidth (80)->setSort ('id')->setTd (function ($obj) { return $obj->account; }),

  Restful\EditColumn::create ('編輯')->setTd (function ($obj, $column) {
    return $column->addDeleteLink (RestfulURL::destroy ($obj))
                  ->addEditLink (RestfulURL::edit ($obj))
                  ->addLink (RestfulURL::url ('admin/Admins@index', $obj), array ('class' =>'icon-29')); }));
?>
</div>

<div class='pagination'><div><?php echo $pagination;?></div></div>
