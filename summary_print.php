<?php
$title = "Print Summary";
include_once 'config.php';
include_once BASE_DIR . '/scripts/process_data.php';
include_once BASE_DIR . '/scripts/generate_tables.php';

$get_params = array_merge(array('start_date' => '', 'end_date' => ''), $_GET);

// returns array(csv_header => [], csv_data => []);
if (($csv_to_array = bg_parse_csv()) === FALSE) die();

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
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">

    <title><?php echo $title ?  'Bill Generator - ' . $title : DEFAULT_TITLE ?></title>
    <link href="./assets/css/style.css" rel="stylesheet" type="text/css" media="print">
</head>

<body>
    <div class="table-responsive">
        <table id="summary_list_table" class="table table-striped">
            <thead class="table-dark text-white">
                <tr>
                    <th>SR.</th>
                    <th class="text-start">PARTY NAME</th>
                    <th class="text-end">TOTAL</th>
                    <th class="text-end">EX TOTAL</th>
                    <th class="text-end">BROKERAGE</th>
                </tr>
            </thead>
            <tbody class="table-light">
                <?php foreach (array_values($data) as $i => $entry) { ?>
                    <tr>
                        <th><?php echo $i + 1 ?></th>
                        <td class="text-start"><?php echo $entry['PARTY NAME'] ?></td>
                        <td class="text-end"><?php echo number_format($entry['TOTAL'], 2, '.', ',') ?></td>
                        <td class="text-end"><?php echo number_format($entry['EX TOTAL'], 2, '.', ',') ?></td>
                        <td class="text-end"><?php echo number_format($entry['BROKERAGE'], 2, '.', ',') ?></td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot class="table-dark text-white">
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
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
