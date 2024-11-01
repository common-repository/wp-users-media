/** CUSTOM SCRIPTS **/
jQuery(document).ready(function($){ 
    // Save settings
    $('#wpusme_elements_form').on('submit', function(e){
        e.preventDefault();
        $.ajaxSetup({ cache: false });
        const settings = {};

        // Serialize all input data
        $("#wpusme_elements_form input:checkbox").each(function(){ if(this.name){ settings[this.name] = this.checked; }});

        // Create the data that will be sent to database
        const data = { 
            'action': 'wpusme_save_settings',
            'data': settings
        };
        
        // Make an call to AJAX to insert data in database
        $.post(ajaxurl, data, function(response){
            $('#wpusme_popup').css({'display' : 'flex'});
        });
    });

    // Make Tabs clickable
    $('.wpusme-tabs-wrapper a').click(function(e){ 
        e.preventDefault();
        $('.wpusme-tabs-wrapper a').removeClass('active');
        $(this).addClass('active');
        $('.div-tab').hide();
        $('#' + $(this).attr("data-id")).show(); 
    }); 

    // Close the popup window
    $('#wpusme_button').on('click', function(e){
        $('#wpusme_popup').css({'display' : 'none'});
    });

    // Display the info box
    $('#info').on('click', function(e){
        $('#wpusme-info-notice').slideToggle();
    });
});