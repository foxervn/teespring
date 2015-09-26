<?php

/*
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(site_url("/")));
}
*/

$page = isset($_GET["page"]) ? $_GET["page"] : 1;
$per_page = 20;
$data = get_products($page, $per_page);
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
                                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-default">Home</a>
                                <a href="<?php echo esc_url( home_url( '/' ) ); ?>?page_id=7" class="btn btn-success">Home new</a>
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
                    if (isset($data->hits) && count($data->hits)) {
                        $i = 0;
                        foreach ($data->hits as $hit) {
                            $i++;
                            $sold = $hit->amount_ordered;
                            $goal = $hit->tippingpoint;
                            $status = ($sold >= $goal) ? "reached" : "not reached";
                            $class = ($sold >= $goal) ? "success" : "primary";
                            $time = get_time_left($hit->enddate);
                            $objectID = $hit->objectID;
                            $product = get_product($objectID);
                            if(isset($hit->endcost) && !is_object($hit->endcost)) {
                                $endcost = $hit->endcost;
                            } else {
                                $endcost = "N/A";
                            }
                            if($product) {
                                $increase_sold = $sold - $product->sold;
                            } else {
                                $increase_sold = "N/A";
                            }
                            ?>
                            <div class="col-md-3">
                                <div class="product">
                                    <div class="image">
                                        <a href="http://teespring.com/<?php echo $hit->url; ?>"><img alt="<?php echo $hit->name; ?>" src="<?php echo $hit->primary_pic_url; ?>"/></a>
                                    </div>
                                    <h4><a title="<?php echo $hit->name; ?>" href="http://teespring.com/<?php echo $hit->url; ?>"><?php echo $hit->name; ?></a></h4>
                                    <div>Price: $<?php echo $endcost; ?></div>
                                    <div>Sold/goal: <span class="label label-info"><?php echo $sold; ?>/<?php echo $goal; ?></span></div>
                                    <div>Sold increased: <span class="label label-primary"><?php echo $increase_sold; ?></span></div>
                                    <div>Status: <span class="label label-<?php echo $class; ?>"><?php echo $status; ?></span></div>
                                    <div>Time left: <?php echo $time; ?></div>
                                </div>
                            </div>
                            <?php if ($i % 4 == 0) : ?>
                                <div class="clearfix"></div>
                            <?php endif; ?>
                        <?php } ?>

                        <?php paging($page, $data->nbPages, 5); ?>

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
    </body>
</html>