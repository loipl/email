<?php

class Lead extends Database
{

    protected $id;
    protected $email;
    protected $address;
    protected $firstName;
    protected $lastName;
    protected $country;
    protected $phone;
    protected $os;
    protected $language;
    protected $state;
    protected $city;
    protected $postalCode;
    protected $domainName;
    protected $sourceUrl;
    protected $campaign;
    protected $username;
    protected $ip;
    protected $subscribeDate;
    protected $birthDay;
    protected $birthMonth;
    protected $birthYear;
    protected $gender;
    protected $seeking;
    protected $hygiene_datetime;
    protected $verification_datetime;
    protected $lockId;
    protected $lockGroup;
    protected $leadGroup;
    protected $subQueryKeys;

    protected $tableName = 'leads';
    const      tableName = 'leads';

    public function __construct($email = null)
    {
        parent::__construct();

        if ($email) {
            $sql  = "SELECT * FROM `$this->tableName` WHERE `email` = '" . mysql_real_escape_string($email) . "';";

            $result = $this->getArrayAssoc($sql);

            $this->email                 = $result['email'];
            $this->address               = $result['address'];
            $this->firstName             = $result['first_name'];
            $this->lastName              = $result['last_name'];
            $this->country               = $result['country'];
            $this->phone                 = $result['phone'];
            $this->os                    = $result['os'];
            $this->language              = $result['language'];
            $this->state                 = $result['state'];
            $this->city                  = $result['city'];
            $this->postalCode            = $result['postal_code'];
            $this->domainName            = $result['source_domain'];
            $this->sourceUrl             = $result['source_url'];
            $this->campaign              = $result['source_campaign'];
            $this->username              = $result['source_username'];
            $this->ip                    = $result['ip'];
            $this->subscribeDate         = $result['subscribe_datetime'];
            $this->birthDay              = $result['birth_day'];
            $this->birthMonth            = $result['birth_month'];
            $this->birthYear             = $result['birth_year'];
            $this->gender                = $result['gender'];
            $this->seeking               = $result['seeking'];
            $this->hygiene_datetime      = $result['hygiene_datetime'];
            $this->verification_datetime = $result['verification_datetime'];
        }
    }
    //--------------------------------------------------------------------------


    private function generateLockId()
    {
        return uniqid(null, true);
    }
    //--------------------------------------------------------------------------


    public function getLockId()
    {
        return $this->lockId;
    }
    //--------------------------------------------------------------------------


    public function getLeads(Database $db, $leadAttributes, $suppressionList = null)
    {
        $this->lockId = $this->generateLockId();

        if (isset($leadAttributes['categoryId']) && isset($leadAttributes['categoryAction'])) {
            $this->leadGroup = $this->initializeLeadGroup($db, $this->lockId, $leadAttributes['count'], $leadAttributes['minScore'],
                                                          $leadAttributes['categoryId'], $leadAttributes['categoryAction']);
        } else {
            $this->leadGroup = $this->initializeLeadGroup($db, $this->lockId, $leadAttributes['count'], $leadAttributes['minScore']);
        }

        if (!is_array($this->leadGroup)) {
            return false;
        }

        $this->refineGroup($db, $leadAttributes, $this->leadGroup, $this->lockId, $suppressionList);

        if (!is_array($this->leadGroup)) {
            return false;
        }

        $this->releaseLocks($db, $this->lockGroup);

        return $this->leadGroup;
    }
    //--------------------------------------------------------------------------


    private function initializeLeadGroup(Database $db, $lockId, $maxSearchReturn, $minScore, $categoryId = null, $categoryAction = null)
    {
        if (!isset($categoryId) && !isset($categoryAction)) {
            $rowCount = $this->countAvailableLeads($db, $minScore);
            $result = $this->selectLeads($db, $rowCount, $maxSearchReturn, $minScore);
        } else {
            $result = $this->selectLeadsByCategoryId($db, $maxSearchReturn, $categoryId, $categoryAction);
        }

        if (isset($result) && !empty($result)) {
            foreach ($result AS $row) {
                $lockGroup[] = $row['email'];
            }

            $this->lockGroup = $lockGroup;
            $this->reserveLeads($db, $lockId, $lockGroup);

            return $result;
        } else {
            return false;
        }

    }
    //--------------------------------------------------------------------------


    private function countAvailableLeads(Database $db, $minScore)
    {
        $sql  = "SELECT COUNT(*)";
        $sql .= " FROM `" . self::tableName. "`";
        $sql .= " WHERE `lock_id` IS NULL";
        $sql .= " AND `score` >= '" . $minScore . "'";
        $rowCount = $db->getUpperLeft($sql);

        return $rowCount;
    }
    //--------------------------------------------------------------------------


