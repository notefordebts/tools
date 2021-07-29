<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Act
 *
 * @author John
 */
class Act {
    public $output_data = [];
    public $removed_ends ;
    public $count;
     public $reps = 0;
    
    
    public function make_sure_spf($record){
        
        foreach($record as $each){
            if(str_contains($each, 'v=')){
                return $each;
            }
        }
        return false;
    }
    
    
   public  function dig($domain , $type){
       
       //@208.67.222.220
       $command = "dig  $domain $type +short 2>&1";
       $command = escapeshellcmd($command);
         //exec("dig  $domain $type +short 2>&1", $output);
          exec($command, $output);
         $spf_record_in_txt = $this->make_sure_spf($output);
         if($spf_record_in_txt != false){
             return $spf_record_in_txt;
         }else{
             return false;
         }
        //return $this->make_sure_spf($output);
        //return $output;
        
        
    }
    
    public function separate($output){
      return   explode(" ", $output);
        
    }
    
    
    public function remove_ends($explode){
        
         array_shift($explode);
         array_pop($explode);
         $this->removed_ends = $explode;
         return $explode;
    }
  
    public function my_switch($count){
//      if($reps < 1){
//             $this->count =  $count++;
//        }
       // $this->count =  $count++;
       // $count = 0 ;
        foreach($this->removed_ends as $each){
            
            //echo '<br>';
        
        switch ($each){
            case '+a':
//                echo "$each<br>";
                  $this->count = ++$count;
//                 echo $count."<br>";
                break;
            case 'a':
//                echo "$each<br>";
                 $this->count = ++$count;
//                echo $count."<br>";
                
                break;
            case str_contains($each, '+a:'):
//                echo "$each<br>";
                 $this->count = ++$count;
//                echo $count."<br>";
                
                break;
             case str_contains($each, 'a:'):
//                echo " $each<br>";
                  $this->count = ++$count;
//                 echo $count."<br>";
                 
                break;
            
            case '+mx':
//                echo "$each<br>";
                 $this->count = ++$count;
//                echo $count."<br>";
                
                break;
            case 'mx':
//                echo "$each<br>";
                $this->count = ++$count;
//                echo $count."<br>";
                
                break;
            case str_contains($each, '+mx:'):
//                echo "$each<br>";
                 $this->count = ++$count;
//                echo $count."<br>";
                
                break;
            case str_contains($each, 'mx:'):
//                echo "$each<br>";
                  $this->count = ++$count;
//               echo $count."<br>";
               
                break;
            case str_contains($each, '+ip4:'):
//               echo "$each<br>";
//                echo $count."<br>";
                break;
            case str_contains($each, 'ip4:'):
//                echo "$each<br>";
//                echo $count."<br>";
                break;
            case str_contains($each, '+ip6:'):
//               echo " $each<br>";
//                echo $count."<br>";
                break;
            case str_contains($each, 'ip6:'):
//                echo "$each<br>";
//                echo $count."<br>";
                break;
            case str_contains($each, '+include:'):
                 $this->count = ++$count;
                $this->count_spf($each, $count);
                
//                echo "$each<br>";
//                echo $count."<br>";
                break;
            case str_contains($each, 'include:'):
                 $this->count = ++$count;
                $this->count_spf($each , $count);
//               echo "$each <br>";
//                echo $count."<br>";
                break;
            default:
                echo $each."default<br>";
                //echo $count."<br>";
         }
         
        
       }
//       echo "reps is ".$reps ."This count ".$this->count;
//       if($reps == 0 ){
//           $this->count = $count;
//       }
      
        
    }
    
    public function count_spf($each , $count){
        
             //$reps = $this->reps =  $this->reps + 1;
       $array_include = $this->break_string($each);
       
      $spf =  $this->dig($array_include[1], 'txt');
      //$this->separate($spf);
     //print_r ($this->remove_ends($this->separate($spf)));
      $this->remove_ends($this->separate($spf));
     $this->my_switch($count);
       // echo $each;
        
    }
    
    public function break_string($string){
       return explode(":", $string);
        
    }
    
    
    
    
    
    
    
    
    
    
}
