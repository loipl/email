<?php

    require_once dirname(__FILE__) . '/../email.php';
    authenticateUser();

    $params = array (
        'apikey' => Config::$apiKey
    );

    $requestUrl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $apiBase = preg_replace('/\/ui\/.*$/', '/api/campaign.php', $requestUrl);
    
    $page = 'campaign';
    $pageTitle = 'Campaigns List';
    $pageName = 'Campaigns';
    $pageDescription = 'Campaigns list, edit-copy-delete action';
?>

<?php if( !isset($_POST['action']) ): ?>

<?php
    $apiUrl = $apiBase . '?' . http_build_query($params);
    $apiResponse = CurlHelper::request($apiUrl);
    
    if ($apiResponse['httpCode'] === 200) {
        $content = json_decode($apiResponse['content'], true);
        $campaigns = $content['data']; 
    } else {
        $campaigns = array();
    }
?>

<?php include('layout/header.php');?>

<?php include('layout/sidebar.php');?>


<!-- Right side -->
<div id="rightSide">

    <?php include('layout/top-navigation.php');?>
    
    <!-- Main content wrapper -->
    <div class="wrapper">
        <input type="button" class="add" href="#add_campaign" value ="Add"/>
        <div style="display:none">
            <div id="add_campaign" style="padding:10px; background:#fff;">
                <div style="float: left; width: 49%">
                    <table id="add_campaign_table">
                        <tbody> 
                            <tr>
                                <td><b>Campaign name</b></td>
                                <td><textarea class="campaign_name"></textarea></td>
                            </tr>
                            <tr>
                                <td><b>Send limit</b></td>
                                <td><input class="send_limit" type="number"/></td>
                            </tr>
                            <tr>
                                <td><b>Send count</b></td>
                                <td><b><input class="sent_count" type="number"/></td>       
                            </tr>
                            <tr>
                                <td><b>Creative Ids</b></td>
                                <td><input class="creative_ids"/></td>                                
                            </tr>
                            <tr>
                                <td><b>End_date</b></td>
                                <td><input class="end_date"/></td>     
                            </tr>
                        </tbody>
                    </table>
                    <input type="button" class="create" value="Create">
                    <input type="button" class="cancel" value="Cancel">
                </div>
                <div style="float: left; width: 49%">
                    <span style="margin-top: 10px;font-weight: bold;">Attributes:</span>
                    <div class="attributes">
                        <?php 
                            $html = HTML::getHtmlForCampaignAttributes(array());
                            echo $html;
                        ?>
                    </div>
                </div>
                <div style="clear: both;"></div>

                
            </div>
        </div>
        
        <!-- Dynamic table -->
        <div class="widget">
            <div class="title"><img src="images/icons/dark/full2.png" alt="" class="titleIcon" /><h6><?php echo $pageName?></h6></div>                          
            <table cellpadding="0" cellspacing="0" border="0" class="display dTable">
            <thead>
            <tr>
                <th>
                    Id
                </th>
                <th>
                    Campaign Name
                </th>
                <th>
                    Attributes
                </th>
                <th>
                    Send Limit
                </th>
                <th>
                    Sent Count
                </th>
                <th>
                    Creative Id
                </th>
                <th>
                    End Date
                </th>
                <th>
                    Action
                </th>
            </tr>
            </thead>
            <tbody>
                <?php foreach ($campaigns as $campaign): ?>
                <tr abbr="<?php echo $campaign['id']; ?>" class="campaign_row gradeA">
                        <td><?php echo $campaign['id']; ?></td>
                        <td>
                            <textarea class="campaign_name"><?php echo trim($campaign['name']); ?></textarea>
                        </td>
                        <td class="attributes">
                            <?php 
                                $attributes = $campaign['attributes'];
                                $html = HTML::getHtmlForCampaignAttributes($attributes);
                                echo $html;
                            ?>
                        </td>
                        <td><input class="send_limit" value="<?php echo $campaign['send_limit']; ?>" type="number"/></td>
                        <td><?php $send_count = !empty($campaign['sent_count']) ? $campaign['sent_count'] : 0; ?>
                            <input class="sent_count" value="<?php echo $send_count; ?>" type="number"/>
                        </td>
                        <td>
                            <?php 
                                $creativeIds = $campaign['creative_ids'];
                                $creativeStr = is_array($creativeIds) ? implode(',', $creativeIds) : "";   
                            ?>
                            <input class="creative_ids" value="<?php echo $creativeStr; ?>"/>
                        </td>
                        <td>
                            <input class="end_date" value="<?php echo $campaign['end_date']; ?>"/>
                        </td>
                        <td>
                            <input class="update_button" type="button" value="Update"/>
                            <input class="copy_button" type="button" value="Copy"/>
                            <input class="delete_button" type="button" value="Delete"/>
                        </td>
                    </tr>
                <?php endforeach;?>
            </tbody>
            </table>  
        </div>
    
    </div>
    
