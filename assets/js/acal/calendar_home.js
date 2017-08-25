
$( document ).ready(function() {

// Some variables are set in /view/calendar/calendar_home_view.php

// set the property range of maxDate in DatePicker
var calendar_start_date = new Date();
/*
  admin can set number of months a user can mark availability (datepicker_month_range).
  the following figures out how many days there are in the last month a user
  can mark availability, and subtracts that number from the current date number.
  This number is then added to the maxDate property in the calendar to make sure
  the full number of days are included in that last month, instead of stopping
  at the current date number on the last month.
*/
var day_padding_til_the_end_of_the_last_month = (new Date(
  calendar_start_date.getFullYear(),
  calendar_start_date.getMonth() + datepicker_month_range + 1, 0
  )
  .getDate()) - (new Date().getDate());

console.log("base_project_date: " + base_project_date);

// InLine datepick by jQuery-UI
// var non_project_dates defined in calendar_home_view
$( "#datepicker" ).datepicker({
    dateFormat: "yy-mm-dd",
    firstDay: 1,
    defaultDate: base_project_date,
    minDate: new Date(), // everything before this date is not selectable
    maxDate: "+" + datepicker_month_range + "m +" + day_padding_til_the_end_of_the_last_month + "d", // returns something like "+3m +15d"
    onChangeMonthYear: function(year, month, inst) {
        // an event triggered when the user navigates by month
        if((month - 1) != datepicker_date.getMonth()) {
            // note: months here use the format 1-12 (js uses 0-11) so subtract 1
            //console.log("Months are not the same.");
            //console.log("Selected month: " + datepicker_date.getMonth());
            // so when the calendar is not showing the selected month, hide the form and show the select date message
            $('#calendar-message-container').show();
            $('#form-container').hide();
        } else if ((month - 1) == datepicker_date.getMonth()) {
            //console.log("Months are the same.")
            // so when the calendar is showing the selected month, show the form and hide the select date message
            $('#calendar-message-container').hide();
            $('#form-container').show();
        }
    },
    beforeShowDay: function(date) {
        // make days selectable or not
        //if ( $.datepicker.formatDate('yy-mm-dd', calendar_start_date) == $.datepicker.formatDate('yy-mm-dd', date) ) {
          //  console.log("Matched today -> change CSS property.");
            //return [false,'',''];
        //}
        if($.inArray($.datepicker.formatDate('yy-mm-dd', date), non_project_dates) > -1) {
            return [false,'',''];
        }
        else {
            return [true,'',''];
        }
    },
    onSelect: function(date) {
        //datepicker_date = $(this).val(date); // needs to be global
        $("input[name='date']").val(date);
        //console.log($("input[name='date']").val());
        window.location.href = base_project_url + date;
    }
});

var datepicker_date = $( "#datepicker" ).datepicker( "getDate" );

/*
    DatePicker
        beforeshowday example: http://jsfiddle.net/yTMwu/18/

 */

/*
$(".ui-state-highlight, .ui-widget-content .ui-state-highlight, .ui-widget-header .ui-state-highlight").css({
	"border":"1px solid #6B8E23",
	"background":"#9ACD32",
	"color":"white"
});
*/

});
