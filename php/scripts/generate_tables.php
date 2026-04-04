<?php
function bg_get_table($data, $csv_header, $csv_meta)
{
    foreach ($data as $party_name => $entries) { ?>

        <div class="bill-container">

            <div class="d-flex justify-content-between align-items-start mb-3 bill-header">
                <div>
                    <h2 class="mb-0"><?php echo htmlspecialchars($party_name); ?></h2>
                </div>
                <div class="text-end">
                    <strong>Shah Paresh Shakralal (Cento)</strong><br>
                    B-6/203, Veena Nagar CHS 2<br>
                    LBS Marg, Mulund (W) Mumbai -80<br>
                    Contact - 9322231596, 8291711596<br>
                    PAN - ASPPS2654H<br>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>SR.</th>
                            <?php foreach ($csv_header as $key) { ?>
                                <th class="d-<?php echo $csv_meta['print'][$key] ?? 'print-none' ?> text-<?php echo $csv_meta['align'][$key] ?? 'start' ?>">
                                    <?php echo htmlspecialchars($key); ?>
                                </th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entries as $i => $entry) { ?>
                            <tr>
                                <th><?php echo $i + 1 ?></th>
                                <?php foreach ($entry as $key => $value) { ?>
                                    <td class="d-<?php echo $csv_meta['print'][$key] ?? 'print-none' ?> text-<?php echo $csv_meta['align'][$key] ?? 'start' ?>">
                                        <?php echo htmlspecialchars($value); ?>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="summary-box mt-3">
                <div class="summary-header">
                    <strong>SUMMARY</strong>
                </div>
                <div class="summary-body">
                    <?php
                    $total_sum = array_sum(array_column($entries, 'TOTAL'));
                    $brokerage = round(floatval($total_sum) / 100, 2);
                    $extra = array_sum(array_column($entries, 'EX TOTAL'));
                    ?>
                    <div><strong>BROKERAGE AT 1%:</strong> <?php echo number_format($brokerage, 2); ?></div>
                    <div><strong>EXTRA:</strong> <?php echo number_format($extra, 2); ?></div>
                    <hr class="my-1">
                    <div><strong>TOTAL:</strong> <?php echo number_format($brokerage + $extra, 2); ?></div>
                </div>
            </div>

            <div class="pagebreak"></div>

        </div>
<?php } // end foreach
} // end function
?>