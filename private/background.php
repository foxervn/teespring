<?php
/**
 * Created by PhpStorm.
 * User: bang
 * Date: 8/29/2015
 * Time: 12:01 PM
 */

function getDomain($link) {
    $parse = parse_url($link);
    return isset($parse['host']) ? $parse['host'] : null;
}

function fetch_product($link, $from = null, $domain = null)
{
    $headers = get_headers($link, 1);
    if (isset($headers["Status"]) && $headers["Status"] == "404 Not Found") {
        return false;
    }

    $domain = getDomain($link);
    $html = file_get_contents($link);

    $fields = array(
        "campaign_id",
        "goal",
        "sold",
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
        $data[$field] = get_product_field($field, $html, $domain);
        if ($field == "price") {
            $data[$field] = floatval($data[$field]);
        } elseif (in_array($field, array("goal", "sold", "ordered"))) {
            $data[$field] = intval($data[$field]);
        }
    }

    $data["link"] = strtolower($link);
    $data["fetch_at"] = time();
    if ($from) {
        $data["from"] = $from;
    }
    if($domain) {
        $data["domain"] = $domain;
    }

    return $data;
}

function getSoldTeespring($html)
{
    $xpath = "//div[@class='campaign_stats__value']";
    $sold = get_node_value($xpath, $html, true);

    return $sold;
}

function getGoalSoldViralStyle($html)
{
    $xpath = "//section[@class='inside-count hidden-xs']";
    $count = trim(get_node_value($xpath, $html, true));
    if (!empty($count)) {
        $tmp = explode(" ", $count);

        return $tmp;
    }

    return null;
}

function getSoldViralStyle($html)
{
    $tmp = getGoalSoldViralStyle($html);

    return isset($tmp[0]) ? $tmp[0] : null;
}

function getGoalViralStyle($html)
{
    $tmp = getGoalSoldViralStyle($html);

    return isset($tmp[2]) ? $tmp[2] : null;
}

function getGoalSoldTeeChip($html)
{
    $re = "/([0-9]+) sold. Only ([0-9]+) more to reach our goal/mi";
    if (preg_match($re, $html, $matches)) {
        return $matches;
    }

    return null;
}

function getSoldTeeChip($html)
{
    $matches = getGoalSoldTeeChip($html);
    if (is_array($matches) && isset($matches[1])) {
        return $matches[1];
    }

    return null;
}

function getGoalTeeChip($html)
{
    $matches = getGoalSoldTeeChip($html);
    if (is_array($matches) && isset($matches[1]) && isset($matches[2])) {
        return $matches[1] + $matches[2];
    }

    return null;
}

function getTimeLeftTeeChip($html)
{
    $re = "/endDate = '([0-9]+)';/mi";
    if (preg_match($re, $html, $matches)) {
        $time = $matches[1];
        return getTimeLeftFromInt($time);
    }

    return null;
}

function getTimeLeftFromInt($time)
{
    $end = new DateTime(date("Y-m-d", $time / 1000));
    $now = new DateTime(date("Y-m-d"));
    $interval = $end->diff($now);

    return $interval->format('%a days');
}

function getPriceViralStyle($html) {
    $re = "/\"price\" : \"([0-9.,]+)\"/mi";
    if (preg_match($re, $html, $matches)) {
        $text = $matches[1];
        $price_vnd = str_replace(",", "", $text);
        $price = $price_vnd / 22727.2727;

        return $price;
    }

    return null;
}

function getPriceTeechip($html) {
    $re = "/\"price\":([0-9]+)/mi";
    if (preg_match($re, $html, $matches)) {
        $price = $matches[1] / 100;

        return $price;
    }

    return null;
}

