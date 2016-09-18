<?php

    $list_tables = array( 
        'rp_brand_name',
        'rp_commentmeta',
        'rp_comments',
        'rp_doisoat_cskh',
        'rp_doi_tac',
        'rp_gia_ban',
        'rp_gia_ban_logs',
        'rp_hop_dong',
        'rp_khach_hang',
        'rp_lien_he_khach_hang',
        'rp_links',
        'rp_nhom',
        'rp_options',
        'rp_postmeta',
        'rp_posts',
        'rp_termmeta',
        'rp_terms',
        'rp_term_relationships',
        'rp_term_taxonomy',
        'rp_tongquat_cskh',
        'rp_tratruoc_naptien',
        'rp_tratruoc_sanluong',
        'rp_usermeta',
        'rp_users'
         ); 
?>

<meta charset="UTF-8" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/css/bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/js/bootstrap.min.js"></script>
<div class="row">
    <div class="col-xs-2"></div>
    <div class="col-xs-8">
        <div class="panel panel-default">
            <div class="panel-body">
                <table class="table table-striped">
                    <tr>
                        <th style="text-align: center; " colspan="2">List các bảng</th>
                    </tr>
                    <tr>
                        <th>Tên bảng</th>
                        <th></th>
                    </tr>
                    <?php foreach( $list_tables as $list ) : ?>
                    <tr>
                        <td><?= $list ?></td>
                        <td><a class="btn btn-default" href="builder.php?table=<?= $list ?>">Builder</a></td>
                    </tr>
                    <?php endforeach; ?>
                    
                </table>
            </div>
        </div>
    </div>
    <div class="col-xs-2"></div>
</div>