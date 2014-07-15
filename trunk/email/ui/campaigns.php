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
                        <td>
                            <textarea class="campaign_name"><?php echo trim($campaign['name']); ?></textarea>
                        </td>
                        <td class="attributes">
                            <?php 
                                $attributes = unserialize($campaign['attributes']);
                                $html = HTML::getHtmlForCampaignAttributes($attributes);
                                echo $html;
                            ?>
                        </td>
                        <td><input class="send_limit" value="<?php echo $campaign['send_limit']; ?>" type="number"></td>
                        <td><?php $send_count = !empty($campaign['sent_count']) ? $campaign['sent_count'] : 0; ?>
                            <input class="sent_count" value="<?php echo $send_count; ?>" type="number">
                        </td>
                        <td>
                            <?php 
                                $creativeIds = unserialize($campaign['creative_ids']);
                                $creativeStr = is_array($creativeIds) ? implode(',', $creativeIds) : "";   
                            ?>
                            <input class="creative_ids" value="<?php echo $creativeStr; ?>">
                        </td>
                        <td>
                            <input class="end_date" value="<?php echo $campaign['end_date']; ?>">
                        </td>
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
            $creativeIds = serialize($data['creative_ids']);
            Campaign::updateCampaignById($id, $data['campaign_name'], $attributes, 
                                            $data['send_limit'], $data['sent_count'],
                                            $creativeIds, $data['end_date']);
            echo "Complete successfully";
        } else {
            echo "Empty attributes";
        }
        
    } else {
        echo "Empty data";
    }
    
?>
<?php endif; ?>
