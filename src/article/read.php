<?php
    /*
    $table_name = "article";
    $model = ESP::auto_find($table_name);
    */
    $model = ESP::auto_find();
?>
<?php ESP::view_header(); ?>
<p>title : <?=$model->title ?></p>
<p>content : <?=$model->content ?></p>

<p><a href="<?= ESP::link_edit() ?>">수정</a></p>
<p><a href="<?= ESP::link_delete() ?>">삭제</a></p>
<p><a href="<?= ESP::link_list() ?>">목록</a></p>

<?php ESP::view_footer(); ?>