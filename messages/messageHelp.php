<?php

/**
 * Outputs the HTML for a container with a help message.
 * Usage: Insert `include 'messages/messageHelp.php'` whereever this message should be displayed.
 */

if (!defined('INCLUDE_ROOT')) {
    exit('Kein direkter Zugriff erlaubt.');
}

?>

<div class="message help-message">
    <i class="material-icons message-icon">help_outline</i>
    <span class="message-text">
        Fehlerhafte Anzeige oder fragwürdiger Plan? Melde Unregelmäßgkeiten gerne an <a href="/kontakte" target="_blank">Frau Stolpe!</a>
    </span>
</div>