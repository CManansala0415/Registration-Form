
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
        country: document.getElementById("country").value.trim(),
        province: document.getElementById("province").value.trim(),
        city: document.getElementById("city").value.trim(),
        barangay: document.getElementById("barangay").value.trim(),
        zipcode: document.getElementById("zipcode").value.trim(),
    };

    $.ajax({
        url: "crud.php",
        type: "POST",
        data: formData,
        dataType: "json", // ✅ expect JSON from PHP
        success: function (res) {
            if (res.status === "success") {
                console.log("✅ " + res.message);
                alert(res.message);
                location.reload()
            } else {
                console.error("❌ " + res.message);
                alert("Error: " + res.message);
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", error);
            alert("Something went wrong with the request.");
        }
    });

    //   console.clear();
    //   console.log("📋 Registered Person Details:");
    //   console.table(formData);
}

