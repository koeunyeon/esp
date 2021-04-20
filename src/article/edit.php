<?php
ESP::auto_save(null, ['title', 'content']);
$model = ESP::auto_find();
?>
<?php ESP::part_header(); ?>
<form method="POST">
    <p>title : <input type="text" name="title" id="title" class="md5" value="<?= $model->title ?>" /></p>
    <p>content : <input type="text" name="content" id="content" value="<?= $model->content ?>" /></p>
    <input type="submit" value="저장" />
</form>
<?php ESP::part_footer(); ?>