<?php

class HTML
{

    public static function encodeToken($email, $subId, $link)
    {
        $token  = self::encodeHash($email) . Config::SEPARATOR_EMAIL;
        $token .= self::encodeHash($subId) . Config::SEPARATOR_SUBID;
        $token .= self::encodeHash($link);

        return $token;
    }
    //--------------------------------------------------------------------------


    public static function decodeToken($token)
    {
        $explodeEmail = explode(Config::SEPARATOR_EMAIL, $token);

        if (isset($explodeEmail) && is_array($explodeEmail) && !empty($explodeEmail[1])) {
            $parts['email'] = $explodeEmail[0];

            if (!strpos($token, Config::SEPARATOR_SUBID)) {
                $explodeCampaign = explode(Config::SEPARATOR_CAMPAIGN, $explodeEmail[1]);
                $parts['campaign'] = $explodeCampaign[0];

                if (!empty($explodeCampaign[1])) {
                    $explodeOffer = explode(Config::SEPARATOR_OFFER, $explodeCampaign[1]);
                    $parts['offer'] = $explodeOffer[0];
                }

                if (!empty($explodeOffer[1])) {
                    $explodeLink = explode(Config::SEPARATOR_LINK, $explodeOffer[1]);
                    $parts['link'] = $explodeLink[0];
                }
            } else {
                $explodeSubId = explode(Config::SEPARATOR_SUBID, $explodeEmail[1]);
                $parts['subid'] = $explodeSubId[0];

                if (!empty($explodeSubId[1])) {
                    $explodeLink = explode(Config::SEPARATOR_LINK, $explodeSubId[1]);
                    $parts['link'] = $explodeLink[0];
                }
            }

            $decodedToken['email'] = self::decodeHash($parts['email']);

            if (isset($parts['campaign'])) {
                $decodedToken['campaign'] = self::decodeHash($parts['campaign']);
            }

            if (isset($parts['offer'])) {
                $decodedToken['offer'] = self::decodeHash($parts['offer']);
            }

            if (isset($parts['subid'])) {
                $decodedToken['subid'] = self::decodeHash($parts['subid']);
            }

            if (isset($parts['link'])) {
                $decodedToken['link'] = self::decodeHash($parts['link']);
            }

            return $decodedToken;
        } else {
            Logging::logDebugging('HTML Helper Token Error', $token);
            return false;
        }
    }
    //--------------------------------------------------------------------------


    public static function getTokenFromUrl($url)
    {
        $explodeUrl = explode('token=', $url);

        return $explodeUrl[1];
    }
    //--------------------------------------------------------------------------


    public static function getPixelLink($email, $subId, $senderDomain)
    {
        return "<img src=\"http://" . Config::$subdomains['images'] . '.' . $senderDomain . "/email/tracking/open.php?token=" . self::encodeToken($email, $subId, 'pixel') . "\" width=\"1\" height=\"1\" border=\"0\">";
    }
    //--------------------------------------------------------------------------


    public static function getEncodedLink($email, $subId, $link, $senderDomain)
    {
        return 'http://' . Config::$subdomains['clicks'] . '.' . $senderDomain . "/email/tracking/click.php?token=" . self::encodeToken($email, $subId, $link);
    }
    //--------------------------------------------------------------------------


    public static function encodeHash($text)
    {
        return urlencode(base64_encode($text));
    }
    //--------------------------------------------------------------------------


    public static function decodeHash($text)
    {
        if (empty($text) || $text == '') {
            return false;
        }

        return base64_decode(rawurldecode($text));
    }
    //--------------------------------------------------------------------------


    public static function getUnsubscribeUrl()
    {
        if (isset(Config::$unsubscribeUrl) && !empty(Config::$unsubscribeUrl)) {
            return Config::$unsubscribeUrl;
        } else {
            return false;
        }
    }
    //--------------------------------------------------------------------------


    public static function doEncoding($email, $subId, $senderDomain, &$htmlBody, &$textBody)
    {
        $regexp = "<a\s[^>]*href\s*=\s*([\"\']??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
        if(preg_match_all("/$regexp/siU", $htmlBody, $matches, PREG_SET_ORDER)) {
            foreach ($matches AS $match) {
                $encodedLink = self::getEncodedLink($email, $subId, $match[2], $senderDomain);
                $htmlBody = str_replace($match[2],$encodedLink,$htmlBody);
                $textBody = str_replace($match[2],$encodedLink,$textBody);
            }
        }
    }
    //--------------------------------------------------------------------------


    public static function addHtmlFooter($email, $subId, $senderDomain, $clickSubdomain, &$htmlBody, $footer)
    {
        $htmlBody .= $footer->addHtml($footer->getHtml(), $clickSubdomain, $senderDomain, $email, $subId);
    }
    //--------------------------------------------------------------------------


    public static function addTextFooter($email, $subId, $senderDomain, $clickSubdomain, &$textBody, $footer)
    {
        $textBody .= $footer->addText($footer->getText(), $clickSubdomain, $senderDomain, $email, $subId);
    }
    //--------------------------------------------------------------------------


    public static function addHtmlPixel($email, $subId, $senderDomain, &$htmlBody)
    {
        $htmlBody .= "<br /><br />" . self::getPixelLink($email, $subId, $senderDomain);
    }
    //--------------------------------------------------------------------------


