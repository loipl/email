<?php

require_once dirname(__FILE__) . '/../email.php';
require_once 'api-call.php';
authenticateUser();

$params = array (
    'apikey' => Config::$apiKey
);

$params += $_GET;

$requestUrl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$apiBase = preg_replace('/\/ui\/.*$/', '/api/throttle.php', $requestUrl);

$sortBy = !empty($params['sort_by']) ? $params['sort_by'] : 'id';
$sortOrder = !empty($params['sort_order']) ? $params['sort_order'] : 'DESC';
$currentPage = !empty($params['page']) ? $params['page'] : '1';
$fromDate = !empty($params['from_date']) ? $params['from_date'] : date('Y-m-d', time() - 86400);
$toDate = !empty($params['to_date']) ? $params['to_date'] : date('Y-m-d');

$sortItems = array(
    'id' => 'Id',
    'type' => "Type",
    'domain' => 'Domain',
    'source_campaign' => 'Source Campaign',
    'tld_group' => 'Tld Group',
    'channel' => 'Chennel',
    'created' => 'Created time'
);

$sortOrders = array (
    'desc' => 'Desc',
    'asc' => 'Asc'
);

$allThrottles = getAllRecords($apiBase, $params);

$countThrottle = countAllRecords($apiBase, $params);
$pageSize = Throttle::pageSize;


$numOfPage = ceil($countThrottle/$pageSize);

$page = 'throttle';
$pageTitle = 'Throttle';
$pageName = 'Throttle';
$pageDescription = 'List of Throttle';
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
            <?php if (!empty($allThrottles)): ?>
            <table cellpadding="0" cellspacing="0" border="0" class="display sTable log_table">
                <thead>
                    <tr>
                        <th>
                            Id
                        </th>
                        <th>
                            Type
                        </th>
                        <th>
                            Domain
                        </th>
                        <th>
                            Source Campaign
                        </th>
                        <th>
                            Tld Group
                        </th>
                        <th>
                            Channel Id
                        </th>
                        <th>
                            Creative Id
                        </th>
                        <th>
                            Campaign Id
                        </th>
                        <th>
                            Category Id
                        </th>
                        <th>
                            Create Time
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($allThrottles as $throttle): ?>
                    <tr abbr="<?php echo $throttle['id']; ?>" class="log_row gradeA">
                            <td><?php echo $throttle['id']; ?></td>
                            <td>
                                <?php echo $throttle['type']; ?>
                            </td>
                            <td>
                                <?php echo $throttle['domain']; ?>
                            </td>
                            <td>
                                <?php echo $throttle['source_campaign']; ?>
                            </td>
                            <td>
                                <?php echo $throttle['tld_group']; ?>
                            </td>
                            <td>
                                <?php echo $throttle['channel']; ?>
                            </td>
                            <td>
                                <?php echo $throttle['creative_id']; ?>
                            </td>
                            <td>
                                <?php echo $throttle['campaign_id']; ?>
                            </td>
                            <td>
                                <?php echo $throttle['category_id']; ?>
                            </td>
                            <td>
                                <?php echo $throttle['created']; ?>
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