// Handle action submit confirmation
document.addEventListener("click", function(e){

  var confirmMessage; 

  switch(e.target.dataset.confirm) {
    case 'deleteUser':
      confirmMessage = confirm('Delete this user?');
      break;
    case 'deleteProject':
      confirmMessage = confirm('Delete this project?');
      break;
    case 'deleteRequirement':
      confirmMessage = confirm('Delete this requirement?');
      break;
    case 'deleteCancelledDate':
      confirmMessage = confirm('Delete this cancelled date?');
      break;
  }
  
  if(confirmMessage == false){ e.preventDefault(); }

});