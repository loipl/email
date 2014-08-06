/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function(){
    $('.update').click(function(){
        var creativeInfo = getCreativeInfo($(this).closest('tr.creative_row'));
//        console.log(creativeInfo); return false;
        $.post(
            'creatives.php', 
            {
                action: 'editCreative',
                data: JSON.stringify(creativeInfo)
            },
            function (response) {
                alert(response);
            }
        )

    })
    
    $(".add").colorbox({inline:true, width:"70%"});
    
    $('.create').click(function(){
        var creativeInfo = getAddCreativeInfo($('#add_creative'));
        if (creativeInfo['sender_id'] == '') {
            alert('Please enter sender_id');
            $('#add_creative .sender_id').focus();
            return;
        }
        
        if (creativeInfo['name'] == '') {
            alert('Please enter name');
            $('#add_creative .name').focus();
            return;
        }
        
        if (creativeInfo['from'] == '') {
            alert('Please enter From');
            $('#add_creative .from').focus();
            return;
        }
        
        if (creativeInfo['subject'] == '') {
            alert('Please enter Subject');
            $('#add_creative .subject').focus();
            return;
        }
        
        $('.create').attr('disabled', 'disabled');
        $.post(
            'creatives.php', 
            {
                action: 'addCreative',
                data: JSON.stringify(creativeInfo)
            },
            function (response) {
                if ($.trim(response) === 'Success') {
                    window.location.reload();
                } else {
                    alert(response);
                    $('.create').removeAttr('disabled');
                }
            }
        )
    });
    
    $('.cancel').click(function(){
        $.colorbox.close();
    });
    
    $('input[type=number]').on('keyup', function(){
        var text = $(this).val();
        $(this).val(text.replace(/[^\d]+/g, ''))
    });
    
    $('.show').click(function(){
        $(this).parent().find('textarea').show();
        $(this).parent().find('.hide').show();
        $(this).hide();
    });
    $('.hide').click(function(){
        $(this).parent().find('textarea').hide();
        $(this).parent().find('.show').show();
        $(this).hide();
    });
    
    function getCreativeInfo($creativeRow) {
        
        var id = $creativeRow.attr('abbr');  
        
        return {
            id: id, 
            class: $.trim($creativeRow.find('.class').val()),
            category_id: $.trim($creativeRow.find('.category_id').val()),
            sender_id: $.trim($creativeRow.find('.sender_id').val()),
            name: $.trim($creativeRow.find('.name').val()),
            from: $.trim($creativeRow.find('.from').val()),
            subject: $.trim($creativeRow.find('.subject').val()),
            html_body: $.trim($creativeRow.find('.html_body').val()),
            text_body: $.trim($creativeRow.find('.text_body').val()),
        };       
    }
    
    function getAddCreativeInfo($creativeRow) {
        return {
            class: $.trim($creativeRow.find('.class').val()),
            category_id: $.trim($creativeRow.find('.category_id').val()),
            sender_id: $.trim($creativeRow.find('.sender_id').val()),
            name: $.trim($creativeRow.find('.name').val()),
            from: $.trim($creativeRow.find('.from').val()),
            subject: $.trim($creativeRow.find('.subject').val()),
            html_body: $.trim($creativeRow.find('.add_html_body').val()),
            text_body: $.trim($creativeRow.find('.add_text_body').val()),
        };       
    }
    
});