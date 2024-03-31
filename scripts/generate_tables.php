<?php
function bg_get_table($data, $csv_header, $csv_meta)
{
    foreach ($data as $party_name => $entries) { ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="bg-secondary text-dark">
                    <tr>
                        <td colspan="16">
                            <div class="float-start display-4"><?php echo $party_name ?></div>
                            <div class="float-end">
                                <div>
                                    Shah Paresh Shakralal (Cento) <br>
                                    B-6/203, Veena Nagar CHS 2 <br>
                                    LBS Marg, Mulund (W) Mumbai -80 <br>
                                    Contact - 9322231596, 8291711596 <br>
                                    PAN - ASPPS2654H <br>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>SR.</th>
                        <?php foreach ($csv_header as $key) { ?>
                            <th class="d-<?php echo $csv_meta['print'][$key] ?? 'print-none' ?> text-<?php echo $csv_meta['align'][$key] ?? 'start' ?>"><?php echo $key ?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody class="table-light">
                    <?php foreach ($entries as $i => $entry) { ?>
                        <tr>
                            <th><?php echo $i + 1 ?></th>
                            <?php foreach ($entry as $key => $value) { ?>
                                <td class="d-<?php echo $csv_meta['print'][$key] ?? 'print-none' ?> text-<?php echo $csv_meta['align'][$key] ?? 'start' ?>"><?php echo $value ?></td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot class="bg-secondary text-dark">
                    <tr>
                        <th>SR</th>
                        <?php foreach ($csv_header as $key) { ?>
                            <th class="d-<?php echo $csv_meta['print'][$key] ?? 'print-none' ?> text-center"><?php echo $key ?></th>
                        <?php } ?>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="card text-dark bg-secondary">
            <div class="card-header">
                SUMMARY
            </div>
            <div class="card-body">
                <?php
                $brokerage = round(array_sum(array_map('floatval', array_column($entries, 'TOTAL'))) / 100, 2);
                $extra = array_sum(array_map('floatval', array_column($entries, 'EX TOTAL')));
                ?>
                <h5 class="card-title">BROKERAGE AT 1 percent : <?php echo $brokerage ?></h5>
                <h5 class="card-title">EXTRA : <?php echo $extra ?></h5>
                <h5 class="card-title">TOTAL : <?php echo $brokerage + $extra ?></h5>
            </div>
        </div>
        <div class="pagebreak"> </div>
<?php }
}
