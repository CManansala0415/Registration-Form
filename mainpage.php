<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="css/bootstrap.min.css" rel="stylesheet" />
  <script src="jquerycdn.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <title>Registration Form (Charles)</title>
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
    <form onsubmit="registerPerson(event)">

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
          <option value="" disabled hidden selected>Loading...</option>
        </select>
      </fieldset>

      <fieldset>
        <legend>Address Information</legend>

        <label for="home">Home</label>
        <input type="text" id="home" name="home" required>

        <label for="country">Country</label>
        <select id="country" name="country" required>
          <option value="" disabled hidden selected>Loading...</option>
        </select>

        <label for="region">Region</label>
        <select id="region" name="region">
          <option value="" disabled hidden selected>Select country first</option>
        </select>

        <label for="province">Province</label>
        <select id="province" name="province" required>
          <option value="" disabled hidden selected>Select country first</option>
        </select>

        <label for="city">City</label>
        <select id="city" name="city" required>
          <option value="" disabled hidden selected>Select province first</option>
        </select>

        <label for="barangay">Barangay</label>
        <input list="barangay_list" id="barangay" name="barangay" required placeholder="Select or type barangay">
        <datalist id="barangay_list"></datalist>

        <label for="zipcode">Zipcode</label>
        <input type="text" id="zipcode" name="zipcode" required placeholder="Select City First">
      </fieldset>

      <button type="submit">Register</button>

    </form>
  </div>

  <script src="js/bootstrap.min.js"></script>
  <script src="myfunction.js"></script>
</body>
</html>
