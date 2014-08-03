<?php

require_once dirname(__FILE__) . '/../email.php';

$params = array (
    'apikey' => Config::$apiKey
);

$params += $_GET;

$requestUrl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$apiBase = preg_replace('/\/ui\/.*$/', '/api/log-scheduler.php', $requestUrl);


$sortItems = array(
    'id' => 'Id',
    'eligible_campaign_count' => "Campaign Count",
    'lead_count' => 'Leads Count',
    'queued_lead_count' => 'Queued Count',
    'create_time' => 'Create Time'
);

$sortOrders = array (
    'desc' => 'Desc',
    'asc' => 'Asc'
);

$allLogs = getAllLog($apiBase, $params);
$countLog = LogScheduler::countAll($apiBase, $params);
$pageSize = LogScheduler::pageSize;

function getAllLog($apiBase, $params) {
    $apiUrl = $apiBase . '?' . http_build_query($params);
    $apiResponse = CurlHelper::request($apiUrl);
    
    if ($apiResponse['httpCode'] === 200) {
        $content = json_decode($apiResponse['content'], true);
        return $content['data']; 
    } else {
        return array();
    }
}

function countAllLog($apiBase, $params) {
    $params['action'] = 'count';
    $apiUrl = $apiBase . '?' . http_build_query($params);
    $apiResponse = CurlHelper::request($apiUrl);
    
    if ($apiResponse['httpCode'] === 200) {
        $content = json_decode($apiResponse['content'], true);
        return $content['data']; 
    } else {
        return 0;
    }
}

$numOfPage = ceil($countLog/$pageSize);

$page = 'log_scheduler';
$pageTitle = 'Log Scheduler';
$pageName = 'Log Scheduler';
$pageDescription = 'List of Log Scheduler';
?>

<?php include('layout/header.php');?>

<?php include('layout/sidebar.php');?>


<!-- Right side -->
<div id="rightSide">

    <?php include('layout/top-navigation.php');?>
    
    <!-- Main content wrapper -->
    <div class="wrapper">
        
        <!-- Filter bar -->
        <div class="filter_bar">  
            <div class="filter_bar_element" >
                Sort By: <?php echo Html::getHtmlForSelect($sortItems, $sortBy, 'sort_by')?>
            </div>
            <div class="filter_bar_element">
                Sort Order: <?php echo Html::getHtmlForSelect($sortOrders, $sortOrder, 'sort_order')?>
            </div>
            <div class="filter_bar_element from_date">
                From:
                <input id="from_date" value="<?php echo $fromDate;?>">
            </div>
            <div class="filter_bar_element to_date">
                To:
                <input id="to_date" value="<?php echo $toDate;?>">
            </div>
            <div class="filter_bar_element">
                <button class="update_table">Update</button>
            </div>
        </div>
        
        <!-- Dynamic table -->
        <div class="widget">
            <div class="title"><img src="images/icons/dark/full2.png" alt="" class="titleIcon" /><h6><?php echo $pageName?></h6></div>                          
            <?php if (!empty($allLogs)): ?>
            <table cellpadding="0" cellspacing="0" border="0" class="display dTable log_table">
                <thead>
                    <tr>
                        <th>
                            Id
                        </th>
                        <th>
                            Scheduler Name
                        </th>
                        <th>
                            Eligible Campaign Count
                        </th>
                        <th>
                            Eligible Campaign Ids
                        </th>
                        <th>
                            Chosen Campaign Id
                        </th>
                        <th>
                            Chosen Campaign Attribute
                        </th>
                        <th>
                            Lead Count
                        </th>
                        <th>
                            Leads
                        </th>
                        <th>
                            Queued Count
                        </th>
                        <th>
                            Message
                        </th>
                        <th>
                            Create Time
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($allLogs as $log): ?>
                    <tr abbr="<?php echo $log['id']; ?>" class="log_row gradeA">
                            <td><?php echo $log['id']; ?></td>
                            <td>
                                <?php echo trim($log['scheduler_name']); ?>
                            </td>
                            <td>
                                <?php echo $log['eligible_campaign_count']; ?>
                            </td>
                            <td>
                                <?php 
                                    if (!empty($log['eligible_campaign_ids'])) {
                                        $campaignIds = unserialize($log['eligible_campaign_ids']);
                                        $campaignIdsStr = implode(',', $campaignIds);
                                        echo $campaignIdsStr;
                                    }
                                ?>
                            </td>
                            <td>
                                <?php echo $log['chosen_campaign_id']; ?>
                            </td>
                            <td class="attributes">
                                <?php 
                                    if (!empty($log['chosen_campaign_attribute'])) {
                                        $attributes = unserialize($log['chosen_campaign_attribute']);
                                        $html = HTML::getHtmlForCampaignAttributes($attributes);
                                        echo '<button class="show">Show</button>';
                                        echo '<button class="hide">Hide</button>';
                                        echo $html;
                                    }
                                ?>
                            </td>
                            <td>
                                <?php echo $log['lead_count']; ?>
                            </td>
                            <td class="leads">
                                <?php 
                                    if (!empty($log['leads'])) {                 
                                        $html = '<button class="show">Show</button>';
                                        $html .= '<button class="hide">Hide</button>';

                                        $html .= '<div class="show_hide">';
                                        $leads = @ unserialize($log['leads']);
                                        if ($leads !== false){
                                            foreach ($leads as $lead) {
                                                $html .= "<div>" . json_encode($lead) . '</div>';
                                            }
                                        } else {
                                            $html .= "<div>Broken serialized string</div>";
                                        }
                                        
                                        $html .= '</div>';
                                        echo $html;
                                    }
                                ?>
                            </td>
                            <td>
                                <?php echo $log['queued_lead_count']; ?>
                            </td>
                            <td>
                                <?php echo $log['message']; ?>
                            </td>
                            <td>
                                <?php echo $log['create_time']; ?>
                            </td>

                        </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
            <div class="paging_bar">
                <?php echo Html::getHtmlForPaging($numOfPage,intval($currentPage));?>
            </div>
        <?php else: ?>
            <div style="margin-top: 20px; font-size: 20px;">
                No log in this period. Please choose another time range!
            </div>
            
        <?php endif; ?>  
           
        </div>
  
    </div>
    
</div>

<?php include('layout/footer.php');?>