    private function selectLeads(Database $db, $rowCount, $maxSearchReturn, $minScore)
    {
        $sql  = "SELECT *";
        $sql .= " FROM `" . self::tableName. "`";
        $sql .= " WHERE `lock_id` IS NULL";
        $sql .= " AND `score` >= '" . $minScore . "'";
        $sql .= " LIMIT " . Random::getRandomRow($rowCount) . "," . $maxSearchReturn;
        $result = $db->getArray($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    private function selectLeadsByCategoryId(Database $db, $maxSearchReturn, $categoryId = null, $categoryAction = null)
    {
        $sql  = "SELECT `activity`.`email`";
        $sql .= " FROM `activity`, `transactions`";
        $sql .= " WHERE `transactions`.`activity_id` = `activity`.`id`";
        $sql .= " AND `activity`.`category_id` = '" . $categoryId . "'";
        $sql .= " AND `transactions`.`type` = '";

        if ($categoryAction == 'open') {
                $sql .= "1'";
        } else if ($categoryAction == 'click') {
                $sql .= "2'";
        }

        $result = $db->getArray($sql);

        if (isset($result) && is_array($result)) {
            $result = array_slice($result, 0, $maxSearchReturn);

            return $result;
        } else {
            return false;
        }
    }
    //--------------------------------------------------------------------------


    private function reserveLeads(Database $db, $lockId, $lockGroup)
    {
        foreach ($lockGroup AS $lead) {
            $sql  = "UPDATE `" . $this->tableName . "` SET `lock_id` = '" . mysql_real_escape_string($lockId) . "',";
            $sql .= " `lock_datetime` = NOW()";
            $sql .= " WHERE `email` =";
            $sql .= " '" . mysql_real_escape_string($lead) . "' LIMIT 1; ";

            $db->query($sql);
        }
    }
    //--------------------------------------------------------------------------


    private function releaseLocks(Database $db, $lockGroup)
    {
        foreach ($lockGroup AS $lead) {
            $sql  = "UPDATE `" . $this->tableName . "` SET `lock_id` = NULL,";
            $sql .= " `lock_datetime` = NULL";
            $sql .= " WHERE `email` =";
            $sql .= " '" . mysql_real_escape_string($lead) . "' LIMIT 1;";

            $db->query($sql);
        }
    }
    //--------------------------------------------------------------------------


    private function refineGroup(Database $db, $leadAttributes, $leadGroup, $lockId, $suppressionList = null)
    {
        $this->processMd5Check($leadGroup);
        $this->processCountryCheck($leadGroup, $leadAttributes);
        $this->processStateCheck($leadGroup, $leadAttributes);
        $this->processGenderCheck($leadGroup, $leadAttributes);
        $this->processHygieneCheck($leadGroup, $leadAttributes);
        $this->processVerificationCheck($leadGroup, $leadAttributes);
        $this->processTldCheck($leadGroup, $leadAttributes);
        $this->processSuppressionCampaignCheck($leadGroup, $suppressionList);
        $this->processSuppressionEmailCheck($db, $leadGroup);
        $this->processSuppressionDomainCheck($db, $leadGroup);
        $this->processSuppressionEmailMd5Check($db, $leadGroup);
        $this->processSuppressionDomainMd5Check($db, $leadGroup);
        $this->processBuildQueueCheck($db, $leadGroup);
        $this->processSendQueueCheck($db, $leadGroup);
        $this->processCampaignCheck($db, $leadGroup, $leadAttributes);
        $this->processIntervalCheck($db, $leadGroup, $leadAttributes);
        $this->processClassificationCheck($db, $leadGroup, $leadAttributes);

        $this->leadGroup = $leadGroup;
    }
    //--------------------------------------------------------------------------


    public function processMd5Check(&$leadGroup)
    {
        foreach ($leadGroup AS $int => $lead) {
            if (!isset($lead['md5_email']) || !isset($lead['md5_domain']) || is_null($lead['md5_email']) || is_null($lead['md5_domain'])) {
                unset($leadGroup[$int]);
            }
        }
    }
    //--------------------------------------------------------------------------


    public function processCountryCheck(&$leadGroup, $leadAttributes)
    {
        $countryList = (!empty($leadAttributes['country'])) ? $leadAttributes['country'] : Config::$defaultCountryList;

        foreach ($leadGroup AS $int => $lead) {
            if (isset($lead['country']) && !is_null($lead['country'])) {
                if (isset($leadAttributes['inverse']['country'])) {
                    if ($leadAttributes['inverse']['country'] === true) {
                        if (in_array($lead['country'], $countryList)) {
                            unset($leadGroup[$int]);
                        }
                    }
                } else {
                    if (!in_array($lead['country'], $countryList)) {
                        unset($leadGroup[$int]);
                    }
                }
            } else {
                unset($leadGroup[$int]);
            }
        }
    }
    //--------------------------------------------------------------------------


    public function processStateCheck(&$leadGroup, $leadAttributes)
    {
        if (isset($leadAttributes['state']) && !empty($leadAttributes['state'])) {
            foreach ($leadGroup AS $int => $lead) {
                if (isset($lead['state']) && !is_null($lead['state'])) {
                    if (isset($leadAttributes['inverse']['state'])) {
                        if ($leadAttributes['inverse']['state'] === true) {
                            if (in_array($lead['state'], $leadAttributes['state'])) {
                                unset($leadGroup[$int]);
                            }
                        }
                    } else {
                        if (!in_array($lead['state'], $leadAttributes['state'])) {
                            unset($leadGroup[$int]);
                        }
                    }
                } else {
                    unset($leadGroup[$int]);
                }
            }
        }
    }
    //--------------------------------------------------------------------------


    public function processSuppressionEmailCheck(Database $db, &$leadGroup)
    {
        foreach ($leadGroup AS $int => $lead) {
            $sql = "SELECT COUNT(*) FROM `suppression_email` WHERE `email` = '" . mysql_real_escape_string($lead['email']) . "'";
            $suppressionCount = $db->getUpperLeft($sql);

            if ($suppressionCount > 0) {
                unset($leadGroup[$int]);
            }
        }
    }
    //--------------------------------------------------------------------------


    public function processSuppressionDomainCheck(Database $db, &$leadGroup)
    {
        foreach ($leadGroup AS $int => $lead) {
            $sql = "SELECT COUNT(*) FROM `suppression_domain` WHERE `domain` = '" . mysql_real_escape_string($lead['domain']) . "'";
            $suppressionCount = $db->getUpperLeft($sql);

            if ($suppressionCount > 0) {
                unset($leadGroup[$int]);
            }
        }
    }
    //--------------------------------------------------------------------------


    public function processSuppressionEmailMd5Check(Database $db, &$leadGroup)
    {
        foreach ($leadGroup AS $int => $lead) {
            $sql = "SELECT COUNT(*) FROM `suppression_email_md5` WHERE `email_md5` = '" . $lead['md5_email'] . "'";
            $suppressionCount = $db->getUpperLeft($sql);

            if ($suppressionCount > 0) {
                unset($leadGroup[$int]);
            }
        }
    }
    //--------------------------------------------------------------------------


    public function processSuppressionDomainMd5Check(Database $db, &$leadGroup)
    {
        foreach ($leadGroup AS $int => $lead) {
            $sql = "SELECT COUNT(*) FROM `suppression_domain_md5` WHERE `domain_md5` = '" . $lead['md5_domain'] . "'";
            $suppressionCount = $db->getUpperLeft($sql);

            if ($suppressionCount > 0) {
                unset($leadGroup[$int]);
            }
        }
    }
    //--------------------------------------------------------------------------


    public function processBuildQueueCheck(Database $db, &$leadGroup)
    {
        foreach ($leadGroup AS $int => $lead) {
            $sql = "SELECT COUNT(*) FROM `queue_build` WHERE `email` = '" . mysql_real_escape_string($lead['email']) . "'";
            $queueCount = $db->getUpperLeft($sql);

            if ($queueCount > 0) {
                unset($leadGroup[$int]);
            }
        }
    }
    //--------------------------------------------------------------------------


    public function processSendQueueCheck(Database $db, &$leadGroup)
    {
        foreach ($leadGroup AS $int => $lead) {
            $sql = "SELECT COUNT(*) FROM `queue_send` WHERE `email` = '" . mysql_real_escape_string($lead['email']) . "'";
            $queueCount = $db->getUpperLeft($sql);

            if ($queueCount > 0) {
                unset($leadGroup[$int]);
            }
        }
    }
    //--------------------------------------------------------------------------


    public function processSuppressionCampaignCheck(&$leadGroup, $suppressionList)
    {
        if (!empty($suppressionList)) {
            foreach ($leadGroup AS $int => $lead) {
                if (in_array($lead['md5_email'], $suppressionList)) {
                    unset($leadGroup[$int]);
                }
            }
        }
    }
    //--------------------------------------------------------------------------


    public function processTldCheck(&$leadGroup, $leadAttributes)
    {
        if ($leadAttributes['tldList'] !== false) {
            $tldList = (!empty($leadAttributes['tldList'])) ? $leadAttributes['tldList'] : TldList::combineTldLists();

            foreach ($leadGroup AS $int => $lead) {
                if (isset($leadAttributes['inverse']['tldList'])) {
                    if ($leadAttributes['inverse']['tldList'] === true) {
                        if (in_array($lead['domain'], $tldList)) {
                            unset($leadGroup[$int]);
                        }
                    }
                } else {
                    if (!in_array($lead['domain'], $tldList)) {
                        unset($leadGroup[$int]);
                    }
                }
            }
        }
    }
    //--------------------------------------------------------------------------


    public function processCampaignCheck(Database $db, &$leadGroup, $leadAttributes)
    {
        if (isset($leadAttributes['campaignId'])) {
            foreach ($leadGroup AS $int => $lead) {
                $sql = "SELECT COUNT(*) FROM `activity` WHERE `email` = '" . $lead['email'] . "'";
                $sql .= " AND `campaign_id` = '" . $leadAttributes['campaignId'] . "'";
                $queueCount = $db->getUpperLeft($sql);

                if ($queueCount > 0) {
                    unset($leadGroup[$int]);
                }
            }
        }
    }
    //--------------------------------------------------------------------------


    public function processIntervalCheck(Database $db, &$leadGroup, $leadAttributes)
    {
        if (isset($leadAttributes['interval'])) {
            $currentDate = new Datetime();
            $currentDate->sub(new Dateinterval('P' . $leadAttributes['interval'] . 'D'));
            $cutOff      = $currentDate->format('Y-m-d H:i:s');

            foreach ($leadGroup AS $int => $lead) {
                $sql  = "SELECT COUNT(*) FROM `activity` WHERE `email` = '" . $lead['email'] . "'";
                $sql .= " AND `datetime` > '" . $cutOff . "'";
                $result = $db->getUpperLeft($sql);

                if ($result > 0) {
                    unset($leadGroup[$int]);
                }
            }
        }
    }
    //--------------------------------------------------------------------------


    public function processHygieneCheck(&$leadGroup, $leadAttributes)
    {
        if (isset($leadAttributes['lastHygiene']) && $leadAttributes['lastHygiene'] !== false) {
            foreach ($leadGroup AS $int => $lead) {
                if ($lead['hygiene_datetime'] < $leadAttributes['lastHygiene']) {
                    unset($leadGroup[$int]);
                }
            }
        }
    }
    //--------------------------------------------------------------------------


    public function processVerificationCheck(&$leadGroup, $leadAttributes)
    {
        if (isset($leadAttributes['lastVerification']) && $leadAttributes['lastVerification'] !== false) {
            foreach ($leadGroup AS $int => $lead) {
                if ($lead['verification_datetime'] < $leadAttributes['lastVerification']) {
                    unset($leadGroup[$int]);
                }
            }
        }
    }
    //--------------------------------------------------------------------------


    public function processGenderCheck(&$leadGroup, $leadAttributes)
    {
        if (isset($leadAttributes['gender']) && $leadAttributes['gender'] !== false) {
            foreach ($leadGroup AS $int => $lead) {
                if ($lead['gender'] != $leadAttributes['gender']) {
                    unset($leadGroup[$int]);
                }
            }
        }
    }
    //--------------------------------------------------------------------------


    private function processClassificationCheck(Database $db, &$leadGroup, $leadAttributes)
    {
        if ($leadAttributes['type'] == 'clickers') {
            foreach ($leadGroup AS $int => $lead) {
                $sql = "SELECT COUNT(*) FROM `clickers` WHERE `email` = '" . $lead['email'] . "'";
                $result = $db->getUpperLeft($sql);

                if ($result == 0) {
                    unset($leadGroup[$int]);
                }
            }
        } else if ($leadAttributes['type'] == 'openers') {
            foreach ($leadGroup AS $int => $lead) {
                $sql = "SELECT COUNT(*) FROM `clickers` WHERE `email` = '" . $lead['email'] . "'";
                $result = $db->getUpperLeft($sql);

                if ($result > 0) {
                    unset($leadGroup[$int]);
                    continue;
                }

                $sql = "SELECT COUNT(*) FROM `openers` WHERE `email` = '" . $lead['email'] . "'";
                $result = $db->getUpperLeft($sql);

                if ($result == 0) {
                    unset($leadGroup[$int]);
                }
            }
        } else if ($leadAttributes['type'] == 'verified') {
            foreach ($leadGroup AS $int => $lead) {
                $sql = "SELECT COUNT(*) FROM `clickers` WHERE `email` = '" . $lead['email'] . "'";
                $result = $db->getUpperLeft($sql);

                if ($result > 0) {
                    unset($leadGroup[$int]);
                    continue;
                }

                $sql = "SELECT COUNT(*) FROM `openers` WHERE `email` = '" . $lead['email'] . "'";
                $result = $db->getUpperLeft($sql);

                if ($result > 0) {
                    unset($leadGroup[$int]);
                }
            }
        }
    }
    //--------------------------------------------------------------------------


    public static function getLeadsWithoutMD5($count)
    {
        $db = new Database;

        $sql  = "SELECT `email` FROM `" . self::tableName . "`";
        $sql .= " WHERE `md5_email` IS NULL OR `md5_domain` IS NULL";
        $sql .= " LIMIT " . $count . "";

        $result = $db->getArray($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function isSuppressed($email)
    {
        $db = new Database;

        $emailParts = explode('@',$email);

        $domain = $emailParts[1];

        $sql  = "SELECT COUNT(*) FROM `suppression_email`";
        $sql .= " WHERE `email` = '" . mysql_real_escape_string($email) . "'";

        $result = $db->getUpperLeft($sql);

        if ($result) { return true; }

        $sql  = "SELECT COUNT(*) FROM `suppression_domain`";
        $sql .= " WHERE `domain` = '" . mysql_real_escape_string($domain) . "'";

        $result = $db->getUpperLeft($sql);

        if ($result) { return true; }

        $sql  = "SELECT COUNT(*) FROM `suppression_email_md5`";
        $sql .= " WHERE `email_md5` = '" . mysql_real_escape_string(md5($email)) . "'";

        $result = $db->getUpperLeft($sql);

        if ($result) { return true; }

        $sql  = "SELECT COUNT(*) FROM `suppression_domain_md5`";
        $sql .= " WHERE `domain_md5` = '" . mysql_real_escape_string(md5($domain)) . "'";

        $result = $db->getUpperLeft($sql);

        if ($result) { return true; }

        return false;
    }
    //--------------------------------------------------------------------------


    public static function getLeadsWithoutDomain($count)
    {
        $db = new Database;

        $sql  = "SELECT `email` FROM `" . self::tableName . "`";
        $sql .= " WHERE `domain` IS NULL OR `domain` = ''";
        $sql .= " LIMIT " . $count . "";

        $result = $db->getArray($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getLeadsWithLocks($count = 10000, $interval = Config::LEAD_TIMEOUT)
    {
        $db = new Database;

        $currentDate = new Datetime();
        $currentDate->sub(new Dateinterval('PT' . $interval . 'S'));
        $cutOff      = $currentDate->format('Y-m-d H:i:s');

        $sql  = "SELECT `email` FROM `" . self::tableName . "`";
        $sql .= " WHERE `lock_id` IS NOT NULL AND `lock_datetime` < '" . $cutOff . "'";
        $sql .= " LIMIT " . $count . "";

        $result = $db->getArray($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function updateMD5($email, $emailMD5, $domainMD5)
    {
        $db = new Database;

        $sql  = "UPDATE `" .self::tableName . "` ";
        $sql .= " SET `md5_email` = '" . mysql_real_escape_string($emailMD5) . "',";
        $sql .= " `md5_domain` = '" . mysql_real_escape_string($domainMD5) . "'";
        $sql .= " WHERE `email` = '" . mysql_real_escape_string($email) . "'";
        $sql .= " LIMIT 1";

        $result = $db->query($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function isClicker($email)
    {
        $db = new Database;

        $sql = "SELECT `email` FROM `clickers` WHERE `email` = '" . mysql_real_escape_string($email) . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        if ($result == $email) {
            return true;
        }

        return false;
    }
    //--------------------------------------------------------------------------


    public static function isOpener($email)
    {
        $db = new Database;

        $sql = "SELECT `email` FROM `openers` WHERE `email` = '" . mysql_real_escape_string($email) . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        if ($result == $email) {
            return true;
        }

        return false;
    }
    //--------------------------------------------------------------------------


    public static function getLastHygiene($email)
    {
        $db = new Database;

        $sql = "SELECT `hygiene_datetime` FROM `leads` WHERE `email` = '" . mysql_real_escape_string($email) . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        if (!empty($result)) {
            return $result;
        }

        return false;
    }
    //--------------------------------------------------------------------------


    public static function getLastVerification($email)
    {
        $db = new Database;

        $sql = "SELECT `verification_datetime` FROM `leads` WHERE `email` = '" . mysql_real_escape_string($email) . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        if (!empty($result)) {
            return $result;
        }

        return false;
    }
    //--------------------------------------------------------------------------


    public static function getAddressByEmail($email)
    {
        $db = new Database;

        $sql = "SELECT `address` FROM `" . self::tableName . "` WHERE `email` = '" . mysql_real_escape_string($email) . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public function getEmail()
    {
        return $this->email;
    }
    //--------------------------------------------------------------------------


    public function getSubscribeDate()
    {
        return $this->subscribeDate;
    }
    //--------------------------------------------------------------------------


    public function getSourceUrl()
    {
        return $this->sourceUrl;
    }
    //--------------------------------------------------------------------------


    public function getAddress()
    {
        return $this->address;
    }
    //--------------------------------------------------------------------------


    public function getFirstName()
    {
        return $this->firstName;
    }
    //--------------------------------------------------------------------------


    public function getLastName()
    {
        return $this->lastName;
    }
    //--------------------------------------------------------------------------


    public function getCountry()
    {
        return $this->country;
    }
    //--------------------------------------------------------------------------


    public function getPhone()
    {
        return $this->phone;
    }
    //--------------------------------------------------------------------------


    public function getOS()
    {
        return $this->os;
    }
    //--------------------------------------------------------------------------


    public function getLanguage()
    {
        return $this->language;
    }
    //--------------------------------------------------------------------------


    public function getState()
    {
        return $this->state;
    }
    //--------------------------------------------------------------------------


    public function getCity()
    {
        return $this->city;
    }
    //--------------------------------------------------------------------------


    public function getPostalCode()
    {
        return $this->postalCode;
    }
    //--------------------------------------------------------------------------


    public function getDomainName()
    {
        return $this->domainName;
    }
    //--------------------------------------------------------------------------


    public function getCampaign()
    {
        return $this->campaign;
    }
    //--------------------------------------------------------------------------


    public function getUsername()
    {
        return $this->username;
    }
    //--------------------------------------------------------------------------


    public function getIP()
    {
        return $this->ip;
    }
    //--------------------------------------------------------------------------


    public function getBirthDay()
    {
        return $this->birthDay;
    }
    //--------------------------------------------------------------------------


    public function getBirthMonth()
    {
        return $this->birthMonth;
    }
    //--------------------------------------------------------------------------


    public function getBirthYear()
    {
        return $this->birthYear;
    }
    //--------------------------------------------------------------------------


    public function getGender()
    {
        return $this->gender;
    }
    //--------------------------------------------------------------------------


    public function getSeeking()
    {
        return $this->seeking;
    }
    //--------------------------------------------------------------------------


    public static function scoreNewLead($email)
    {
        $db = new Database;

        $sql = "UPDATE `" . self::tableName. "` SET `score` = '" . Config::SCOREMOD_NEW . "' WHERE `email` = '" . mysql_real_escape_string($email) . "' LIMIT 1;";
        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function scoreSend($email)
    {
        $db = new Database;

        $sql = "SELECT `score` FROM `" . self::tableName. "` WHERE `email` = '" . mysql_real_escape_string($email) . "' LIMIT 1;";
        $oldScore = $db->getUpperLeft($sql);

        $newScore = ($oldScore + Config::SCOREMOD_SEND);

        if ($newScore < 0) {
            $newScore = 0;
        } else if ($newScore > 100) {
            $newScore = 100;
        }

        $sql = "UPDATE `" . self::tableName. "` SET `score` = '" . $newScore . "' WHERE `email` = '" . mysql_real_escape_string($email) . "' LIMIT 1;";
        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function scoreOpen($email)
    {
        $db = new Database;

        $sql = "SELECT `score` FROM `" . self::tableName . "` WHERE `email` = '" . mysql_real_escape_string($email) . "' LIMIT 1;";
        $oldScore = $db->getUpperLeft($sql);

        $newScore = ($oldScore + Config::SCOREMOD_OPEN);

        if ($newScore < 0) {
            $newScore = 0;
        } else if ($newScore > 100) {
            $newScore = 100;
        }

        $sql = "UPDATE `" . self::tableName . "` SET `score` = '" . $newScore . "' WHERE `email` = '" . mysql_real_escape_string($email) . "' LIMIT 1;";
        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function scoreClick($email)
    {
        $db = new Database;

        $sql = "SELECT `score` FROM `" . self::tableName . "` WHERE `email` = '" . mysql_real_escape_string($email) . "' LIMIT 1;";
        $oldScore = $db->getUpperLeft($sql);

        $newScore = ($oldScore + Config::SCOREMOD_CLICK);

        if ($newScore < 0) {
            $newScore = 0;
        } else if ($newScore > 100) {
            $newScore = 100;
        }

        $sql = "UPDATE `" . self::tableName . "` SET `score` = '" . $newScore . "' WHERE `email` = '" . mysql_real_escape_string($email) . "' LIMIT 1;";
        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function scoreComplaint($email)
    {
        $db = new Database;

        $sql = "SELECT `score` FROM `" . self::tableName. "` WHERE `email` = '" . mysql_real_escape_string($email) . "' LIMIT 1;";
        $oldScore = $db->getUpperLeft($sql);

        $newScore = ($oldScore + Config::SCOREMOD_COMPLAINT);

        if ($newScore < 0) {
            $newScore = 0;
        } else if ($newScore > 100) {
            $newScore = 100;
        }

        $sql = "UPDATE `" . self::tableName  . "` SET `score` = '" . $newScore . "' WHERE `email` = '" . mysql_real_escape_string($email) . "' LIMIT 1;";
        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function scoreSoftBounce($email)
    {
        $db = new Database;

        $sql = "SELECT `score` FROM `" . self::tableName . "` WHERE `email` = '" . mysql_real_escape_string($email) . "' LIMIT 1;";
        $oldScore = $db->getUpperLeft($sql);

        $newScore = ($oldScore + Config::SCOREMOD_SOFTBOUNCE);

        if ($newScore < 0) {
            $newScore = 0;
        } else if ($newScore > 100) {
            $newScore = 100;
        }

        $sql = "UPDATE `" . self::tableName . "` SET `score` = '" . $newScore . "' WHERE `email` = '" . mysql_real_escape_string($email) . "' LIMIT 1;";
        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function scoreHardBounce($email)
    {
        $db = new Database;

        $sql = "SELECT `score` FROM `" . self::tableName . "` WHERE `email` = '" . mysql_real_escape_string($email) . "' LIMIT 1;";
        $oldScore = $db->getUpperLeft($sql);

        $newScore = ($oldScore + Config::SCOREMOD_HARDBOUNCE);

        if ($newScore < 0) {
            $newScore = 0;
        } else if ($newScore > 100) {
            $newScore = 100;
        }

        $sql = "UPDATE `" . self::tableName . "` SET `score` = '" . $newScore . "' WHERE `email` = '" . mysql_real_escape_string($email) . "' LIMIT 1;";
        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function scoreUnsubscribe($email)
    {
        $db = new Database;

        $sql = "SELECT `score` FROM `" . self::tableName . "` WHERE `email` = '" . mysql_real_escape_string($email) . "' LIMIT 1;";
        $oldScore = $db->getUpperLeft($sql);

        $newScore = ($oldScore + Config::SCOREMOD_UNSUBSCRIBE);

        if ($newScore < 0) {
            $newScore = 0;
        } else if ($newScore > 100) {
            $newScore = 100;
        }

        $sql = "UPDATE `" . self::tableName . "` SET `score` = '" . $newScore . "' WHERE `email` = '" . mysql_real_escape_string($email) . "' LIMIT 1;";
        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function scoreHygieneFail($email)
    {
        $db = new Database;

        $sql = "SELECT `score` FROM `" . self::tableName . "` WHERE `email` = '" . mysql_real_escape_string($email) . "' LIMIT 1;";
        $oldScore = $db->getUpperLeft($sql);

        $newScore = ($oldScore + Config::SCOREMOD_HYGIENEFAIL);

        if ($newScore < 0) {
            $newScore = 0;
        } else if ($newScore > 100) {
            $newScore = 100;
        }

        $sql = "UPDATE `" . self::tableName . "` SET `score` = '" . $newScore . "' WHERE `email` = '" . mysql_real_escape_string($email) . "' LIMIT 1;";
        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function setLock($email, $lockId)
    {
        $db = new Database;

        $sql = "UPDATE `" . self::tableName . "` SET `lock_id` = '" .mysql_real_escape_string($lockId). "', `lock_datetime` = NOW() WHERE `email` = '" . mysql_real_escape_string($email) . "' LIMIT 1;";
        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function removeLock($email)
    {
        $db = new Database;

        $sql = "UPDATE `" . self::tableName . "` SET `lock_id` = NULL, `lock_datetime` = NULL WHERE `email` = '" . mysql_real_escape_string($email) . "' LIMIT 1;";
        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function setHygieneDatetime($email)
    {
        $db = new Database;

        $sql = "UPDATE `" . self::tableName . "` SET `hygiene_datetime` = NOW() WHERE `email` = '" . mysql_real_escape_string($email) . "' LIMIT 1;";
        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function addUnsubscribe($email)
    {
        Suppression_Email::addEmailSuppression($email, 2, 6);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function addUnsubscribeTransaction($email, $subId = NULL)
    {
        $db = new Database;

        $sql  = "INSERT INTO `transactions` (id, type, email, activity_id, datetime) VALUES";
        $sql .= " (NULL,";
        $sql .= " '" . Config::TRANSACTION_UNSUBSCRIBE . "',";
        $sql .= " '" . mysql_real_escape_string($email) . "',";

        if ($subId) {
            $sql .= " '" . mysql_real_escape_string($subId) . "',";
        } else {
            $sql .= " NULL,";
        }

        $sql .= " NOW())";

        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function archiveLead(Database $db, $email)
    {
        $sql = "SELECT * FROM `" . self::tableName . "` WHERE `email` = '" . mysql_real_escape_string($email) . "' LIMIT 1;";
        $record = $db->getArrayAssoc($sql);

        $sql  = "INSERT IGNORE INTO `leads_archive`";
        $sql .= " (";
        foreach (array_keys($record) AS $key) {
            $sql .= $key . ', ';
        }
        $sql = substr($sql, 0, -2);
        $sql .= ")";

        $sql .= "VALUES (";
        foreach ($record AS $key => $value) {
            if ($value == '') {
                $sql .= "NULL, ";
            } else {
                $sql .= " '" . mysql_real_escape_string($value) . "', ";
            }
        }
        $sql = substr($sql, 0, -2);
        $sql .= ")";

        if($db->query($sql)) {
            $sql = "DELETE FROM `leads` WHERE `email` = '" . mysql_real_escape_string($email) . "' LIMIT 1;";
            $db->query($sql);

            return true;
        }

        return false;
    }
    //--------------------------------------------------------------------------
    
    
    public static function addRecord($data)
    {
        $db = new Database;
        
        if (empty($data['email'])) {
            return false;
        }
        // ticket #89: Set subscription date to today if not explicitly set on lead import
        if (empty($data['subscribe_datetime'])) {
            $data['subscribe_datetime'] = date('Y-m-d H:i:s');
        }
        
        $fields = array('email','domain','score','md5_email','md5_domain','address','first_name','last_name',
                        'country','phone','os','language','state','city','postal_code',
                        'source_url','source_domain','source_campaign','source_username',
                        'ip','birth_day','birth_month','birth_year','gender','seeking','lock_id',
                        'lock_datetime','subscribe_datetime','verification_datetime','hygiene_datetime'
                  );
        
        $insertSql = "INSERT INTO `" . self::tableName . "` (";
        
        $valueSql = " VALUES (";
        
        foreach($fields as $field) {
            if (!empty($data[$field])) {
                $insertSql .= $field . ",";
                $valueSql  .= "'" . mysql_real_escape_string($data[$field]). "',";
            }
        }
        
        $insertSql = rtrim($insertSql, ',') . ')';
        $valueSql = rtrim($valueSql, ',') . ')';
        
        $sql = $insertSql . $valueSql . ';';

        return $db->query($sql);

    }
    //--------------------------------------------------------------------------
    
    public static function updateRecord($data)
    {
        $db = new Database;
        
        if (empty($data['email'])) {
            return false;
        }
        
        $fields = array('score','address','first_name','last_name',
                        'country','phone','os','language','state','city','postal_code',
                        'source_url','source_domain','source_campaign','source_username',
                        'ip','birth_day','birth_month','birth_year','gender','seeking','lock_id',
                        'lock_datetime','subscribe_datetime','verification_datetime','hygiene_datetime'
                  );
        
        $sql = "UPDATE `" . self::tableName . "` SET";
        
        $emptyData = true;
        
        foreach($fields as $field) {
            if (!empty($data[$field])) { 
                $sql  .= " `$field` = " . "'" . mysql_real_escape_string($data[$field]). "',";
                $emptyData = false;
            }
        }
        
        $sql = rtrim($sql, ',');
        
        $sql .= " WHERE `email` = '" . mysql_real_escape_string($data['email']) . "';";

        if (!$emptyData) {
            return $db->query($sql);
        } else {
            return false;
        }  
    }
    //--------------------------------------------------------------------------
    
    public static function deleteRecord($id) 
    {
        $db = new Database;
        $sql = "DELETE FROM `" . self::tableName . "` WHERE `email` = '" . mysql_real_escape_string($id) . "';";
        return $db->query($sql);   
    }
    //--------------------------------------------------------------------------
    
    public static function getLeadDataFromXml($xml) 
    {
        $parts = explode('@', $xml->lead->email[0]);

        $email = (string)$xml->lead->email[0];
        $domain = $parts[1];
        $md5_email = md5($email);
        $md5_domain = md5($domain);
        
        $data = array(
            'email' => $email
            ,'domain' => $domain
            ,'md5_email' => $md5_email
            ,'md5_domain' => $md5_domain
        );
        $data['address'] = (string)$xml->lead->address[0];
        $data['first_name'] = (string)$xml->lead->firstname[0];
        $data['last_name'] = (string)$xml->lead->lastname[0];
        $data['country'] = (string)$xml->lead->country[0];
        $data['phone'] = (string)$xml->lead->phone[0];
        $data['os'] = (string)$xml->lead->os[0];
        $data['language'] = (string)$xml->lead->language[0];
        $data['state'] = (string)$xml->lead->state[0];
        $data['city'] = (string)$xml->lead->city[0];
        $data['postal_code'] = (string)$xml->lead->postalcode[0];
        $data['source_domain'] = (string)$xml->lead->sourcedomain[0];
        $data['source_url'] = (string)$xml->lead->sourceurl[0];
        $data['source_campaign'] = (string)$xml->lead->sourcecampaign[0];
        $data['source_username'] = (string)$xml->lead->sourceusername[0];
        $data['ip'] = (string)$xml->lead->ip[0];
        $data['subscribe_datetime'] = (string)$xml->lead->subscribedate[0];
        $data['birth_day'] = (string)$xml->lead->birthday[0];
        $data['birth_month'] = (string)$xml->lead->birthmonth[0];
        $data['birth_year'] = (string)$xml->lead->birthyear[0];
        $data['gender'] = (string)$xml->lead->gender[0];
        $data['seeking'] = (string)$xml->lead->seeking[0];
        $data['hygiene_datetime'] = (string)$xml->lead->hygienedatetime[0];

        if (!empty($xml->lead->score[0])) {
            $feedScore = (int)$xml->lead->score[0];

            if (($feedScore <= 100) && ($feedScore >= 0)) {
                $data['score'] = $feedScore;
            } else {
                $data['score'] = Config::SCOREMOD_NEW;
            }
        } else {
            $data['score'] = Config::SCOREMOD_NEW;
        }
        return $data;
    }
    //--------------------------------------------------------------------------
    
    public static function formatLead($lead, $format = 'xml') 
    {
        if ($format === 'xml') {
            $result = "";
            $result .= "<lead>\n";
            $result .= "\t<email>" . $lead->getEmail() . "</email>\n";
            $result .= "\t<address>" . $lead->getAddress() . "</address>\n";
            $result .= "\t<first_name>" . $lead->getFirstName() . "</first_name>\n";
            $result .= "\t<last_name>" . $lead->getLastName() . "</last_name>\n";
            $result .= "\t<country>" . $lead->getCountry() . "</country>\n";
            $result .= "\t<phone>" . $lead->getPhone() . "</phone>\n";
            $result .= "\t<os>" . $lead->getOS() . "</os>\n";
            $result .= "\t<language>" . $lead->getLanguage() . "</language>\n";
            $result .= "\t<state>" . $lead->getState() . "</state>\n";
            $result .= "\t<city>" . $lead->getCity() . "</city>\n";
            $result .= "\t<postal_code>" . $lead->getPostalCode() . "</postal_code>\n";
            $result .= "\t<sourcedomain>" . $lead->getDomainName() . "</sourcedomain>\n";
            $result .= "\t<sourceurl>" . $lead->getSourceUrl() . "</sourceurl>\n";
            $result .= "\t<sourcecampaign>" . $lead->getCampaign() . "</sourcecampaign>\n";
            $result .= "\t<sourceusername>" . $lead->getUsername() . "</sourceusername>\n";
            $result .= "\t<ip>" . $lead->getIP() . "</ip>\n";
            $result .= "\t<subscribedate>" . $lead->getSubscribeDate() . "</subscribedate>\n";
            $result .= "\t<birthday>" . $lead->getBirthDay() . "</birthday>\n";
            $result .= "\t<birthmonth>" . $lead->getBirthMonth() . "</birthmonth>\n";
            $result .= "\t<birthyear>" . $lead->getBirthYear() . "</birthyear>\n";
            $result .= "\t<gender>" . $lead->getGender() . "</gender>\n";
            $result .= "\t<seeking>" . $lead->getSeeking() . "</seeking>\n";
            $result .= "</lead>\n";
            return $result;
        } else {
            return "Unsupported format";
        }
    }
    //--------------------------------------------------------------------------
}