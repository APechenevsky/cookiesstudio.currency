<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>
<div class="currency-list">
	<table>
		<tr>
			<th>Код</th>
			<th>Дата</th>
			<th>Курс</th>
		</tr>
		<?php foreach($arResult["0"] as $arItem):?>
			<tr>
				<td><?=$arItem["CODE"];?></td>
				<td><?=$arItem["DATE"]->format('d-m-Y');?></td>
				<td><?=$arItem["COURSE"];?></td>
			</tr>
		<?php endforeach;?>
	</table>
</div>
<?php
$APPLICATION->IncludeComponent(
   "bitrix:main.pagenavigation",
   "",
   array(
      "NAV_OBJECT" => $arResult["NAV"],
      "SEF_MODE" => "N",
   ),
   false
);
?>