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
    
});