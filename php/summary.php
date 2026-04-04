<?php
$title = "Summary";
$message = $error = null;

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

include_once BASE_DIR . '/templates/header.php'; ?>

<div class="d-print-none">
    <form class="row g-3 py-5" action="<?= $_SERVER['PHP_SELF'] ?>" method="GET">
        <div class="col-md-4">
            <div class="input-group mb-3">
                <span class="input-group-text" id="start_date_addon" for="start_date" label="start_date">FROM</span>
                <input type="date" class="form-control" id="start_date" name="start_date" aria-label="start_date" aria-describedby="start_date_addon" value="<?php echo $get_params['start_date'] ?>">
            </div>
        </div>
        <div class="col-md-4">
            <div class="input-group mb-3">
                <span class="input-group-text" id="end_date_addon" for="end_date" label="end_date">TO</span>
                <input type="date" class="form-control" id="end_date" name="end_date" aria-label="end_date" aria-describedby="end_date_addon" value="<?php echo     $get_params['end_date'] ?>">
            </div>
        </div>
        <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Filter</button>
        </div>
    </form>
</div>

<link rel="stylesheet" href="./assets/css/datatables.min.css">
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
<script src="./assets/js/jquery-3.6.0.min.js"></script>
<script src="./assets/js/datatables.min.js"></script>
<script>
    jQuery(document).ready(function() {
        jQuery('#summary_list_table').DataTable();
    });
</script>
<?php include_once BASE_DIR . '/templates/footer.php' ?>