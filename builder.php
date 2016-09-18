<?php

    require_once( 'dbconfig.php' );
    $table = isset( $_GET['table'] ) ? $_GET['table'] : '';
    $result = mysqli_query( $conn, "select * from $table limit 1" );
    $data = mysqli_fetch_assoc( $result );
    // print_r( $data );
    $field_list = array_keys( $data );
    # khoi tao cac bien
    $field_value = isset( $_POST['field_value'] ) ? $_POST['field_value'] : array_fill( 0, count( $field_list ), '' );
    $kind_data = isset( $_POST['kind_data'] ) ? $_POST['kind_data'] : array_fill( 0, count( $field_list ), '' );
    $kind_view = isset( $_POST['kind_view'] ) ? $_POST['kind_view'] : array_fill( 0, count( $field_list ), '' );
    // print_r( $data );
    $info = array();
    if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
        
        foreach( $field_list as $key => $list ) {

            array_push( $info, array(

                                    'table'         => $table,
                                    'field_name'    => $list,
                                    'field_value'   => $field_value[$key],
                                    'kind_data'     => $kind_data[$key],
                                    'kind_view'     => $kind_view[$key]
                                )
                        );
        }

        if ( isset( $_POST['new'] ) ) {

            $file_name = str_replace( 'rp_', '', $table ) ."_new.php";
            # tạo file new
            newModel( $info );
            modelBuilder( 'model_new.php', $file_name );
            exit;
        }
        elseif ( isset( $_POST['list'] ) ) {

            $file_name = str_replace( 'rp_', '', $table ) ."_list.php";
            listModel( $info );
            modelBuilder( 'model_list.php', $file_name );
            exit;
        }
        else {

            $file_name = str_replace( 'rp_', '', $table ) ."_edit.php";
            editModel( $info );
            modelBuilder( 'model_edit.php', $file_name );
            exit;
        }        

        // print_r( $info );
    }
?>
<meta charset="UTF-8" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/css/bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/js/bootstrap.min.js"></script>
<div class="row">
    <div class="col-xs-2"></div>
    <div class="col-xs-8">
        <div class="panel panel-default">
            <div class="panel-header">Tên bảng: <strong><?= $table ?></strong></div>
            <div class="panel-body">
                <form action="" method="post">
                    <table class="table table-striped">
                        <tr>
                            <th style="text-align: right; ">Tên Field</th>
                            <th style="text-align: center; ">Giá trị</th>
                            <th>Kiểu dữ liệu</th>
                            <th>Kiểu hiển thị</th>
                        </tr>
                        <?php foreach( $field_list as $key => $fields ) : ?>
                        <tr>
                            <td style="text-align: right; "><?= $fields ?>:</td>
                            <td style="text-align: right; "><input type="text" name="field_value[]" class="form-control" value="<?= $field_value[$key] ?>" /></td>
                            <td>
                                <select name="kind_data[]" id="" class="form-control">
                                    <option value="chuoi" <?php if ( $kind_data[$key] == 'chuoi' ) echo 'selected'; ?> >chuỗi</option>
                                    <option value="so" <?php if ( $kind_data[$key] == 'so' ) echo 'selected'; ?> >số</option>
                                </select>
                            </td>
                            <td>
                                <select name="kind_view[]" id="" class="form-control">
                                    <option value="input" <?php if ( $kind_view[$key] == 'input' ) echo 'selected'; ?> >Input</option>
                                    <option value="textarea" <?php if ( $kind_view[$key] == 'textarea' ) echo 'selected'; ?> >Textarea</option>
                                    <option value="select" <?php if ( $kind_view[$key] == 'select' ) echo 'selected'; ?> >Select</option>
                                    <option value="datepicker" <?php if ( $kind_view[$key] == 'datepicker' ) echo 'selected'; ?> >Datepicker</option>
                                </select>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="4">
                                <input type="submit" name="new" class="btn btn-primary" value="Builder New" />
                                <input type="submit" name="list" class="btn btn-primary" value="Builder List" />
                                <input type="submit" name="edit" class="btn btn-primary" value="Builder Edit" />
                            </td>
                            
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
    <div class="col-xs-2"></div>
