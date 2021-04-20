<?php
// P::login_required();
/*
if (ESP::is_post()){
    
    $table_name = "article";
    $use_columns = ["title", "content"];
    $model_id = ESP::db($table_name)->param($use_columns)->insert();
    ESP::redirect("/$table_name/view/?id=$model_id");
    
}
*/
ESP::auto_save(null, ['title', 'content']);
?>
<?php ESP::view_header(); ?>
<h2><?= ESP::$_resource ?> CREATE</h2>
<form method="POST">
    <p>title : <input type="text" name="title" id="title" /></p>
    <p>content : <input type="text" name="content" id="content" /></p>
    <p><input type="submit" value="저장" /></p>
</form>
<?php ESP::view_footer(); ?>