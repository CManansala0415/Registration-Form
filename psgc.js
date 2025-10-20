document.addEventListener("DOMContentLoaded", function () {
  const countrySelect = document.getElementById("country");
  const regionSelect = document.getElementById("region");
  const provinceSelect = document.getElementById("province");
  const citySelect = document.getElementById("city");
  const barangaySelect = document.getElementById("barangay");
  const zipCodeInput = document.getElementById("zipcode");
  const nationalityInput = document.getElementById("nationality");

  const api = "https://psgc.gitlab.io/api";
  let zipData = {};
  let nationalityList = [];


  // 📨 LOAD ZIP CODE JSON

  fetch("zipcodes.json")
    .then(res => res.json())
    .then(data => {
      zipData = data;
      console.log("ZIP code data loaded successfully.");
    })
    .catch(err => console.error("Error loading ZIP codes:", err));

  // LOAD COUNTRIES (Using first.org API)

  fetch("https://api.first.org/data/v1/countries")
    .then(res => res.json())
    .then(data => {
      const countries = Object.values(data.data);
      countries.sort((a, b) => a.country.localeCompare(b.country));

      countrySelect.innerHTML = '<option value="">Select Country</option>';
      countries.forEach((c) => {
        const opt = document.createElement("option");
        opt.value = c.country;
        opt.textContent = c.country;
        countrySelect.appendChild(opt);
      });

      const phOption = Array.from(countrySelect.options).find(
        (opt) => opt.textContent.toLowerCase() === "philippines"
      );
      if (phOption) phOption.selected = true;
    })
    .catch((err) => {
      console.error("Error loading countries:", err);
      countrySelect.innerHTML = '<option>Error loading countries</option>';
    });


  // 🌐 LOAD NATIONALITIES JSON 

  fetch('nationalities.json')
    .then(res => res.json())
    .then(data => {
      const nationalities = Array.isArray(data) ? data : (data.data || []);
      console.log('Nationalities loaded successfully. Count:', nationalities.length);

      const el = document.getElementById('nationality');
      if (!el) return;


      if (el.tagName && el.tagName.toLowerCase() === 'select') {
        el.innerHTML = '';
        const defaultOpt = document.createElement('option');
        defaultOpt.value = '';
        defaultOpt.textContent = 'Select Nationality';
        el.appendChild(defaultOpt);
        nationalities.forEach(n => {
          const opt = document.createElement('option');
          opt.value = String(n).toLowerCase(); 
          opt.textContent = n;
          el.appendChild(opt);
        });
        el.value = '';
        return;
      }

      
      if (el.tagName && el.tagName.toLowerCase() === 'input') {
        if (el._downshiftAttached) return;
        el._downshiftAttached = true;

       
        nationalityList = nationalities.slice();

        const suggestionBox = document.createElement('div');
        suggestionBox.style.position = 'absolute';
        suggestionBox.style.background = '#fff';
        suggestionBox.style.border = '1px solid #ccc';
        suggestionBox.style.borderRadius = '5px';
        suggestionBox.style.maxHeight = '200px';
        suggestionBox.style.overflowY = 'auto';
        suggestionBox.style.zIndex = '9999';
        suggestionBox.style.display = 'none';
        suggestionBox.id = 'nationality-suggestions';
        document.body.appendChild(suggestionBox);

        let items = [];
        let highlighted = -1;

        function positionBox() {
          const rect = el.getBoundingClientRect();
          suggestionBox.style.width = rect.width + 'px';
          suggestionBox.style.left = rect.left + window.pageXOffset + 'px';
          suggestionBox.style.top = rect.bottom + window.pageYOffset + 'px';
        }

        function render() {
          suggestionBox.innerHTML = '';
          if (!items || items.length === 0) { suggestionBox.style.display = 'none'; return; }
          items.slice(0, 10).forEach((text, idx) => {
            const div = document.createElement('div');
            div.textContent = text;
            div.dataset.index = idx;
            div.style.padding = '8px';
            div.style.cursor = 'pointer';
            if (idx === highlighted) div.style.background = '#eef';
            div.addEventListener('mousemove', () => { highlighted = idx; updateHighlight(); });
            div.addEventListener('click', () => selectIndex(idx));
            suggestionBox.appendChild(div);
          });
          positionBox();
          suggestionBox.style.display = 'block';
        }

        function updateHighlight() {
          Array.from(suggestionBox.children).forEach((ch, i) => {
            ch.style.background = (i === highlighted) ? '#eef' : '#fff';
          });
        }

        function selectIndex(i) {
          const val = items[i];
          if (val !== undefined) {
            el.value = String(val).toLowerCase();
          }
          hide();
          el.focus();
        }

        function hide() { suggestionBox.style.display = 'none'; highlighted = -1; items = []; }

        function onInput() {
          const q = el.value.trim().toLowerCase();
          if (!q) { hide(); return; }
          const filtered = nationalityList.filter(n => String(n).toLowerCase().includes(q));
          items = filtered;
          highlighted = 0;
          render();
        }

        el.addEventListener('input', onInput);
        el.addEventListener('keydown', function (e) {
          if (suggestionBox.style.display === 'none') return;
          if (e.key === 'ArrowDown') { e.preventDefault(); highlighted = Math.min(highlighted + 1, items.length - 1); updateHighlight(); ensureVisible(highlighted); }
          else if (e.key === 'ArrowUp') { e.preventDefault(); highlighted = Math.max(highlighted - 1, 0); updateHighlight(); ensureVisible(highlighted); }
          else if (e.key === 'Enter') { e.preventDefault(); if (highlighted >= 0) selectIndex(highlighted); }
          else if (e.key === 'Escape') { hide(); }
        });

        function ensureVisible(idx) { const node = suggestionBox.children[idx]; if (node) node.scrollIntoView({ block: 'nearest' }); }

        document.addEventListener('click', function (ev) { if (ev.target === el) return; if (!suggestionBox.contains(ev.target)) hide(); });
        window.addEventListener('resize', positionBox);
        window.addEventListener('scroll', positionBox, true);
      }
    })
    .catch(err => console.error('Error loading nationalities:', err));


  // 🇵🇭 LOAD REGIONS
  fetch(`${api}/regions/`)
    .then(res => res.json())
    .then(regions => {
      regionSelect.innerHTML = '<option value="">Select Region</option>';
      regions.forEach(r => {
        const opt = document.createElement("option");
        opt.value = r.code;
        opt.textContent = r.regionName || r.name || r.code;
        regionSelect.appendChild(opt);
      });
    })
    .catch(err => {
      regionSelect.innerHTML = '<option>Error loading regions</option>';
      console.error("Error loading regions:", err);
    });


  //  LOAD PROVINCES BY REGION
  regionSelect.addEventListener("change", function () {
    const regionCode = this.value;
    provinceSelect.innerHTML = '<option>Loading...</option>';
    citySelect.innerHTML = '<option>Select City / Municipality</option>';
    barangaySelect.innerHTML = '<option>Select Barangay</option>';
    zipCodeInput.value = "";

    if (!regionCode) {
      provinceSelect.innerHTML = '<option value="">Select Province</option>';
      return;
    }

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
      .catch(err => {
        provinceSelect.innerHTML = '<option>Error loading provinces</option>';
        console.error("Error loading provinces:", err);
      });
  });


  // LOAD CITIES BY PROVINCE

  provinceSelect.addEventListener("change", function () {
    const provinceCode = this.value;
    citySelect.innerHTML = '<option>Loading...</option>';
    barangaySelect.innerHTML = '<option>Select Barangay</option>';
    zipCodeInput.value = "";

    if (!provinceCode) {
      citySelect.innerHTML = '<option value="">Select City / Municipality</option>';
      return;
    }

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
      .catch(err => {
        citySelect.innerHTML = '<option>Error loading cities</option>';
        console.error("Error loading cities:", err);
      });
  });


 
// 🏘️ LOAD BARANGAYS BY CITY + FIX ZIP MATCH (Province-aware)
citySelect.addEventListener("change", function () {
  const cityCode = this.value;
  const cityName = this.options[this.selectedIndex].text;
  const provinceName = provinceSelect.options[provinceSelect.selectedIndex]?.text || "";
  barangaySelect.innerHTML = '<option>Loading...</option>';
  if (zipCodeInput) zipCodeInput.value = "";

  if (!cityCode) {
    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
    return;
  }

  // ✅ Improved normalization function
  function normalize(s) {
    return s
      .toLowerCase()
      .replace(/^city of\s+/i, "")
      .replace(/^municipality of\s+/i, "")
      .replace(/\bsto\b\.?/gi, "santo") // Sto. → Santo
      .replace(/\bsta\b\.?/gi, "santa") // Sta. → Santa
      .replace(/\bsan\b\.?/gi, "san")
      .replace(/\bcity\b/gi, "")
      .replace(/[\.\'\"]+/g, "")
      .replace(/\s+/g, " ")
      .trim();
  }

  const cityNorm = normalize(cityName);
  const provinceNorm = normalize(provinceName);
  let foundZip = null;
  let provinceMatchZip = null;

  console.debug("ZIP match try:", { cityName, cityNorm, provinceName, provinceNorm });

  for (const [zip, area] of Object.entries(zipData)) {
    const checkArea = (a) => {
      const s = normalize(a);
      // ✅ Exact city + province match
      if (s.includes(cityNorm) && s.includes(provinceNorm)) return "provinceMatch";
      // ✅ Exact city match
      if (s === cityNorm) return "exact";
      // ✅ Partial city match
      if (s.includes(cityNorm)) return "partial";
      return null;
    };

    if (Array.isArray(area)) {
      for (const a of area) {
        const matchType = checkArea(a);
        if (matchType === "provinceMatch") provinceMatchZip = zip;
        else if (matchType === "exact" && !foundZip) foundZip = zip;
        else if (matchType === "partial" && !foundZip) foundZip = zip;
      }
    } else if (typeof area === "string") {
      const matchType = checkArea(area);
      if (matchType === "provinceMatch") provinceMatchZip = zip;
      else if (matchType === "exact" && !foundZip) foundZip = zip;
      else if (matchType === "partial" && !foundZip) foundZip = zip;
    }
  }

  // ✅ Prefer province match over general match
  const finalZip = provinceMatchZip || foundZip;

  console.debug("ZIP matched:", finalZip, "Province priority:", !!provinceMatchZip);

  if (zipCodeInput) zipCodeInput.value = finalZip || "N/A";

  // 🔹 Load Barangays
  fetch(`${api}/cities-municipalities/${cityCode}/barangays/`)
    .then((res) => res.json())
    .then((brgs) => {
      barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
      brgs.forEach((b) => {
        const opt = document.createElement("option");
        opt.value = b.name;
        opt.textContent = b.name;
        barangaySelect.appendChild(opt);
      });
    })
    .catch((err) => {
      barangaySelect.innerHTML = '<option>Error loading barangays</option>';
      console.error("Error loading barangays:", err);
    });
});
});