</div>

<?php

    function modelBuilder( $model_file, $file ) {

        if (file_exists($model_file)) {

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($model_file));
            readfile($model_file);
            exit;
        }
    }
    function newModel( $table_info ) {

        $new = fopen( 'model_new.php', 'w' );
        $content = '';
        $content_post = '';
        foreach( $table_info as $key => $info ) {
                
            $content .= '$'. $info['field_name'] .' = \'\';';
            $content .= "\n\t";
            
        }

        $content .= "# kiem tra loi \n\t";
        $content .= '$error = array(' . "\n\t\t\t";
        foreach( $table_info as $key => $info ) {
                
            $content .= '\'is_'. $info['field_name'] .'\''.' => 0,'. "\n\t\t\t";            
        }
        $content .= ');';
        // print_r( $content );
        $content = 
'<?php

    # new model

    # khoi tao cac bien
    '. $content .'
    if ( $_SERVER[\'REQUEST_METHOD\'] == \'POST\' ) {

        ';
    
        foreach( $table_info as $key => $info ) {
                
            $content_post .= '$'. $info['field_name'] .' = isset( $_POST[\''. $info['field_name'] .'\'] ) ? $_POST[\''. $info['field_name'] .'\'] : \'\';';
            $content_post .= "\n\t\t";
            
        }

        $content .= $content_post ."\n\t\t# validate data\n\t\t";
        $content_post = '';
        foreach ($table_info as $key => $info) {
            
            $content_post .= "if ( $". $info['field_name'] ." == '' ) {\n\t\t\t";
            $content_post .= '$error[\'is_'. $info['field_name'] .'\'] = 1;'. "\n\t\t}\n\t\t";
        }
        $content .= $content_post;
        $content .= "# insert vao database\n\t\t";
        $content .= '$error_values = array_count_values( $error );'. "\n\t\t";
        $content .= 'if ( !isset( $error_values[1] ) ) {'. "\n\t\t\t";
        $content .= '$result = $wpdb->insert('. "\n\t\t\t\t\t\t\t";
        $content .= '\''. $table_info[0]['table'] .'\''.",\n\t\t\t\t\t\t\t";
        $content .= 'array('. "\n\t\t\t\t\t\t\t";
        $content_post = '';
        foreach ($table_info as $key => $info) {
            
            $content_post .= '\''. $info['field_name'] .'\' => $'. $info['field_name'] .",\n\t\t\t\t\t\t\t";
        }
        $content .= $content_post .')'."\n\t\t\t\t\t\t);\n\t\t\t";
        $content .= 'if ( $result ) {'. "\n\t\t\t\t";
        $content .= 'echo "<script>alert(\'Them moi thanh cong!\');";'. "\n\t\t\t\t";
        $content .= 'echo "window.location = \'". home_url( \'?mod='. str_replace( 'rp_', '', $table_info[0]['table'] ) .'_list\' ) ."\';</script>";'. "\n\t\t\t}";
        $content .= "\n\t\t}\n\t}\n?>\n";
        $content .= '<?php require_once(\'khachhang_breadcrumb.php\'); ?>'. "\n";
        $content .= '<section class="content">'. "\n\t";
        $content .= '<div class="box box-default">'. "\n\t\t";
        $content .= '<div class="box-header with-border">'. "\n\t\t\t";
        $content .= '<h3 class="box-title"></h3>'. "\n\t\t";
        $content .= '</div>'."\n\t\t".'<div class="box-body">'. "\n\t\t\t";
        $content .= '<form action="" method="post">'."\n\t\t\t\t";
        $content .= '<div class="panel panel-default">'. "\n\t\t\t\t\t";
        $content .= '<div class="panel-body">'. "\n\t\t\t\t\t\t";

        $content_post = '';
        foreach ( $table_info as $key => $info ) {
            
            $content_post .= '<div class="form-group">'. "\n\t\t\t\t\t\t\t";
            $content_post .= '<label for="">'. $info['field_value'] .'</label>'. "\n\t\t\t\t\t\t\t";
            if ( $info['kind_view'] == 'select' ) {
                $content_post .= '<select name="'. $info['field_name'] .'" id="" class="form-control">'. "\n\t\t\t\t\t\t\t\t";
                $content_post .= '<option value="" <?php if ( $'. $info['field_name'] .' == "" ) echo "selected"; ?> ></option>'. "\n\t\t\t\t\t\t\t";
                $content_post .= '</select>'. "\n\t\t\t\t\t\t";
            }
            elseif ( $info['kind_view'] == 'input' && $info['kind_data'] == 'so' ) {
                $content_post .= '<?php if ( $error[\'is_'. $info['field_name'] .'\'] ) echo "<span style=\'color: red; \'>(*) Tên '. $info['field_value'] .' không được rỗng</span>"; ?>'. "\n\t\t\t\t\t\t\t";
                $content_post .= '<input name="'. $info['field_name'] .'" class="form-control" type="number" value="<?= $'. $info['field_name'] .' ?>" />'. "\n\t\t\t\t\t\t\t\t";
                $content_post .= "\n\t\t\t\t\t\t";
            }
            elseif ( $info['kind_view'] == 'input' && $info['kind_data'] == 'chuoi' ) {
                $content_post .= '<?php if ( $error[\'is_'. $info['field_name'] .'\'] ) echo "<span style=\'color: red; \'>(*) Tên '. $info['field_value'] .' không được rỗng</span>"; ?>'. "\n\t\t\t\t\t\t\t";
                $content_post .= '<input name="'. $info['field_name'] .'" class="form-control" type="text" value="<?= $'. $info['field_name'] .' ?>" />'. "\n\t\t\t\t\t\t\t\t";
                $content_post .= "\n\t\t\t\t\t\t";
            }
            elseif ( $info['kind_view'] == 'datepicker' ) {
                $content_post .= '<?php if ( $error[\'is_'. $info['field_name'] .'\'] ) echo "<span style=\'color: red; \'>(*) Tên '. $info['field_value'] .' không được rỗng</span>"; ?>'. "\n\t\t\t\t\t\t\t";
                $content_post .= '<input name="'. $info['field_name'] .'" class="form-control datepicker" type="text" value="<?= $'. $info['field_name'] .' ?>" />'. "\n\t\t\t\t\t\t\t\t";
                $content_post .= "\n\t\t\t\t\t\t";
            }
            elseif ( $info['kind_view'] == 'textarea' ) {
                $content_post .= '<?php if ( $error[\'is_'. $info['field_name'] .'\'] ) echo "<span style=\'color: red; \'>(*) Tên '. $info['field_value'] .' không được rỗng</span>"; ?>'. "\n\t\t\t\t\t\t\t";
                $content_post .= '<textarea name="'. $info['field_name'] .'" class="form-control" cols="60" rows="5" ></textarea>'. "\n\t\t\t\t\t\t\t\t";
                $content_post .= "\n\t\t\t\t\t\t";
            }
            $content_post .= "</div>\n\t\t\t\t\t\t";
        }
        $content .= $content_post ."\n\t\t\t\t\t</div>";
        $content .= "\n\t\t\t\t</div>\n\t\t\t\t". '<input type="submit" class="btn btn-primary" value="Thêm vào" />' ."\n\t\t\t</form>\n\t\t</div>\n\t";
        $content .= "</div>\n</section>\n";
        fwrite( $new, $content );
        fclose( $new );
    }

    function listModel( $table_info ) {

        $list = fopen( 'model_list.php', 'w' );
        $content = "<?php\n\n\t#list model\n\n\t";
        $content .= 'global $wpdb;'."\n\n\t";
        $content .= '$sql_search = "";'."\n\t".'$search_value = "";'."\n\n\t";
        $content .= 'if ( isset( $_POST[\'search\'] ) ) {'."\n\n\t\t";
        $content .= '$search_value = isset( $_POST[\'search_value\'] ) ? $_POST[\'search_value\'] : \'\';'."\n\t\t";
        $content .= 'if ( $search_value != \'\' ) {'."\n\n\t\t\t";
        $content .= '$sql_search = "'. "\n\t\t\t(\n\t\t\t\t";

        $content_post = '';
        for ( $i=0; $i < count( $table_info ) - 1; $i++ ) {
            
            $content_post .= $table_info[$i]['field_name'] .' like \'%". $search_value ."%\' or'. "\n\t\t\t\t";
        }
        $content_post .= $content_post . $table_info[ count( $table_info ) - 1 ]['field_name'] .' like \'%". $search_value ."%\''. "\n\t\t\t) and\n\t\t\t".'";';
        $content .= $content_post ."\n\t\t}\n\t}\n\t";
        $content .= '$totalposts = $wpdb->get_row( "select count(*) as \'total\' from '. $table_info[0]['table'] .' where $sql_search 1", OBJECT );';
        $content .= "\n\t".'$ppp = 5;'."\n\t";
        $content .= '$on_page = isset( $_REQUEST[\'paged\'] ) ? $_REQUEST[\'paged\'] : 1;'."\n\t";
        $content .= 'if ( $on_page == 0 ) $on_page = 1;'."\n\t";
        $content .= '$offset = ($on_page-1) * $ppp;'."\n\t";
        $content .= '$data = $wpdb->get_results( "select * from '. $table_info[0]['table'] .' where $sql_search 1 limit $ppp offset $offset", OBJECT );'."\n";
        $content .= '?>'."\n";
        $content .= '<?php require_once(\'khachhang_breadcrumb.php\'); ?>'."\n";
        $content .= '<form action="" method="post">'."\n\t";
        $content .= '<section class="content-fluid">'."\n\t";
        $content .= '<div class="box box-default">'."\n\t\t";
        $content .= '<div class="box-header with-border">'."\n\t\t\t";
        $content .= '<ul class="list-inline">'."\n\t\t\t\t";
        $content .= '<li><input type="text" name="search_value" class="form-control" placeholder="nhập từ khóa tìm kiếm ở đây" value="<?= $search_value ?>" /></li>'."\n\t\t\t\t";
        $content .= '<li><button type="submit" name="search" class="btn btn-primary" value="1"><i class="fa fa-search"></i></button></li>'."\n\t\t\t\t";
        $content .= '<li><a href="?mod='. str_replace( 'rp_', '', $table_info[0]['table'] ) .'_new" class="btn btn-default"><i class="fa fa-clone"></i></a></li>'."\n\t\t\t";
        $content .= '</ul>'."\n\t\t";
        $content .= '</div>'."\n\t\t";
        $content .= '<div class="box-body" style="padding:0px; margin:0px;">'."\n\t\t\t";
        $content .= '<div class="table-responsive">'."\n\t\t";
        $content .= '<?php if ( $wpdb->num_rows ) : ?>'."\n\t\t";
        $content .= '<table class="table table-striped table-condensed table-bordered">'."\n\t\t\t";
        $content .= '<thead>'."\n\t\t\t\t";
        $content .= '<tr>'."\n\t\t\t\t\t".'<th></th>'."\n\t\t\t\t\t";

        $content_post = '';
        foreach( $table_info as $key => $info ) {

            $content_post .= '<th>'. $info['field_value'] .'</th>'."\n\t\t\t\t\t";
        }
        $content .= $content_post ."\n\t\t\t\t";
        $content .= '</tr>'."\n\t\t\t".'</thead>'."\n\t\t";
        $content .= '<?php'."\n\n\t\t\t".'echo "<tbody>";'."\n\t\t\t";
        $content .= 'foreach( $data as $key => $row ) {'."\n\n\t\t\t\t";
        $content .= 'echo "<tr>";'."\n\t\t\t\t";
        $content .= 'echo "<td align=\'center\'>";'."\n\t\t\t\t";
        $content .= 'echo "<a class=\'btn btn-default btn-sm\' href=\'?mod='. str_replace( 'rp_', '', $table_info[0]['table'] ) .'_edit&id=1\'><i class=\'fa fa-edit\'></i></a>";'."\n\t\t\t\t";
        $content .= 'echo "</td>";'."\n\t\t\t\t";

        $content_post = '';
        foreach( $table_info as $key => $info ) {

            $content_post .= 'echo "<td align=\'center\'>{$row->'. $info['field_name'] .'}</td>";'."\n\t\t\t\t";
        }
        $content .= $content_post .'echo "</tr>";'."\n\t\t\t";
        $content .= '}'."\n\t\t\t";
        $content .= 'echo "</tbody>";'."\n\t\t";
        $content .= '?>'."\n\t\t";
        $content .= '</table>'."\n\t\t";
        $content .= '<?php endif; ?>'."\n\t\t";
        $content .= '</div>'."\n";
        $content .= '<?php'."\n\n\t";
        $content .= '$nav = paginate_links( array('."\n\t\t\t\t";
        $content .= '\'base\' => add_query_arg('."\n\t\t\t\t\t";
        $content .= ' array('."\n\t\t\t\t\t\t";
        $content .= '\'paged\'=>\'%#%\','."\n\t\t\t\t\t\t";
        $content .= '\'search_value\'    => $search_value'."\n\t\t\t\t\t";
        $content .= ')'."\n\t\t\t\t".'),'."\n\t\t\t\t";
        $content .= '\'type\'   => \'list\','."\n\t\t\t\t";
        $content .= '\'format\' => \'\','."\n\t\t\t\t";
        $content .= '\'prev_text\' => __(\'&laquo;\'),'."\n\t\t\t\t";
        $content .= '\'next_text\' => __(\'&raquo;\'),'."\n\t\t\t\t";
        $content .= '\'total\' => ceil($totalposts->total / $ppp),'."\n\t\t\t\t";
        $content .= '\'current\' => $on_page'."\n\t\t\t\t";
        $content .= ')'."\n\t".');'."\n".'?>'."\n\t";
        $content .= '</div>'."\n\t\t";
        $content .= '<div class="box-footer" style="text-align: center"><?= $nav ?></div>'."\n\t\t";
        $content .= '</div>'."\n\t";
        $content .= '</section>'."\n";
        $content .= '</form>'."\n";
        
        fwrite( $list, $content );
        fclose( $list );
    }
    function editModel( $table_info ) {

        $edit = fopen( 'model_edit.php', 'w' );
        $content = '<?php '."\n\n\t# edit model\n\n\t".'global $wpdb;'."\n\n\t";
        $content .= '$id_in_url = isset( $_GET[\'id\'] ) ? $_GET[\'id\'] : \'\';'."\n\t";
        $content .= '$data = $wpdb->get_row( "select * from '. $table_info[0]['table'] .' where id = \'$id_in_url\' limit 1", OBJECT );'."\n\t";
        $content .= '# kiem tra loi'."\n\t".'$error = array('."\n\t\t";

        $content_post = '';
        foreach( $table_info as $key => $info ) {

            $content_post .= '\'is_'. $info['field_name'] .'\' => 0,'."\n\t\t";
        }
        $content .= $content_post ."\n\t);\n\t# khoi tao cac bien\n\t";

        $content_post = '';
        foreach( $table_info as $key => $info ) {

            $content_post .= '$'. $info['field_name'] .' = \'\';'."\n\t";
        }
        $content .= $content_post ."\n\t";
        $content .= 'if ( $_SERVER[\'REQUEST_METHOD\'] == \'POST\' ) {'."\n\n\t\t";

        $content_post = '';
        foreach ( $table_info as $key => $info ) {
            
            $content_post .= '$'. $info['field_name'] .' = isset( $_POST[\''. $info['field_name'] .'\'] ) ? $_POST[\''. $info['field_name'] .'\'] : \'\';'."\n\t\t";
        }
        $content .= $content_post ."\n\t\t# validate data\n\t\t";

        $content_post = '';
        foreach ( $table_info as $key => $info ) {
            
            $content_post .= 'if ( $'. $info['field_name'] .' == \'\' ) {'."\n\t\t\t";
            $content_post .= '$error[\'is_'. $info['field_name'] .'\'] = 1;'."\n\t\t}\n\t\t";
            
        }   
        $content .= $content_post ."\n\t\t";
        $content .= '# update database'."\n\t\t";
        $content .= '$error_values = array_count_values( $error );'."\n\t\t";
        $content .= 'if ( !isset( $error_values[1] ) ) {'."\n\n\t\t\t";
        $content .= '$result = $wpdb->update('."\n\t\t\t";
        $content .= '\''. $table_info[0]['table'] .'\','."\n\t\t\t";
        $content .= 'array('."\n\t\t\t\t";

        $content_post = '';
        foreach( $table_info as $key => $info ) {

            $content_post .= '\''. $info['field_name'] .'\' => $'. $info['field_name'] .','."\n\t\t\t\t";
        }
        $content .= $content_post ."\n\t\t\t";
        $content .= '),'."\n\t\t\t";
        $content .= 'array('."\n\t\t\t\t";
        $content .= '\'\'   => \'\''."\n\t\t\t)\n\t\t);\n\t\t";
        $content .= 'if ( $result ) {'."\n\n\t\t\t";
        $content .= 'echo "<script>alert(\'cập nhật thành công.\');";'."\n\t\t\t";
        $content .= 'echo "window.location=\'". home_url(\'?mod='. str_replace( 'rp_', '', $table_info[0]['table'] ) .'_list\') ."\';</script>";'."\n\t\t";
        $content .= '}'."\n\t";
        $content .= '}'."\n";
        $content .= '}'."\n\n?>\n";
        $content .= '<section class="content-fluid">'."\n\t";
        $content .= '<div class=="box box-default">'."\n\t\t";
        $content .= '<div class="col-xs-2"></div>'."\n\t\t";
        $content .= '<div class="col-xs-8">'."\n\t\t\t";
        $content .= '<form action="" method="post">'."\n\t\t\t\t";
        $content .= '<div class="panel panel-default">'."\n\t\t\t\t\t";
        $content .= '<div class="panel-body">'."\n\t\t\t\t\t\t";
        
        $content_post = '';
        foreach ( $table_info as $key => $info ) {
                        
            if ( $info['kind_view'] == 'select' ) {
                
                $content_post .= '<div class="form-group">'."\n\t\t\t\t\t\t\t";
                $content_post .= '<label for="">'. $info['field_value'] .'</label>'."\n\t\t\t\t\t\t\t";
                $content_post .= '<select name="'. $info['field_name'] .'" id="" class="form-control">'."\n\t\t\t\t\t\t\t\t";
                $content_post .= '<option value="" ></option>'."\n\t\t\t\t\t\t\t";
                $content_post .= '</select>'."\n\t\t\t\t\t\t";
                $content_post .= '</div>'."\n\t\t\t\t\t\t";
            }
            elseif ( $info['kind_view'] == 'input' && $info['kind_data'] == 'so' ) {
                
                $content_post .= '<div class="form-group">'."\n\t\t\t\t\t\t\t";
                $content_post .= '<label for="">'. $info['field_value'] .'</label>'."\n\t\t\t\t\t\t\t";
                $content_post .= '<?php if ( $error[\'is_'. $info['field_name'] .'\'] ) echo "<span style=\'color: red; \'>(*) Tên '. $info['field_value'] .' không được rỗng</span>" ?>'."\n\t\t\t\t\t\t\t";
                $content_post .= '<input name="'. $info['field_name'] .'" type="number" class="form-control" value="<?= $data->'. $info['field_name'] .' ?>" />'."\n\t\t\t\t\t\t";
                $content_post .= '</div>'."\n\t\t\t\t\t\t";
            }
            elseif ( $info['kind_view'] == 'input' && $info['kind_data'] == 'chuoi' ) {
                
                $content_post .= '<div class="form-group">'."\n\t\t\t\t\t\t\t";
                $content_post .= '<label for="">'. $info['field_value'] .'</label>'."\n\t\t\t\t\t\t\t";
                $content_post .= '<?php if ( $error[\'is_'. $info['field_name'] .'\'] ) echo "<span style=\'color: red; \'>(*) Tên '. $info['field_value'] .' không được rỗng</span>" ?>'."\n\t\t\t\t\t\t\t";
                $content_post .= '<input name="'. $info['field_name'] .'" type="text" class="form-control" value="<?= $data->'. $info['field_name'] .' ?>" />'."\n\t\t\t\t\t\t";
                $content_post .= '</div>'."\n\t\t\t\t\t\t";
            }
            elseif ( $info['kind_view'] == 'datepicker' ) {
                
                $content_post .= '<div class="form-group">'."\n\t\t\t\t\t\t\t";
                $content_post .= '<label for="">'. $info['field_value'] .'</label>'."\n\t\t\t\t\t\t\t";
                $content_post .= '<?php if ( $error[\'is_'. $info['field_name'] .'\'] ) echo "<span style=\'color: red; \'>(*) Tên '. $info['field_value'] .' không được rỗng</span>" ?>'."\n\t\t\t\t\t\t\t";
                $content_post .= '<input name="'. $info['field_name'] .'" type="text" class="form-control datepicker" value="<?= $data->'. $info['field_name'] .' ?>" />'."\n\t\t\t\t\t\t";
                $content_post .= '</div>'."\n\t\t\t\t\t\t";
            }
            elseif ( $info['kind_view'] == 'textarea' ) {
                
                $content_post .= '<div class="form-group">'."\n\t\t\t\t\t\t\t";
                $content_post .= '<label for="">'. $info['field_value'] .'</label>'."\n\t\t\t\t\t\t\t";
                $content_post .= '<?php if ( $error[\'is_'. $info['field_name'] .'\'] ) echo "<span style=\'color: red; \'>(*) Tên '. $info['field_value'] .' không được rỗng</span>" ?>'."\n\t\t\t\t\t\t\t";
                $content_post .= '<textarea name="'. $info['field_name'] .'" class="form-control" cols="60" rows="5" ></textarea>'."\n\t\t\t\t\t\t";
                $content_post .= '</div>'."\n\t\t\t\t\t\t";
            }

        }
        $content .= $content_post ."\n\t\t\t\t\t";
        $content .= '</div>'."\n\t\t\t\t";        
        $content .= '</div>'."\n\t\t\t\t";// div default
        $content .= '<a class="btn btn-default" href="?mod='. str_replace( 'rp_', '', $table_info[0]['table'] ) .'_list"><span class="glyphicon glyphicon-arrow-left"></span> Quay lại</a>'."\n\t\t\t\t";
        $content .= '<input id="save_edit" name="save" type="submit" class="btn btn-primary" value="Lưu lại" />'."\n\t\t\t";
        $content .= '</form>'."\n\t\t";
        $content .= '</div>'."\n\t\t";
        $content .= '<div class="col-xs-2"></div>'."\n\t";
        $content .= '</div>'."\n";
        $content .= '</section>'."\n";

        fwrite( $edit, $content );
        fclose( $edit );
    }
?>