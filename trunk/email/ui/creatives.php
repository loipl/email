<?php

require_once dirname(__FILE__) . '/../email.php';
$creatives = Creative::getAllCreatives();
?>

<?php if( !(isset($_POST['action']) && $_POST['action'] === 'editCreative') ): ?>
<html>
    <head>
        <title>Creatives List</title>
        <link rel="stylesheet" type="text/css" href="css/creatives.css">
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
                <tr abbr="<?php echo $creative['id']; ?>" class="creative_row">
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
                            <button class="update">Set</button>
                        </td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
        <script type="text/javascript" src="js/creatives.js"></script>
    </body>
</html>

<?php else: ?>
<?php 
    $data = json_decode($_POST['data'], true);
    if (!empty($data)) {
        $id = $data['id'];

        Creative::updateCreativeById($id, $data['class'], $data['category_id'], $data['sender_id'],
                                        $data['name'], $data['from'], $data['subject'],
                                        $data['html_body'], $data['text_body']);
        echo "Complete successfully";
        
    } else {
        echo "Empty data";
    }
    
?>
<?php endif; ?>
