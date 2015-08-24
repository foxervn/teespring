<?php

global $wpdb;

$secret = $_GET["secret"];
if ($secret == "123456aA@") {
    $page = 1;
    $per_page = 1000;
    $data = get_products($page, $per_page);
    if (isset($data->hits) && count($data->hits)) {
        $time = current_time('mysql');
        foreach ($data->hits as $hit) {
            $objectID = $hit->objectID;
            $wpdb->replace(
                'product', array(
                'objectID' => $objectID,
                'sold' => $hit->amount_ordered,
                'updated_at' => $time
            ), array(
                    '%d',
                    '%d',
                    '%s'
                )
            );
        }
    }
}

