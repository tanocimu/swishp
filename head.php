<?php
session_start();
require_once("config.php");
require_once(DIR_LOGIN . "db_write.php");
shelfmng_submit();
?>
<!DOCTYPE html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <meta name="description" content="Mountain Top Tourist Servicesの説明を記載">
    <meta charset="UTF-8">

    <meta property="og:title" content="Mountain Top Tourist Services">
    <meta property="og:site_name" content="Mountain Top Tourist Services">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https:///">
    <meta property="og:description" content="Mountain Top Tourist Servicesの説明を記載">

    <title>Mountain Top Tourist Services</title>

    <script src="./js/jquery.min.js"></script>
    <link rel="stylesheet" href="reset.css">
    <link href="https://fonts.googleapis.com/css?family=Sawarabi+Mincho" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=M+PLUS+1p" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="modal.css">
</head>