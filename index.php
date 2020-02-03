
<?php

include_once './bootstrap.php';
$sessions_object = new sessions();
$autoComplete = [];
$get = $sessions_object->get_all_session_name_without_ducplicat();

foreach ($get as $key => $row) {
    array_push($autoComplete, $row["name"]);
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdn.metroui.org.ua/v4/css/metro-all.min.css">
        <link rel="stylesheet" type="text/css" 
              href="./DataTables/datatables.min.css"/>

        <script src="https://cdn.metroui.org.ua/v4/js/metro.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.4.1.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.js"></script>

        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
        <script type="text/javascript" src="./DataTables/datatables.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/howler/2.1.3/howler.core.min.js"></script>
        <title>SESSION MANAGER</title>
    </head>
    <body>

        <div class="container">
            <div data-role="accordion" 
                 data-material="true"
                 data-one-frame="false" 
                 data-show-active="true" >
                <div class="frame">
                    <div class="heading bg-blue fg-white">Add Session</div>
                    <div class="content">
                        <section class="add_session_section " id="add_session_section">
                            <div class="form-group">
                                <label>SESSION STARTS</label>
                                <input data-role="timepicker"
                                       data-seconds="false" 
                                       id="session_long"
                                       data-history="false"
                                       data-value="00:25">
                            </div>

                            <div class="form-group">
                                <label>SESSION NAME</label>
                                <input data-role="input"
                                       data-autocomplete="<?php echo join(", ", $autoComplete) ?>"
                                       id="session_name">
                            </div>
                            <div class="form-group">
                                <label>SESSION NOTES</label>
                                <textarea data-role="textarea"
                                          id="session_notes"></textarea>
                            </div>

                            <button class="button primary mt-3" id="add_session_btn">Add session</button>
                            <hr>
                        </section>
                    </div>
                </div>

                <div class="frame active">
                    <div class="heading bg-blue fg-white">Old Sessions</div>
                    <div class="content">
                        <section class="show_sessions_info" id="show_sessions_info">
                            <?php include_once './sessions_table.php'; ?>
                        </section>
                    </div>
                </div>
                <div class="frame active ">
                    <div class="heading bg-blue fg-white">Statistics</div>
                    <div class="content">
                        <section id="statistikSection" class="statistikSection "></section>
                    </div>
                </div>
            </div>
        </div>
    </body>
    <style>
        section{

        }
    </style>
</html>
<script >
    window.invertals = {};
    $(document).ready(() => {
        /**
         * 
         */
        $(`#add_session_btn`).click(() => {
            var session_long = Metro.getPlugin("#session_long", "timepicker").time();
            var session_name = $(`#session_name`).val();
            var session_notes = $(`#session_notes`).val();
            var selected_h = session_long.h * 60 * 60;
            var selected_m = session_long.m * 60;
            var long_in_millsc = (selected_h + selected_m) * 1000;

            var time_starts = new Date().getTime() / 1000;
            var time_end = time_starts + (long_in_millsc / 1000);

            if (session_name !== "") {
                $.ajax("post.php", {
                    type: 'POST',
                    data: {
                        order: "set_new_session",
                        session_name: session_name,
                        session_notes: session_notes,
                        long_in_millsc: long_in_millsc,
                        time_starts: time_starts,
                        time_end: time_end
                    },
                    success: function (data) {
                        update_table();
                        update_statistic();
                        console.log(data);
                    }
                });
            } else {
                alert("PLEASE ENTER SESSION NAME.");
            }

        });

        window.invertals.updateTable = setInterval(update_table, 1000 * 60);

        $(document).on("click", '.clickablerow', (event) => {
            var id = $(event.target).closest("tr").attr("id");
            if (event.ctrlKey) {
                $.ajax("post.php", {
                    type: 'POST',
                    data: {
                        order: "remove_row_from_db",
                        row_id: id
                    },
                    success: function (data) {
                        update_table();
                        update_statistic();
                    }
                });
            }
        });

        /**
         * 
         * @returns {undefined}
         */
        update_statistic();
    });
    /**
     * 
     * @returns {undefined}
     */
    function update_table() {
        $.ajax("post.php", {
            type: 'POST',
            data: {
                order: "get_session_table"
            },
            success: function (data) {
                $(`#show_sessions_info`).html(data);
            }
        });
    }
    var update_statistic = () => {

        $(`#statistikSection`).load("./statistic.php", {}, () => {
            //console.log("complit");
        });
    }


    var convertMinsToHrsMins = function (minutes) {
        var h = Math.floor(minutes / 60);
        var m = minutes % 60;
        h = h < 10 ? '0' + h : h;
        m = m < 10 ? '0' + m : m;
        return h + ':' + m;
    }

    function msToTime(s) {
        var ms = s % 1000;
        s = (s - ms) / 1000;
        var secs = s % 60;
        s = (s - secs) / 60;
        var mins = s % 60;
        var hrs = (s - mins) / 60;

        return  ('0' + hrs).slice(-2) + ':' + ('0' + mins).slice(-2) + ':' + ('0' + secs).slice(-2);
    }

</script>
