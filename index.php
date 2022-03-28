<?php
define('INCLUDE_ROOT', 1);

$title   = "Vertretungsplan";
$heading = 'Die aktuellen Vertretungspläne';

include_once 'header.php';
include_once 'functions.php';
include_once 'parser.php';

?>

<p>In den Ferien bitte den Plan aus der <a href="list.php">Liste der Pläne</a> entnehmen.</p>
<br />

<?php
if (!isset($_GET['show'])) {
?>

    <h3>Heute (<?php echo formatdate(time()) ?>)</h3>

    <?php

    $file_path = get_schedule_path();
    if (!file_exists($file_path)) {
        error_msg('Für heute, ' . formatdate(time()) . ', gibt es keinen Vertretungsplan');
    } else {
        $schedule = parseSchedule($file_path);
        include 'schedule_html.php';
    }

    ?>

    <br>

    <h3>Nächster Schultag (<?php echo formatdate(next_school_day()) ?>)</h3>

    <?php

    $file_path = get_schedule_path(next_school_day());
    if (!file_exists($file_path)) {
        error_msg('Für den nächsten Schultag gibt es noch keinen Vertretungsplan');
    } else {
        $schedule = parseSchedule($file_path);
        include 'schedule_html.php';
    }
    ?>

<?php
} else if (is_valid_date($_GET['show'])) {
    $date = convert_date($_GET['show']);
?>

    <h3><?php echo formatdate($date) ?></h3>

    <?php
    $file_path = get_schedule_path($date);
    if (!file_exists($file_path)) {
        error_msg('Für diesen Tag gibt es keinen Vertretungsplan');
    } else {
        $schedule = parseSchedule($file_path);
        include 'schedule_html.php';
    }
    ?>

<?php
} else {
    error_msg('Der Link ist ungültig');
}

include_once 'footer.php';
?>