    public static function getUnsub($clickSubdomain, $senderDomain, $email, $subId)
    {
        $emailHash  = HTML::encodeHash($email);
        $subidHash  = HTML::encodeHash($subId);
        $unsubUrl   = 'http://' . $clickSubdomain . '.' . $senderDomain;
        $unsubUrl  .= '/email/tracking/unsubscribe.php';
        $unsubUrl  .= '?id='. $emailHash . '&sub=' . $subidHash;

        return $unsubUrl;
    }
    //--------------------------------------------------------------------------
    
    public static function getHtmlForCampaignAttributes($attributes) {
        
        $allAttributes = self::getCampaignAttributeDescription();
        
        $html = '<table class="no-border"><tbody>';
        foreach ($allAttributes as $attrName => $description) {
            $attrValue = isset($attributes[$attrName]) ? $attributes[$attrName] : null;
            $html .= '<tr>';
            $html .= '<td class="attrName" abbr="' . $attrName . '">' . $attrName . '</td>';
            $html .= '<td></td>';
            $html .= '<td class="attrData">';
            switch ($description['type']){
                case 'number':
                    $html .= '<input value="' . $attrValue . '" type="number">' ;
                    break;
                case 'bool':
                    $checkedStr = $attrValue ? 'checked' : '';
                    $html .= '<input type="checkbox" ' . $checkedStr . '>';
                    break;
                case 'select':
                    $html .= self::getHtmlForSelect($description['options'], $attrValue);
                    break;
                case 'list':
                    if (is_array($attrValue)) {
                        $attrValue = implode(',', $attrValue);
                    }
                    $html .= '<input value="' . $attrValue . '" type="list">' ;
                    break;
                case 'bool_list':
                    $list = $description['list'];
                    foreach ($list as $item) {
                        if (!empty($attrValue) && is_array($attrValue) && $attrValue[$item]) {
                            $html .= ' <div><input type="checkbox" name="' . $item . '" checked>' . $item . '</div>';
                        } else {
                            $html .= ' <div><input type="checkbox" name="' . $item . '">' . $item . '</div>';
                        }
                    }
                    break;
                default:
                    $html .= '<input value="' . $attrValue . '" type="text">' ;
                    break;
            }
            $html .= '</td>';
            if (isset($description['enableIgnore']) && $description['enableIgnore']) {
                if ($attrValue === false) {
                    $html .= ' <td><input type="checkbox" class="ignore" checked> Ignore </td>';
                } else {
                    $html .= ' <td><input type="checkbox" class="ignore"> Ignore </td>' ;
                }
            }
            $html .= '</tr>';
        }
        $html .= '<tr><td></td><td></td><td><input class="edit_attr_button" type="button" value="Set"></td></tr>';
        $html .= '</tbody></table>';
        return $html;
    }
    //--------------------------------------------------------------------------
    
    public static function getHtmlForSelect($options, $value, $class = null) {
        $html = '<select class="' . $class .'">';
        foreach ($options as $optionValue => $optionText) {
            if ($optionValue == $value) {
                $html .= '<option value="' . $optionValue . '" selected="selected">';
            } else {
                $html .= '<option value="' . $optionValue . '">';
            }
            $html .= $optionText;
            $html .= '</option>';
        }
        $html .= '</select>';
        return $html;
    }
    //--------------------------------------------------------------------------
    
    public static function getHtmlForPaging($numberOfPage, $pageNumber) {
        $displayPages = array($pageNumber - 2, $pageNumber - 1, $pageNumber, $pageNumber + 1, $pageNumber + 2);
        foreach ($displayPages as $key => $value) {
            if ($value <= 0 || $value > $numberOfPage) {
                unset($displayPages[$key]);
            }
        }
        
        $html = "<button class=\"page_number\" value=\"1\">First</button>";
        foreach ($displayPages as $page) {
            if ($page === $pageNumber) {
                $html .= "<button class=\"page_number current\" value=\"$page\">$page</button>";
            } else {
                $html .= "<button class=\"page_number\" value=\"$page\">$page</button>";
            }
        }
        $html .= "<button class=\"page_number\" value=\"$numberOfPage\">Last</button>";
        return $html;
    }

    //--------------------------------------------------------------------------
    public static function getCampaignAttributeDescription() {
        return array (
            'categoryId' => array (
                'type' => 'number', 'default' => null
            ),
            'categoryAction' => array (
                'type' => 'text', 'default' => null
            ),
            'count' => array (
                'type' => 'number', 'default' => 0
            ),
            'interval' => array (
                'type' => 'number', 'default' => null
            ), 
            'minScore' => array (
                'type' => 'number', 'default' => 0
            ),
            'campaignId' => array (
                'type' => 'number', 'default' => null
            ),
            
            'type' => array (
                'type' => 'select', 
                'options' => array (
                    'verified' => 'Verified',
                    'openers'  => 'Openers',
                    'clickers'  => 'Clickers'
                )
            ),
            
            'country' => array (
                'type' => 'list',
                'default' => null,
            ),
            'state' => array (
                'type' => 'list',
                'default' => null,
            ),
            'tldList' => array (
                'type' => 'list',
                'default' => null,
                'enableIgnore' => true
            ),
            'inverse' => array(
                'type' => 'bool_list',
                'list' => array('country', 'state', 'tldList')
            ),
            'gender' => array (
                'type' => 'select', 
                'options' => array(
                    ''  => 'Both',
                    'M' => 'Male',
                    'F' => 'Female'
                ),
                'enableIgnore' => true
            ),
            'lastHygiene' => array (
                'type' => 'text', 
                'enableIgnore' => true
            ),
            'lastVerification' => array (
                'type' => 'text', 
                'enableIgnore' => true
            )
          );
    }
}