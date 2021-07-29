<?php
require 'classes/Act.php';
require 'classes/Db.php';
require 'classes/Dns.php';
require 'classes/Clean.php';


$servers = new Dns();
$clean = new Clean();
$db = new Db();
//$act = new Act();
//$user_ip = $db->ip_add();


//array
$servers_IP = $servers->servers;
$types = $servers->record_type;
//print_r($types);
//if (isset($_POST['submit'])) {
//
//
//
//    $domain_name = $_POST['domain_name'];
//    $type = $_POST['type'];
//    $submit = $_POST['submit'];
//}
  $host = "";
if (isset($_GET['ip'])) {
    // $ip = $_GET['ip'];
    $ip = trim($_GET['ip']);
    //echo $ip;
    $url = "http://ip-api.com/json/{$ip}";
    $json = file_get_contents($url);
    $json_data = json_decode($json, true);
    
    
    //for host 
  
    if($json_data['status'] == "success"){
        
        $host = $servers->check_host($ip);
        $host = $host[0];
        
     }
   
    
}
if (isset($_GET['dn'])) {
    $domain_name = $_GET['dn'];
   
       $domain_name = $clean->clean_domain($domain_name);
      $db->add_domain_name($domain_name);
//       $user_ip = $db->ip_add($domain_name);
       if($domain_name == false){
      // echo $domain_name;
   
       $domain_name = 'domain.com';
       $_GET['dn'] = 'domain.com';
   }
}else{
    $domain_name = '';
}
if (isset($_GET['type'])) {
    
    $type = $_GET['type'];
  $is_type_clean = $clean->clean_type($type);
    if($is_type_clean != true){
      $type = 'A';
    }
}else{
    $type = '';
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>DNS Checker </title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
        
        <!-- Bootstrap Font Icon CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

<script
            src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
        crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

        


    </head>
    <body>

        <div class="container-fluid">
             <div  class="row" style="margin-top:30px;margin-bottom:40px;">
                 <div class="col-lg-12 text-center">
                     <h5 style="margin-bottom:20px;">Last 10 Domain Lookups by your IP <a class="text-info" href="https://tools.johnrit.com/?ip=<?php echo $_SERVER['REMOTE_ADDR']; ?>"><?php echo $_SERVER['REMOTE_ADDR']; ?></a> </h5>
                     <?php
                     $domain_list = $db->get_last_domains();
                     if($domain_list != false){
                       foreach($domain_list as $each){
                         echo '<span style="margin-right:20px;">';
                         echo "<a href='/?dn=".$each['domain_name']."&type=A'>".$each['domain_name']." </a><a href='http://".$each['domain_name']."' target='_blank'><i class='bi bi-search'></i></a>";
                         echo '</span >';
                     }  
                     }else{
                         echo "No domains Searched yet ";
                     }
                     
                     ?>
                     
                     <hr/>
                   
                 </div>
                
            </div>
            
            <div class="row">

                <div class="col-4 ">

                    <div class="row">
                        <div class="col-lg-2">
                        </div>
                        <div class="col-lg-8">

                            <!-- form test  -->
                            <form class="form-group" action="" method="get">

                                <div class="form-group">
                                    <label for="IP-address ">IP address</label>
                                    <input type="text" class="form-control" id="IP-address" name="ip" aria-describedby="emailHelp" value="<?php
                                    if (isset($_GET['ip'])) {
                                        echo $ip;
                                    }
                                    ?>" required="true" placeholder="123.123.123.123">

                                </div>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-primary" name="submit" value="Check">
                                    
                                    
                                </div> 
                            </form>

                            <!-- form test  -->
                        </div>
                        <div class="col-lg-2">
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-2">
                        </div>
                        <div class="col-8">
                            <div class="row">
                            <ul>
                                <?php
                                if (isset($_GET['ip'])) {
                                    echo "<hr/>";
                                    foreach ($json_data as $key => $each) {
                                        echo "<li>" . ucfirst($key) . "  &nbsp; : &nbsp;  <span style=\"color:red;\">" . strtoupper($each) . "</span></li>";
                                    }
                                    echo "<hr/>";
                                }
                                ?>

                            </ul>
                            
                            
                            
                            </div>
                            <!-- row for checking the host-->
                            <div class="row text-center" style="margin-bottom:25px;">
                                <div class="col-12">
                                     <?php 
                                if($host != ""){
                                    echo "<hr/>";
                                    echo "<H4>Hosts PTR </h4>";
                                    echo "<span>{$ip}</span><br/>";
                                    echo "<span class='text-success'>";
                                    echo $host;
                                    echo "</span>";
                                    echo "<hr/>";
                                }
                               
                                
                                ?>
                                </div>
                               
                            </div>
                             <!--end row for checking the host-->
                            <div class="row">
                                <div class="col-lg-12">
                                    <?php
                                    if(isset($ip)){
                                    ?>
                                    <button type="button "class="btn btn-success" id="click_ip" onclick="whois_ip()"> IP abuse</button>
                                    <?php
                                    }
                                    ?>
                                    <script>
function whois_ip(){
      
    $.ajax({
    type:'post',
  url: 'whois_ip.php',
  data:{
      'ip':'<?php if(isset($ip)){echo $ip;} ?>'
  },
  success: function(data) {
     //alert(data);
    $('#whois_ip').html(data);
  }
});
}
  
                                      

                                </script>
                                    <ul id="whois_ip">
                                        
                                        
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-2"></div>
                    </div>

                </div>
                <div class="col-4">
                    <form class="form-group" action="" method="get">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="dn ">Domain Name</label>
                                    <input type="text" class="form-control" id="dn" name="dn" aria-describedby="domain_name" value="<?php
                                    if (isset($domain_name)) {
                                        echo $domain_name;
                                    }
                                    ?>" required="true" placeholder="Domain.com here">

                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="type">Type</label>
                                    <select class="form-select form-control" name="type" aria-label="Default select example">
                                        <?php
                                        if ($types) {

                                            foreach ($types as $each) {
                                                if ($type == $each) {
                                                    echo "<option  value='$each' selected >$each</option>";
                                                } else {
                                                    echo "<option  value='$each'>$each</option>";
                                                }
                                            }
                                        }
                                        ?>

                                    </select>
                                </div>
                            </div> 
                            <div class="col-2">
                                <div class="form-group">
                                    <label for="submit">Submit</label>
                                    <input type="submit" class="btn btn-primary" name="submit" value="Check">
                                   
                                    
                                </div> 
                            </div>
                            
                            
                        </div>
                        
                        <div class="row" style="margin-top:25px;margin-bottom:20px;">
                            
                            <div class="col-lg-12 text-center" >
                                <?php
                            if (isset($domain_name)) {
                                
                                echo '<span class="text-info"><a href="http://'.strtoupper($domain_name).'" target="_blank">' . strtoupper($domain_name).'</a></span>';
                                
                                //SPF checker code 
                                
                               // $servers->execute($each, $domain_name, $type);
                                if($type == "TXT"){
                                     $servers->checkspf($domain_name,$type);
                                }
                                
                                //echo $servers->type . $servers->domain;
//                                if($type == "TXT"){
//                                    $my_out = $act -> dig($domain_name , 'TXT');
//                                if($my_out != false){
//                                    $act->remove_ends($act->separate($my_out));
//                                    $act ->my_switch(0);
//                                    echo " <span>$act->count</span>";
//                                }else{
//                                    echo " NO SPF ";
//                                }
//                                }
                                
                                
                            }
                            ?>
                            </div>
                        </div>
                        
                    </form>

                    <div class="row">
                        <div class="col-12">
                            <?php
                            if (isset($_GET['dn'])) {
                                     //echo "yoyoyoy";
                                foreach ($servers_IP as $each) {
                                    $output = $servers->execute($each, $domain_name, $type);
                                }
                                echo '<pre>';
                                // print_r($servers->output_data);
                                echo '</pre>';
                            }
                            ?>

                            <table class="table">
                                <?php
                                if (isset($_GET['dn'])) {
                                    $true = true;
                                } else {
                                    $true = false;
                                }
                                echo $servers->table($servers->output_data, $true, $type, $domain_name);
                                ?>
                            </table>
                        </div>


                    </div>



                </div>
                <div class="col-4">
                    <div class="row ">
                        <div class="col-6">
                            <?php
                             if (isset($_GET['dn'])) {
                            ?>
                            <h5>Status</h5>
                             <?php
                            
                                echo '<span class="text-info"><a href="http://'.strtoupper($domain_name).'" target="_blank">' . strtoupper($domain_name).'</a></span>';
                             
                            ?>  
                            
                        </div>
                        <div class="col-6">
                            
                            <button type="button "class="btn btn-success" id="click" onclick="whois()">Get Whois </button>
                           
                        </div>
                        <?php 
                             }
                        ?>
                        
                    </div>
                        
                        
                        
                            <script>
function whois(){
      
    $.ajax({
    type:'post',
  url: 'whois.php',
  data:{
      'dn':'<?php echo $domain_name; ?>'
  },
  success: function(data) {
      //alert(data);
    $('#whois').html(data);
  }
});
}
  
                                      

                                </script>

                                <ul id="whois">

                           
                            <?php
                            //whois information 
//                            if (isset($_GET['dn'])) {
//                                $who_dn = $servers->whois_domain($domain_name);
//
//                                $status_domain = preg_grep("/(Status)|(Regist)|(name)/i", $who_dn);
//                                //print_r($status_domain);
//    
//
//                                foreach ($status_domain as $each) {
//                                                               
//                                    echo '<li>' . $each . '</li>';
//                                }
//                                
//                                
//                                
//
//                            }
                            ?>
                        </ul>


                    
                </div>

            </div>

        </div>


    </body>
</html>