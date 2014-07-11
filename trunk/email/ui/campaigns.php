<?php

require_once dirname(__FILE__) . '/../email.php';
$campaigns = Campaign::getAllCampaign();
?>

<?php if( !(isset($_POST['action']) && $_POST['action'] === 'editAttributes') ): ?>
<html>
    <head>
        <title>Campaigns List</title>
        <link rel="stylesheet" type="text/css" href="css/campaigns.css">
        <script type="text/javascript" src="js/jquery-1.7.3.js"></script>
    </head>
    <body>
        <table class="campaigns_table">
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
                </tr>
            </thead>
            <tbody>
                <?php foreach ($campaigns as $campaign): ?>
                <tr abbr="<?php echo $campaign['id']; ?>" class="campaign_row">
                        <td><?php echo $campaign['id']; ?></td>
                        <td><?php echo $campaign['name']; ?></td>
                        <td class="attributes">
                            <?php 
                                $attributes = unserialize($campaign['attributes']);
                                $html = HTML::getHtmlForCampaignAttributes($attributes);
                                echo $html;
                            ?>
                        </td>
                        <td><?php echo $campaign['send_limit']; ?></td>
                        <td><?php echo !empty($campaign['send_count']) ? $campaign['send_count'] : 0; ?></td>
                        <td>
                            <?php 
                                $creativeIds = unserialize($campaign['creative_ids']);
                                echo implode(',', $creativeIds);
                            ?>
                        </td>
                        <td><?php echo $campaign['end_date']; ?></td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
        <script type="text/javascript" src="js/campaigns.js"></script>
    </body>
</html>

<?php else: ?>
<?php 
    $data = json_decode($_POST['data'], true);
    if (!empty($data)) {
        $id = $data['id'];
        if (!empty($data['attributes'])) {
            $attributes = serialize($data['attributes']);
            Campaign::updateAttributesById($id, $attributes);
            echo "Complete successfully";
        } else {
            echo "Empty attributes";
        }
        
    } else {
        echo "Empty data";
    }
    
?>
<?php endif; ?>
