
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
  // gather select display text (not only values/codes)
  const regionEl = document.getElementById('region');
  const provinceEl = document.getElementById('province');
  const cityEl = document.getElementById('city');

  const regionText = regionEl && regionEl.options && regionEl.selectedIndex >= 0
    ? (regionEl.options[regionEl.selectedIndex].text || regionEl.value)
    : (regionEl ? regionEl.value : '');

  const provinceText = provinceEl && provinceEl.options && provinceEl.selectedIndex >= 0
    ? (provinceEl.options[provinceEl.selectedIndex].text || provinceEl.value)
    : (provinceEl ? provinceEl.value : '');

  const cityText = cityEl && cityEl.options && cityEl.selectedIndex >= 0
    ? (cityEl.options[cityEl.selectedIndex].text || cityEl.value)
    : (cityEl ? cityEl.value : '');

  const formData = {
    firstName: document.getElementById("fname").value.trim(),
    middleName: document.getElementById("mname").value.trim(),
    lastName: document.getElementById("lname").value.trim(),
    suffix: document.getElementById("suffix").value.trim(),
    birthday: document.getElementById("bday").value,
    gender: document.getElementById("gender").value,
    nationality: document.getElementById("nationality").value.trim(),
    home: document.getElementById("home").value.trim(),
    // send the display text instead of the code
    region: String(regionText).trim(),
    country: document.getElementById("country").value.trim(),
    province: String(provinceText).trim(),
    city: String(cityText).trim(),
    barangay: document.getElementById("barangay").value.trim(),
    zipcode: document.getElementById("zipcode").value.trim(),
  };

  $.ajax({
    url: "crud.php",
    type: "POST",
    data: formData,
    dataType: "json",
    success: function (res, textStatus, xhr) {
      // sometimes server returns HTML or an error page instead of JSON
      // try to detect and log raw response for debugging
      try {
        if (typeof res === 'string') res = JSON.parse(res);
      } catch (e) {
        console.error('AJAX: response is not valid JSON', xhr && xhr.responseText);
        Swal.fire({ icon: 'error', title: 'Server Response Error', text: 'Server returned invalid JSON. Check console for raw response.' });
        return;
      }

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
        text: "Something went wrong while saving data. Check console for details.",
      });
      console.error("AJAX Error:", error);
      if (xhr && xhr.responseText) {
        console.error('Raw server response:', xhr.responseText);
      }
    },
  });
}


