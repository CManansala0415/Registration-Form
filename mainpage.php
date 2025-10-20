<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.8/dist/sweetalert2.min.css" rel="stylesheet">
  <title>Registration Form</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f8f9fa;
      padding: 20px;
    }
    .container {
      max-width: 900px;
      margin: 0 auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 20px;
    }
    form {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }
    fieldset {
      border: 1px solid #ccc;
      border-radius: 8px;
      padding: 15px 20px;
    }
    legend {
      font-weight: bold;
      color: #555;
    }
    label {
      display: block;
      margin-top: 10px;
      font-weight: bold;
      font-size: 0.9rem;
    }
    input, select {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
      box-sizing: border-box;
      font-size: 0.95rem;
    }
    button {
      grid-column: span 2;
      padding: 12px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 1rem;
      cursor: pointer;
      margin-top: 10px;
    }
    button:hover {
      background-color: #0056b3;
    }
    @media (max-width: 768px) {
      form {
        grid-template-columns: 1fr;
      }
      button {
        grid-column: span 1;
      }
    }
  </style>
</head>
<body>

  <div class="container">
    <h2>Registration Form</h2>
    <form id="registrationForm" onsubmit="return registerPerson(event)">

      <fieldset>
        <legend>Personal Information</legend>

        <label for="fname">First Name</label>
        <input type="text" id="fname" name="fname" required>

        <label for="mname">Middle Name</label>
        <input type="text" id="mname" name="mname">

        <label for="lname">Last Name</label>
        <input type="text" id="lname" name="lname" required>

        <label for="suffix">Suffix Name</label>
        <input type="text" id="suffix" name="suffix" placeholder="e.g., Jr., Sr., III">

        <label for="bday">Birthday</label>
        <input type="date" id="bday" name="bday" required>

        <label for="gender">Gender</label>
        <select id="gender" name="gender" required>
          <option value="" disabled selected>Select Gender</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
          <option value="Other">Other</option>
        </select>

        <label for="nationality">Nationality</label>
        <select id="nationality" name="nationality" required>
          <option value="" disabled selected>Select Nationality</option>
          <option value="Filipino">Filipino</option>
          <option value="American">American</option>
          <option value="Japanese">Japanese</option>
          <option value="Other">Other</option>
        </select>
      </fieldset>

      <fieldset>
        <legend>Address Information</legend>

        <label for="home">Home</label>
        <input type="text" id="home" name="home" required>

        <label for="country">Country</label>
        <select id="country" name="country" required>
          <option value="" disabled selected>Select Country</option>
          <option value="Philippines">Philippines</option>
          <option value="USA">United States</option>
          <option value="Japan">Japan</option>
        </select>

        <label for="region">Region</label>
        <select id="region" name="region" required>
          <option value="" disabled selected>Select Region</option>
        </select>

        <label for="province">Province</label>
        <select id="province" name="province" required>
          <option value="" disabled selected>Select Province</option>
        </select>

        <label for="city">City</label>
        <select id="city" name="city" required>
          <option value="" disabled selected>Select City</option>
        </select>

        <label for="barangay">Barangay</label>
        <select id="barangay" name="barangay" required>
          <option value="" disabled selected>Select Barangay</option>
        </select>

        <label for="zipcode">Zipcode</label>
        <input type="text" id="zipcode" name="zipcode" required>
      </fieldset>

      <button type="submit">Register</button>
    </form>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.8/dist/sweetalert2.all.min.js"></script>

  <script>
    const PSGC_API = "https://psgc.gitlab.io/api";

    // Load regions when country changes
    document.getElementById('country').addEventListener('change', function() {
      var country = this.value;
      
      // Reset dropdowns
      document.getElementById("region").innerHTML = '<option value="" disabled selected>Select Region</option>';
      document.getElementById("province").innerHTML = '<option value="" disabled selected>Select Province</option>';
      document.getElementById("city").innerHTML = '<option value="" disabled selected>Select City</option>';
      document.getElementById("barangay").innerHTML = '<option value="" disabled selected>Select Barangay</option>';
      
      if (country === "Philippines") {
        document.getElementById("region").innerHTML = '<option value="" disabled selected>Loading...</option>';
        
        $.ajax({
          url: "locations.php",
          type: "GET",
          data: { action: "getRegions" },
          dataType: "json",
          success: function(response) {
            if (response.status === "success") {
              document.getElementById("region").innerHTML = '<option value="" disabled selected>Select Region</option>';
              
              response.data.forEach(function(region) {
                var option = document.createElement("option");
                option.value = region;
                option.text = region;
                document.getElementById("region").appendChild(option);
              });
            }
          },
          error: function() {
            alert("Error loading regions");
          }
        });
      }
    });

    // Load provinces when region changes
    document.getElementById('region').addEventListener('change', function() {
      var region = this.value;
      
      document.getElementById("province").innerHTML = '<option value="" disabled selected>Loading...</option>';
      document.getElementById("city").innerHTML = '<option value="" disabled selected>Select City</option>';
      document.getElementById("barangay").innerHTML = '<option value="" disabled selected>Select Barangay</option>';
      
      $.ajax({
        url: "locations.php",
        type: "GET",
        data: { action: "getProvinces", region: region },
        dataType: "json",
        success: function(response) {
          if (response.status === "success") {
            document.getElementById("province").innerHTML = '<option value="" disabled selected>Select Province</option>';
            
            response.data.forEach(function(province) {
              var option = document.createElement("option");
              option.value = province;
              option.text = province;
              document.getElementById("province").appendChild(option);
            });
          }
        }
      });
    });

    // Load cities when province changes
    document.getElementById('province').addEventListener('change', function() {
      var province = this.value;
      
      document.getElementById("city").innerHTML = '<option value="" disabled selected>Loading...</option>';
      document.getElementById("barangay").innerHTML = '<option value="" disabled selected>Select Barangay</option>';
      
      $.ajax({
        url: "locations.php",
        type: "GET",
        data: { action: "getCities", province: province },
        dataType: "json",
        success: function(response) {
          if (response.status === "success") {
            document.getElementById("city").innerHTML = '<option value="" disabled selected>Select City</option>';
            
            response.data.forEach(function(city) {
              var option = document.createElement("option");
              option.value = city;
              option.text = city;
              document.getElementById("city").appendChild(option);
            });
          }
        }
      });
    });

    // Barangay (PSGC API)
    document.getElementById('city').addEventListener('change', function() {
      const cityName = this.value;
      const barangaySelect = document.getElementById('barangay');

      barangaySelect.innerHTML = '<option disabled selected>Loading...</option>';

      // First: get city code
      fetch(`${PSGC_API}/cities-municipalities`)
        .then(res => res.json())
        .then(cities => {
          const match = cities.find(c => c.name === cityName);
          if (!match) {
            barangaySelect.innerHTML = '<option disabled selected>No barangays found</option>';
            return;
          }

          const cityCode = match.code;

          // Second: get barangays using the city code
          fetch(`${PSGC_API}/cities-municipalities/${cityCode}/barangays`)
            .then(res => res.json())
            .then(barangays => {
              barangaySelect.innerHTML = '<option disabled selected>Select Barangay</option>';
              barangays.forEach(b => {
                const opt = document.createElement("option");
                opt.value = b.name;
                opt.textContent = b.name;
                barangaySelect.appendChild(opt);
              });
            })
            .catch(err => {
              barangaySelect.innerHTML = '<option disabled selected>Error loading barangays</option>';
            });
        });
    });

    // Register function
    function registerPerson(event) {
      event.preventDefault();

      var formData = {
        firstName: document.getElementById("fname").value,
        middleName: document.getElementById("mname").value,
        lastName: document.getElementById("lname").value,
        suffix: document.getElementById("suffix").value,
        birthday: document.getElementById("bday").value,
        gender: document.getElementById("gender").value,
        nationality: document.getElementById("nationality").value,
        home: document.getElementById("home").value,
        country: document.getElementById("country").value,
        region: document.getElementById("region").value,
        province: document.getElementById("province").value,
        city: document.getElementById("city").value,
        barangay: document.getElementById("barangay").value,
        zipcode: document.getElementById("zipcode").value
      };

      $.ajax({
        url: "crud.php",
        type: "POST",
        data: formData,
        dataType: "json",
        success: function(res) {
          if (res.status === "success") {
            Swal.fire({
              icon: "success",
              title: "Success!",
              text: res.message,
              showConfirmButton: false,
              timer: 2000
            });
            document.getElementById("registrationForm").reset();
          } else {
            Swal.fire({
              icon: "error",
              title: "Error!",
              text: res.message
            });
          }
        },
        error: function() {
          Swal.fire({
            icon: "error",
            title: "Request Failed",
            text: "Something went wrong."
          });
        }
      });
      
      return false;
    }
  </script>
</body>
</html>
