<?php
@session_start();
session_start ();
session_unset ();
session_destroy();
header("Location: index.php?section=home");
ob_end_flush ();
?>
