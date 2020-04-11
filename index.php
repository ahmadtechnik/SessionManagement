
<?php
if (!isset($_GET["ITSME"])) {
    die("<h1>NO ACCESS...</h1>");
}
include_once './bootstrap.php';
$sessions_object = new sessions();
$autoComplete = [];
$get = $sessions_object->get_all_session_name_without_ducplicat();
// TEST CHANGE...
foreach ($get as $key => $row) {
    $autoComplete[$row["name"]] = ucfirst($row["name"]);
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
                    <div class="content bg-light">
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

                            <button class="button primary mt-3" id="add_session_btn">Add Session</button>
                            <button class="button alert mt-3" id="add_completed_session">Add <small>Completed / Started</small> Session</button>
                            <hr>
                        </section>
                    </div>
                </div>

                <div class="frame active">
                    <div class="heading bg-blue fg-white">Old Sessions</div>
                    <div class="content bg-light">
                        <section class="show_sessions_info" id="show_sessions_info">
                            <?php include_once './sessions_table.php'; ?>
                        </section>
                    </div>
                </div>
                <div class="frame active ">
                    <div class="heading bg-blue fg-white">Statistics</div>
                    <div class="content bg-light">
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
        ajaxSetup();
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
            console.log(time_end);
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
                    success: function (data, textStatus, jqXHR) {
                        reCallUpdates();
                    }
                });
            } else {
                alert("PLEASE ENTER SESSION NAME.");
            }

        });
        $(`#add_completed_session`).click(() => {

            $.ajax("addCompletedSession.php", {
                success: function (data) {
                    var selected_long = null;
                    var entered_name = null;
                    var entered_note = null;
                    var dialog = Metro.dialog.create({
                        title: "Use Windows location service?",
                        content: `${data}`,
                        closeButton: false,
                        overlayAlpha: 0.9,
                        removeOnClose: true,
                        actions: [
                            {
                                caption: "Submit",
                                cls: " primary",
                                onclick: function () {
                                    /**
                                     * Form input data
                                     var selected_long = $(`#selected_long`);
                                     var selected_date = $(`#selected_date`);
                                     var selected_time = $(`#selected_time`);
                                     var entered_name = $(`#entered_name`);
                                     var entered_note = $(`#entered_note`);
                                     */

                                    var session_selected_date = Metro.getPlugin("#selected_date", "datepicker").date();
                                    var session_selected_start_time = Metro.getPlugin("#selected_time", "timepicker").time();
                                    var startsFixed = new Date(
                                            session_selected_date.getFullYear(),
                                            session_selected_date.getMonth(),
                                            session_selected_date.getDate(),
                                            session_selected_start_time.h,
                                            session_selected_start_time.m
                                            ).getTime() / 1000;

                                    if (entered_name.val() !== "" && selected_long.val() !== "00:00:00") {
                                        var session_long = Metro.getPlugin("#selected_long", "timepicker").time();
                                        // convert long into millisecound
                                        var inMillSecount = ((session_long.h * 60 * 60) + (session_long.m * 60));
                                        var endOfSesion = startsFixed + inMillSecount;
                                        $.post("post.php", {
                                            order: "inseret_finished_session",
                                            selected_long: inMillSecount * 1000,
                                            selected_date: startsFixed,
                                            session_end: endOfSesion,
                                            entered_name: entered_name.val(),
                                            entered_note: entered_note.val()
                                        }, (response) => {
                                            if (typeof response === "object") {
                                                if (response.INSERTED) {
                                                    Metro.dialog.close(dialog);
                                                }
                                            }
                                            reCallUpdates();
                                        });
                                    } else {
                                        alert("Please enter session name or check Session long.");
                                    }
                                }
                            },
                            {
                                caption: "Cancel",
                                cls: "js-dialog-close"
                            }
                        ],
                        onShow: () => {
                            selected_long = $(`#selected_long`);
                            entered_name = $(`#entered_name`);
                            entered_note = $(`#entered_note`);
                        }
                    });
                }
            });
        });
        /**
         * 
         */
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
                    success: function (data, textStatus, jqXHR) {
                        reCallUpdates();
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
    /**
     * 
     * @returns {undefined}
     */
    var update_statistic = () => {

        $(`#statistikSection`).load("./statistic.php", {}, () => {
            //console.log("complit");
        });
    }

    /**
     * 
     * @param {type} minutes
     * @returns {String}
     */
    var convertMinsToHrsMins = function (minutes) {
        var h = Math.floor(minutes / 60);
        var m = minutes % 60;
        h = h < 10 ? '0' + h : h;
        m = m < 10 ? '0' + m : m;
        return h + ':' + m;
    }
    /**
     * 
     * @param {type} s
     * @returns {String}
     */
    function msToTime(s) {
        var ms = s % 1000;
        s = (s - ms) / 1000;
        var secs = s % 60;
        s = (s - secs) / 60;
        var mins = s % 60;
        var hrs = (s - mins) / 60;
        return  ('0' + hrs).slice(-2) + ':' + ('0' + mins).slice(-2) + ':' + ('0' + secs).slice(-2);
    }
    /**
     * 
     * @returns {undefined}
     */
    function ajaxSetup() {
        $.ajaxSetup({
            dataFilter: (data, type) => {
                try {
                    return JSON.parse(data);
                } catch (e) {
                    return data;
                }
            },
            success: (result, status, xhr) => {

                //console.log(result, status, xhr);
            }
        });
    }
    function reCallUpdates() {
        update_table();
        update_statistic();
    }
</script>
