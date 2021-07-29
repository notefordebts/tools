<?php

if (isset($_POST['ip'])) {
    require 'classes/Dns.php';
    require 'classes/Clean.php';
    
    $servers = new Dns();
    $clean = new Clean();
    $ip = $_POST['ip'];
    if($clean->clean_ip($ip) == false){
        echo 'false';
        $ip = '123.123.123.123';
    }
    
} else {
    header("/");
    exit();
}



$who_ip = $servers->whois_ip($ip);
$status_ip = preg_grep("/(abuse)| (Abuse)|(Country)|(Organization)|(city)|(state)/i", $who_ip);
foreach ($status_ip as $each) {

    echo '<li>' . $each . '</li>';
}