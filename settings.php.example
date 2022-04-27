<?php
if (!defined('INCLUDE_ROOT')) {
    exit('Kein direkter Zugriff erlaubt.');
}

require_once('permissions.php');
require_once('schedule.php');

/* --- Users ---------------------------------------------- */

// To add user: Insert `new User(...)` into $GLOBALS['users']. See permissions.php for available UserRoles.

$GLOBALS['users'] = [
  new User(/* CHANGE ME */, /* CHANGE ME */, UserRole::ADMIN, /* CHANGE ME */),
];


/* --- API ----------------------------------------------- */

/* When querying the Schedule API, this key needs to be sent. */

$GLOBALS['api_key'] = /* CHANGE ME */;


/* --- SCHEDULE DISPLAY DEFAULTS ------------------------- */

/* A tag gives additional meaning to Lessons.
It may change the appearance in the schedule.
A tag can be associated with a Lesson by inserting the tag_string into the 
  respective schedule table cell.
  
Example: By default, `&neu;` within a cell highlights this period as recent change.

The following list maps a tag_string (which you could choose freely) 
  to a code-internal class (changes here require changes in the code base).
*/

$GLOBALS['tags'] = [
    new Tag("&neu;", "new-changes")
];

/* Displayed as accordion section title for information which are not associated to a class. */

$GLOBALS['schedule_common_information'] = "Allgemein";

/* Displayed as group box title for groups without specified title (e.g. if no teacher was given). */

$GLOBALS['schedule_default_group'] = "Gesamte Klasse";


/* --- SCHEDULE FILES ---------------------------------- */

/* Where schedule files are kept.
Must be relative to schedule repository.
Directory must exist.
Trailing `/` required.
You may need to update the .gitignore file. */

$GLOBALS['data_dir'] = "data/";

/* Extension of schedule files.
If changed, existing files must be renamed.
You may need to update the .gitignore file. */

$GLOBALS['path_file_ending'] = ".plan";

/* Date format of schedule files.
If changed, existing files must be renamed.
See https://www.php.net/manual/de/datetime.createfromformat.php
  for possible formats.

Schedule files always have the format of `<date>.<extension>`.
*/

$GLOBALS['path_file_date_format'] = "Y-m-d";  // equals date format: `2022-12-01`
