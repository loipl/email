<?php

require_once dirname(__FILE__) . '/../email.php';
authenticateUser();

$params = array (
    'apikey' => Config::$apiKey
);

$params += $_GET;

$requestUrl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$apiBase = preg_replace('/\/ui\/.*$/', '/api/log-debug.php', $requestUrl);

$currentPage = !empty($params['page']) ? $params['page'] : '1';
$fromDate = !empty($params['from_date']) ? $params['from_date'] : date('Y-m-d', time() - 86400);
$toDate = !empty($params['to_date']) ? $params['to_date'] : date('Y-m-d');
$searchWord = !empty($params['search_word']) ? $params['search_word'] : "";

$page = 'log_debug';
$pageTitle = 'Log Debug';
$pageName = 'Log Debug';
$pageDescription = 'View, search debug log';

$logs = LogDebug::getAll($fromDate, $toDate, $searchWord, $currentPage);
$countLog = LogDebug::countAll($fromDate, $toDate, $searchWord);
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
                    Description
                </th>
                <th>
                    Data
                </th>
                
            </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr abbr="<?php echo $log['id']; ?>" class="campaign_row gradeA">
                        <td><?php echo $log['id']; ?></td>
                        <td><?php echo $log['datetime']; ?></td>
                        <td><?php echo $log['description']; ?></td>
                        <td>
                            <div class="debug_data">
                                <textarea disabled><?php echo $log['data']; ?></textarea>
                            </div> 
                        </td>
                         
                    </tr>
                <?php endforeach;?>
            </tbody>
            </table>  
             <div class="paging_bar">
                <?php echo Html::getHtmlForPaging($numOfPage,intval($currentPage));?>
            </div>
        </div>
    
    </div>
    
</div>

<?php include('layout/footer.php');?>
