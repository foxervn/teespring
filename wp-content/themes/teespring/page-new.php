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
$per_page = 20;
$products = get_products_mongo($page, $per_page);
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
            <div class="col-md-12">
                <h3>Hot campaigns from Teespring</h3>
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
                    $sold = $product["sold"];
                    $last_sold = $product["last_sold"];
                    $goal = $product["goal"];
                    $status = ($sold >= $goal) ? "reached" : "not reached";
                    $class = ($sold >= $goal) ? "success" : "primary";
                    $time = $product["time_left"];
                    $objectID = $product["campaign_id"];
                    $price = $product["price"];
                    $link = $product["link"];
                    $name = $product["name"];
                    $image_front = $product["image_front"];
                    $image_back = $product["image_back"];
                    $sold_increased = $sold - $last_sold;
                    ?>
                    <div class="col-md-3">
                        <div class="product">
                            <div class="image">
                                <a href="<?php echo $link; ?>" data-image-front="<?php echo $image_front; ?>" data-image-back="<?php echo $image_back; ?>">
                                    <img class="lazy" data-original="<?php echo $image_front; ?>" alt="<?php echo $name; ?>" src="<?php echo $image_front; ?>"/>
                                </a>
                            </div>
                            <h4><a title="<?php echo $name; ?>" href="<?php echo $link; ?>"><?php echo $name; ?></a>
                            </h4>

                            <div>Price: $<?php echo $price; ?></div>
                            <div>
                                Sold/goal: <span class="label label-info"><?php echo $sold; ?>/<?php echo $goal; ?></span>
                            </div>
                            <div>Sold increased: <span class="label label-primary"><?php echo $sold_increased; ?></span>
                            </div>
                            <div>Status: <span class="label label-<?php echo $class; ?>"><?php echo $status; ?></span>
                            </div>
                            <div>Time left: <?php echo $time; ?></div>
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
<script>
    $(function() {
        $("img.lazy").lazyload();
    });
    $(".image a").on({
        mouseover: function () {
            var image_back = $(this).data("image-back");
            $("img", this).attr("src", image_back);
        },
        mouseout: function () {
            var image_front = $(this).data("image-front");
            $("img", this).attr("src", image_front);
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