</div>

<?php include('layout/footer.php');?>

<?php elseif ($_POST['action'] === 'editAttributes') : ?>
<?php 
    $data = json_decode($_POST['data'], true);
    if (!empty($data)) {
        $id = $data['id'];
        $params['id'] = $id;
        if (!empty($data['attributes'])) {
            $data['attributes'] = serialize($data['attributes']);
            $data['creative_ids'] = serialize($data['creative_ids']);
            $data['name'] = $data['campaign_name'];
            $data['action'] = 'update';

            $apiUrl = $apiBase . '?' . http_build_query($params);
            $apiResponse = CurlHelper::request($apiUrl, 'POST', $data);

            if ($apiResponse['httpCode'] === 200) {
                $response = json_decode($apiResponse['content'],true);
                if ($response['status'] === '1') {
                    echo "Complete successfully";
                } else {
                    echo $response['message'];
                } 
            } else {
                echo $apiResponse['httpErr'];
            }
            
        } else {
            echo "Empty attributes";
        }
        
    } else {
        echo "Empty data";
    }
    
?>
<?php elseif ($_POST['action'] === 'addCampaign') : ?>
<?php 
    $data = json_decode($_POST['data'], true);
    if (!empty($data)) {
        if (!empty($data['attributes'])) {
            $data['attributes'] = serialize($data['attributes']);
            $data['creative_ids'] = serialize($data['creative_ids']);
            $data['name'] = $data['campaign_name'];
            $data['action'] = 'add';

            $apiUrl = $apiBase . '?' . http_build_query($params);
            $apiResponse = CurlHelper::request($apiUrl, 'POST', $data);

            if ($apiResponse['httpCode'] === 200) {
                $response = json_decode($apiResponse['content'],true);
                if ($response['status'] === '1') {
                    echo "Success";
                } else {
                    echo $response['message'];
                } 
            } else {
                echo $apiResponse['httpErr'];
            }
            
        } else {
            echo "Empty attributes";
        }
        
    } else {
        echo "Empty data";
    }
    
?>
<?php elseif ($_POST['action'] === 'copyCampaign'): ?>
<?php 
    $id = $_POST['id'];
    $params['id'] = $id;
    if (!empty($id) && is_numeric($id)) {
        $data['action'] = 'copy';
        $apiUrl = $apiBase . '?' . http_build_query($params);
        $apiResponse = CurlHelper::request($apiUrl, 'POST', $data);

        if ($apiResponse['httpCode'] === 200) {
            $response = json_decode($apiResponse['content'],true);
            if ($response['status'] === '1') {
                echo "Success";
            } else {
                echo $response['message'];
            } 
        } else {
            echo $apiResponse['httpErr'];
        }
    } else {
        echo "Invalid Campaign Id";
    }
    
?>
<?php elseif ($_POST['action'] === 'deleteCampaign'): ?>
<?php 
    $id = $_POST['id'];
    $params['id'] = $id;
    if (!empty($id) && is_numeric($id)) {
        $apiUrl = $apiBase . '?' . http_build_query($params);
        $apiResponse = CurlHelper::request($apiUrl, 'DELETE');

        if ($apiResponse['httpCode'] === 200) {
            $response = json_decode($apiResponse['content'],true);
            if ($response['status'] === '1') {
                echo "Success";
            } else {
                echo $response['message'];
            } 
        } else {
            echo $apiResponse['httpErr'];
        }
    } else {
        echo "Invalid Campaign Id";
    }
    
?>
<?php endif; ?>