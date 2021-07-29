<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Dns
 *
 * @author John
 */
class Dns {

    public $servers = [];
    public $record_type = [];
    public $output_data = [];
    public $type;
    public $domain;

    // public $test = [];

    public function __construct() {
        
        $this->servers = [
           'ns1.InMotionHosting'=> "74.124.210.242",
           'ns1.WebHostingHub.com' => "209.182.197.185",
            'Holtsville NY' => '208.67.222.220',
            'Canoga Park, ' => '204.117.214.10',
            'Brossard Canada' => '208.79.56.204',
            //'Amsterdam' => '80.80.80.80',
            'Austria'=> '83.137.41.9',
            'Google' => '8.8.8.8',
            'Yekaterinburg, Russian Federation' => '195.46.39.39',
            'Paris, France' => '163.172.107.158',
            'London, United Kingdom' => '158.43.128.1',
            'Berkley , US' => '9.9.9.9'
            //'here' => 'ip'
        ];

        $this->record_type = [
            'A',
            'MX',
            'TXT',
            'DMARC',
            'DKIM',
            'CNAME',
            'NS'
        ];
    }

    public function clean_input($domain_ip) {
        $domain_ip = trim($domain_ip);
        $filter_domain = filter_var($domain_ip, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME);
        if ($filter_domain) {

            return $filter_domain;
        } else {
            return false;
        }
    }

    public function execute($server, $domain, $type) {
        $this->type = $type;
        $this->domain = $domain;
        if ($type == 'DMARC') {
            $type = 'TXT';
            $domain = '_dmarc.' . $domain;
        } elseif ($type == 'DKIM') {
            $type = 'TXT';
            $domain = 'default._domainkey.' . $domain;
        }

       
          $command = "dig @$server $domain $type +short 2>&1";
          $command = escapeshellcmd("dig @$server $domain $type +short 2>&1");
       
          exec($command , $output);
       
          array_push($this->output_data, $output); 
              
;
        return $output;
    }

    public function table($output, $true, $type, $domain_name) {

        $count = 0;

        foreach ($this->servers as $key => $value) {
            echo '<tr>';
            echo "<td>$key <br><span style='font-size:.6em;color:grey;'>$value</span></td>";
            echo "<td>";
            if ($true) {
                 
                
                if ($type == 'A') {
                    
                    if(!empty($output[$count])){
                    
                    foreach ($output[$count] as $key=>$each) {
                        
                         
                        
                        echo '<a href="/?ip=' . $each . '&dn=' . $domain_name . '&type=' . $type . '" ">' . $each . '</a> <br>';
                        
                    }
                    
                    }else{
                        echo "<span class='text-danger'>No $type record</span>";
                    }
                    
                } elseif ($type == 'TXT' || $type == 'DMARC' || $type == 'DKIM') {
                    //print_r($output[$count]);
                    if(empty($output[$count])){
                        echo "<span class='text-danger'>There is no $type Record</span>";
                     
                    }
                    $count_record = 1;
                    foreach ($output[$count] as $each) {
                        //echo $count;
                       
                        echo "<span class='text-info'>$count_record:</span><span style='font-size:10px;word-break: break-all;font-weight:bold;'> " . $each . "</span><br>";
                    $count_record++;
                        
                    }
                } elseif ($type == 'MX') {
                    
                     if($this->array_is_not_empty($output,$count)){
                        
                    
                    
                    foreach ($output[$count] as $each) {
                        
                       echo  "<span style='font-weight:bold;'>$each</span><br>";
                      
                           
                       
                       $split = explode(" ",$each);
                       if(count($split)> 1){
                         
                       
                      // print_r($split);
                       //echo "<br>";
                     $MX =  $split[1];
                       }else{
                           $MX = $each;
                       }
                       $record = (dns_get_record($MX, DNS_A));
//                       echo "<pre>";
//                       //print_r($record);
//                       echo "</pre>";
                       $count_IP = 0 ;
                       if(!empty($record)){
                           
                       
                       $count_record_array = count($record);
                       
                       //echo $count_record_array. "id record count";
                      echo 'MX resoves to: <br>';
                       for($I=0 ; $I < $count_record_array; $I++){
                           $ip_for_mx =  $record[$I]['ip'] ; 
                            echo ' <a href="/?ip=' . $ip_for_mx . '&dn=' . $domain_name . '&type=' . $type . '" ">' . $ip_for_mx . '</a> <br>';
                            if($I == ($count_record_array - 1) ){
                                echo '<br>';
                            }
                       }
                    }else{
                        echo 'No MX';
                    }
                       
                       //$ip_for_mx =  $record[0]['ip'];
                      // echo 'MX resoves to <a href="/?ip=' . $ip_for_mx . '&dn=' . $domain_name . '&type=' . $type . '" ">' . $ip_for_mx . '</a> <br>';
                       //echo '<br>';
                       
                    }
                    }else{
                        echo "<span class='text-danger'>No MX record</span>";
                    }
                } else {
                    //this section is for any other type record besides A MX txt DKIM of dmarc
                    //if(!empty($output[$count])){
                    if($this->array_is_not_empty($output,$count)){
                    foreach ($output[$count] as $each) {
                        $record = (dns_get_record($each, DNS_A));
                          if(!empty($record)){
                           
                       
                       $count_record_array = count($record);
                       
                       //echo $count_record_array. "id record count";
                      echo $each . ":<br>";
                     // echo 'Record resolves to: <br>';
                       for($I=0 ; $I < $count_record_array; $I++){
                           $ip_for_other =  $record[$I]['ip'] ; 
                            echo ' <a href="/?ip=' . $ip_for_other . '&dn=' . $domain_name . '&type=' . $type . '" ">' . $ip_for_other . '</a> <br>';
                            if($I == ($count_record_array - 1) ){
                                echo '<br>';
                            }
                       }
                    }else{
                        echo "No $type record";
                    }
                        
//                        echo 'this is other ';
//                         
//                         print_r($record);
//                         echo $record[$count]['ip'];
//                        echo $each . "<br>";
                    }
                    }else{
                        echo "<span class='text-danger'>No $type record</span>";
                    }
                }
            }

            echo "</td>";
            echo '</tr>';
            $count++;
        }
        
    }
   
    
    public function array_is_not_empty($array , $count){
        if(!empty($array[$count])){
            return true;
        }
        return false;
    }

    public function whois_domain($domain_name) {
        exec("whois $domain_name  2>&1", $output);
        return $output;
    }

    public function whois_ip($ip) {
        exec("whois $ip 2>&1", $output);
        return $output;
    }
    
    
    
    public function check_host($ip){
        exec("host $ip 2>&1",$output);
        return $output;
    }
    
    
    
    public function checkspf($domain_name , $type){
        //echo $this->domain;
        
        $act = new Act();
         //SPF checker code 
                                if( $type == 'TXT'){
                                    
                                    $my_out = $act -> dig($domain_name , 'TXT');
                                if($my_out != false){
                                    $act->remove_ends($act->separate($my_out));
                                    $act->my_switch(0);
                                    if($act->count <=10){
                                         echo " <span class='text-success'>SPF lookups are $act->count</span>";
                                    }else{
                                    
                                         echo " <span class='text-danger'> HAS too many lookups $act->count</span>";
                                    }
                                    
                                   
                                }else{
                                    echo " <span class='text-danger'>NO SPF </span>";
                                }
                                }
        
    }
    
    
    
    
    
    
    
    
    

}
