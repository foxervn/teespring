<?php

include "XPath.class.php";

$api_key = "5cf4b4f788d542e9e1661cb977480f0dcb5acfdae52786e3bf9593ba8da3ddd4";
$app_id = "XNF09CCDO4";

function make_request($url, $data, $method) {
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "X-Algolia-API-Key: $api_key",
        "X-Algolia-Application-Id: $app_id",
        'Content-Length: ' . strlen($data))
    );

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

function get_url_paging($page) {
    $string = $_SERVER['REQUEST_URI'];
    if (preg_match("/page=[0-9]+/", $string)) {
        return preg_replace('(page=[0-9]+)', "page=" . $page, $string);
    } else {
        $x = (strpos($string, '?') !== false) ? "&" : "?";
        $string .= $x . "page=" . $page;

        return $string;
    }
}

function paging($page, $last, $range) {
    $totalpages = $last;
    $page = $page;

    if ($page > $totalpages) {
        $page = $totalpages;
    }
    if ($page < 1) {
        $page = 1;
    }

    echo '<nav class="pagination-nav"><ul class="pagination">';
    if ($page > 1) {
        echo "<li><a href='" . get_url_paging(1) . "'>&laquo;</a></li>";
        $prevpage = $page - 1;
        echo "<li><a href='" . get_url_paging($prevpage) . "'>prev</a></li>";
    }

    for ($x = ($page - $range); $x < (($page + $range) + 1); $x++) {
        if (($x > 0) && ($x <= $totalpages)) {
            if ($x == $page) {
                echo "<li class='active'><a href='#'>$x<span class='sr-only'>(current)</span></a></li>";
            } else {
                echo "<li><a href='" . get_url_paging($x) . "'>$x</a></li>";
            }
        }
    }

    if ($page != $totalpages) {
        $nextpage = $page + 1;
        echo "<li><a href='" . get_url_paging($nextpage) . "'>next</a></li>";
        echo "<li><a href='" . get_url_paging($totalpages) . "'>&raquo;</a></li>";
    }

    echo '</ul></nav>';
}

function get_time_left($date) {
    $time = strtotime($date) - time();

    $days = $time / 24 / 60 / 60;

    if (floor($days) == $days) {
        return $days . " days";
    } elseif($time > 0) {
        $mins = floor(($days - floor($days)) * 24 * 60);
        return floor($days) . " days " . $mins . " mins";
    } else {
        return "stopped";
    }
}

function get_products($page, $per_page) {
    global $app_id, $api_key;
    
    $url = "http://xnf09ccdo4-3.algolia.io/1/indexes/site_wide_search_index_production/query";
    $method = "POST";

    $args = array(
        "params" => http_build_query(array(
            "hitsPerPage" => $per_page,
            "page" => $page - 1,
            "attributesToRetrieve" => "*"
                )
        ),
        "apiKey" => $api_key,
        "appID" => $app_id,
        "appID" => $app_id,
        "X-Algolia-TagFilters" => "-relaunched"
    );

    $json = json_encode($args);

    $result = make_request($url, $json, $method);
    $data = json_decode($result);

    return $data;
}

function get_product($objectID) {
    global $wpdb;
    $product = $wpdb->get_row("SELECT * FROM product WHERE objectID = $objectID");
    if($product != null) {
        return $product;
    }
    
    return false;
}

function fetch_product($link) {
    $html = file_get_contents($link);

    $fields = array(
        "campaign_id",
        "goal",
        "ordered",
        "price",
        "image",
        "image_front",
        "image_back",
        "default_side",
        "name",
        "time_left"
    );

    $data = [];
    foreach($fields as $field) {
        $data[$field] = get_product_field($field, $html);
    }
    $data["sold"] = get_sold($html);
    $data["link"] = $link;

    return $data;
}

function get_sold($html) {
    $xpath = "//div[@class='campaign_stats__value']";
    $sold = get_node_value($xpath, $html, true);

    return $sold;
}

function get_product_field($field, $html) {
    switch($field) {
        case "campaign_id" :
            $re = "/\"campaign_id\":([0-9]+)/mi";
            break;
        case "goal" :
            $re = "/\"goal\":([0-9]+)/mi";
            break;
        case "ordered" :
            $re = "/\"ordered\":([0-9]+)/mi";
            break;
        case "price" :
            $re = "/\"price\":([0-9.]+)/mi";
            break;
        case "image" :
            $re = "/\"image_url\":\"(http(.*).jpg)/mi";
            break;
        case "image_front" :
            $re = "/\"front\":\"(([a-zA-Z0-9_\\/\\-.]+).png)/mi";
            break;
        case "image_back" :
            $re = "/\"back\":\"(([a-zA-Z0-9_\\/\\-.]+).png)/mi";
            break;
        case "default_side" :
            $re = "/\"default_side\":\"([a-z]+)\"/mi";
            break;
        case "name" :
            $re = "/\"name\":\"([^\"]+)\"/mi";
            break;
        case "time_left" :
            $re = "/\"time_left\":\"([^\"]+)\"/mi";
            break;
        default:
            return "";
    }

    if(preg_match($re, $html, $matches)) {
        return $matches[1];
    }

}

function get_node_value($xpath, $url, $single = true) {
    $html_dom = new DOMDocument();
    if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
        $content = file_get_contents($url);
    } else {
        $content = $url;
    }
    @$html_dom->loadHTML($content);
    $x_path = new DOMXPath($html_dom);
    $nodes = $x_path->query($xpath);
    if(count($nodes)) {
        if($single) {
            return $nodes->item(0)->nodeValue;
        }

        $nodeValues = [];
        foreach ($nodes as $node) {
            $nodeValues[] = $node->nodeValue;
        }

        return $nodeValues;
    }
}