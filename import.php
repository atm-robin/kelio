<?php
    require('config.php');
    dol_include_once('/core/lib/function.lib.php');
    dol_include_once('/projet/class/task.class.php');
    require_once(NUSOAP_PATH.'/nusoap.php');
    
    
    function importFromKelio(){
        
        global $db, $user, $conf, $user;
        
        $wsurl = "kelio webservice url";
        $resultClient = new nusoap_client($wsurl);
        
        
        $TResult=fetchFromKelio($resultClient);
        foreach ($employee as $user =>$value) {
            foreach ($job as $task => $value) {
                updateTaskFromKelio($task->jobKey, $task->jobCode, $task->durationInHours, $task->periodStartTime, $task->periodEndTime, $task->jobDescription, $task->percentage);
            }
            
        }
        
    }
    
    
    
    function fetchFromKelio($result){
        
        global $db, $user, $conf, $user;
        
        $parser= new nusoap_parser($result);
        
        
        return $parser;
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
    
