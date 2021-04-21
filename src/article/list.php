<?php ESP::part_header(); ?>
<?php
$page_list = ESP::auto_pagenate();
?>
<ul>
    <?php
    foreach ($page_list as $row) {
        ESP::part_auto("row", $row->items());
    }
    ?>
</ul>
<p><a href="<?= ESP::link_create() ?>">만들기</a></p>

<?= ESP::part_footer(); ?>