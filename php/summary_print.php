<?php
$title = "Print Summary";
include_once 'config.php';
include_once BASE_DIR . '/scripts/process_data.php';

// Default empty values for get_params
$get_params = array_merge(array('start_date' => '', 'end_date' => ''), $_GET);

// returns array(csv_header => [], csv_data => []);
if (($csv_to_array = bg_parse_csv()) === FALSE) die('Could not parse CSV file.');

extract($csv_to_array);

$data = array();
foreach (get_filtered_data($csv_data, $get_params) as $party_name => $entries) {
    $data[$party_name] = array('PARTY NAME' => $party_name, 'TOTAL' => 0, 'EX TOTAL' => 0, 'BROKERAGE' => 0,);
    foreach ($entries as $i => $entry) {
        $data[$party_name]['TOTAL'] += $entry['TOTAL'];
        $data[$party_name]['EX TOTAL'] += $entry['EX TOTAL'];
        $data[$party_name]['BROKERAGE'] += (($entry['TOTAL'] / 100) + $entry['EX TOTAL']);
    }
}

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">
    <title><?php echo $title ? 'Bill Generator - ' . $title : DEFAULT_TITLE ?></title>

    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                /* Ensures backgrounds print in Chrome */
                color-adjust: exact;
                /* Standard */
                margin: 20px;
            }

            .page-header {
                text-align: center;
                margin-bottom: 2rem;
            }

            /* Use simple bordered table for printing instead of Bootstrap's dark/striped themes */
            .table-bordered th,
            .table-bordered td {
                border: 1px solid #dee2e6 !important;
            }

            /* Override Bootstrap's colors for print */
            thead {
                background-color: #f8f9fa !important;
                /* A light grey for the header */
                color: #212529 !important;
                /* Black text */
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="page-header">
            <h1>Summary Report</h1>
            <?php if (!empty($get_params['start_date']) && !empty($get_params['end_date'])): ?>
                <h4>
                    From: <?php echo htmlspecialchars($get_params['start_date']); ?>
                    To: <?php echo htmlspecialchars($get_params['end_date']); ?>
                </h4>
            <?php endif; ?>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>SR.</th>
                        <th class="text-start">PARTY NAME</th>
                        <th class="text-end">TOTAL</th>
                        <th class="text-end">EX TOTAL</th>
                        <th class="text-end">BROKERAGE</th>
                    </tr>
                </thead>
                <tbody> <?php foreach (array_values($data) as $i => $entry): ?>
                        <tr>
                            <th><?php echo $i + 1 ?></th>
                            <td class="text-start"><?php echo htmlspecialchars($entry['PARTY NAME']); ?></td>
                            <td class="text-end"><?php echo number_format($entry['TOTAL'], 2, '.', ','); ?></td>
                            <td class="text-end"><?php echo number_format($entry['EX TOTAL'], 2, '.', ','); ?></td>
                            <td class="text-end"><?php echo number_format($entry['BROKERAGE'], 2, '.', ','); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>SR</th>
                        <th class="text-start">PARTY NAME</th>
                        <th class="text-end">TOTAL</th>
                        <th class="text-end">EX TOTAL</th>
                        <th class="text-end">BROKERAGE</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</body>

</html>