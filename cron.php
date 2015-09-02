<?php
/**
 * Created by PhpStorm.
 * User: bang
 * Date: 8/29/2015
 * Time: 12:01 PM
 */

function fetch_product($link, $from = null)
{
    $headers = get_headers($link, 1);
    if (isset($headers["Status"]) && $headers["Status"] == "404 Not Found") {
        return false;
    }

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
    foreach ($fields as $field) {
        $data[$field] = get_product_field($field, $html);
        if ($field == "price") {
            $data[$field] = (float)$data[$field];
        } elseif (in_array($field, array("campaign_id", "goal", "ordered"))) {
            $data[$field] = (int)$data[$field];
        }
    }
    $data["sold"] = (int)get_sold($html);
    $data["link"] = strtolower($link);
    $data["fetch_at"] = time();
    if ($from) {
        $data["from"] = $from;
    }

    return $data;
}

function get_sold($html)
{
    $xpath = "//div[@class='campaign_stats__value']";
    $sold = get_node_value($xpath, $html, true);

    return $sold;
}

function get_product_field($field, $html)
{
    switch ($field) {
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

    if (preg_match($re, $html, $matches)) {
        return $matches[1];
    }

    return "";
}

function get_node_value($xpath, $url, $single = true)
{
    $html_dom = new DOMDocument();
    if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
        $content = file_get_contents($url);
    } else {
        $content = $url;
    }
    @$html_dom->loadHTML($content);
    $x_path = new DOMXPath($html_dom);
    $nodes = $x_path->query($xpath);
    if (sizeof($nodes) > 0) {
        if ($single) {
            return $nodes->item(0)->nodeValue;
        }

        $nodeValues = [];
        foreach ($nodes as $node) {
            $nodeValues[] = $node->nodeValue;
        }

        return $nodeValues;
    }

    return null;
}

function upsert($link, $last = false)
{
    global $collection;

    $product_info = fetch_product($link, "fb");
    if ($product_info) {
        if ($last) {
            $product_info["last_sold"] = $product_info["sold"];
            $product_info["last_fetch"] = time();
        }

        $collection->update(
            array("link" => $link),
            array('$set' => $product_info),
            array("upsert" => true)
        );
    }
}

if (isset($argv[1])) {
    $mongo = new MongoClient("mongodb://teespring:19001221@127.0.0.1/teespring");
    $collection = $mongo->selectCollection("teespring", "product");

    $action = $argv[1];

    if ($action == "craw") {
        $data = file_get_contents("fb.txt");

        $convert = str_replace(array('\\\\/', '\/'), array('/', '/'), $data);

        $re = "/(https?:\\/\\/teespring.com\\/[a-zA-Z0-9-]+)/mi";

        if (preg_match_all($re, $convert, $matches)) {
            $links = [];
            if (isset($matches[0])) {
                foreach ($matches[0] as $link) {
                    if (!in_array($link, $links)) {
                        $links[] = $link;
                    }
                }
            }

            if (count($links)) {
                foreach ($links as $link) {
                    upsert($link);
                }
            }
        }
    } elseif ($action == "cron") {
        $time = time();
        $limit = 1000;
        $products = $collection->find()->sort(array("sold" => -1))->limit($limit);
        if($products) {
            $last = false;
            $expected_time = strtotime(date("Y-m-d") . " 00:00:00");
            $min_time = $expected_time - 5 * 60;
            $max_time = $expected_time + 5 * 60;
            if($time >= $min_time && $time <= $max_time) {
                $last = true;
            }
            foreach($products as $product) {
                $link = $product["link"];
                upsert($link, $last);
            }
        }
    }
}