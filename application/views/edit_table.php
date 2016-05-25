                    <div class="span10" id="content">
                    <div class="row-fluid">   
                    <div class="row-fluid">
                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                            <?php if ($this_id == 0) : ?>
                                <div class="muted pull-left">Добавление в таблицу &laquo;<?php echo $table_title; ?>&raquo;</div>
                            <?php else : ?>
                                <div class="muted pull-left span12">Редактирование таблицы <?php echo $table_title . '->' . $this_id ?>
                                <span class="pull-right">
                                    Связанные таблицы: 
                                    <?php
                                    foreach ($relations_pages as $key => $value) : ?>
                                        <a href="<?php echo $value; ?>" target="_blank"><?php echo $relations_titles[$key]; ?></a> | 
                                    <?php endforeach; ?>
                                </span>
                                </div>
                            <?php endif; ?>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                                    <div class="span12">
                                        <form method="post" <?php if(in_array('file', $types)) echo 'enctype="multipart/form-data"'; ?>>
                                        
                                        <?php if (! empty($success) ): ?>
                                            <div class="alert alert-success bs-alert-old-docs">
                                                <?php echo $success; ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php 
                                            foreach ($labels as $name => $label): ?>
                                            <?php if ($enabled[$name] == 1) : ?>
                                                <div>
                                                    <label class="span3">
                                                        <?php echo $label ?>
                                                    </label>
                                                    <div class="span9">
                                                        <?php echo $values[$name] ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>

                                        <button type="submit" class="btn btn-success">Сохранить</button>
                                        <?php if($this_id!=0): ?> <a type"button" href="<?php echo url::base(); ?>pages/delete/<?php echo $this_table . '/' . $this_id ?>" 
onClick="return window.confirm('Подтвердите удаление')"
                                            class="btn btn-danger">Удалить</a><?php endif; ?>
                                       
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /block -->
                    </div>
                    </div>
                    </div>


                    <!-- SELECT COLUMNS FROM `table` WHERE `key`=’pri’ -->
<!-- <script src="//cdn.tinymce.com/4/tinymce.min.js"></script> -->
<script src="/admin/media/editor/js/tinymce.min.js"></script>
  <script>
  tinymce.init({ 
    selector:'textarea.editor',
    plugins : 'code advlist autolink link image lists charmap preview anchor media table contextmenu paste jbimages',
   	toolbar1: 'bold italic | fontsizeselect fontselect | alignleft aligncenter alignright | bullist numlist | code', //styleselect
    toolbar2: 'link image jbimages | preview',
    language: 'ru',
    height: 350,
    menubar: false,
    relative_urls : false,
remove_script_host : true
    });
  </script>