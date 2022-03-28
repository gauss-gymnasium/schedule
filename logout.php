<?php
define('INCLUDE_ROOT', 1);

session_start();
session_unset();
?>

Erfolgreich ausgeloggt. Lade auf Startseite weiter...
<script>
    location.href = "index.php"
</script>