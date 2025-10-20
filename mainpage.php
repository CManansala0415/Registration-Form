<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="css/bootstrap.min.css" rel="stylesheet" />
  <script src="jquerycdn.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <title>Registration Form</title>
  <style>
    .autocomplete-list {
  position: absolute;
  z-index: 1000;
  width: 100%;
  background: white;
  border: 1px solid #ccc;
  border-radius: 5px;
  max-height: 200px;
  overflow-y: auto;
}
.autocomplete-item {
  padding: 5px 10px;
  cursor: pointer;
}
.autocomplete-item:hover {
  background-color: #f1f1f1;
}

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
      transition: background 0.2s ease;
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
    <!-- Turn off browser autofill for this form to avoid saved-info popup covering suggestions -->
    <form onsubmit="registerPerson(event)" autocomplete="off">

      <!-- Hidden dummy inputs trick to reduce browser password/autofill popup -->
      <input type="text" name="fakeusernameremembered" id="fakeusernameremembered" style="position:absolute;left:-9999px;top:-9999px;opacity:0;" autocomplete="off">
      <input type="password" name="fakepasswordremembered" id="fakepasswordremembered" style="position:absolute;left:-9999px;top:-9999px;opacity:0;" autocomplete="new-password">

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
  <select id="nationality" name="nationality" required autocomplete="off">
    <option value="">Select Nationality</option>
  </select>

      </fieldset>

      <fieldset>
        <legend>Address Information</legend>

        <label for="home">Home</label>
        <input type="text" id="home" name="home" required>

            

            <label for="country">Country:</label>
  <select id="country" name="country" required autocomplete="off">
    <option value="">Select Country</option>
  </select>

            <label for="region">Region</label>
  <select id="region" name="region" required autocomplete="off">
    <option value="">Select Region</option>
  </select>

            <label for="province">Province:</label>
  <select id="province" name="province" required autocomplete="off">
    <option value="">Select Province</option>
  </select>

            <label for="city">City</label>
  <select id="city" name="city" required autocomplete="off">
    <option value="">Select City</option>
  </select>
            <label for="barangay">Barangay</label>
  <select id="barangay" name="barangay" required autocomplete="off">
    <option value="">Select Barangay</option>
  </select>
  
    <label for="zipcode">ZIP Code</label>
    <input type="text" id="zipcode" readonly placeholder="Auto-filled ZIP Code" disabled>

      <button type="submit">Register</button>

    </form>
  </div>
    
  <script src="js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="myfunction.js"></script>
</body>
</html>
