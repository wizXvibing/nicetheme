<?php require "app/app.php"; // Loading APP -> SystemPanel ?>
<?php require "api/index.php"; // Loading APP -> SystemPanel ?>
<?php require "app/appMessages.php"; // Loading APP -> SystemPanel ?>

<!--
    APP: System Panel
    APP DISCORD: https://discord.com/invite/EnktmSQKPn

    AUTHOR: AUTHTERN
    AUTHOR DISCORD: AUTHTERN#1625

    VERSION: 1.2.1
    THANK YOU FOR SUPPORTING THE APP
-->

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Panel</title>

    <link rel="stylesheet" href="public/styles/root.css">
    <link rel="stylesheet" href="public/styles/styles.css">
    <link rel="stylesheet" href="public/styles/nprogress.css">
    <link rel="stylesheet" href="public/styles/nice-select.css">
    <link rel="stylesheet" href="public/styles/animate.min.css">
    <link rel="icon" type="image/x-icon" href="favicon.ico">

    <script type="text/javascript" src="public/scripts/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="public/scripts/nprogress.js"></script>
    <script type="text/javascript" src="public/scripts/theme.js"></script>
    <script type="text/javascript" src="public/scripts/jquery.nice-select.js"></script>

    <script>
    $(document).ready(function() {
    $('select').niceSelect();
    });
    </script>   
</head>
<body>
<?php require "app/appGen.php"; // Loading APP -> SystemPanel ?>
</body>
</html>
