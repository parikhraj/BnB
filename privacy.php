<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Statement</title>
</head>

<body>
    <?php
    include "checksession.php";
    include "converted template/header.php";
    include "converted template/menu.php";
    echo '<div id="site_content">';
    include "converted template/sidebar.php";
    echo '<div id="content">';
    checkUser();
    loginStatus();
    include "converted template/config.php";
    ?>
    <img src="privacy.png" >
    <?php
    echo '</div></div>';
    include "converted template/footer.php";
    ?>
</body>

</html>