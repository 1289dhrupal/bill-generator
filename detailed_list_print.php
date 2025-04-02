<?php
$title = "Print Detailed List";
include_once 'config.php';
include_once BASE_DIR . '/scripts/process_data.php';
include_once BASE_DIR . '/scripts/generate_tables.php';

$get_params = array_merge(array('party_name' => ''), $_GET);

// returns array(csv_header => [], csv_data => []);
if (($csv_to_array = bg_parse_csv()) === FALSE) die();

extract($csv_to_array);

$data = get_filtered_data($csv_data, $get_params);
?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">

    <title><?php echo $title ?  'Bill Generator - ' . $title : DEFAULT_TITLE ?></title>
    <link href="./assets/css/style.css" rel="stylesheet" type="text/css" media="print">
</head>

<body>
    <?php bg_get_table($data, $csv_header, $csv_meta); ?>
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
