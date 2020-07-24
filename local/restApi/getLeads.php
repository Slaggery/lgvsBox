<?php

class Leads
{
    public static function whatDoLeads()
    {
        $caller = new Caller();
        $methodGetLeads = "crm.lead.list";
        $methodGetActivityList = "crm.activity.list";

        $leadsList = $caller->bx24query($methodGetLeads, ['filter' => ['>DATE_CREATE' => '2020-07-16']]);

        $result = [];
        foreach ($leadsList['result'] as $lead) {
            $idLead = APILead::addLead($lead);

            $activityList = $caller->bx24query($methodGetActivityList, ['filter' => ['OWNER_ID' => $lead['ID']]]);

            foreach ($activityList['result'] as $activity) {
                $activity = array_diff_key($activity, array_flip(['ID', 'END_TIME', 'DEADLINE', 'START_TIME']));
                $activity['OWNER_ID'] = $idLead;
                $activity['EDITOR_ID'] = 1;

                if ($activity['PROVIDER_ID'] === "TASKS" && $activity['COMPLETED'] === "N") {
                    $activity['TITLE'] = $activity['SUBJECT'];
                    $activity['RESPONSIBLE_ID'] = 1;
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
}