function get_product_field($field, $html, $domain = 'teespring.com')
{
    if ($domain == 'teespring.com') {
        switch ($field) {
            case "campaign_id" :
                $re = "/\"campaign_id\":([0-9]+)/mi";
                break;
            case "goal" :
                $re = "/\"goal\":([0-9]+)/mi";
                break;
            case "sold" :
                return getSoldTeespring($html);
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
                return null;
        }

        if (isset($re)) {
            if (preg_match($re, $html, $matches)) {
                return $matches[1];
            }
        }
    } elseif ($domain == 'viralstyle.com') {
        switch ($field) {
            case "campaign_id" :
                $re = "/\"campaign_id\" value=\"([0-9]+)\"/mi";
                break;
            case "goal" :
                return getGoalViralStyle($html);
            case "sold" :
                return getSoldViralStyle($html);
            case "ordered" :
                break;
            case "price" :
                return getPriceViralStyle($html);
            case "image" :
                $xpath = "//section[@class='product_box']";
                return get_node_value($xpath, $html, true);
            case "image_front" :
                $xpath = "//li[@data-id='front']//img[@class='img_thumbnail']/@data-source-image";
                return get_node_value($xpath, $html, true);
            case "image_back" :
                $xpath = "//li[@data-id='back']//img[@class='img_thumbnail']/@data-source-image";
                return get_node_value($xpath, $html, true);
            case "default_side" :
                break;
            case "name" :
                $xpath = "//*[@class='order_container']/*[@class='container']/h1";
                return get_node_value($xpath, $html, true);
            case "time_left" :
                $xpath = "//*[@class='goal_area clearfix']/*[@class='desc']";
                return get_node_value($xpath, $html, true);
            default:
                return null;
        }

        if (isset($re)) {
            if (preg_match($re, $html, $matches)) {
                return $matches[1];
            }
        }
    } elseif ($domain == 'teechip.com') {
        switch ($field) {
            case "campaign_id" :
                $re = "/campaignId = '([0-9a-z]+)'/mi";
                break;
            case "goal" :
                return getGoalTeeChip($html);
            case "sold" :
                return getSoldTeeChip($html);
            case "ordered" :
                break;
            case "price" :
                return getPriceTeechip($html);
            case "image" :
                $xpath = "//*[@property='og:image']/@content";
                return get_node_value($xpath, $html, true);
            case "image_front" :
                $xpath = "//*[@property='og:image']/@content";
                return get_node_value($xpath, $html, true);
            case "image_back" :
                break;
            case "default_side" :
                break;
            case "name" :
                $xpath = "//*[@property='og:title']/@content";
                return get_node_value($xpath, $html, true);
            case "time_left" :
                return getTimeLeftTeeChip($html);
            default:
                return null;
        }

        if (isset($re)) {
            if (preg_match($re, $html, $matches)) {
                return $matches[1];
            }
        }
    } elseif ($domain == 'represent.com') {
        switch ($field) {
            case "campaign_id" :
                $re = "/id=\"campaign_id\" value=\"([0-9]+)\"/mi";
                break;
            case "goal" :
                $xpath = "//*[@class='campaign-goal-tilts-at']/b";
                return get_node_value($xpath, $html, true);
            case "sold" :
                $xpath = "//*[@class='campaign-goal-shirts']/b";
                return get_node_value($xpath, $html, true);
            case "ordered" :
                break;
            case "price" :
                $xpath = "//*[@property='og:price:amount']/@content";
                return get_node_value($xpath, $html, true);
            case "image" :
                $xpath = "//*[@property='og:image']/@content";
                return get_node_value($xpath, $html, true);
            case "image_front" :
                $xpath = "//*[@property='og:image']/@content";
                return get_node_value($xpath, $html, true);
            case "image_back" :
                break;
            case "default_side" :
                break;
            case "name" :
                $xpath = "//*[@property='og:title']/@content";
                return get_node_value($xpath, $html, true);
            case "time_left" :
                return getTimeLeftTeeChip($html);
            default:
                return null;
        }

        if (isset($re)) {
            if (preg_match($re, $html, $matches)) {
                return $matches[1];
            }
        }
    }

    return null;
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

function upsert($link, $last = false, $from = null, $domain = null)
{
    if ($from) {
        echo "update or insert link: $link from $from \n";
    } else {
        echo "update link: $link \n";
    }

    global $collection;

    $product_info = fetch_product($link, $from, $domain);
    if ($product_info) {
        if ($last) {
            $product_info["last_sold"] = $product_info["sold"];
            $product_info["last_fetch"] = $product_info["fetch_at"];
        }

        $collection->update(
            array("link" => $link),
            array('$set' => $product_info),
            array("upsert" => true)
        );
    }
}

if (isset($argv[1])) {
    $action = $argv[1];

    $mongo = new MongoClient("mongodb://teespring:19001221@127.0.0.1/teespring");
    $collection = $mongo->selectCollection("teespring", "product");

    if ($action == "craw") {
        if (isset($argv[2]) && isset($argv[3])) {
            $from = $argv[2];
            $domain = $argv[3];
            $file = "{$from}-{$domain}.txt";
            $data = file_get_contents("/home/www/html/bcdcnt.net/demo.bcdcnt.net/teespring/private/$file");

            $convert = str_replace(array('\\\\/', '\/'), array('/', '/'), $data);

            $re = "/(https?:\\/\\/{$domain}\\/[a-zA-Z0-9-\/]+)/mi";

            if (preg_match_all($re, $convert, $matches)) {
                $links = [];
                if (isset($matches[0])) {
                    foreach ($matches[0] as $link) {
                        if (!in_array($link, $links)) {
                            $links[] = strtolower($link);
                        }
                    }
                }

                if (count($links)) {
                    foreach ($links as $link) {
                        upsert($link, false, $from, $domain);
                    }
                }
            }
        }
    } elseif ($action == "cron") {
        $time = time();
        $limit = 1000;
        $products = $collection->find()->sort(array("sold" => -1))->limit($limit);
        if ($products) {
            echo "preparing update " . sizeof($products) . " links \n";
            $last = false;
            $end_time = strtotime(date("Y-m-d") . " 00:00:01");
            $min_time = $end_time - 5 * 60;
            $max_time = $end_time + 5 * 60;
            if ($time >= $min_time && $time <= $max_time) {
                $last = true;
            }

            foreach ($products as $product) {
                $link = $product["link"];
                upsert($link, $last);
            }
        } else {
            echo "no product to update";
        }
    }
}
