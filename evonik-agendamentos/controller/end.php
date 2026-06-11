<?php

include_once 'session.php';

    session_destroy();
    echo "<script>window.location='../index.php'</script>";
?>