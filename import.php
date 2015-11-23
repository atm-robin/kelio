<?php
    require('config.php');
    dol_include_once('/core/lib/function.lib.php');
    dol_include_once('/projet/class/task.class.php');
    require_once(NUSOAP_PATH.'/nusoap.php');
    
    
    importFromKelio();
    
    function importFromKelio(){
        
        global $db, $user, $conf, $user;
        
        // A l'appel du webservice passer en paramètres enDate >= currentDate et startDate <= currentDate
        //
        $wsurl = "http://www.webservicex.net/sunsetriseservice.asmx?WSDL"; //url du webservice
        $wsname= 'GetSunSetRiseTime';
        $params=array('Latitude'=>44.9,'Longitude'=> 4.9);
        $client = new SoapClient($wsurl);

        
        $TResult=fetchFromKelio($client, $wurl, $wsname, $params);
        var_dump($TResult);
        exit;
        
        
        $dureeEffective=$task->periodEndTime - $task->peridoStartTime;
        
        
        foreach ($employee as $user) {
            foreach ($job as $task) {        
                
                if ($task->percentage==null){
                    $pourcentageTache = $task->durationInHours*(($task->periodEndTime-$task->periodStartTime)/100);
                    updateTaskFromKelio($task->jobKey, $task->jobCode, $task->durationInHours, $task->periodStartTime, $task->periodEndTime, $task->jobDescription, $pourcentage);

                }else{
                    updateTaskFromKelio($task->jobKey, $task->jobCode, $task->durationInHours, $task->periodStartTime, $task->periodEndTime, $task->jobDescription, $task->percentage, $dureeEffective);
                }
            }
            
        }
        
    }
    
    
    
    function fetchFromKelio($client, $wsurl, $wsname, $params){
        
        global $db, $user, $conf, $user;
        
        
        $result=$client->__soapcall($wsname, $params); //nom du webservice appelé et paramètres

//        simplexml_load_string($xml);
        

        return $result;
    }
    
    
    
    
    function updateTaskFromKelio(&$PDOdb, $idTask, $refTask, $duration, $dHStart, $dHEnd, $label, $percentage){
        global $db, $user, $conf, $user;
        
        
        $dureeTache="";
        $dureeTache+=$duration*3600;
        
        $sql ='IF EXISTS (SELECT * FROM '.MAIN_DB_PREFIX.'project_task WHERE Column1='.$idTask.')';
        $sql.='UPDATE '.MAIN_DB_PREFIX.'PROJET_TASK SET duration_effective='.$dureeTache.' progress='.$dureeTache.' WHERE rowid='.$idTask;
        $sql.='ELSE';
        $sql='INSERT INTO '.MAIN_DB_PREFIX.'project_task (rowid, ref, duration_effective, tasklist_time_start, label, progress) VALUES ('.$idTask.','.$refTask.', '.$dureeTache.', '.$dHStart.', '.$label.', '.$percentage.')';
        $PDOdb->Execute($sql);
    }
    
