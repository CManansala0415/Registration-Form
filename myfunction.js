
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
    region: document.getElementById("region") ? document.getElementById("region").value.trim() : '',
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
            // clear form after success and reload top-level dropdowns
            resetForm();
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

// Reset the form and dropdowns for a new registration
function resetForm() {
  const form = document.querySelector('form');
  if (form) form.reset();

  // placeholders for selects
  const placeholders = {
    nationality: 'Select Nationality',
    country: 'Select Country',
    region: 'Select country first',
    province: 'Select country first',
    city: 'Select province first'
  };

  Object.keys(placeholders).forEach(id => {
    const el = document.getElementById(id);
    if (!el) return;
    if (el.tagName === 'SELECT') {
      populateSelect(id, [], placeholders[id]);
    } else {
      // input with list (unlikely here) or other
      el.value = '';
      if (placeholders[id]) el.placeholder = placeholders[id];
    }
  });

  // clear barangay datalist and input
  const dl = document.getElementById('barangay_list');
  const barangayInput = document.getElementById('barangay');
  if (dl) dl.innerHTML = '';
  if (barangayInput) barangayInput.value = '';

  // clear zipcode
  const zipEl = document.getElementById('zipcode');
  if (zipEl) { zipEl.value = ''; zipEl.placeholder = 'Select City First'; }

  // reload nationality and country
  fetchJson('get_nationalities.php').then(data => populateSelect('nationality', data, 'Select Nationality'))
    .catch(() => populateSelect('nationality', [], 'Unable to load'));
  fetchJson('get_countries.php').then(data => populateSelect('country', data, 'Select Country'))
    .catch(() => populateSelect('country', [], 'Unable to load'));
}

// Helper to populate a select element with data [{id,name},...]
function populateSelect(selectId, items, placeholder) {
  const sel = document.getElementById(selectId);
  if (!sel) return;

  // If target is not a <select> (e.g., zipcode input), just set its value/placeholder
  if (sel.tagName !== 'SELECT') {
    // If this input has a list attribute pointing to a datalist, populate that datalist
    const listId = sel.getAttribute('list');
    if (listId) {
      const dl = document.getElementById(listId);
      if (!dl) return;
      dl.innerHTML = '';
      if (!items || items.length === 0) {
        const opt = document.createElement('option');
        opt.value = '';
        dl.appendChild(opt);
        sel.value = '';
        if (placeholder) sel.placeholder = placeholder;
        return;
      }
      items.forEach(item => {
        const opt = document.createElement('option');
        opt.value = item.name;
        dl.appendChild(opt);
      });
      return;
    }

    const first = (items && items.length > 0) ? items[0] : null;
    if (first && first.name) {
      sel.value = first.name;
    } else {
      // clear or set placeholder text if available
      sel.value = '';
      if (placeholder) sel.placeholder = placeholder;
    }
    return;
  }

  sel.innerHTML = '';
  if (!items || items.length === 0) {
    const opt = document.createElement('option');
    opt.value = '';
    // show placeholder as the selected label but don't include it in the selectable list
    opt.disabled = true;
    opt.hidden = true; // hide from dropdown options
    opt.selected = true;
    opt.textContent = placeholder || 'No options';
    sel.appendChild(opt);
    return;
  }

  const defaultOpt = document.createElement('option');
  defaultOpt.value = '';
  // default placeholder should be shown as the label but not selectable
  defaultOpt.disabled = true;
  defaultOpt.hidden = true;
  defaultOpt.selected = true;
  defaultOpt.textContent = placeholder || 'Select...';
  sel.appendChild(defaultOpt);

  items.forEach(item => {
    const opt = document.createElement('option');
    // prefer numeric id for option value; fall back to name if id missing
    opt.value = (item.id !== undefined && item.id !== null && item.id !== '') ? item.id : (item.name !== undefined ? item.name : '');
    opt.textContent = item.name;
    // store optional code (e.g., region_code) as data attribute for later use
    if (item.code !== undefined) {
      opt.dataset.code = item.code;
    }
    // store optional zip (from ph_cities) as data attribute
    if (item.zip !== undefined) {
      opt.dataset.zip = item.zip;
    }
    sel.appendChild(opt);
  });
}

function fetchJson(url, params) {
  const query = params ? '?' + new URLSearchParams(params).toString() : '';
  return fetch(url + query).then(res => res.json());
}

