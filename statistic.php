

<?php
include_once './bootstrap.php';
$sessions_object = new sessions();
$get_all = $sessions_object->get_all_inserted_rows();

$total_mins = 0;
$long_in_min = [];
$long_statistic = [];
/**
 * 
 */
foreach ($get_all as $key => $value) {
    $single_long = $value["long"];
    $total_mins += $single_long;
    $name = $value["name"];
    $long = $value["long"];

    if (!isset($long_statistic[$name])) {
        $long_statistic[$name] = $long;
        $long_in_min[$name] = $long;
    } else {
        $long_statistic[$name] += $long;
        $long_in_min[$name] += $long;
    }
}

foreach ($long_statistic as $key => $value) {
    $long_statistic[$key] = number_format(($value / $total_mins) * 100);
}
?>
<div class="pt-6"></div>

<div class=" row rounded mt-1 bg-gray">
    <div class="cell-md  bg-dark fg-white p-1 rounded m-1">Total : <strong><?php echo formatMilliseconds($total_mins); ?></strong> </div>
    <?php if ($get_all) : ?>
        <div class="cell-md  bg-dark fg-white p-1 rounded m-1">First : <strong><?php echo $get_all[0]["set_dat"] ?></strong></div>
        <div class="cell-md  bg-dark fg-white p-1 rounded m-1">Last : <strong><?php echo end($get_all)["set_dat"] ?></strong></div>
    <?php endif; ?>
</div>
<hr>
<div class="tiles-grid">
    <?php foreach ($long_statistic as $key => $value) { ?>
        <div class="tile" data-role="tile" data-size="medium">
            <span class="d-flex flex-justify-center flex-align-center h-100 w-100"><h2 ><?php echo $value ?><sup>%</sup></h2></span>
            <span class="branding-bar"><?php echo $key ?></span>
            <span class="badge-top"><?php echo formatMilliseconds($long_in_min[$key]); ?></span>
        </div>
    <?php } ?>
</div>
