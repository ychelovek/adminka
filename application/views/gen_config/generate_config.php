<form method="post">
<div class="block" style="width: 100%;">
		
		<div class="navbar navbar-inner block-header">
		<input type="submit" value="Сохранить" class="btn btn-success">
		<a href="?add_tables" class="btn">Добавить / Изменить таблицы</a>
	        <div class="muted pull-left"><?php echo $from; ?>&nbsp;</div>
	        <!-- <div class="muted pull-right"><span style="color: red;">Поля отмеченные флажком будут удалены</span></div> -->

	    </div>
		<div class="navbar navbar-inner block-header">
	        <div class="muted pull-left">Путь файлов по умолчанию:</div>
	    </div>
	    <div class="block-content collapse in">
	        <div class="span12">
	        	<input type="text" name="my_config[filepath]" value="<?php echo $filepath; ?>" placeholder="Путь файлов по умолчанию">
	        </div>
	    </div>

	    <div class="block-content collapse in">
				<!-- Выводим параметры таблиц -->
				<?php foreach ($content['tables'] as $name => $params) : ?>
					<div class="span12" style="margin-left: 0px;">
					<!-- Выводим названия таблиц -->
						<div class="navbar navbar-inner block-header">
							<div class="muted pull-left">Таблица <?php echo $name; ?></div> 
							<!-- <div class="muted pull-right"><label>Удалить <input type="checkbox" value="<?=$name ?>" name="del_tables[]"  style="vertical-align: top;"></label></div> -->
						</div>
						<div class="span12" style="padding-top: 10px; overflow: scroll;">
							<!-- <legend class="span11">Параметры</legend> -->
							<div class="span3">
								<div class="span1" style="display: none;"></div>
								<div class="span12">
									<label class="span4">
										Рус. название: 
									</label>
									<div class="span8">
										<input type="text" name="tables[<?php echo $name; ?>][title]" value="<?php echo $content['tables'][$name]['title']; ?>">
									</div>
								</div>
								<div class="span12">
									<label class="span4">
										Сортировка:
									</label>
									<div class="span8">
										<input type="text" name="tables[<?php echo $name; ?>][order]" value="<?php echo $content['tables'][$name]['order']; ?>">
									</div>
								</div>
								<div class="span12">
									<label class="span4">
										Использовать:
									</label>
									<div class="span8">
										<select name="tables[<?php echo $name; ?>][visible]">
											<option value="1" <?php if ($content['tables'][$name]['visible'] == 1) echo 'selected' ?>>Да</option>
											<option value="0" <?php if ($content['tables'][$name]['visible'] == 0) echo 'selected' ?>>Нет</option>
										</select>
									</div>
								</div>
							</div>
							<div class="span7">
								<textarea class="span7" placeholder="Связи" name="tables[<?php echo $name; ?>][relations]" style="height: 115px"><?php foreach ($content['tables'][$name]['relations'] as $key => $value) : ?><?php echo $value . "\r\n" ?><?php endforeach; ?></textarea>
							</div>
								<!-- <legend class="span11" style="margin-top: 50px;">Поля</legend> -->
								<table>
									<tr>
										<?php foreach ($content['tables'][$name]['fields'] as $field_name => $field_arr) : ?>
											<td width="150" style="width: 150px; min-width: 150px; max-width: 150px;">
												<label class="span12"

												<?php if (! in_array($field_name, $arrRealTables[$name])) : ?>
													style= "color: red;" title="Поле не существует. Рекомендуется - удалить"
												<?php endif; ?>

												><b><?php echo $field_name; ?></b> 
													
												</label>
												<?php foreach ($content['tables'][$name]['fields'][$field_name] as $k => $field_param) : ?>

													<?php if ($k == 'visible') { ?>
														<select class="span8" name="tables[<?php echo $name; ?>][fields][<?php echo $field_name?>][<?php echo $k; ?>]">
															<option value="1" <?php if ($field_param == 1) echo 'selected' ?>>Выводить при просмотре</option>
															<option value="0" <?php if ($field_param == 0) echo 'selected' ?>>Не выводить при просмотре</option>
														</select>
													<?php } elseif ($k=='enabled') { ?>
														<select class="span8" name="tables[<?php echo $name; ?>][fields][<?php echo $field_name?>][<?php echo $k; ?>]">
															<option value="1" <?php if ($field_param == 1) echo 'selected' ?>>Выводить при редактировании</option>
															<option value="0" <?php if ($field_param == 0) echo 'selected' ?>>Не выводить при редактировании</option>
														</select>
													<?php } elseif ($k=='type') { ?>
														<select title="<?php echo $k; ?>" class="span8" name="tables[<?php echo $name; ?>][fields][<?php echo $field_name?>][<?php echo $k; ?>]">
															<?php	foreach ($arr_fields_types as $key_type => $typ) : ?>
																<?php if ($typ == $content['tables'][$name]['fields'][$field_name][$k]) {$sel = ' selected'; } else {$sel = '';} ?> 
																<option value="<?php echo $typ ?>" <?php echo $sel ?>><?php echo $typ ?></option>
															<?php endforeach; ?>
														</select>
													<?php } else { ?>
														<input title="<?php echo $k; ?>" class="span8" type="text" name="tables[<?php echo $name; ?>][fields][<?php echo $field_name?>][<?php echo $k; ?>]" value="<?php echo $field_param; ?>" placeholder="<?php echo $k; ?>">
													<?php } ?>
												<?php endforeach; ?>
												<?php if (! in_array($field_name, $arrRealTables[$name])) : ?>
													<input type="button" onclick="$(this).parent().remove()" class="span8 btn btn-danger" value="Удалить" title="Поле не существует. Нажмите что бы удалить.">
												<?php else: ?>
													<input type="button" onclick="$(this).parent().remove()" class="span8 btn" value="Удалить" disabled>
												<?php endif; ?>
											</td>
										<?php endforeach; ?>
									</tr>
								</table>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
</div>
		<input type="submit" value="Сохранить" class="btn btn-success">
</form>
