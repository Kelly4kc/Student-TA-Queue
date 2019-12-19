function get_events(schedule_type) {
    var events_url = "";
    if (schedule_type === "ta") {
        events_url = "../php_helpers/getTASchedule.php";
    } else {
        events_url = "../php_helpers/getLabSchedule.php";
    }
    var sch = fetch(events_url, {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        }
    });
    sch.then(data => data.json())
        .then(function (d) {
            var data = {
                events: d
            };
            new Vue({
                components: {"vue-cal": vuecal},
                el: "#calendar",
                data: function () {
                    return data
                }
            });
        })
        .catch(console.error);
    return sch;
}

