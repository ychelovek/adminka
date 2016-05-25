<form method="post">
<div class="block" style="width: 100%;">
		
		<div class="navbar navbar-inner block-header">
		<input type="submit" value="Сохранить" class="btn btn-success">
	        <div class="muted pull-left">Добавить таблицы&nbsp;</div>
	    </div>

	    <div class="block-content collapse in">
	    		<label class="span4">Выберите таблицы</label>
	    		<div style="clear:both;">&nbsp;</div>
				<!-- Выводим все таблицы БД -->
				
				<?php foreach ($tables as $key => $table) : ?>
					<label  style="width: 25%; float:left;"><input type="checkbox" name="tables[]" value="<?=$key ?>" <?php if (in_array($key, $tables_config)) echo 'checked'?>> <?=$key ?></label>
				<?php endforeach; ?>
		</div>

			<div class="navbar navbar-inner block-header">
				<div class="muted pull-left">Несуществующие таблицы&nbsp;(Рекомендуется отменить)</div>
			</div>
			<div class="block-content collapse in">
				<?php foreach ($tables_config as $key => $table) : ?>
					<?php if ( ! array_key_exists($table, $tables) ) : ?>
						<label  style="width: 25%; float:left; color: red;"><input type="checkbox" name="tables[]" value="<?=$table ?>" checked> <?=$table ?></label>
					<?php endif; ?>
				<?php endforeach; ?>
				
				
			</div>
</div>
		
		<input type="submit" value="Сохранить" class="btn btn-success">
		<input type="hidden" name="update_table" value="1">
</form>
