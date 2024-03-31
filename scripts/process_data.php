<?php

function bg_parse_csv()
{
    $handle = fopen(BASE_URL . '/assets/uploads/' . UPLOAD_FILE_NAME, 'r');
    if ($handle !== FALSE) {
        $data = array();

        // gets the header
        $header = array();
        while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
            $row[0] = 'DATE';
            $header = array_map(fn ($row) => strtoupper(trim($row)), $row);
            break;
            print_r(trim($row[0]) == 'DATE' ? 'y' : 'n');
        }

        // gets the data
        while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
            if (strtotime($row[0]) !== FALSE) {
                $current_row = array_combine($header, array_map(fn ($row) => strtoupper(trim($row)), $row));

                $date = explode('-', $current_row['DATE']);

                if (strlen($date[2]) !== 4) $current_row['DATE'] = "{$date[0]}-{$date[1]}-20{$date[2]}";

                $current_row['RATE'] = number_format(round(floatval($current_row['RATE']), 2), 2, '.', '');
                $current_row['TOTAL'] = number_format(round(floatval($current_row['TOTAL']), 2), 2, '.', '');
                $current_row['EX RATE'] = number_format(round(floatval($current_row['EX RATE']), 2), 2, '.', '');
                $current_row['EX TOTAL'] = number_format(round(floatval($current_row['EX TOTAL']), 2), 2, '.', '');

                $data[] = $current_row;
            }
        }
        fclose($handle);

        usort($data, fn ($a, $b) => $a['PARTY NAME'] <=> $b['PARTY NAME'] ?: strtotime($a['DATE']) <=> strtotime($b['DATE']));
        usort($data, fn ($a, $b) => $a['PARTY NAME'] <=> $b['PARTY NAME'] ?: strtotime($a['DATE']) <=> strtotime($b['DATE']));

        $meta = array(
            'print' => array('DATE' => 'print', 'BILL' => 'print', 'ITEM' => 'print', 'MAKE' => 'print', 'QTY' => 'print', 'RATE' => 'print', 'TOTAL' => 'print', 'PARTY' => 'print', 'EX RATE' => 'print', 'EX TOTAL' => 'print'),
            'align' => array('DATE' => 'center', 'PARTY NAME' => 'start', 'QTY' => 'end', 'RATE' => 'end', 'TOTAL' => 'end', 'PARTY' => 'start', 'EX RATE' => 'end', 'EX TOTAL' => 'end', 'REMARKS', 'BROK.TIME', 'OTHER NOTI.', 'PAY DATE'),
        );
        return array('csv_header' => $header, 'csv_data' => $data, 'csv_meta' => $meta);
    }

    return $handle;
}

function get_filtered_data($data, $filter = array())
{
    $filtered_data = array();
    $top_header = array_keys($data[0]);
    foreach ($data as $entry) {
        $add = (
            (!isset($filter['party_name']) or $filter['party_name'] == 'ALL' or $filter['party_name'] === $entry['PARTY NAME']) and
            (!isset($filter['start_date']) or strtotime($filter['start_date']) === FALSE or strtotime($filter['start_date']) <= strtotime($entry['DATE'])) and
            (!isset($filter['end_date']) or strtotime($filter['end_date']) === FALSE or strtotime($filter['end_date']) >= strtotime($entry['DATE']))
        );

        if ($add) {
            for ($index = 11; $index < count($top_header); $index++) {
                $extra_key = strtolower(preg_replace('/[^A-Za-z0-9]/', '_', $top_header[$index]));
                if (
                    (array_key_exists($extra_key, $filter)
                        and ($filter[$extra_key] == 'ALL'
                            or $filter[$extra_key] === $entry[$top_header[$index]]))
                ) {
                } else {
                    $add = false;
                    break;
                }
            }
        }
        if ($add) {
            if (isset($filtered_data[$entry['PARTY NAME']])) $filtered_data[$entry['PARTY NAME']][] = $entry;
            else $filtered_data[$entry['PARTY NAME']] = array($entry);
        }
    }
    return $filtered_data;
}
