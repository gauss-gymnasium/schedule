<?php
define('INCLUDE_ROOT', 1);

$title   = 'Vertretungsplan verwalten';
$heading = 'Vertretunspl&auml;ne verwalten';

include_once 'header.php';
include_once 'functions.php';
include_once 'parser.php';

require_admin_permission();

$action = isset($_GET['action']) && $_GET['action'] != '' ? $_GET['action'] : 'new';
$schedule = $_POST['plan'] ?? '';

if ($action == 'new') {
    $date = time();
} else if ($action == 'add') {
    $date = convert_date($_POST['date']);
} else if ($action == 'load') {
    $date = convert_date($_GET['date']);
    $file_path = get_schedule_path($date);
    if (file_exists($file_path)) {
        $schedule = preg_replace("/\r|\n/", "", file_get_contents($file_path));
    } else {
        error_msg("Die Datei existiert nicht. Wenn Sie in der Liste der Pläne auf bearbeiten geklickt haben wenden Sie sich an den Administrator. Der Dateipfad war '$file_path'");
    }
} else {
    error_msg("Undefinierte Aktion: " . $action);
}

if ($action == 'new' || $action == 'load') {
?>

    <h2>Vertretungsplan <?php $action == 'new' ? print("hinzufügen") : print("bearbeiten") ?></h2>
    <p>
    <form method="post" action="edit.php?action=add">
        <div class="my-2">
            <label for="date">Datum:</label>
            <input type="date" name="date" value=<?php echo date("Y-m-d", $date); ?> required />
        </div>

        <textarea>  </textarea>

        <input type="button" class="btn btn-sm btn-primary mt-2" value="<?php $action == 'new' ? print("Hinzufügen") : print("Speichern") ?>" onclick="saveSchedule()" />

        <script>
            tinyMCE.init({
                selector: 'textarea',
                plugins: 'table paste',
                toolbar: 'undo redo | bold italic | table | print ',
                height: "30em",
                elementpath: false,

                setup: function(editor) {
                    editor.on('init', function() {
                        tinymce.activeEditor.setContent(<?php echo "'" . $schedule . "'" ?>);
                    });
                },

                language: 'de',
            });
        </script>

        <div class="d-none">
            <input id="submitBtn" type="submit" value="Hinzufügen" />
            <input type="text" name="plan" id="plan">
        </div>
    </form>

<?php
}

if ($action == 'add') {

    $schedule_valid = true;

    if ($schedule != '') {
    } else {
        $schedule_valid = false;
        error_msg("Der Plan scheint leer zu sein!");
    }

    if ($schedule_valid) {
        $file_path = get_schedule_path($date);

        saveSchedule($file_path, $schedule);

        try {
            // Check whether schedule can be parsed
            parseSchedule($file_path);
        } catch (Exception $e) {
            error_msg("Der Plan ist nicht valide! Überprüfen Sie ihn auf fehlerhafte Eingaben.<br>Bei der Überprüfung ist folgender Fehler aufgetreten:<br>" . $e);
            exit();
        }

        // Delete schedules which are older than 5 days
        $filenames = get_all_schedule_filenames();
        $threshold = strtotime("-5 days");

        $files_to_delete = [];
        foreach ($filenames as $filename) {
            if (get_file_date($filename) < $threshold) {
                array_push($files_to_delete, $filename);
            }
        }

        if (count($files_to_delete) > 0) {
            info_msg("Es gibt Pläne, die älter als 5 Tage sind. Diese werden nun gelöscht.");
            foreach ($files_to_delete as $filename) {
                $file_date = get_file_date($filename);
                $file_path = $GLOBALS['data_dir'] . $filename;
                if (unlink($file_path)) {
                    info_msg('Der Plan vom <strong> ' . date('d.m.Y', $file_date) . '</strong> wurde gelöscht.');
                } else {
                    error_msg('Der Plan vom ' . date('d.m.Y', $file_date) . ' konnte nicht gelöscht werden!');
                }
            }
        }

        success_msg('Datei wurde erfolgreich gespeichert!');
        info_msg('Sie können nun den Plan <a href="index.php?show=' . date($GLOBALS['path_file_date_format'], $date) . '">hier</a> einsehen.');
    }
}

?>

<script lang="js">
    /**
     * Prepares form and triggers saving of schedule via regular HTML forms.
     */
    function saveSchedule() {
        // Copy schedule from TinyMCE to form text area
        const schedule = tinymce.activeEditor.getContent();
        const scheduleContainer = document.getElementById("plan");
        scheduleContainer.value = schedule;

        // Submit form, send via PHP
        document.getElementById("submitBtn").click();
    }
</script>

<?php
include_once 'footer.php';
?>