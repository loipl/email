/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function(){
    $('.edit_attr_button').click(function(){
        var campaignInfo = getCampaignInfo($(this).closest('tr.campaign_row'));
        $.post(
                'campaigns.php', 
                {
                    action: 'editAttributes',
                    data: JSON.stringify(campaignInfo)
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
    
    function getCampaignInfo($campRow) {
        var id = $campRow.attr('abbr');
        var attributes = {};
        $campRow.find('.attributes table tr').each(function(){
            var attrName = $(this).find('.attrName').attr('abbr');
            var attrValue = "";
            if ($(this).find('.attrData input').length > 0) {
                var $input = $(this).find('.attrData input');
                if ($input.attr('type') === 'checkbox') {
                    attrValue = $input.is(':checked');
                } else if ($input.attr('type') === 'number'){
                    if ($input.val() !== "") {
                        attrValue = parseInt($input.val());
                    } else {
                        attrValue = 0;
                    }
                } else {
                    attrValue = $input.val();
                }
            } else if ($(this).find('.attrData select').length > 0) {
                attrValue = $(this).find('.attrData select').val();
            }
            if (typeof attrName !== 'undefined') {
                attributes[attrName] = attrValue;
            } 
        })
        return {
            id: id, attributes: attributes
        };       
    }
});