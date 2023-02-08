<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/static/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Zenith Project</title>
    <script src="/static/node_modules/jquery/dist/jquery.min.js"></script>
    <script src="/static/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="app">
        <?=$this->include('templates/inc/navbar.php')?>

        <div class="container mb-5 mt-5">
            <?=$this->renderSection('content')?>
        </div>
    </div>

    <?=$this->renderSection('script')?>
    <em>&copy; 2021</em>
</body>
</html>