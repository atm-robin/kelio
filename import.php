<?php
    require('config.php');
    dol_include_once('/core/lib/function.lib.php');
    dol_include_once('/projet/class/task.class.php');

    
    importFromKelio();
    
    function importFromKelio(){
        
        global $db, $user, $conf, $user;
        
        // A l'appel du webservice passer en paramètres enDate >= currentDate et startDate <= currentDate
        //
        $wsdl = "http://www.webservicex.net/sunsetriseservice.asmx?WSDL"; //url du webservice
        $client = new SoapClient($wsdl);
        $wsname= 'exportJobAssignments';
        $TParam=array(
            'calculationMode' => 0 //0 => Réalisé || 1 => Planifié
            ,'endDate' => '' //format : yyyy-MM-dd
            ,'groupFilter' => '' // str 40
            ,'populationFilter' => '' //str 40
            ,'startDate' => '' //format : yyyy-MM-dd
        );
        
/*****//*
        $wsdl = "http://www.webservicex.net/length.asmx?WSDL";
        $client = new SoapClient($wsdl);
        
        $wsname= 'ChangeLengthUnit';
        $LengthValue=1;
        $fromLengthUnit='Miles';
        $toLengthUnit='Kilometers';
        $params=array('LengthValue' => $LengthValue , 'fromLengthUnit' => $fromLengthUnit,'toLengthUnit' => $toLengthUnit);
 

 
        $objStd = $client->__soapCall('ChangeLengthUnit',array('LengthValue' => $LengthValue , 'fromLengthUnit' => $fromLengthUnit,'toLengthUnit' => $toLengthUnit ));
        //print $res->ChangeLengthUnitResult;
        $objStd2 = $client->ChangeLengthUnit(array('LengthValue' => $LengthValue , 'fromLengthUnit' => $fromLengthUnit,'toLengthUnit' => $toLengthUnit ));
        var_dump( $objStd, $objStd2);
        
        exit;*/
/*****/
        
        
        $objStd = $client->__soapCall($wsname, $TParam);
        $objStd2 = $client->{$wsname}($TParam);
        
        var_dump($objStd, $objStd2);
        exit;
        
        $exportJobAssignmentsResponse = $obj->exportJobAssignmentsResponse;
        foreach ($exportJobAssignmentsResponse as $JobAssignment) 
        {
            $ref_task = $JobAssignment->jobCode; //A modifier en fonction de ce que je vois dans le var_dump plus haut 
            $id_task = $JobAssignment->jobKey;
            $label_task= $JobAssignment->jobDescription;
            $task_progress=$JobAssignment->percentage;
            
            $fk_user = $JobAssignment->employeeKey; //_getIdUserByBadge($db, $JobAssignment->employeeBadgeCode);
            
            
            if ($fk_user > 0) _updateTimeTask($db, $fk_user, $ref_task, $duration, $progress, $label_task, $task_progress);
            else exit("userNotFound => ".$JobAssignment->employeeBadgeCode);
        }
        
        
        /*
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
        */
    }
    
    function _updateTimeTask(&$db,$id_task , $fk_user, $ref_task, $duration, $progress, $desc, $label_task, $progress){
        global $user;
        
        $task = new Task($db);
        $task->id=$id_task;
        $task->ref=$ref_task;
        $task->label=$label_task;
        $task->duration_effective=$duration*3600; //duration est en heures dans le webservice et en secondes en bd
        $task->progress=$progress;

        $r = $task->fetch($id_task, $ref_task);
        
        if ($r > 0)
        {
            
            
            $task->addTimeSpent($user);
        }
        else
        {
            exit("taskCantBeFetched =>".$ref_task);
        }
        
    }
    
    
    
    /*
    //fonction qui va récupérer l'id de l'utilisateur via son code de badge récupéré du webservice
    //Créer Extrafield BadgeCode à la table utilisateur!!
    function _getIdUserByBadge(&$db, $employeeBadgeCode){
        
    }
    
     */
     
     
     
/*     
    function fetchFromKelio($client, $wsurl, $wsname, $params){
        
        global $db, $user, $conf, $user;
        
        $result=$client->__soapcall($wsname, $params); //nom du webservice appelé et paramètres

//        simplexml_load_string($xml);
        return $result;
    }
    
    
    
    
    function updateTaskFromKelio(&$PDOdb, $idTask, $refTask, $duration, $dHStart, $dHEnd, $label, $percentage, $dureeEffective){
        global $db, $user, $conf, $user;
        
        
        $dureeTache="";
        $dureeTache+=$dureeEffective*3600;
        if (false){
            $sql ='IF EXISTS (SELECT * FROM '.MAIN_DB_PREFIX.'project_task WHERE Column1='.$idTask.')';
            $sql.='UPDATE '.MAIN_DB_PREFIX.'PROJET_TASK SET duration_effective='.$dureeTache.' progress='.$percentage.' planned_workload='.$duration.'  WHERE rowid='.$idTask;
            $sql.='ELSE';
            $sql='INSERT INTO '.MAIN_DB_PREFIX.'project_task (rowid, ref, duration_effective, tasklist_time_start, label, progress) VALUES ('.$idTask.','.$refTask.', '.$dureeTache.', '.$dHStart.', '.$label.', '.$percentage.')';
            $PDOdb->Execute($sql);
        }
    }
    
*/