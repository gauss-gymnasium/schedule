<?php
if (!defined('INCLUDE_ROOT')) {
    exit('Kein direkter Zugriff erlaubt.');
}

include_once 'settings.php';
include_once 'schedule.php';

/**
 * Returns Schedule parsed from `.plan` file.
 * 
 * @param string $file_path Path to `.plan` file to parse
 * @return Schedule Parsed schedule
 */
function parseSchedule(string $file_path): Schedule
{
    $table = get_table($file_path);
    $date = get_date($file_path);

    // First row only contains date
    $table = delete_first_row($table);

    return get_schedule($date, $table);
}

/**
 * Parses and returns schedule table from `$file_path`.
 * 
 * @param string $file_path Path to `.plan` file
 * @return string[][] Parsed schedule table as array with rows*cols
 */
function get_table(string $file_path)
{
    $doc = new DOMDocument();
    $doc->loadHTMLFile($file_path);
    $doc->preserveWhiteSpace = false;

    $table = [];
    $table_node = $doc->getElementsByTagName("table");

    if (!$table_node[0]) throw new Exception("Table node missing");

    $rows = $table_node[0]->getElementsByTagName('tr');
    foreach ($rows as $row) {
        $col_contents = [];
        $cols = $row->getElementsByTagName("td");
        foreach ($cols as $col) {
            array_push($col_contents, clean($col->nodeValue));
        }
        array_push($table, $col_contents);
    }

    return $table;
}

/**
 * Returns schedule date from `$file_path`.
 * 
 * @param string $file_path Path to `.plan` file
 * @return int Schedule date as timestamp
 */
function get_date(string $file_path): int
{
    $date_string = basename($file_path, $GLOBALS['path_file_ending']);
    return DateTime::createFromFormat($GLOBALS['path_file_date_format'], $date_string)->getTimestamp();
}

/**
 * Returns Schedule object of `$date` containing the schedule data in `$table`.
 * Consumes `$table` from top to bottom, filling the found data into a Schedule.
 * 
 * @param int $date Date of Schedule
 * @param string[][] $table Table with schedule data
 * @return Schedule Schedule of `$date` containing the schedule data of `$table`
 */
function get_schedule(int $date, $table): Schedule
{
    $schedule = new Schedule($date);

    while (count($table) > 0) {
        $row = current($table);

        if (is_empty_row($row)) {
            $table = delete_first_row($table);
        } else if (is_schedule_begin($row)) {
            $table = read_lessons($table, $schedule);
        } else {
            $table = read_info($table, $schedule);
        }
    }

    return $schedule;
}

/**
 * Returns table without first row.
 * 
 * @param string[][] $table Table to delete first row from
 * @return string[][] `$table` without first row
 */
function delete_first_row($table)
{
    return array_slice($table, 1);
}

/**
 * Returns true if row is empty.
 * 
 * @param string[] $row Row to check
 * @return bool True if all cells of the row are empty or only contain whitespace
 */
function is_empty_row($row)
{
    foreach ($row as $cell) {
        if ($cell != '') {
            return false;
        }
    }
    return true;
}

/**
 * Returns whether the current row is the head of the actual schedule table (after the general information section).
 * The head is determined by a cell containing only `Stunde`.
 * 
 * @param string[] $row A row of the schedule
 * @return bool True if first row cell contains only `Stunde` (as lowercase). False otherwise
 */
function is_schedule_begin($row): bool
{
    $cell_content = $row[0] ?? '';
    return strtolower($cell_content) == "stunde";
}

/**
 * Adds the general information in first row of `$table` to `$schedule`.
 * A general information applys to all students, e.g. `Ab 6. Stunde Hitzefrei`.
 * A row with a general information contains the general information in the first cell while the others are empty.
 * This method consumes only the first row, thereby deleting it from `$table`.
 * Assumes `$table`'s first row to contain a general information.
 * 
 * @param string[][] $table Schedule table with general information in first row
 * @param Schedule &$schedule Schedule to which the general information will be added
 * @return string[][] `$table` without first consumed row
 */
function read_info($table, Schedule &$schedule)
{
    $first_cell_content = $table[0][0];
    $schedule->add_info($first_cell_content);
    return delete_first_row($table);
}

/**
 * Adds one block of schedule data to `$schedule`.
 * A schedule data block consists of a schedule begin (row containing `Stunde` and grades like `9d`),
 * a row of groups (e.g. teacher `Hr. Mustermann`) and one or multiple rows of periods (e.g. `1.`, `2.`, ...).
 * This method only consumes the first schedule data block, thereby deleting all consumed rows from `$table`. 
 * Assumes `$table`'s first row to contain a schedule begin.
 * 
 * @param string[][] $table Schedule table with schedule data block starting at first row
 * @param Schedule &$schedule Schedule to which consumed lessons will be added
 * @return string[][] `$table` without all consumed rows
 */
function read_lessons($table, Schedule &$schedule)
{
    $grade_row = $table[0] ?? [];
    $teacher_row = $table[1] ?? [];
    $period_row_index = 0;
    $delete_rows_until_index = 0;

    // iterate over all grades (first grade starst at column index 1)
    for ($col = 1; $col < count($grade_row); $col++) {
        $period_row_index = 2;
        update_maximum($period_row_index, $delete_rows_until_index);

        $grade = $grade_row[$col];
        $teacher = $teacher_row[$col];
        $group = new Group($teacher);

        // skip if no grade was given
        if ($grade == '') continue;

        // iterate over all periods (first period starts at row index 2)
        while ($period_row_index < count($table)) {
            $period = $period_row_index - 1;
            if ($period . '.' != $table[$period_row_index][0]) break;

            $cell_content = $table[$period_row_index][$col];
            if ($cell_content != '') $group->add_lesson($period, $cell_content);
            $period_row_index++;
            update_maximum($period_row_index, $delete_rows_until_index);
        }

        $schedule->add_group($grade, $group);
    }

    return array_slice($table, $delete_rows_until_index);
}

/**
 * Overwrites `$maximum` if `$value` is greater.
 * 
 * @param int $value Value which may be written to `$maximum`
 * @param int $maximum Value which is the maximum that may be overwritten
 * @return int Maximum after a possible update
 */
function update_maximum(int $value, &$maximum)
{
    if ($value > $maximum) $maximum = $value;
    return $maximum;
}

/**
 * Removes unwanted whitespace from `$content`.
 * 
 * @param string $content Content with possible spaces at beginning and end, or empty tyble cells (displayed as `&nbsp;`)
 * @return string $content without surrounding whitespace. Empty string if table cell was empty
 */
function clean($content): string
{
    /* properly empties content */
    if ($content == '&nbsp;' || $content == ' ') {
        $content = '';
    }

    /* delete whitespace at beginning and end */
    $content = trim($content, " \t\n\r\0\x0B\xC2\xA0");

    return $content;
}
