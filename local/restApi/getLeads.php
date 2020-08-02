<?php

class Leads
{
    public static function whatDoLeads()
    {
        $caller = new Caller();
        $methodGetLeads = "crm.lead.list";
        $methodGetActivityList = "crm.activity.list";

        $leadsList = $caller->bx24query($methodGetLeads, ['filter' => ['STATUS_ID' => ['IN_PROCESS', 'NEW']], 'start' => '150']);

        $result = [];
        $result['next'] = $leadsList['next'];
        $result['total'] = $leadsList['total'];

        foreach ($leadsList['result'] as $lead) {
            $lead['ASSIGNED_BY_ID'] = self::getIdAssigned($lead['ASSIGNED_BY_ID']);
            $lead['CREATED_BY_ID'] = self::getIdAssigned($lead['CREATED_BY_ID']);

            $idLead = APILead::addLead($lead);

            $activityList = $caller->bx24query($methodGetActivityList, ['filter' => ['OWNER_ID' => $lead['ID']]]);

            foreach ($activityList['result'] as $activity) {
                $activity = array_diff_key($activity, array_flip(['ID', 'END_TIME', 'DEADLINE', 'START_TIME']));
                $activity['OWNER_ID'] = $idLead;
                $activity['EDITOR_ID'] = self::getIdAssigned( $activity['EDITOR_ID']);

                if ($activity['PROVIDER_ID'] === "TASKS" && $activity['COMPLETED'] === "N") {
                    $activity['TITLE'] = $activity['SUBJECT'];
                    $activity['RESPONSIBLE_ID'] = self::getIdAssigned($activity['RESPONSIBLE_ID']);
                    $activity['UF_CRM_TASK'] = ['L_'.$idLead];

                    $idTask = APITasks::addTask($activity);
                    $activity['ASSOCIATED_ENTITY_ID'] = $idTask;
                }

                APIActivity::addActivity($activity);
            }

            $result[] = $idLead;
        }

        return $result;
    }

    final static function getIdAssigned($idAssignedCloud = null)
    {
        switch ($idAssignedCloud) {
            case "38":
                $idAssignedBox = '12';
                break;
            case "18":
                $idAssignedBox = '10';
                break;
            case "26":
                $idAssignedBox = '13';
                break;
            case "20":
                $idAssignedBox = '14';
                break;
            case "1":
                $idAssignedBox = '4';
                break;
            case "10":
                $idAssignedBox = '6';
                break;
            case "14":
                $idAssignedBox = '8';
                break;
            case "8":
                $idAssignedBox = '5';
                break;
            case "16":
                $idAssignedBox = '9';
                break;
            case "12":
                $idAssignedBox = '7';
                break;
            default:
                $idAssignedBox = '4';
        }
        return $idAssignedBox;
    }
}