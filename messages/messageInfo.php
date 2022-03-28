<?php

/**
 * Outputs the HTML for a container with an information, e.g. about a beta version of the schedule software.
 * Usage: Insert `include 'messages/messageInfo.php'` whereever this message should be displayed.
 */

if (!defined('INCLUDE_ROOT')) {
    exit('Kein direkter Zugriff erlaubt.');
}

?>

<div class="message info-message">
    <i class="material-icons message-icon">info_outline</i>
    <span class="message-text">
        Der Vertretungsplan hat ein Update erhalten, um langfristig weniger fehleranfällig zu sein. Trotzdem kann es in der Anfangszeit zu Darstellungsfehlern kommen. Bitte <a href="/kontakte" target="_blank">melde diese bei Frau Stolpe</a>.
    </span>
</div>