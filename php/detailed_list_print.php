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
    <!-- <link href="./assets/css/style.css" rel="stylesheet" type="text/css" media="print"> -->
</head>

<body>
    <style>
        /* Screen-only styles can go here if needed */

        @media print {
            body {
                background-color: #fff;
                /* Ensure white background */
            }

            /* Hide non-essential elements from the printed page */
            .d-print-none {
                display: none !important;
            }

            /* Force a page break AFTER each bill container */
            .pagebreak {
                page-break-after: always;
            }

            /* Prevent a table row or the summary box from splitting across two pages */
            tr,
            .summary-box {
                page-break-inside: avoid;
            }

            /* Style the summary box with a simple border instead of a background */
            .summary-box {
                border: 1px solid #6c757d;
                /* A light grey border */
                padding: 0;
                margin-top: 1.5rem !important;
            }

            .summary-header {
                background-color: #f8f9fa !important;
                /* Very light grey header */
                padding: 0.5rem 1rem;
                border-bottom: 1px solid #6c757d;
            }

            .summary-body {
                padding: 1rem;
            }

            /* Ensure table headers have a light background for clarity, not a dark one */
            table thead {
                background-color: #f8f9fa !important;
                /* Light grey background */
                color: #000 !important;
                /* Black text */
            }
        }
    </style>
    <?php bg_get_table($data, $csv_header, $csv_meta); ?>
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
