
function test() {
    $.ajax({
        url: "crud.php",
        type: 'GET',
        data: {

        },
        success: function (res) {
            console.log(res);
        }
    });
}



function registerPerson(event) {
  event.preventDefault(); // prevent form reload

  const formData = {
    firstName: document.getElementById("fname").value.trim(),
    middleName: document.getElementById("mname").value.trim(),
    lastName: document.getElementById("lname").value.trim(),
    suffix: document.getElementById("suffix").value.trim(),
    birthday: document.getElementById("bday").value,
    gender: document.getElementById("gender").value,
    nationality: document.getElementById("nationality").value.trim(),
    home: document.getElementById("home").value.trim(), 
    country: $('#country option:selected').text().trim(),
    region: $('#region option:selected').text().trim(),
    province: $('#province option:selected').text().trim(),
    city: $('#city option:selected').text().trim(),
    barangay: $('#barangay option:selected').text().trim(),
    zipcode: document.getElementById("zipcode").value.trim(),
  };

  $.ajax({
    url: "crud.php",
    type: "POST",
    data: formData,
    dataType: "json",
    success: function (res) {
      if (res.status === "success") {
        Swal.fire({
          icon: "success",
          title: "Success!",
          text: res.message,
          showConfirmButton: false,
          timer: 2000
        });

        // optional: clear form after success
        document.querySelector("form").reset();
      } else {
        Swal.fire({
          icon: "error",
          title: "Error!",
          text: res.message,
        });
      }
    },
    error: function (xhr, status, error) {
      Swal.fire({
        icon: "error",
        title: "Request Failed",
        text: "Something went wrong while saving data.",
      });
      console.error("AJAX Error:", error);
    },
  });
}

