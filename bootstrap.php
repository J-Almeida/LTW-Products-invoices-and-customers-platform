<?php
session_save_path(dirname(__FILE__) . "/sessions");
session_start();
require_once dirname(__FILE__).'/deprecatedfunctions.php';