/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function(){
    
    
    $('.update_table').click(function(){
        var filters = getFilters();
        refreshPage(filters);
    })
    
    $('#from_date').datepicker({ dateFormat: 'yy-mm-dd' });
    $('#to_date').datepicker({ dateFormat: 'yy-mm-dd' });
    
    $('.page_number').click(function(){
        var filters = getFilters();
        filters['page'] = $(this).val();
        refreshPage(filters);
    })
    
    function getFilters() {
        var result = {
            from_date: $('#from_date').val(),
            to_date: $('#to_date').val()
        }
        
        var search_word = $.trim($('#search').val());
        if (search_word !== "") {
            result.search_word = encodeURIComponent(search_word);
        }
        
        return result;
    }
    
    function refreshPage(filters) {
        var url = 'log-debug.php?';
        for (var i in filters) {
            url += i + '=' + filters[i] + '&';
        }
        url = url.replace(/&$/, '');
        window.location.href = url;
    }
    
});