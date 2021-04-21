<?php
ESP::login_required();
ESP::auto_save(null, ['title', 'content']);
?>
<?php ESP::part_header(); ?>
<h2><?= ESP::$_resource ?> CREATE</h2>
<form method="POST">
    <p>title : <input type="text" name="title" id="title" /></p>
    <p>content : <input type="text" name="content" id="content" /></p>
    <p><input type="submit" value="저장" /></p>
</form>
<?php ESP::part_footer(); ?>