<?php
session_start();
session_destroy();
header("Location: /backoffice/login");
exit();
