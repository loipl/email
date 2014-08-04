<?php

require_once dirname(__FILE__) . '/../email.php';
$params = array (
    'apikey' => Config::$apiKey
);

$requestUrl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$apiBase = preg_replace('/\/ui\/.*$/', '/api/creative.php', $requestUrl);
    
$page = 'creative';
$pageTitle = 'Creatives List';
$pageName = 'Creatives';
$pageDescription = 'Creative list';
?>

<?php if( !(isset($_POST['action']) && $_POST['action'] === 'editCreative') ): ?>

<?php
    $apiUrl = $apiBase . '?' . http_build_query($params);
    $apiResponse = CurlHelper::request($apiUrl);
    
    if ($apiResponse['httpCode'] === 200) {
        $content = json_decode($apiResponse['content'], true);
        $creatives = $content['data']; 
    } else {
        $creatives = array();
    }
?>


<?php include('layout/header.php');?>

<?php include('layout/sidebar.php');?>


<!-- Right side -->
<div id="rightSide">

    <?php include('layout/top-navigation.php');?>
    
    <!-- Main content wrapper -->
    <div class="wrapper">
    
        <!-- Dynamic table -->
        <div class="widget">
            <div class="title"><img src="images/icons/dark/full2.png" alt="" class="titleIcon" /><h6><?php echo $pageName?></h6></div>                          
            <table cellpadding="0" cellspacing="0" border="0" class="display sTable">
            <thead>
            <tr>
                <th>
                    Id
                </th>
                <th>
                    Class
                </th>
                <th>
                    Category Id
                </th>
                <th>
                    Sender Id
                </th>
                <th>
                    Name
                </th>
                <th>
                    From
                </th>
                <th>
                    Subject
                </th>
                <th>
                    Html Body
                </th>
                <th>
                    Text Body
                </th>
                <th>
                    Action
                </th>
            </tr>
            </thead>
            <tbody>
                <?php foreach ($creatives as $creative): ?>
                <tr abbr="<?php echo $creative['id']; ?>" class="creative_row gradeA">
                        <td><?php echo $creative['id']; ?></td>
                        <td>
                            <input class="class" value="<?php echo $creative['class']; ?>">
                        </td>
                        <td>
                            <input class="category_id" value="<?php echo $creative['category_id']; ?>" type="number">
                        </td>
                        <td>
                            <input class="sender_id" value="<?php echo $creative['sender_id']; ?>" type="number">
                        </td>
                        <td>
                            <textarea class="name"><?php echo $creative['name']; ?></textarea>
                        </td>
                        <td>
                            <textarea class="from"><?php echo $creative['from']; ?></textarea>
                        </td>
                        <td>
                            <textarea class="subject"><?php echo $creative['subject']; ?></textarea>
                        </td>
                        <td>
                            <button class="show">Show</button>
                            <button class="hide">Hide</button>
                            <div>
                                <textarea class="html_body"><?php echo $creative['html_body']; ?></textarea>
                            </div>
                        </td>
                        <td>
                            <button class="show">Show</button>
                            <button class="hide">Hide</button>
                            <div>
                                <textarea class="text_body"><?php echo $creative['text_body']; ?></textarea>
                            </div>
                        </td>
                        <td>
                            <button class="update">Update</button>
                        </td>
                    </tr>
                <?php endforeach;?>
            </tbody>
            </table>  
        </div>
    
    </div>
    
</div>

<?php include('layout/footer.php');?>

<?php else: ?>
<?php 
    $data = json_decode($_POST['data'], true);
    if (!empty($data)) {
        $id = $data['id'];
        $params['id'] = $id;
        $data['action'] = 'update';
//            $apiUrl = $apiBase . '/' . $id . '?' . http_build_query($params);
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
        echo "Empty data";
    }
    
?>
<?php endif; ?>
