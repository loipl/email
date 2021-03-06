<?php

require_once dirname(__FILE__) . '/../email.php';
require_once 'api-call.php';
authenticateUser();

$params = array (
    'apikey' => Config::$apiKey
);

$params += $_GET;

$requestUrl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$apiBase = preg_replace('/\/ui\/.*$/', '/api/log-php.php', $requestUrl);

$currentPage = !empty($params['page']) ? $params['page'] : '1';
$fromDate = !empty($params['from_date']) ? $params['from_date'] : date('Y-m-d', time() - 86400);
$toDate = !empty($params['to_date']) ? $params['to_date'] : date('Y-m-d');
$searchWord = !empty($params['search_word']) ? $params['search_word'] : "";

$page = 'log_php';
$pageTitle = 'Log PHP';
$pageName = 'Log PHP';
$pageDescription = 'View, search PHP error log';

$logs = getAllRecords($apiBase, $params);
$countLog = countAllRecords($apiBase, $params);
$pageSize = LogDebug::pageSize;
$numOfPage = ceil($countLog/$pageSize);

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
            <div class="filter_bar_element from_date">
                From:
                <input id="from_date" value="<?php echo $fromDate;?>">
            </div>
            <div class="filter_bar_element to_date">
                To:
                <input id="to_date" value="<?php echo $toDate;?>">
            </div>
            <div class="filter_bar_element">
                Search:
                <input id="search" value="<?php echo $searchWord;?>" placeholder="Enter word to search">
            </div>
            <div class="filter_bar_element">
                <input id="group_result" type="checkbox" name="group_result" <?php if (isset($_GET['group_result']) && (int) $_GET['group_result'] === 1) {echo 'checked = "checked"';}?>> &nbsp; Group result
            </div>
            <div class="filter_bar_element">
                <button class="update_table">Update</button>
            </div>
        </div>
        
        <!-- Dynamic table -->
        <div class="widget">
            <div class="title"><img src="images/icons/dark/full2.png" alt="" class="titleIcon" /><h6><?php echo $pageName?></h6></div>                          
            <table cellpadding="0" cellspacing="0" border="0" class="display sTable log_table">
            <thead>
            <tr>
                <th>
                    Id
                </th>
                <th>
                    Time
                </th>
                <th>
                    Error Number
                </th>
                <th>
                    Description
                </th>
                <th>
                    File
                </th>
                <th>
                    Line
                </th>
            </tr>
            </thead>
            <tbody>
                <?php 
                if (!empty($logs)) :
                    foreach ($logs as $log): ?>
                    <tr abbr="<?php echo $log['id']; ?>" class="campaign_row gradeA">
                        <td><?php echo $log['id']; ?></td>
                        <td><?php echo $log['datetime']; ?></td>
                        <td><?php echo $log['error_number']; ?></td>
                        <td>
                            <div class="error_string">
                                <textarea disabled><?php echo $log['error_string']; ?></textarea>
                            </div> 
                        </td>
                        <td><?php echo $log['error_file']; ?></td>
                        <td><?php echo $log['error_line']; ?></td>
                    </tr>
                <?php 
                    endforeach; 
                endif;?>
            </tbody>
            </table>  
             <div class="paging_bar">
                <?php echo Html::getHtmlForPaging($numOfPage,intval($currentPage));?>
            </div>
        </div>
    
    </div>
    
</div>

<?php include('layout/footer.php');?>
