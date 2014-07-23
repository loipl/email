/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function(){
    
    $('.show').click(function(){
        $(this).parent().find('table').show();
        $(this).parent().find('.show_hide').show();
        $(this).parent().find('.hide').show();
        $(this).hide();
    });
    $('.hide').click(function(){
        $(this).parent().find('table').hide();
        $(this).parent().find('.show_hide').hide();
        $(this).parent().find('.show').show();
        $(this).hide();
    });
    
    $('.update_table').click(function(){
        var filters = getFilters();
        refreshPage(filters);
    })
    
    $('.page_number').click(function(){
        var filters = getFilters();
        filters['page'] = $(this).val();
        refreshPage(filters);
    })
    
    function getFilters() {
        return {
            sort_by: $('.sort_by').val(),
            sort_order: $('.sort_order').val()
        }
    }
    
    function refreshPage(filters) {
        var url = 'log-scheduler.php?';
        for (var i in filters) {
            url += i + '=' + filters[i] + '&';
        }
        url = url.replace(/&$/, '');
        window.location.href = url;
    }
    
   $('.attributes input').attr('disabled','disabled');
   $('.attributes select').attr('disabled','disabled');
    
});