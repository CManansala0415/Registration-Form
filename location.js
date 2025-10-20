$(document).ready(function() {
  $.ajax({
    url: "https://countriesnow.space/api/v0.1/countries/positions",
    method: "GET",
    success: function(response) {
      const countries = response.data;
      countries.sort((a, b) => a.name.localeCompare(b.name));
      countries.forEach(function(country) {
        $('#country').append(
          $('<option>', { value: country.name, text: country.name })
        );
      });
    },
    error: function() {
      alert('Failed to load countries.');
    }
  });


    $.ajax({
    url: "https://restcountries.com/v3.1/all?fields=name,demonyms",
    method: "GET",
    success: function(response) {
      const nationality = response;
      nationality.sort((a, b) => a.demonyms.eng.m.localeCompare(b.demonyms.eng.m));
      nationality.forEach(function(item) {
          if (item.name.common != 'Bouvet Island') {
            $('#nationality').append(
              $('<option>', { value: item.demonyms.eng.m + ' ( ' + item.name.common + ' )', text: item.demonyms.eng.m + ' ( ' + item.name.common + ' )' })
            );
          }
      });
    },
    error: function() {
      alert('Failed to load nationality.');
    }
  });
});


  $('#country').change(function() {
    const country = $(this).val();
    if (country === 'Philippines') {
      $('#region').prop('disabled', false);
      loadRegions();
    } else {
      resetDropdowns(['region', 'province', 'city', 'barangay']);
    }
  });

  function loadRegions() {
    $.getJSON('https://psgc.cloud/api/v2/regions/', function(response) {
        const data = response.data;
      data.sort((a, b) => a.name.localeCompare(b.name));
      fillDropdown('#region', data, 'Select a region');
    });
  }

  $('#region').change(function() {
    const regionCode = $('#region option:selected').text();
    resetDropdowns(['province', 'city', 'barangay']);
    $('#zipcode').val('');

    if (regionCode) {
        $('#province').prop('disabled', false);

      $.getJSON('https://psgc.cloud/api/v2/provinces/', function(response) {
        const data = response.data;
        const filtered = data.filter(item => item.region === regionCode);
        filtered.sort((a, b) => a.name.localeCompare(b.name));
        fillDropdown('#province', filtered, 'Select a province');
      });
    }

  });

  $('#province').change(function() {
    const regionCode = $('#region option:selected').text();
    const provinceCode = $('#province option:selected').text();
    resetDropdowns(['city', 'barangay']);
    $('#zipcode').val('');

    if (provinceCode) {
      $('#city').prop('disabled', false);
      $.getJSON(`https://psgc.cloud/api/v2/cities-municipalities/`, function(response) {
        const data = response.data;
        const filtered1 = data.filter(city => city.region === regionCode);
        const filtered = filtered1.filter(city => city.province === provinceCode);

        filtered.sort((a, b) => a.name.localeCompare(b.name));
        fillDropdown('#city', filtered, 'Select a city');
      });
    }
  });

  $('#city').change(function() {
    const cityCode = $('#city option:selected').text();
    const Code = $('#city option:selected').val();
    resetDropdowns(['barangay']);
    $('#zipcode').val('');

    if (cityCode) {
      $('#barangay').prop('disabled', false);

      $.getJSON(`https://psgc.cloud/api/v2/cities-municipalities/`+Code+`/barangays`, function(response) {
        const data = response.data;
        data.sort((a, b) => a.name.localeCompare(b.name));
        fillDropdown('#barangay', data, 'Select a barangay');
        $('#zipcode').val(data['0'].zip_code)
      });

    }
  });


  function resetDropdowns(ids) {
    ids.forEach(id => {
      $(`#${id}`).prop('disabled', true).html(`<option value="">Select a ${id}</option>`);
    });
  }

  function fillDropdown(selector, data, placeholder) {
    const dropdown = $(selector);
    dropdown.html(`<option value="">${placeholder}</option>`);
    $.each(data, function(i, item) {
      dropdown.append(`<option value="${item.code}">${item.name}</option>`);
    });
  }
