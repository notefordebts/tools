<?php

if (isset($_POST['dn'])) {
    require 'classes/Dns.php';
    require 'classes/Clean.php';
    
    $servers = new Dns();
    $clean = new Clean();
    $domain_name = $_POST['dn'];
    if($clean->clean_domain($domain_name) == false){
        $domain_name = 'domain.com';
    }
    
} else {
    header("/");
    exit();
}



$who_dn = $servers->whois_domain($domain_name);
$status_domain = preg_grep("/(Status)|(Regist)|(name)/i", $who_dn);
foreach ($status_domain as $each) {

    echo '<li>' . $each . '</li>';
}