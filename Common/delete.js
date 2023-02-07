console.log("Script is running...");

var delButton = document.querySelector("input[type=submit]");
  
/* set onclick on submit input */   
delButton.setAttribute("onclick", "return deleteConfirmation()");
//submit.addEventListener("click", test);


// event listeners for all checkboxes for courses
const checkboxes = document.querySelectorAll("input[name='courseId[]']");
for (var checkbox of checkboxes) {
    checkbox.addEventListener("change", (e) => {
      if (e.target.checked) {
        console.log("Checkbox is checked..");
        $('.text-warning').text("");
      } else {
        console.log("Checkbox is not checked..");
      }
    });
}


function deleteConfirmation() {
    
    let selectedCourses = document.querySelectorAll('input[name="courseId[]"]:checked');

    let values = [];
    selectedCourses.forEach((checkbox) => {
        values.push(checkbox.value);
    });
    
    if (values !== false) {
        // something selected
        if (confirm('The following registration(s) will be deleted: \n' + values + '\nAre you sure?')) {         
            return true;         
        } else {
            return false;
        }
    }
}