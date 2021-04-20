<?php ESP::part_header(); ?>
<?php
$page = ESP::param_uri(0, "1");
$all = ESP::db()->all();
ESP::html_ul_open();
foreach ($all as $row) {
    ESP::part_auto("row", $row->items());
}
ESP::html_ul_close();
?>
<p><a href="<?= ESP::link_create() ?>">만들기</a></p>

<?= ESP::part_footer(); ?>