// Load top-level dropdowns on page load
document.addEventListener('DOMContentLoaded', function() {
  // nationality and country
  fetchJson('get_nationalities.php').then(data => populateSelect('nationality', data, 'Select Nationality'))
    .catch(() => populateSelect('nationality', [], 'Unable to load'));

  fetchJson('get_countries.php').then(data => populateSelect('country', data, 'Select Country'))
    .catch(() => populateSelect('country', [], 'Unable to load'));
  // When country changes, load regions
  document.getElementById('country').addEventListener('change', function() {
    const countryVal = this.value;
    let params = {};
    if (/^\d+$/.test(countryVal)) params.country_id = countryVal;
    else params = { country_name: countryVal };

    // reset downstream selects
    populateSelect('region', [], 'Loading...');
    populateSelect('province', [], 'Select region first');
    populateSelect('city', [], 'Select province first');
    populateSelect('barangay', [], 'Select city first');
    populateSelect('zipcode', [], 'Select City first');

    // Load regions from local endpoint
    fetchJson('get_regions.php', params).then(data => populateSelect('region', data, 'Select Region'))
      .catch(() => populateSelect('region', [], 'Unable to load'));
  });

  // When region changes, load provinces
  document.getElementById('region').addEventListener('change', function() {
    const regionVal = this.value;
    let params = {};
    // prefer sending numeric region_id when option value is numeric
    if (/^\d+$/.test(regionVal)) params.region_id = regionVal;

    // if the selected option has a data-code attribute, pass region_code instead
    const selectedOption = this.options[this.selectedIndex];
    if (selectedOption && selectedOption.dataset && selectedOption.dataset.code) {
      params.region_code = selectedOption.dataset.code;
    } else if (!params.region_id) {
      // fallback to region_name if no id or code
      params.region_name = regionVal;
    }

    populateSelect('province', [], 'Loading...');
    fetchJson('get_provinces.php', params).then(data => {
      if (data && data.length && data.length > 0) {
        populateSelect('province', data, 'Select Province');
      } else {
        // try external API if local returns empty
        const selectedOption = this.options[this.selectedIndex];
        const code = selectedOption && selectedOption.dataset ? selectedOption.dataset.code : null;
        fetchExternalProvinces(code || params.region_code || params.region_id || params.region_name);
      }
    }).catch(() => {
      const selectedOption = this.options[this.selectedIndex];
      const code = selectedOption && selectedOption.dataset ? selectedOption.dataset.code : null;
      fetchExternalProvinces(code || params.region_code || params.region_id || params.region_name);
    });
  });

  // Fetch provinces from external Buonzz API by region_code and populate the province select
  function fetchExternalProvinces(regionCode) {
    if (!regionCode) {
      populateSelect('province', [], 'No region code');
      return;
    }
    // If regionCode is numeric id, we still pass it as region_code parameter — Buonzz expects region_code like '04'.
    fetch("https://ph-locations-api.buonzz.com/v1/provinces?region_code=" + encodeURIComponent(regionCode))
      .then(res => res.json())
      .then(payload => {
        if (!payload || !payload.data) throw new Error('Invalid external response');
        const items = payload.data.map(p => ({ id: p.id, name: p.name, code: p.code || null }));
        if (items.length > 0) {
          populateSelect('province', items, 'Select Province');
        } else {
          populateSelect('province', [], 'No provinces found');
        }
      })
      .catch(err => {
        console.error('External provinces fetch failed:', err);
        populateSelect('province', [], 'Unable to load');
      });
  }

  document.getElementById('province').addEventListener('change', function() {
    const provinceVal = this.value;
    let params = {};
    if (/^\d+$/.test(provinceVal)) params.province_id = provinceVal;
    else params = { province_name: provinceVal };

    populateSelect('city', [], 'Loading...');
    fetchJson('get_cities.php', params).then(data => populateSelect('city', data, 'Select City'))
      .catch(() => populateSelect('city', [], 'Unable to load'));
  });

  document.getElementById('city').addEventListener('change', function() {
    const cityVal = this.value;
    let params = {};
    if (/^\d+$/.test(cityVal)) params.city_id = cityVal;
    else params = { city_name: cityVal };

    // If the selected city has a zip code attached, populate zipcode select immediately
    const selectedOption = this.options[this.selectedIndex];
    const zip = selectedOption && selectedOption.dataset ? selectedOption.dataset.zip : null;
    if (zip) {
      // set zipcode directly: support select or input
      const zipEl = document.getElementById('zipcode');
      if (zipEl) {
        if (zipEl.tagName === 'SELECT') {
          // add option if not exists
          let optExists = false;
          for (let i = 0; i < zipEl.options.length; i++) {
            if (zipEl.options[i].value === zip) { optExists = true; break; }
          }
          if (!optExists) {
            const newOpt = document.createElement('option');
            newOpt.value = zip;
            newOpt.textContent = zip;
            zipEl.appendChild(newOpt);
          }
          zipEl.value = zip;
        } else {
          // input or other
          zipEl.value = zip;
        }
      }
      // still load barangays normally
      populateSelect('barangay', [], 'Loading...');
      fetchJson('get_barangays.php', params).then(data => populateSelect('barangay', data, 'Select Barangay'))
        .catch(() => populateSelect('barangay', [], 'Unable to load'));
    } else {
      populateSelect('barangay', [], 'Loading...');
      fetchJson('get_barangays.php', params).then(data => populateSelect('barangay', data, 'Select Barangay'))
        .catch(() => populateSelect('barangay', [], 'Unable to load'));
    }
  });

  document.getElementById('barangay').addEventListener('change', function() {
    const barangayVal = this.value;
    let params = {};
    if (/^\d+$/.test(barangayVal)) params.barangay_id = barangayVal;
    else params = { barangay_name: barangayVal };
    // Zipcode is provided by the selected city (ph_cities.zip_code). No separate zipcode endpoint.
    // If you want to attempt to set zipcode by barangay, you can add logic here later.
    // For now, do nothing on barangay change.
  });

});

