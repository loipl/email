<?php

class Rapleaf extends DataAppend
{

    protected $tokens = array(
        'age',
        'gender',
        'education',
        'home_owner',
        'years_at_address',
        'months_at_address',
        'married',
        'occupation',
        'children',
        'home_value',
        'high_net_worth',
        'annual_income',
        'monthly_income',
        'postal_code',
        'city',
        'state_or_region',
        'state_or_region_code',
        'interests'
        );

    public function getName()
    {
        return 'Rapleaf';
    }
    //--------------------------------------------------------------------------


    public function executeLookup()
    {
    }
    //--------------------------------------------------------------------------
}