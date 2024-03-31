<?php
$title = "Detailed List";
$message = $error = null;

include_once 'config.php';
include_once BASE_DIR . '/scripts/process_data.php';
include_once BASE_DIR . '/scripts/generate_tables.php';

$get_params = array_merge(array('party_name' => '', 'start_date' => '', 'end_date' => ''), $_GET);

// returns array(csv_header => [], csv_data => []);
if (($csv_to_array = bg_parse_csv()) === FALSE) die();
extract($csv_to_array);

$data = get_filtered_data($csv_data, $get_params);

$list_of_parties = array_values(array_unique(array_column($csv_data, 'PARTY NAME')));

$other_lists = array();
for ($index = 11; $index < count($csv_header); $index++) {
    $extra_key = strtolower(preg_replace('/[^A-Za-z0-9]/', '_', $csv_header[$index]));
    $other_lists[$extra_key] = array_values(array_unique(array_column($csv_data, $csv_header[$index])));
}
include_once BASE_DIR . '/templates/header.php'; ?>

<div class="d-print-none">
    <form class="row g-3 py-5" action="<?= $_SERVER['PHP_SELF'] ?>" method="GET">
        <div class="col-md-4">
            <div class="input-group mb-3">
                <span class="input-group-text" id="party_name_addon" for="party_name" label="party_name">PARTY</span>
                <input aria-label="part_name" aria-describedby="part_name_addon" class="form-control" name="party_name" list="partyNames" id="party_name" placeholder="Type to search..." value="<?php echo $get_params['party_name'] ?>">
            </div>
            <datalist id="partyNames">
                <option value="ALL"> LIST ALL BILLS</option>
                <?php foreach ($list_of_parties as $party_name) { ?>
                    <option value="<?php echo $party_name ?>"><?php echo $party_name ?></option>
                <?php } ?>
            </datalist>
        </div>
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
        <?php foreach ($other_lists as $key => $list) { ?>
            <div class="col-md-4">
                <div class="input-group md-3">
                    <span class="input-group-text" id="<?php echo $key ?>_addon" for="<?php echo $key ?>" label="<?php echo $key ?>"><?php echo strtoupper(str_replace('_', ' ', $key)) ?></span>
                    <select class="form-select" id="<?php echo $key ?>" name="<?php echo $key ?>" aria-label="<?php echo $key ?>" aria-describedby="<?php echo $key ?>_addon">
                        <option value="ALL">ALL</option>
                        <option value="">Empty Entries</option>
                        <?php $list = array_filter($list,  fn ($value) => $value !== ''); ?>
                        <?php foreach ($list as $entry) { ?>
                            <option value="<?php echo $entry ?>"><?php echo $entry ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        <?php } ?>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>
</div>

<link rel="stylesheet" href="./assets/css/datatables.min.css">
<div class="table-responsive">
    <table id="detailed_list_table" class="table table-striped">
        <thead class="table-dark text-white">
            <tr>
                <th>SR.</th>
                <?php foreach ($csv_header as $key) { ?>
                    <th class="d-<?php echo $csv_meta['print'][$key] ?? 'print-none' ?> text-center"><?php echo $key ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody class="table-light">
            <?php foreach ($data as $party_name => $entries) { ?>
                <?php foreach ($entries as $i => $entry) { ?>
                    <tr>
                        <th><?php echo $i + 1 ?></th>
                        <?php foreach ($entry as $key => $value) { ?>
                            <td class="d-<?php echo $csv_meta['print'][$key] ?? 'print-none' ?> text-<?php echo $csv_meta['align'][$key] ?? 'start' ?>"><?php echo $value ?></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            <?php } ?>
        </tbody>
        <tfoot class="table-dark text-white">
            <tr>
                <th>SR</th>
                <?php foreach ($csv_header as $key) { ?>
                    <th class="d-<?php echo $csv_meta['print'][$key] ?? 'print-none' ?> text-center"><?php echo $key ?></th>
                <?php } ?>
            </tr>
        </tfoot>
    </table>
</div>
<script src="./assets/js/jquery-3.6.0.min.js"></script>
<script src="./assets/js/datatables.min.js"></script>
<script>
    jQuery(document).ready(function() {
        jQuery('#detailed_list_table').DataTable();
    });
</script>
<?php include_once BASE_DIR . '/templates/footer.php' ?>