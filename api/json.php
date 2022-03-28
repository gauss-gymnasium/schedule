<?php

/**
 * Returns schedule in JSON format.
 * 
 * POST Request structure:
 * 
 *   - (string) key:   Required. Identification key
 *   - (string) date:  Optional. If set, only the schedule of this day will be returned. Default: Returns all available schedules.
 * 
 * Responses:
 *   - <condition>: <HTTP status code>, <payload>
 * 
 *  - If successful:          200, schedule data as JSON
 *  - If key missing/wrong:   401, error message (string)
 *  - If date format invalid: 400, error message (string)
 * 
 * For debugging: How to manually post the request in a browser
 *   - Request Type:       POST
 *   - In Request headers: Add `Content-type: application/x-www-form-urlencoded`
 *   - In Request body:    Insert POST request in the format `key=<API key>&date=<YYYY-MM-DD>`
 * 
 */

define('INCLUDE_ROOT', 1);

include_once '../settings.php';
include_once '../functions.php';
include_once '../parser.php';

if (!isset($_POST['key'])) {
    http_response_code(401);
    exit('API key missing.');
}

if ($_POST['key'] !== $GLOBALS['api_key']) {
    http_response_code(401);
    exit('API key wrong. Sent key: "' . $_POST['key'] . '"');
}

$date = null;

if (isset($_POST['date'])) {
    if (!is_valid_date($_POST['date'])) {
        http_response_code(400);
        exit('Format of requested date wrong. Expected: "YYYY-MM-DD". Received: "' . $_POST['date'] . '".');
    }
    $date = convert_date(clean($_POST['date']));
}

header('Content-type: application/json');

$data_dir = '../' . $GLOBALS['data_dir'];

if (is_null($date)) {
    $schedule_filenames = get_all_schedule_filenames($data_dir);
    $schedules = [];
    foreach ($schedule_filenames as $filename) {
        $file_path = $data_dir . $filename;
        array_push($schedules, parseSchedule($file_path));
    }
    exit(json_encode($schedules));
}

$file_path = get_schedule_path($date);

if (!file_exists($file_path)) {
    http_response_code(404);
    exit('There is no schedule for this date: "' . $_POST['date'] . '"');
}

$schedule = parseSchedule($file_path);
exit(json_encode($schedule));
