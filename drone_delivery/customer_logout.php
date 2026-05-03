<?php
require_once "auth_cliente.php";
session_unset();
session_destroy();
header("Location: login_customer.php");
exit;
