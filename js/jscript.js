
   jQuery(document).ready(function($){
	$('#gdr_add_more').click(function(e){
	    e.preventDefault();
	    $('#gdr_add_more_tr').append('<div>Value:<input type="text" size="10" name="product_form_price_value[]" value="" /> Name:<input type="text" size="10" name="product_form_price_name[]" value="" /><button class="gdr_delete">Delete</button></div>');
	    });
          $('.gdr_delete').click(function(e){
	    e.preventDefault();
             $(this).parent().remove();
	    //$('#add_more_tr').append('<div>Value:<input type="text" size="10" name="product_form_price_value[]" value="" /> Name:<input type="text" size="10" name="product_form_price_name[]" value="" /><button id="delete_">Delete</button></div>');
	    });
          $('#gdr_add_more_tr').on('click', '.gdr_delete', function(event) {
               event.preventDefault();
               $(this).parent().remove();
          });
	});