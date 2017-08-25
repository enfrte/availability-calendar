/* Select day
---------------------------------------------------------------------------- */
// execute a call to a controller based on the users select day action
$( "select" ).change(function() {
  // console.log( controller_uri );
  controller_uri = controller_uri + select_day.value; // append the day argument
  // execute submit action
  window.location.href = controller_uri; // problems with window.location.href? See http://stackoverflow.com/a/11690095/3442612
});

/* Submit form event 
---------------------------------------------------------------------------- */
$('button[type="submit"]').click(function() {
	// check if any positions exist (ie, user has not removed all the positions)
	$("form").submit(function (event) {
		// check acal-position-container for children
		if($('.acal-position-container').has('div').length < 1){
			alert("You cannot submit an empty form.\n\nIf you want to delete the current day, use the Delete button without removing any current positions.");
			//event.preventDefault();
			return false;
		}
	});
	
  // create the form_action post variable to decide what post action will be executed on the server
	// I don't know why I didn't just use the post's submit->value here, but oh well ¯\_(ツ)_/¯
  var form_action = $("<input>").attr("type", "hidden").attr("name", "submit_type");
  // unset any old action properties (in case of a cancelled user action)
  $('form').append($(form_action).val(''));
  // confirm various actions
  // confirm() returns true of false. False stops the form submission
  switch ( this.value ) {
    case 'delete':
      $('form').append($(form_action).val('delete'));
      return confirm("Delete all positions for this day?");
    case 'publish':
      $('form').append($(form_action).val('publish'));
      return confirm("Publish all positions for this day?");
    case 'draft':
      $('form').append($(form_action).val('draft'));
      return confirm("Save all positions for this day as a draft?");
    default:
      console.log('Notice: No submit action detected!');
  }

});
/* Add remove position cards
---------------------------------------------------------------------------- */
$('.acal-position-container').on('click','.remove-position-card' ,function() {
  // functionality removed now we are archiving all edits and outputting positions by latest edit datetime.
  var remove_card = $(this).closest('.acal-card-container').find("input[name='id[]']").val(); // get the id of the removed card
  $('<input>').attr({ type: 'hidden', name: 'removed[]', value: remove_card }).appendTo('form');
  //$(this).closest("div.options").find("input[name='quantity']").val();
  //$(this).closest('.acal-card-container').remove(); // without animation
  $(this).closest('.acal-card-container').slideUp("slow").delay(10).queue(function(){$(this).remove();});
});

$('#add_position_card').click(function() {
  $('.acal-position-container').append('<div class="acal-card-container">'+
'    <input type="hidden" name="id[]" value="">'+
'    <div class="acal-card cal-card-hover">'+
'      <div style="text-align:right;margin-bottom:10px;">'+
'       <a href="javascript:;" class="remove-position-card"><strong>Remove position </strong><span class="glyphicon glyphicon-remove"></span></a></div>'+
'      <div class="row">'+
'        <div class="form-group col-sm-6">'+
'          <label for="title[]">Title (required)</label>'+
'          <input type="text" name="title[]" value="" class="form-control" required>'+
'        </div>'+
'        <div class="form-group col-sm-6">'+
'          <label for="max_vol[]">Maximum participants needed</label>'+
'          <input type="number" name="max_vol[]" min="1" max="999" value="" class="form-control">'+
'        </div>'+
'      </div>'+
'      <div class="form-group">'+
'        <label for="description[]">Description</label>'+
'        <textarea name="description[]" cols="40" rows="10" style="height:100px;" class="form-control"></textarea>'+
'      </div>'+
'    </div>'+
'  </div>');
});

// Prevent form submission without pressing the submit button.
$('form input').on('keypress keydown keyup', function(e) {
    if (e.keyCode == 13) {
        e.preventDefault();
    }
});
