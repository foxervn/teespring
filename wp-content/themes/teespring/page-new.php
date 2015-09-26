<?php date_default_timezone_set("Asia/Ho_Chi_Minh"); ?>

<?php
/*
Template name: New
*/

/*
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(site_url("/")));
}
*/

$page = isset($_GET["page"]) ? $_GET["page"] : 1;
$from = isset($_GET["from"]) ? $_GET["from"] : "all";
$domain = isset($_GET["domain"]) ? $_GET["domain"] : "all";
$per_page = 20;
$products = get_products_mongo($page, $per_page, $from, $domain);
$total_page = $products["total_page"];
?>
<html>
<head>
    <title>Teespring</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php bloginfo("template_url"); ?>/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php bloginfo("template_url"); ?>/css/style.css">
    <?php wp_head(); ?>
</head>
<body>
<div class="header">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="header-buttons">
                    <div class="pull-left btn-home">
                        <a href="<?php echo esc_url(home_url('/')); ?>?page_id=7" class="btn btn-success">Home</a>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-default">Home old</a>
                    </div>
                    <div class="pull-left">
                        <div class="btn-group" role="group" aria-label="...">
                            <a href="<?php the_permalink(); ?>&domain=all"
                               class="btn btn-default<?php echo (!isset($domain) || $domain == "all") ? " active" : "" ?>">Tất
                                cả</a>
                            <a href="<?php the_permalink(); ?>&domain=teespring.com"
                               class="btn btn-default<?php echo (isset($domain) && $domain == "teespring.com") ? " active" : "" ?>">Teespring</a>
                            <a href="<?php the_permalink(); ?>&domain=viralstyle.com"
                               class="btn btn-default<?php echo (isset($domain) && $domain == "viralstyle.com") ? " active" : "" ?>">Viralstyle</a>
                            <a href="<?php the_permalink(); ?>&domain=teechip.com"
                               class="btn btn-default<?php echo (isset($domain) && $domain == "teechip.com") ? " active" : "" ?>">Teechip</a>
                            <a href="<?php the_permalink(); ?>&domain=represent.com"
                               class="btn btn-default<?php echo (isset($domain) && $domain == "represent.com") ? " active" : "" ?>">Represent</a>
                        </div>
                    </div>
                    <div class="pull-right">
                        <div class="btn-group" role="group" aria-label="...">
                            <a href="<?php the_permalink(); ?>&from=all"
                               class="btn btn-default<?php echo (!isset($from) || $from == "all") ? " active" : "" ?>">Tất
                                cả</a>
                            <a href="<?php the_permalink(); ?>&from=fb"
                               class="btn btn-default<?php echo (isset($from) && $from == "fb") ? " active" : "" ?>">Facebook</a>
                            <a href="<?php the_permalink(); ?>&from=pin"
                               class="btn btn-default<?php echo (isset($from) && $from == "pin") ? " active" : "" ?>">Pinterest</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="main">
    <div class="container">
        <div class="row">
            <?php
            if (count($products["data"])) {
                $i = 0;
                foreach ($products["data"] as $product) {
                    $i++;
                    $sold = intval($product["sold"]);
                    $last_sold = intval($product["last_sold"]);
                    $goal = $product["goal"];
                    $status = ($sold >= $goal) ? "reached" : "not reached";
                    $class = ($sold >= $goal) ? "success" : "primary";
                    $time = $product["time_left"];
                    $objectID = $product["campaign_id"];
                    $price = $product["price"];
                    $link = $product["link"];
                    $name = $product["name"];
                    $image_front = $product["image_front"];
                    $image_back = !empty($product["image_back"]) ? $product["image_back"] : $image_front;
                    $from = $product["from"] == "fb" ? "facebook" : "pinterest";
                    $from_class = $product["from"] == "fb" ? "primary" : "warning";
                    $domain = $product["domain"];
                    $fetch_at = $product["fetch_at"] > 0 ? date("Y-m-d H:i:s", $product["fetch_at"]) : "";
                    $sold_increased = $last_sold > 0 ? $sold - $last_sold : "N/A";
                    ?>
                    <div class="col-md-3">
                        <div class="product">
                            <div class="image"
                                 style="background-image:url(<?php echo $image_front; ?>);background-size: auto 229px;"
                                 data-image-front="<?php echo $image_front; ?>"
                                 data-image-back="<?php echo $image_back; ?>">
                                <a target="_blank" href="<?php echo $link; ?>">
                                    &nbsp;
                                </a>
                            </div>
                            <h4><a target="_blank" title="<?php echo $name; ?>"
                                   href="<?php echo $link; ?>"><?php echo $name; ?></a>
                            </h4>

                            <div>Price: $<?php echo $price; ?></div>
                            <div>
                                Sold/goal: <span class="label label-info"><?php echo $sold; ?>
                                    /<?php echo $goal; ?></span>
                            </div>
                            <div>Sold increased: <span class="label label-primary"><?php echo $sold_increased; ?></span>
                            </div>
                            <div>Status: <span class="label label-<?php echo $class; ?>"><?php echo $status; ?></span>
                            </div>
                            <div>Time left: <?php echo $time; ?></div>
                            <div class="from">
                                <span
                                    class="label label-<?php echo $from_class; ?> label-mini label-sm"><?php echo $from; ?></span>
                            </div>
                            <div class="domain">
                                <span class="label label-success label-mini label-sm"><?php echo $domain; ?></span>
                            </div>
                            <div class="last-fetch">
                                <small>
                                    <time class="timeago" datetime="<?php echo $fetch_at; ?>"></time>
                                </small>
                            </div>
                        </div>
                    </div>
                    <?php if ($i % 4 == 0) : ?>
                        <div class="clearfix"></div>
                    <?php endif; ?>
                <?php } ?>

                <?php if ($total_page > 1) : ?>
                    <div class="clearfix"></div>
                    <?php paging($page, $total_page, 5); ?>
                <?php endif; ?>

                <?php
            } else {
                echo "<p>Không có sản phẩm nào</p>";
            }
            ?>
        </div>
    </div>
</div>

<div class="footer">
    <div class="container">
        <div class="row">
            <p>Copyright 2015</p>
        </div>
    </div>
    <?php wp_footer(); ?>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="<?php bloginfo("template_url"); ?>/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php bloginfo("template_url"); ?>/js/jquery.lazyload.min.js"></script>
<script src="<?php bloginfo("template_url"); ?>/js/jquery.timeago.js"></script>
<script>
    $(document).ready(function () {
        $("img.lazy").lazyload();
        $.timeago.settings.strings = {
            suffixAgo: "trước",
            suffixFromNow: "trước",
            inPast: 'any moment now',
            seconds: "vài giây",
            minute: "1 phút",
            minutes: "%d phút",
            hour: "1 giờ",
            hours: "%d giờ",
            day: "1 ngày",
            days: "%d ngày",
            month: "1 tháng",
            months: "%d tháng",
            year: "1 năm",
            years: "%d năm",
        };
        $("time.timeago").timeago();
    });

    $(".image").on({
        mouseover: function () {
            var image_back = $(this).data("image-back");
            $(this)
                .css("opacity", "0")
                .animate({ opacity: 1 }, 3000)
                .css("background-image", "url(" + image_back + ")");
        },
        mouseout: function () {
            var image_front = $(this).data("image-front");
            $(this).css("background-image", "url(" + image_front + ")");
        },
    });

    function changeImage() {
        $(".front").toggle();
        $(".back").toggle();
    }
    //setInterval(changeImage, 1000 * 5);
</script>
</body>
</html>