<?php

/**
 * Outputs the HTML for a container with a warning, e.g. on an ongoing maintenance.
 * Usage: Insert `include 'messages/messageWarning.php'` whereever this message should be displayed.
 */

if (!defined('INCLUDE_ROOT')) {
    exit('Kein direkter Zugriff erlaubt.');
}

?>

<div class="message warning-message">
    <i class="material-icons message-icon">error_outline</i>
    <span class="message-text">
        Aktuell werden Wartungsarbeiten durchgeführt. Der Plan ist unter Umständen nicht korrekt. Bitte probiere es um 23:00 noch einmal.
    </span>
</div>