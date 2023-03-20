<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <link href="/static/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/static/node_modules/daterangepicker/daterangepicker.css" rel="stylesheet">
    <link href="/static/css/common.css" rel="stylesheet"> 
    <link href="/static/css/ui.css" rel="stylesheet"> 
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <title><?= $this->renderSection('title') ?></title>
    <script src="/static/node_modules/jquery/dist/jquery.min.js"></script>
    <script src="/static/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="/static/node_modules/moment/moment.js"></script>
    <script src="/static/node_modules/daterangepicker/daterangepicker.js"></script>
    <script src="/static/js/twbsPagination.js"></script>
    <script src="/static/js/ui.js"></script>
    <?=$this->renderSection('header')?>
</head>
<body>
<?=$this->renderSection('guestContent')?>