                   <div class="span8" id="content">
                     <div class="row-fluid">
                       
                        <div class="block" style="width: auto; position: absolute; clear: both;">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left"><?php echo $table_title ?></div>
                            </div>
                            <div class="block-content" style="width: 95%; min-width: 950px;">
                                <div class="span12">
                                <form class="pull-left span12" style="margin-bottom: 0px;" type="get">
                                  <input type="text" class="pull-left" style="margin-bottom: 0px;" name="query" placeholder="Введите запрос">
                                  <select class="pull-left" style="margin-bottom: 0px; margin-left: 10px;" name="field">
                                      <?php foreach ($keys as $key => $value) : ?>
                                          <option value="<?php echo $value; ?>"><?php echo $keys_name[$key]; ?></option>
                                      <?php endforeach; ?>
                                  </select>
                                  <a type"button" href="<?php echo url::base(); ?>pages/edit/<?=$this_table ?><?php echo !empty($link) ? '?link=' . $link : ''?>" class="btn btn-success" style="margin-left: 10px;">Добавить <i class="icon-plus icon-white"></i></a>
                                  <button type="submit" class="btn btn-primary" style="margin-left: 6px;">Найти</button>
                                  <label class="checkbox">
                                  <input type="checkbox" name="strict"> Точное совпадение
                                  </label>
                                </form>
                                    <table class="table table-striped table-bordered dataTable">
                                      <thead>
                                        <tr>
                                        <!--<th>PRI</th>-->
                                        <?php foreach ($keys_name as $k => $value): ?>
                                          <th class="sorting <?php if ($order == $keys[$k]) : ?>current_sort<?php endif; ?>" role="columnheader" style="white-space: nowrap;">
                                            <?php
                                            $build_page = $build_query;
                                            if ($order == $keys[$k]) {
                                                if ($order_type == 'asc') {
                                            $build_page['sorttype'] = 'desc';
                                            $s_class = 'icon-chevron-down';
                                                } else {
                                                    $build_page['sorttype'] = 'asc';
                                                    $s_class = 'icon-chevron-up';
                                                }
                                            } else {
                                                $build_page['sorttype'] = 'asc';
                                                $s_class = NULL;
                                            }
                                            $build_page['sort'] = $keys[$k];
                                            $url_sort = http_build_query($build_page);
                                            ?>
                                            <a href="?<?php echo $url_sort; ?>"><?php echo $value; ?></a>
                                            <span class="<?php echo $s_class; ?>"></span>
                                          </th>
                                        <?php endforeach; ?>
                                        <th></th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php foreach ($tablica as $key => $value): ?>
                                            <tr>
                                                <?php foreach ($value as $key2 => $value2): ?>
                                                    <?php if ($key2 == 'primary_key') continue; ?>
                                                    <td>
                                                    <div style="display: block; height:85px; max-width: 300px; overflow: hidden;">
                                                <?php
                                                // if (array_key_exists($this_t[$key2][0], $arr_type))
                                                // {
                                                //     //echo str_replace('**', $value2, $arr_type[$this_t[$key2][0]]);
                                                // }
                                                //print_r($key2);
                                                echo htmlspecialchars($value2);
                                                ?>
                                                </div>
                                                    </td>
                                                 <?php endforeach; ?>
                                                  <td><a href="<?php echo url::base(); ?>pages/edit/<?=$this_table ?>/<?=$value->primary_key ?><?php echo !empty($link) ? '?link=' . $link : ''?>" class="icon-edit"></a></td>
                                            </tr>     
                                        <?php endforeach; ?>
                                      </tbody>
                                    </table>
                                    <a type"button" href="<?php echo url::base(); ?>pages/edit/<?=$this_table ?><?php echo !empty($link) ? '?link=' . $link : ''?>" class="btn btn-success" style="margin-left: 10px;">Добавить <i class="icon-plus icon-white"></i></a>
                                    <div class="span12" align="center"><div class="btn-group">
                                    <?php 

            $prev_p =($page>1) ? TRUE : FALSE;
            $next_p =($page!=$num_pages) && ($num_pages!=0) ? TRUE : FALSE;

            
                if($prev_p)
                {
                    $build_query['page'] = $page - 1;
                    $url_prev = http_build_query($build_query);
                    $build_query['page'] = 1;
                    $url_first = http_build_query($build_query);
                    echo '
                    <a href="?'.$url_first.'" class="btn btn-default">начало</a>
                    <a href="?'.$url_prev.'" class="btn btn-default">&larr; предыдущая</a>
                    ';
                }
                else
                {
                    echo '
                    <a href="#" class="btn btn-default disabled">начало</a>
                    <a href="#" class="btn btn-default disabled">&larr; предыдущая</a>
                    ';
                }

            echo '<a class="btn btn-default disabled">'.$page.'</a>';

                if($next_p)
                {
                    $build_query['page'] = $page + 1;
                    $url_next = http_build_query($build_query);
                    $build_query['page'] = $num_pages;
                    $url_end = http_build_query($build_query);
                    echo '
                    <a href="?'.$url_next.'" class="btn btn-default">следующая &rarr;</a>
                    <a href="?'.$url_end.'" class="btn btn-default">конец</a>';
                }
                else
                {
                    echo '
                    <a class="btn btn-default disabled">следующая &rarr;</a>
                    <a class="btn btn-default disabled">конец</a>
                    ';
                }
            

            ?>
                                </div><br><br></div> </div>
                            </div>
                            <div style="clear: both;"></div>
                        </div>
                       
                    </div>
                    </div>