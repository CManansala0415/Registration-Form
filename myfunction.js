
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
<<<<<<< HEAD

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

=======
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

>>>>>>> f759c81 (My Prefinal 10/20/25)
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
<<<<<<< HEAD
        text: "Something went wrong while saving data.",
      });
      console.error("AJAX Error:", error);
=======
        text: "Something went wrong while saving data. Check console for details.",
      });
      console.error("AJAX Error:", error);
      if (xhr && xhr.responseText) {
        console.error('Raw server response:', xhr.responseText);
      }
>>>>>>> f759c81 (My Prefinal 10/20/25)
    },
  });
}





document.addEventListener("DOMContentLoaded", function () {
  console.log("✅ myfunction.js loaded");

  // ELEMENTS
  const countrySelect = document.getElementById("country");
  const nationalitySelect = document.getElementById("nationality");
  const regionSelect = document.getElementById("region");
  const provinceSelect = document.getElementById("province");
  const citySelect = document.getElementById("city");
  const barangaySelect = document.getElementById("barangay");
  const zipCodeInput = document.getElementById("zipcode");

  const api = "https://psgc.gitlab.io/api";
  let zipData = {};

  // ============================
  // 📨 LOAD ZIP CODE JSON
  // ============================
  fetch("json/zipcodes.json")
    .then(res => res.json())
    .then(data => {
      zipData = data;
      console.log("📦 ZIP codes loaded:", Object.keys(zipData).length);
    })
    .catch(err => console.error("❌ ZIP load error:", err));

  // ============================
  // 🌍 LOAD COUNTRIES
  // ============================
  fetch("json/countries.json")
    .then(res => res.json())
    .then(data => {
      const list = Array.isArray(data)
        ? data.map(c => c.name || c)
        : Object.values(data).map(c => c.name || c);
      populateSelect(countrySelect, list, "Select Country");
    })
    .catch(err => console.error("❌ Country load error:", err));

  // ============================
  // 🌐 LOAD NATIONALITIES
  // ============================
  fetch("json/nationalities.json")
    .then(res => res.json())
    .then(data => {
      const list = Array.isArray(data)
        ? data.map(n => n.name || n)
        : Object.values(data).map(n => n.name || n);
      populateSelect(nationalitySelect, list, "Select Nationality");
    })
    .catch(err => console.error("❌ Nationality load error:", err));

  // ============================
  // 🇵🇭 REGION → PROVINCE → CITY → BARANGAY
  // ============================
  loadRegions();

  function loadRegions() {
    fetch(`${api}/regions/`)
      .then(res => res.json())
      .then(regions => {
        regionSelect.innerHTML = '<option value="">Select Region</option>';
        regions.forEach(r => {
          const opt = document.createElement("option");
          opt.value = r.code;
          opt.textContent = r.regionName || r.name;
          regionSelect.appendChild(opt);
        });
      })
      .catch(err => console.error("❌ Region load error:", err));
  }

  // Load provinces when region selected
  regionSelect.addEventListener("change", function () {
    const regionCode = this.value;
    if (!regionCode) return;
    provinceSelect.innerHTML = '<option>Loading...</option>';
    citySelect.innerHTML = '<option value="">Select City / Municipality</option>';
    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';

    fetch(`${api}/regions/${regionCode}/provinces/`)
      .then(res => res.json())
      .then(provs => {
        provinceSelect.innerHTML = '<option value="">Select Province</option>';
        provs.forEach(p => {
          const opt = document.createElement("option");
          opt.value = p.code;
          opt.textContent = p.name;
          provinceSelect.appendChild(opt);
        });
      })
      .catch(err => console.error("❌ Province load error:", err));
  });

  // Load cities when province selected
  provinceSelect.addEventListener("change", function () {
    const provinceCode = this.value;
    if (!provinceCode) return;
    citySelect.innerHTML = '<option>Loading...</option>';
    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';

    fetch(`${api}/provinces/${provinceCode}/cities-municipalities/`)
      .then(res => res.json())
      .then(cities => {
        citySelect.innerHTML = '<option value="">Select City / Municipality</option>';
        cities.forEach(c => {
          const opt = document.createElement("option");
          opt.value = c.code;
          opt.textContent = c.name;
          citySelect.appendChild(opt);
        });
      })
      .catch(err => console.error("❌ City load error:", err));
  });

  // --- When user selects a city ---
  citySelect.addEventListener("change", function () {
    const cityCode = this.value;
    if (!cityCode) return;
    barangaySelect.innerHTML = '<option>Loading...</option>';

    // Load Barangays
    fetch(`${api}/cities-municipalities/${cityCode}/barangays/`)
      .then(res => res.json())
      .then(brgs => {
        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
        brgs.forEach(b => {
          const opt = document.createElement("option");
          opt.value = b.name;
          opt.textContent = b.name;
          barangaySelect.appendChild(opt);
        });
      })
      .catch(err => console.error("❌ Barangay load error:", err));

    // --- Auto-generate ZIP Code (province + city-based matching) ---
    const cityName = this.options[this.selectedIndex].text.trim().toLowerCase();
    const provinceName =
      provinceSelect.options[provinceSelect.selectedIndex]?.text.trim().toLowerCase() || "";

    let foundZip = null;

    // Normalize for consistent matching
    const normalize = str =>
      str
        .toLowerCase()
        .replace(/^city of\s+/i, "")
        .replace(/\s+city$/i, "")
        .replace(/\s+municipality of\s+/i, "")
        .replace(/\s*\(.*?\)\s*/g, "")
        .replace(/\./g, "")
        .replace(/\s+/g, " ")
        .trim();

    const targetCity = normalize(cityName);
    const targetProv = normalize(provinceName);

    // 1️⃣ Try province + city match
    for (const [zip, places] of Object.entries(zipData)) {
      const values = Array.isArray(places) ? places : [places];
      for (const place of values) {
        const normalized = normalize(place);
        if (normalized.includes(targetCity) && normalized.includes(targetProv)) {
          foundZip = zip;
          break;
        }
      }
      if (foundZip) break;
    }

    // 2️⃣ If not found, try filtering by province first
    if (!foundZip) {
      const provinceFiltered = Object.entries(zipData).filter(([zip, places]) => {
        const arr = Array.isArray(places) ? places : [places];
        return arr.some(p => normalize(p).includes(targetProv));
      });

      for (const [zip, places] of provinceFiltered) {
        const arr = Array.isArray(places) ? places : [places];
        if (arr.some(p => normalize(p).includes(targetCity))) {
          foundZip = zip;
          break;
        }
      }
    }

    // 3️⃣ Last fallback: city-only match
    if (!foundZip) {
      for (const [zip, places] of Object.entries(zipData)) {
        const values = Array.isArray(places) ? places : [places];
        if (values.some(p => normalize(p).includes(targetCity))) {
          foundZip = zip;
          break;
        }
      }
    }

    // Apply result
    zipCodeInput.removeAttribute("disabled");
    if (foundZip) {
      zipCodeInput.value = foundZip;
      console.log(`📮 ZIP for ${cityName}, ${provinceName}: ${foundZip}`);
    } else {
      zipCodeInput.value = "";
      console.warn(`⚠️ ZIP not found for ${cityName}, ${provinceName}`);
    }
    zipCodeInput.setAttribute("disabled", "true");
  });

  // Helper: Populate dropdown
  function populateSelect(select, list, placeholder) {
    select.innerHTML = `<option value="">${placeholder}</option>`;
    list.forEach(item => {
      const opt = document.createElement("option");
      opt.value = item;
      opt.textContent = item;
      select.appendChild(opt);
    });
  }
});
