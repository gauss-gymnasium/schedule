<?php
if (!defined('INCLUDE_ROOT')) {
    exit('Kein direkter Zugriff erlaubt.');
}

/**
 * Returns timestamp for date string.
 * 
 * @param string $date Date to convert
 * @return int Timestamp of given date
 */
function convert_date(string $date): int
{
    return DateTime::createFromFormat($GLOBALS['path_file_date_format'], $date)->getTimestamp();
}

/**
 * Returns the file path of a schedule of a certain day. Default: today's schedule file path.
 * 
 * @param int|null $date Date to get schedule file path for. Returns today's schedule file path if `null`.
 * @return string File path of `$date`'s schedule
 */
function get_schedule_path(?int $date = null): string
{
    $date = $date ?? time();
    $format = $GLOBALS['path_file_date_format'];
    $dir = $GLOBALS['data_dir'];
    $extension = $GLOBALS['path_file_ending'];
    return $dir . date($format, $date) . $extension;
}

/**
 * Returns timestamp of next regular school day, i.e. Monday to Friday.
 * Does not take holidays into account.
 * 
 * @return int Timestamp of next regular school day
 */
function next_school_day(): int
{
    $offset_days = 1;
    $today = date("l");
    if ($today === 'Saturday') {
        $offset_days = 2;
    } else if ($today === 'Friday') {
        $offset_days = 3;
    }

    $seconds_per_day = 86400;
    $offset_seconds = $seconds_per_day * $offset_days;

    return time() + $offset_seconds;
}

/**
 * Returns the file names of all available schedules.
 * 
 * @param string $data_dir_path Path to the schedule data files directory. Default: defined in $GLOBALS.
 * @return string[] All available schedule file na,es
 */
function get_all_schedule_filenames($data_dir_path = null)
{
    $data_dir_path = $data_dir_path ?? $GLOBALS['data_dir'];
    $all_files = scandir($data_dir_path);
    return array_filter($all_files, 'is_schedule_file_path');
}

/**
 * Returns whether given filename may be a valid schedule file.
 * 
 * @param string filename Filename to validate
 * @return bool True if the filename ends with schedule file extension
 */
function is_schedule_file_path(string $filename)
{
    $extension = $GLOBALS['path_file_ending'];
    return substr($filename, -strlen($extension)) === $extension;
};

/**
 * Returns date from given schedule filename.
 * 
 * @param string $filename Filename of schedule file (without directory path)
 * @return int Timestamp from `$filename`'s date
 */
function get_file_date($filename): int
{
    $extension = $GLOBALS['path_file_ending'];
    return convert_date(substr($filename, 0, -strlen($extension)));
}

/**
 * Saves schedule data in file.
 * 
 * @param string $file_path Path to schedule file
 * @param string $schedule Path to schedule file
 */
function saveSchedule(string $file_path, string $schedule)
{
    if (!$handler = fopen($file_path, "w")) {
        error_msg('Fehler: Verzeichnis existiert nicht: "' . $file_path . '".<br>Wenden Sie sich an den Administrator.');
        exit();
    };

    if (!fwrite($handler, stripslashes($schedule))) {
        error_msg('Fehler: Kann nicht in die Datei schreiben: "' . $file_path . '".');
        exit();
    }
}

/**
 * Returns `$date` formatted as German `Day, D. Month YYYY`, e.g. "Montag, 01. Januar 2022"
 * 
 * @param int Date as timestamp
 * @return string Formatted date
 */
function formatdate(int $date): string
{
    $day   = array("Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag");
    $month = array('', 'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember');
    return $day[date('w', $date)] . ', ' . date('j', $date) . '. ' . $month[date('n', $date)] . ' ' . date('Y', $date);
}

/**
 * Returns whether given date is formatted as `YYYY-MM-DD`
 * 
 * @param string Date as string
 * @return bool True if `$date_string` has the format `YYYY-MM-DD`
 */
function is_valid_date(string $date_string): bool
{
    $regexp = '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/';
    return preg_match($regexp, $date_string);
}

/**
 * Prints error message.
 * 
 * @param string $message Message to display
 * @param bool $containered If true, the message is displayed within a colored container. If not, only text is displayed
 */
function error_msg(string $message, bool $containered = false)
{
    print "<p class='error" . ($containered ? " bold" : "") . "'>$message</p>";
}

function info_msg($message)
{
    print "<p class='info'>$message</p>";
}

function success_msg($message)
{
    print "<p class='success'>$message</p>";
}
