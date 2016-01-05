/**
 * 
 * Posts View access management for roles support
 * 
 **/

jQuery(function() {

    // "Posts View" button at User Role Editor dialog
    jQuery("#ure_posts_view_access_button").button({
        label: ure_data_posts_view_access.posts_view
    }).click(function(event) {
        event.preventDefault();
        ure_posts_view_access_dialog_prepare();
    });

});



function ure_posts_view_access_dialog_prepare() {
    
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        dataType: 'html',
        data: {
            action: 'ure_ajax',
            sub_action: 'get_categories_list',
            current_role: ure_current_role,
            wp_nonce: ure_data.wp_nonce
        },
        success: function(response) {
            var data = jQuery.parseJSON(response);
            if (typeof data.result !== 'undefined') {
                if (data.result === 'success') {                    
                    ure_posts_view_access_dialog(data);
                } else if (data.result === 'failure') {
                    alert(data.message);
                } else {
                    alert('Wrong response: ' + response)
                }
            } else {
                alert('Wrong response: ' + response)
            }
        },
        error: function(XMLHttpRequest, textStatus, exception) {
            alert("Ajax failure\n" + exception);
        },
        async: true
    });    
    
}


function ure_posts_view_access_dialog(data) {
    jQuery(function($) {      
        $('#ure_posts_view_access_dialog').dialog({
            dialogClass: 'wp-dialog',           
            modal: true,
            autoOpen: true, 
            closeOnEscape: true,      
            width: 550,
            height: 500,
            resizable: false,
            title: ure_data_posts_view_access.dialog_title +' for "'+ ure_current_role +'"',
            'buttons'       : {
            'Update': function () {                                  
                    var form = $('#ure_posts_view_access_form');
                    form.submit();
                    $(this).dialog('close');
            },
            'Cancel': function() {
                $(this).dialog('close');
                return false;
            }
          }
      });    
      $('.ui-dialog-buttonpane button:contains("Update")').attr("id", "dialog-update-button");
      $('#dialog-update-button').html(ure_data_posts_view_access.update_button);
      $('.ui-dialog-buttonpane button:contains("Cancel")').attr("id", "dialog-cancel-button");
      $('#dialog-cancel-button').html(ure_data.cancel);
      
      $('#ure_posts_view_access_container').html(data.html);
      $('#ure_cb_select_all').click(ure_posts_view_auto_select);
    });                                
    
}


function ure_posts_view_auto_select(event) {
    jQuery(function($) {
        if (event.shiftKey) {
            $('.ure-cb-column').each(function () {   // reverse selection                
                $(this).prop('checked', !$(this).prop('checked'));
            });
        } else {    // switch On/Off all checkboxes
            $('.ure-cb-column').prop('checked', $('#ure_cb_select_all').prop('checked'));
        }
    });
}

