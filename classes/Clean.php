<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Clean
 *
 * @author John
 */
class Clean {
  
  
    
    private function trim($var){
         return  trim($var);
         
    }
    
     public function clean_type($type){
        $servers = new Dns();
       $list_types =  $servers->record_type;
        foreach($list_types as $each){
            if($type == $each){
                return true;
            }
        }
        return false;
        
    }
    
    public function clean_domain($domain){
        $domain_count = strlen($domain);
        if($domain_count > 64){
            return false;
        }
        if($domain[0] == "-"){
            return false;
        }
        
        
        $domain_replace_white = str_replace(' ', '', $domain);
        $domain_replace_white = trim($domain_replace_white);
      $domain_replace_white = str_replace('|', '', $domain_replace_white);
      $domain_replace_white = str_replace("\\", '', $domain_replace_white);
      //echo $domain_replace_white;
      
       // $domain_replace_white =  preg_replace('/[^A-Za-z0-9\-.]/', '', $domain_replace_white); 
         $domain_match_pattern = preg_match('/^(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,6}$/', $domain_replace_white);
         if($domain_match_pattern != false){
             //echo $domain_replace_white;
             return $domain_replace_white;
         }else{
            // echo  $domain_match_pattern;
             return false;
         }
       
        
        
        
        
        
        
    }
    
    public function clean_ip($ip){
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            
    return true;
} else {
    return false;
}
    }
    
    
    
    
    
}
