<?php

class LeadStandardize
{
    public static function getGenderCode($gender) {
        $stdGender = strtoupper(trim($gender));
        switch ($stdGender) {
            case 'MALE':
            case 'MAN':
            case 'M':
                return 'M';
                
            case 'FEMALE':
            case 'WOMAN':
            case 'F':
                return 'F';
                
            default:
                return null;
        }
    }
    
    public static function getCountryCode($countryName) {
        
        if (empty($countryName)) {
            return "";
        }
        
        $stdName = strtoupper(trim($countryName));
        $countryCodeList = self::getCountryCodeList();
        if (isset($countryCodeList[$stdName])) {
            return $countryCodeList[$stdName];
        } else {
            return substr($stdName, 0, 2);
        }
    }
    // -------------------------------------------------------------------------
    
    public static function getCountryCodeList() {
        return array(
            'AFGHANISTAN' => 'AF',
            'ALBANIA' => 'AL',
            'ALGERIA' => 'DZ',
            'AMERICAN SAMOA' => 'AS',
            'ANDORRA' => 'AD',
            'ANGOLA' => 'AO',
            'ANGUILLA' => 'AI',
            'ANTARCTICA' => 'AQ',
            'ANTIGUA AND BARBUDA' => 'AG',
            'ARGENTINA' => 'AR',
            'ARMENIA' => 'AM',
            'ARUBA' => 'AW',
            'AUSTRALIA' => 'AU',
            'AUSTRIA' => 'AT',
            'AZERBAIJAN' => 'AZ',
            'BAHAMAS' => 'BS',
            'BAHRAIN' => 'BH',
            'BANGLADESH' => 'BD',
            'BARBADOS' => 'BB',
            'BELARUS' => 'BY',
            'BELGIUM' => 'BE',
            'BELIZE' => 'BZ',
            'BENIN' => 'BJ',
            'BERMUDA' => 'BM',
            'BHUTAN' => 'BT',
            'BOLIVIA' => 'BO',
            'BOSNIA AND HERZEGOVINA' => 'BA',
            'BOTSWANA' => 'BW',
            'BRAZIL' => 'BR',
            'BRITISH INDIAN OCEAN TERRITORY' => 'IO',
            'BRITISH VIRGIN ISLANDS' => 'VG',
            'BRUNEI' => 'BN',
            'BULGARIA' => 'BG',
            'BURKINA FASO' => 'BF',
            'BURMA' => 'MM',
            'BURUNDI' => 'BI',
            'CAMBODIA' => 'KH',
            'CAMEROON' => 'CM',
            'CANADA' => 'CA',
            'CAPE VERDE' => 'CV',
            'CAYMAN ISLANDS' => 'KY',
            'CENTRAL AFRICAN REPUBLIC' => 'CF',
            'CHAD' => 'TD',
            'CHILE' => 'CL',
            'CHINA' => 'CN',
            'CHRISTMAS ISLAND' => 'CX',
            'COCOS ISLANDS' => 'CC',
            'COLOMBIA' => 'CO',
            'COMOROS' => 'KM',
            'COOK ISLANDS' => 'CK',
            'COSTA RICA' => 'CR',
            'CROATIA' => 'HR',
            'CUBA' => 'CU',
            'CYPRUS' => 'CY',
            'CZECH REPUBLIC' => 'CZ',
            'DEMOCRATIC REPUBLIC OF THE CONGO' => 'CD',
            'DENMARK' => 'DK',
            'DJIBOUTI' => 'DJ',
            'DOMINICA' => 'DM',
            'DOMINICAN REPUBLIC' => 'DO',
            'ECUADOR' => 'EC',
            'EGYPT' => 'EG',
            'EL SALVADOR' => 'SV',
            'EQUATORIAL GUINEA' => 'GQ',
            'ERITREA' => 'ER',
            'ESTONIA' => 'EE',
            'ETHIOPIA' => 'ET',
            'FALKLAND ISLANDS' => 'FK',
            'FAROE ISLANDS' => 'FO',
            'FIJI' => 'FJ',
            'FINLAND' => 'FI',
            'FRANCE' => 'FR',
            'FRENCH POLYNESIA' => 'PF',
            'GABON' => 'GA',
            'GAMBIA' => 'GM',
            'GEORGIA' => 'GE',
            'GERMANY' => 'DE',
            'GHANA' => 'GH',
            'GIBRALTAR' => 'GI',
            'GREECE' => 'GR',
            'GREENLAND' => 'GL',
            'GRENADA' => 'GD',
            'GUAM' => 'GU',
            'GUATEMALA' => 'GT',
            'GUINEA' => 'GN',
            'GUINEA-BISSAU' => 'GW',
            'GUYANA' => 'GY',
            'HAITI' => 'HT',
            'HOLY SEE' => 'VA',
            'HONDURAS' => 'HN',
            'HONG KONG' => 'HK',
            'HUNGARY' => 'HU',
            'ICELAND' => 'IS',
            'INDIA' => 'IN',
            'INDONESIA' => 'ID',
            'IRAN' => 'IR',
            'IRAQ' => 'IQ',
            'IRELAND' => 'IE',
            'ISLE OF MAN' => 'IM',
            'ISRAEL' => 'IL',
            'ITALY' => 'IT',
            'IVORY COAST' => 'CI',
            'JAMAICA' => 'JM',
            'JAPAN' => 'JP',
            'JERSEY' => 'JE',
            'JORDAN' => 'JO',
            'KAZAKHSTAN' => 'KZ',
            'KENYA' => 'KE',
            'KIRIBATI' => 'KI',
            'KUWAIT' => 'KW',
            'KYRGYZSTAN' => 'KG',
            'LAOS' => 'LA',
            'LATVIA' => 'LV',
            'LEBANON' => 'LB',
            'LESOTHO' => 'LS',
            'LIBERIA' => 'LR',
            'LIBYA' => 'LY',
            'LIECHTENSTEIN' => 'LI',
            'LITHUANIA' => 'LT',
            'LUXEMBOURG' => 'LU',
            'MACAU' => 'MO',
            'MACEDONIA' => 'MK',
            'MADAGASCAR' => 'MG',
            'MALAWI' => 'MW',
            'MALAYSIA' => 'MY',
            'MALDIVES' => 'MV',
            'MALI' => 'ML',
            'MALTA' => 'MT',
            'MARSHALL ISLANDS' => 'MH',
            'MAURITANIA' => 'MR',
            'MAURITIUS' => 'MU',
            'MAYOTTE' => 'YT',
            'MEXICO' => 'MX',
            'MICRONESIA' => 'FM',
            'MOLDOVA' => 'MD',
            'MONACO' => 'MC',
            'MONGOLIA' => 'MN',
            'MONTENEGRO' => 'ME',
            'MONTSERRAT' => 'MS',
            'MOROCCO' => 'MA',
            'MOZAMBIQUE' => 'MZ',
            'NAMIBIA' => 'NA',
            'NAURU' => 'NR',
            'NEPAL' => 'NP',
            'NETHERLANDS' => 'NL',
            'NETHERLANDS ANTILLES' => 'AN',
            'NEW CALEDONIA' => 'NC',
            'NEW ZEALAND' => 'NZ',
            'NICARAGUA' => 'NI',
            'NIGER' => 'NE',
            'NIGERIA' => 'NG',
            'NIUE' => 'NU',
            'NORFOLK ISLAND' => 'NF',
            'NORTH KOREA' => 'KP',
            'NORTHERN MARIANA ISLANDS' => 'MP',
            'NORWAY' => 'NO',
            'OMAN' => 'OM',
            'PAKISTAN' => 'PK',
            'PALAU' => 'PW',
            'PANAMA' => 'PA',
            'PAPUA NEW GUINEA' => 'PG',
            'PARAGUAY' => 'PY',
            'PERU' => 'PE',
            'PHILIPPINES' => 'PH',
            'PITCAIRN ISLANDS' => 'PN',
            'POLAND' => 'PL',
            'PORTUGAL' => 'PT',
            'PUERTO RICO' => 'PR',
            'QATAR' => 'QA',
            'REPUBLIC OF THE CONGO' => 'CG',
            'ROMANIA' => 'RO',
            'RUSSIA' => 'RU',
            'RWANDA' => 'RW',
            'SAINT BARTHELEMY' => 'BL',
            'SAINT HELENA' => 'SH',
            'SAINT KITTS AND NEVIS' => 'KN',
            'SAINT LUCIA' => 'LC',
            'SAINT MARTIN' => 'MF',
            'SAINT PIERRE AND MIQUELON' => 'PM',
            'SAINT VINCENT AND THE GRENADINES' => 'VC',
            'SAMOA' => 'WS',
            'SAN MARINO' => 'SM',
            'SAO TOME AND PRINCIPE' => 'ST',
            'SAUDI ARABIA' => 'SA',
            'SENEGAL' => 'SN',
            'SERBIA' => 'RS',
            'SEYCHELLES' => 'SC',
            'SIERRA LEONE' => 'SL',
            'SINGAPORE' => 'SG',
            'SLOVAKIA' => 'SK',
            'SLOVENIA' => 'SI',
            'SOLOMON ISLANDS' => 'SB',
            'SOMALIA' => 'SO',
            'SOUTH AFRICA' => 'ZA',
            'SOUTH KOREA' => 'KR',
            'SPAIN' => 'ES',
            'SRI LANKA' => 'LK',
            'SUDAN' => 'SD',
            'SURINAME' => 'SR',
            'SVALBARD' => 'SJ',
            'SWAZILAND' => 'SZ',
            'SWEDEN' => 'SE',
            'SWITZERLAND' => 'CH',
            'SYRIA' => 'SY',
            'TAIWAN' => 'TW',
            'TAJIKISTAN' => 'TJ',
            'TANZANIA' => 'TZ',
            'THAILAND' => 'TH',
            'TIMOR-LESTE' => 'TL',
            'TOGO' => 'TG',
            'TOKELAU' => 'TK',
            'TONGA' => 'TO',
            'TRINIDAD AND TOBAGO' => 'TT',
            'TUNISIA' => 'TN',
            'TURKEY' => 'TR',
            'TURKMENISTAN' => 'TM',
            'TURKS AND CAICOS ISLANDS' => 'TC',
            'TUVALU' => 'TV',
            'UGANDA' => 'UG',
            'UKRAINE' => 'UA',
            'UNITED ARAB EMIRATES' => 'AE',
            'UNITED KINGDOM' => 'GB',
            'UNITED STATES' => 'US',
            'URUGUAY' => 'UY',
            'US VIRGIN ISLANDS' => 'VI',
            'UZBEKISTAN' => 'UZ',
            'VANUATU' => 'VU',
            'VENEZUELA' => 'VE',
            'VIETNAM' => 'VN',
            'WALLIS AND FUTUNA' => 'WF',
            'WESTERN SAHARA' => 'EH',
            'YEMEN' => 'YE',
            'ZAMBIA' => 'ZM',
            'ZIMBABWE' => 'ZW',
            'AFG' => 'AF',
            'ALB' => 'AL',
            'DZA' => 'DZ',
            'ASM' => 'AS',
            'AND' => 'AD',
            'AGO' => 'AO',
            'AIA' => 'AI',
            'ATA' => 'AQ',
            'ATG' => 'AG',
            'ARG' => 'AR',
            'ARM' => 'AM',
            'ABW' => 'AW',
            'AUS' => 'AU',
            'AUT' => 'AT',
            'AZE' => 'AZ',
            'BHS' => 'BS',
            'BHR' => 'BH',
            'BGD' => 'BD',
            'BRB' => 'BB',
            'BLR' => 'BY',
            'BEL' => 'BE',
            'BLZ' => 'BZ',
            'BEN' => 'BJ',
            'BMU' => 'BM',
            'BTN' => 'BT',
            'BOL' => 'BO',
            'BIH' => 'BA',
            'BWA' => 'BW',
            'BRA' => 'BR',
            'IOT' => 'IO',
            'VGB' => 'VG',
            'BRN' => 'BN',
            'BGR' => 'BG',
            'BFA' => 'BF',
            'MMR' => 'MM',
            'BDI' => 'BI',
            'KHM' => 'KH',
            'CMR' => 'CM',
            'CAN' => 'CA',
            'CPV' => 'CV',
            'CYM' => 'KY',
            'CAF' => 'CF',
            'TCD' => 'TD',
            'CHL' => 'CL',
            'CHN' => 'CN',
            'CXR' => 'CX',
            'CCK' => 'CC',
            'COL' => 'CO',
            'COM' => 'KM',
            'COK' => 'CK',
            'CRC' => 'CR',
            'HRV' => 'HR',
            'CUB' => 'CU',
            'CYP' => 'CY',
            'CZE' => 'CZ',
            'COD' => 'CD',
            'DNK' => 'DK',
            'DJI' => 'DJ',
            'DMA' => 'DM',
            'DOM' => 'DO',
            'ECU' => 'EC',
            'EGY' => 'EG',
            'SLV' => 'SV',
            'GNQ' => 'GQ',
            'ERI' => 'ER',
            'EST' => 'EE',
            'ETH' => 'ET',
            'FLK' => 'FK',
            'FRO' => 'FO',
            'FJI' => 'FJ',
            'FIN' => 'FI',
            'FRA' => 'FR',
            'PYF' => 'PF',
            'GAB' => 'GA',
            'GMB' => 'GM',
            'GEO' => 'GE',
            'DEU' => 'DE',
            'GHA' => 'GH',
            'GIB' => 'GI',
            'GRC' => 'GR',
            'GRL' => 'GL',
            'GRD' => 'GD',
            'GUM' => 'GU',
            'GTM' => 'GT',
            'GIN' => 'GN',
            'GNB' => 'GW',
            'GUY' => 'GY',
            'HTI' => 'HT',
            'VAT' => 'VA',
            'HND' => 'HN',
            'HKG' => 'HK',
            'HUN' => 'HU',
            ' IS' => 'IS',
            'IND' => 'IN',
            'IDN' => 'ID',
            'IRN' => 'IR',
            'IRQ' => 'IQ',
            'IRL' => 'IE',
            'IMN' => 'IM',
            'ISR' => 'IL',
            'ITA' => 'IT',
            'CIV' => 'CI',
            'JAM' => 'JM',
            'JPN' => 'JP',
            'JEY' => 'JE',
            'JOR' => 'JO',
            'KAZ' => 'KZ',
            'KEN' => 'KE',
            'KIR' => 'KI',
            'KWT' => 'KW',
            'KGZ' => 'KG',
            'LAO' => 'LA',
            'LVA' => 'LV',
            'LBN' => 'LB',
            'LSO' => 'LS',
            'LBR' => 'LR',
            'LBY' => 'LY',
            'LIE' => 'LI',
            'LTU' => 'LT',
            'LUX' => 'LU',
            'MAC' => 'MO',
            'MKD' => 'MK',
            'MDG' => 'MG',
            'MWI' => 'MW',
            'MYS' => 'MY',
            'MDV' => 'MV',
            'MLI' => 'ML',
            'MLT' => 'MT',
            'MHL' => 'MH',
            'MRT' => 'MR',
            'MUS' => 'MU',
            'MYT' => 'YT',
            'MEX' => 'MX',
            'FSM' => 'FM',
            'MDA' => 'MD',
            'MCO' => 'MC',
            'MNG' => 'MN',
            'MNE' => 'ME',
            'MSR' => 'MS',
            'MAR' => 'MA',
            'MOZ' => 'MZ',
            'NAM' => 'NA',
            'NRU' => 'NR',
            'NPL' => 'NP',
            'NLD' => 'NL',
            'ANT' => 'AN',
            'NCL' => 'NC',
            'NZL' => 'NZ',
            'NIC' => 'NI',
            'NER' => 'NE',
            'NGA' => 'NG',
            'NIU' => 'NU',
            'NFK' => 'NF',
            'PRK' => 'KP',
            'MNP' => 'MP',
            'NOR' => 'NO',
            'OMN' => 'OM',
            'PAK' => 'PK',
            'PLW' => 'PW',
            'PAN' => 'PA',
            'PNG' => 'PG',
            'PRY' => 'PY',
            'PER' => 'PE',
            'PHL' => 'PH',
            'PCN' => 'PN',
            'POL' => 'PL',
            'PRT' => 'PT',
            'PRI' => 'PR',
            'QAT' => 'QA',
            'COG' => 'CG',
            'ROU' => 'RO',
            'RUS' => 'RU',
            'RWA' => 'RW',
            'BLM' => 'BL',
            'SHN' => 'SH',
            'KNA' => 'KN',
            'LCA' => 'LC',
            'MAF' => 'MF',
            'SPM' => 'PM',
            'VCT' => 'VC',
            'WSM' => 'WS',
            'SMR' => 'SM',
            'STP' => 'ST',
            'SAU' => 'SA',
            'SEN' => 'SN',
            'SRB' => 'RS',
            'SYC' => 'SC',
            'SLE' => 'SL',
            'SGP' => 'SG',
            'SVK' => 'SK',
            'SVN' => 'SI',
            'SLB' => 'SB',
            'SOM' => 'SO',
            'ZAF' => 'ZA',
            'KOR' => 'KR',
            'ESP' => 'ES',
            'LKA' => 'LK',
            'SDN' => 'SD',
            'SUR' => 'SR',
            'SJM' => 'SJ',
            'SWZ' => 'SZ',
            'SWE' => 'SE',
            'CHE' => 'CH',
            'SYR' => 'SY',
            'TWN' => 'TW',
            'TJK' => 'TJ',
            'TZA' => 'TZ',
            'THA' => 'TH',
            'TLS' => 'TL',
            'TGO' => 'TG',
            'TKL' => 'TK',
            'TON' => 'TO',
            'TTO' => 'TT',
            'TUN' => 'TN',
            'TUR' => 'TR',
            'TKM' => 'TM',
            'TCA' => 'TC',
            'TUV' => 'TV',
            'UGA' => 'UG',
            'UKR' => 'UA',
            'ARE' => 'AE',
            'GBR' => 'GB',
            'USA' => 'US',
            'URY' => 'UY',
            'VIR' => 'VI',
            'UZB' => 'UZ',
            'VUT' => 'VU',
            'VEN' => 'VE',
            'VNM' => 'VN',
            'WLF' => 'WF',
            'ESH' => 'EH',
            'YEM' => 'YE',
            'ZMB' => 'ZM',
            'ZWE' => 'ZW'
        );
    }
    //--------------------------------------------------------------------------
}