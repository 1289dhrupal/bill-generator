<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Dhrupal Shah">
    <meta name="generator" content="Hugo 0.88.1">

    <title><?php echo $title ?  'Bill Generator - ' . $title : DEFAULT_TITLE ?></title>

    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/css/style.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid">
        <div class="row">

            <?php include_once BASE_DIR . '/templates/sidebar.php'; ?>
            <main class="col-10 col-md-9 col-lg-10 px-0 mx-0">
                <div class="display-4 pt-3 sticky-top text-light bg-dark">
                    <?php echo $title ?>
                    <hr class="rounded" style="border:double">
                    <?php if ($message) { ?>
                        <div class="alert alert-success" role="alert"><?php echo $message ?></div>
                    <?php } ?>
                    <?php if ($error) { ?>
                        <div class="alert alert-success" role="alert"><?php echo $error ?></div>
                    <?php } ?>
                </div>
                <div class="px-3">