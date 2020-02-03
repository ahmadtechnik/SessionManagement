

<?php
include_once './bootstrap.php';
$sessions_object = new sessions();
$get_all = $sessions_object->get_all_inserted_rows();

$total_mins = 0;
$object = [];
$long_statistic = [];
/**
 * 
 */
foreach ($get_all as $key => $value) {
    $single_long = $value["long"];
    $total_mins += $single_long;
    $name = $value["name"];
    $long = $value["long"];

    if (!isset($object[$name])) {
        $object[$name] = 0;
    } else {
        $object[$name] ++;
    }

    if (!isset($long_statistic[$name])) {
        $long_statistic[$name] = $long;
    } else {
        $long_statistic[$name] += $long;
    }
}

foreach ($long_statistic as $key => $value) {
    $long_statistic[$key] = number_format(($value / $total_mins) * 100);
}

/**
 * 
 */
foreach ($object as $key => $value) {
    $object[$key] = number_format(($value / count($get_all) * 100), 2) . "%";
}
?>
<div class="pt-6"></div>

    <div class=" row rounded mt-1 bg-gray">
        <div class="cell-md  bg-dark fg-white p-1 rounded m-1">Total : <?php echo formatMilliseconds($total_mins); ?> </div>
        <?php if ($get_all) : ?>
            <div class="cell-md  bg-dark fg-white p-1 rounded m-1">Starts : <?php echo $get_all[0]["set_dat"] ?></div>
            <div class="cell-md  bg-dark fg-white p-1 rounded m-1">Ends : <?php echo end($get_all)["set_dat"] ?></div>
        <?php endif; ?>
    </div>
    <hr>
    <div class="tiles-grid">
        <?php foreach ($long_statistic as $key => $value) { ?>
            <div class="tile"  data-role="tile" data-size="medium">
                <span class="mif-chart-bars icon"></span>
                <span class="branding-bar"><?php echo $key ?></span>
                <span class="badge-top"><?php echo $value ?>% <sup>
                        <?php echo formatMilliseconds($value / 100 * ($total_mins )); ?>m</sup>
                </span>
            </div>
        <?php } ?>
    </div>
