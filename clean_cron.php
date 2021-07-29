<?php
//this cron will clean  up the db for all records older than set date 
require 'classes/Db.php';



$db = new Db();


$db->clean_up_db();



