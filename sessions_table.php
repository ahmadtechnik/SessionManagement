
<?php
include_once './bootstrap.php';
$sessions_object = new sessions();
$get = $sessions_object->get_all_session_name_without_ducplicat();
$filter = [];
?>

<table id="exists_sessions"  style="max-width:100%" class="display w-100 compact">
    <thead>
        <tr>
            <th>#</th>
            <th>Long</th>
            <th>Starts</th>
            <th>Ends</th>
            <th>Name</th>
            <th>Notes</th>
            <th>Add Date</th>
            <th>Completed</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($get as $key => $value) {
            $left = $value["ends"] - (int) strtotime(date("y-m-d H:i:s"));

            $status = "";
            $completed = 0;
            if ($left < 0) {
                $status = " bg-red fg-dark ";
                $completed = 1;
            } else {
                $status = " bg-green fg-dark ";
                $filter[$value["id"]] = [
                    "left" => $left,
                    "session" => $value
                ];
            }
            ?>
            <tr id="<?php echo $value["id"] ?>" class="<?php echo $status ?> clickablerow" >
                <td><?php echo $value["id"] ?></td>
                <td><?php echo $value["long"] / 60 / 1000 ?> <small>M</small></td>
                <td><?php echo date("D. m Y - H:i", $value["starts"]) ?></td>
                <td><?php echo date("D. m Y - H:i", $value["ends"]) ?></td>
                <td><?php echo $value["name"] ?></td>
                <td><?php echo $value["notes"] ?></td>
                <td><?php echo $value["set_dat"] ?></td>
                <td><?php echo $completed ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<div id="runningSessions"
     class="runningSessions"
     data-role="charms" 
     style="width: 300px;"
     data-position="left">
    <ul class="single_running">
        <?php foreach ($filter as $key => $value) { ?>
            <li>ID : <?php echo $key ?> </li>
            <li>Left Sec. : <span id="left_<?php echo $value["session"]["id"] ?>"> <?php echo convertToHoursMins($value["left"]) ?> </span></li>
            <li>End in : <?php echo date("D. m Y - H:i", $value["session"]["ends"]) ?></li>
            <li class="devider"></li>
        <?php } ?>
    </ul> 
</div>
<button id="show_charmes" style="
        position: fixed;
        top: 10px;
        right:  10px; "
        class="button"
        ><span class="mif-menu "></span>
</button>
<script>
    /**
     * 
     */
    if (!window.invertalsSes) {
        window.invertalsSes = {};
    }
    /**
     * 
     * @type type
     */
    if (Object.keys(window.invertalsSes).length > 0) {
        $.each(invertalsSes, (index) => {
            clearTimeout(window.invertalsSes[index]);
            clearInterval(window.invertalsSes[index]);
            window.invertalsSes[index] = null;
        });
    }
    /**
     * 
     * @type Array|Object
     */
    var active = JSON.parse(`<?php echo json_encode($filter) ?>`);
    if (Object.keys(active).length > 0) {

        $.each(active, (index, sessRow) => {

            var sess_left = sessRow.left * 1000;

            window.invertalsSes[index] = setTimeout(() => {
                
                window.sound = new Howl({
                    src: ['notification.mp3'],
                    autoplay: true,
                    volume: 1,
                    loop: true,
                    onend: function () {
                        update_table();
                    }
                });
                sound.play();

                window.playerTimeout = setTimeout(() => {
                    window.sound.stop();
                }, 1000 * 30);

            }, sess_left);

            // console.log("timeOutStarted For id :" + index + " left : " + sessRow.left);
        });

    }
    console.log(window.invertalsSes);
    // set interval to update page title and charms values
    var title_ = [];
    clearInterval(window.updater);
    window.update = null;
    window.updater = setInterval(() => {
        if (Object.keys(active).length > 0) {
            title_ = [];
            $.each(active, (index, active) => {
                active.left = active.left - 1;
                var active_time = msToTime(active.left * 1000);
                $(`#left_${index}`).html(active_time);
                title_.push(active_time);
            });
            $(`title`).text(title_.join(", "));
        } else {
            $(`title`).text("NO SESSION");
        }
    }, 1000);

    /**
     * 
     * @type type
     */
    DataTable = $(`#exists_sessions`).DataTable({
        responsive: true,

        "order": [[7, "asc"]]
    });

    $(`#show_charmes`).click(() => {
        Metro.charms.toggle($(`#runningSessions`));
    });
</script>
