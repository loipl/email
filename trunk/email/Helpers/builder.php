<?php

class Builder
{

    public static function buildLeadData($leads)
    {
        foreach($leads AS $lead) {
            $leadData[md5(strtolower($lead['email']))] = $lead;
        }

        if (!empty($leadData)) {
            return $leadData;
        } else {
            return false;
        }
    }
    //--------------------------------------------------------------------------
}