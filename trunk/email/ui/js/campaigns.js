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
        
        // get attributes
        var attributes = getAttributes($campRow);
        
        // get creatives id
        var creativeStr = $campRow.find('.creative_ids').val();
        var creativeIds = creativeStr !== "" ? creativeStr.split(',') : null;
        
        return {
            id: id, 
            attributes: attributes,
            campaign_name: $.trim($campRow.find('.campaign_name').val()),
            creative_ids: creativeIds,
            send_limit: $campRow.find('.send_limit').val(),
            sent_count: $campRow.find('.sent_count').val(),
            end_date: $campRow.find('.end_date').val()
        };       
    }
    
    function getAttributes($campRow) {
        var attributes = {};
        var defaultZero = ['count', 'minScore'];
        $campRow.find('.attributes table tr').each(function(){
            var attrName = $(this).find('.attrName').attr('abbr');
            var attrValue = "";
            if ($(this).find('.ignore').is(':checked')) {
                attrValue = false;
            } else if (attrName === 'inverse') {
                attrValue = {};
                $(this).find('.attrData input').each(function(){
                    var itemName = $(this).attr('name');
                    attrValue[itemName] = $(this).is(':checked');
                });
            } else if ($(this).find('.attrData input').length > 0) {
                var $input = $(this).find('.attrData input');
                if ($input.attr('type') === 'checkbox') {
                    attrValue = $input.is(':checked');
                } else if ($input.attr('type') === 'number'){
                    if ($input.val() !== "") {
                        attrValue = parseInt($input.val());
                    } else if (defaultZero.indexOf(attrName) !== -1){
                        attrValue = 0;
                    } else {
                        attrValue = null;
                    }
                } else if ($input.attr('type') === 'list') {
                    if ($input.val() !== "") {
                        attrValue = $input.val().split(',');
                    } else {
                        attrValue = null;
                    }
                } else {
                    attrValue = $input.val() !== "" ? $input.val() : null;
                }
            } else if ($(this).find('.attrData select').length > 0) {
                attrValue = $(this).find('.attrData select').val();
            }
            if (typeof attrName !== 'undefined' && attrValue !== null) {
                attributes[attrName] = attrValue;
            } 
        })
        return attributes;
    }  
});