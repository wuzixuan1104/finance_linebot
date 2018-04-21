<?php echo $search; ?>

<div class='panel'>
<?php echo $search->setTableClomuns (
  Restful\Column::create ('ID')->setWidth (50)->setSort ('id')->setTd (function ($obj) { return $obj->id; }),
  Restful\Column::create ('會員名稱')->setWidth (100)->setTd (function ($obj) { return $obj->user->name; }),
  Restful\Column::create ('會員信箱')->setWidth (200)->setTd (function ($obj) { return $obj->user->email; }),
  Restful\Column::create ('名稱')->setTd (function ($obj) { return $obj->name; }),
  Restful\Column::create ('銀行分行')->setWidth (150)->setTd (function ($obj) { return $obj->bank_branch; }),
  Restful\Column::create ('銀行帳戶')->setWidth (150)->setTd (function ($obj) { return $obj->bank_account; }),
  Restful\Column::create ('電話')->setWidth (150)->setTd (function ($obj) { return $obj->phone; }),
  Restful\EditColumn::create ('編輯')->setTd (function ($obj, $column) {
    return $column->addLink (RestfulUrl::show ($obj), array ('class' =>'icon-29'));
    })
  );
?>
</div>

<div class='pagination'><div><?php echo $pagination;?></div></div>
