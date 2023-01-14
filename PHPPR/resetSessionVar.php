<?php

session_start();
//reset session variables
foreach ($_SESSION as $key => $value) {
    unset($_SESSION[$key]);
}
