<?php
include "checksession.php";
include "converted template/header.php";
include "converted template/menu.php";
echo '<div id="site_content">';
include "converted template/sidebar.php";
echo '<div id="content">';

include "converted template/content.php";

echo '</div></div>';
include "converted template/footer.php";
?>
