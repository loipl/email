<?php

class Probability extends Database
{

    protected $id;
    protected $metricType;
    protected $metricId;
    protected $adjustmentType;
    protected $adjustmentFactor;
    protected $matchType;
    protected $matchAttributes;
    protected $created;
    protected $expiration;

    protected $tableName = 'probability';
    const      tableName = 'probability';

    public function __construct($probabilityId)
    {
        parent::__construct();

        $sql  = "SELECT * FROM `$this->tableName` WHERE `id` = '" . $probabilityId . "';";

        $result = $this->getArrayAssoc($sql);

        $this->id               = $probabilityId;
        $this->metricType       = $result['metric_type'];
        $this->metricId         = $result['metric_id'];
        $this->adjustmentType   = $result['adjustment_type'];
        $this->adjustmentFactor = $result['adjustment_factor'];
        $this->matchType        = $result['matchType'];
        $this->matchAttributes  = $result['matchAttributes'];
        $this->created          = $result['created'];
        $this->expiration       = $result['expiration'];
    }
    //--------------------------------------------------------------------------


    public static function addRecord($metricType, $metricId, $adjustmentType, $adjustmentFactor, $matchType, $matchAttributes, $expiration = 3600)
    {
        $dateObject = new DateTime();
        $dateObject->add(new DateInterval('PT' . $expiration . 'S'));
        $expirationDatetime = $dateObject->format('Y-m-d H:i:s');

        $db = new Database;

        $sql  = "INSERT INTO `" . self::tableName. "` (id, metric_type, metric_id,";
        $sql .= " adjustment_type, adjustment_factor, match_type, match_attributes, created, expiration) VALUES";
        $sql .= "(NULL,";
        $sql .= " ' " . mysql_real_escape_string($metricType) . " ',";
        $sql .= " ' " . mysql_real_escape_string($metricId) . " ',";
        $sql .= " ' " . mysql_real_escape_string($adjustmentType) . " ',";
        $sql .= " ' " . mysql_real_escape_string($adjustmentFactor) . " ',";

        if (!empty($matchType)) {
            $sql .= " ' " . mysql_real_escape_string($matchType) . " ',";
        } else {
            $sql .= " NULL,";
        }

        if (!empty($matchAttributes)) {
            $sql .= " ' " . mysql_real_escape_string($matchAttributes) . " ',";
        } else {
            $sql .= " NULL,";
        }

        $sql .= " NOW(),";
        $sql .= " ' " . mysql_real_escape_string($expirationDatetime) . " ')";

        $result = $db->query($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getSuppressedCampaigns()
    {
        $db = new Database;

        $sql  = "SELECT `metric_id` FROM `" . self::tableName. "`";
        $sql .= " WHERE `expiration`        > NOW()";
        $sql .= " AND   `adjustment_type`   =  '2'";
        $sql .= " AND   `adjustment_factor` =  '100'";
        $sql .= " AND   `metric_type`       =  '1'";
        $sql .= " AND   `match_type`        IS NULL";

        $result = $db->getArray($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getExpiredRows()
    {
        $db = new Database;

        $sql = "SELECT `id` FROM `" . self::tableName. "` WHERE `expiration` < NOW()";
        $result = $db->getArray($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getAttributesById($probabilityId)
    {
        $db = new Database;

        $sql = "SELECT * FROM `" . self::tableName. "` WHERE `id` = '" . $probabilityId . "' LIMIT 1;";
        $result = $db->getArrayAssoc($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getMetricTypeById($probabilityId)
    {
        $db = new Database;

        $sql = "SELECT `metric_type` FROM `" . self::tableName. "` WHERE `id` = '" . $probabilityId . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getMetricIdById($probabilityId)
    {
        $db = new Database;

        $sql = "SELECT `metric_id` FROM `" . self::tableName. "` WHERE `id` = '" . $probabilityId . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getAdjustmentTypeById($probabilityId)
    {
        $db = new Database;

        $sql = "SELECT `adjustment_type` FROM `" . self::tableName. "` WHERE `id` = '" . $probabilityId . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getAdjustmentFactorById($probabilityId)
    {
        $db = new Database;

        $sql = "SELECT `adustment_factor` FROM `" . self::tableName. "` WHERE `id` = '" . $probabilityId . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getMatchTypeById($probabilityId)
    {
        $db = new Database;

        $sql = "SELECT `match_type` FROM `" . self::tableName. "` WHERE `id` = '" . $probabilityId . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getMatchAttributesById($probabilityId)
    {
        $db = new Database;

        $sql = "SELECT `match_attributes` FROM `" . self::tableName. "` WHERE `id` = '" . $probabilityId . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getCreatedById($probabilityId)
    {
        $db = new Database;

        $sql = "SELECT `created` FROM `" . self::tableName. "` WHERE `id` = '" . $probabilityId . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getExpirationById($probabilityId)
    {
        $db = new Database;

        $sql = "SELECT `expiration` FROM `" . self::tableName. "` WHERE `id` = '" . $probabilityId . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public function getAttributes()
    {
        $attributes = array(
            'id'               => $this->id,
            'metricType'       => $this->metricType,
            'metricId'         => $this->metricId,
            'adjustmentType'   => $this->adjustmentType,
            'adjustmentFactor' => $this->adjustmentFactor,
            'matchType'        => $this->matchType,
            'matchAttributes'  => $this->matchAttributes,
            'created'          => $this->created,
            'expiration'       => $this->expiration
        );

        return $attributes;
    }


    public function getMetricType()
    {
        return $this->metricType;
    }
    //--------------------------------------------------------------------------


    public function getMetricId()
    {
        return $this->metricId;
    }
    //--------------------------------------------------------------------------


    public function getAdjustmentType()
    {
        return $this->adjustmentType;
    }
    //--------------------------------------------------------------------------


    public function getAdjustmentFactor()
    {
        return $this->adjustmentFactor;
    }
    //--------------------------------------------------------------------------


    public function getMatchType()
    {
        return $this->matchType;
    }
    //--------------------------------------------------------------------------


    public function getMatchAttributes()
    {
        return $this->matchAttributes;
    }
    //--------------------------------------------------------------------------


    public function getCreated()
    {
        return $this->created;
    }
    //--------------------------------------------------------------------------


    public function getExpiration()
    {
        return $this->expiration;
    }
    //--------------------------------------------------------------------------
}