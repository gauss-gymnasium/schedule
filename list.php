<?php
define('INCLUDE_ROOT', 1);

include_once 'settings.php';

$title   = 'Vertretungspläne';
$heading = 'Liste der Vertretungspläne';

include_once 'header.php';
include_once 'functions.php';

if (isset($_GET['delete'])) {
    require_admin_permission();

    $date = $_GET['delete'];

    if (is_valid_date($date)) {
        if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
            $delete_file_path = get_schedule_path(convert_date($date));
            if (unlink($delete_file_path)) {
                success_msg('Der Plan wurde erfolgreich gelöscht!');
            } else {
                error_msg('Die Datei konnte nicht gelöscht werden!');
            }
        } else {
            echo "<p>Sind Sie sich sicher, dass Sie den Vertretungsplan vom " . formatdate(strtotime($date)) . " löschen wollen? Sie können dies nicht rückgängig machen!<p>" . " <a href='?delete=$date&confirm=yes'>Ja, unwiderruflich löschen</a>" . " | " . "<a href='list.php'>Abbrechen</a>";
            include_once 'footer.php';
            exit();
        }
    } else {
        error_msg('Ungültiges Datum. Erwartet: YYYY-MM-DD. Erhalten: "' . $date . '".');
    }
}

$schedule_filenames = get_all_schedule_filenames();
?>

<ul>
    <?php foreach ($schedule_filenames as $filename) {
        $date = get_file_date($filename);
        $date_str = date($GLOBALS['path_file_date_format'], $date);

        if (!is_admin()) { ?>
            <li class="mb-2">
                <a href="index.php?show=<?php echo $date_str; ?>"><?php echo formatdate($date) ?></a>
            </li>
        <?php
        } else { ?>
            <li class="mb-2">
                <?php echo formatdate($date) ?>
                <ul>
                    <li>
                        <a href="index.php?show=<?php echo $date_str; ?>">ansehen</a> |
                        <a href="edit.php?action=load&date=<?php echo $date_str; ?>">bearbeiten</a> |
                        <a href="?delete=<?php echo $date_str; ?>">löschen</a>
                    </li>
                </ul>
            </li>
    <?php
        }
    }
    ?>
</ul>

<?php include_once 'footer.php'; ?>