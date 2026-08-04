let basicReload = document.getElementById('basicReload');
let newsNumber = document.getElementById('newsNumber');
let layoutOrientation = document.getElementById('layoutOrientation');
let tickerSpeed = document.getElementById('tickerSpeed');
let tickerOrientation = document.getElementById('tickerOrientation');
let tickerHeight = document.getElementById('tickerHeight');
let showTicker = document.getElementById('showTicker');
let slideDelay = document.getElementById('slideDelay');
let dataConfig = {}


function showSettings() {
    apiFetch('api/read_settings')
      .then((response) => response.json())
      .then((data) => {
        dataConfig = data
        basicReload.value = data.generalReload
        newsNumber.value = data.newsNumber
        layoutOrientation.value = data.layout_type
        tickerSpeed.value = data.tickerSpeed
        tickerOrientation.value = data.tickerOrientation
        tickerHeight.value = data.tickerHeight
        showTicker.value = data.showTicker
        slideDelay.value = data.slideDelay
      })
  }


function saveSettings() {
    let data = new FormData()
    data.append('basicReload', basicReload.value)
    data.append('newsNumber', newsNumber.value)
    data.append('layoutOrientation', layoutOrientation.value)
    data.append('tickerSpeed', tickerSpeed.value)
    data.append('tickerOrientation', tickerOrientation.value)
    data.append('tickerHeight', tickerHeight.value)
    data.append('showTicker', showTicker.value)
    data.append('slideDelay', slideDelay.value)

    apiFetch('api/save_settings', { method: 'POST', body: data }).then((response) => {
      if (response.ok) {
        document.getElementById('txtHint').innerHTML =
        "<div class='alert alert-success' role='alert'>Successfully saved</div>"
      setTimeout(function () {
        document.getElementById('txtHint').style.display = 'none';
      }, 2000)
      } else {
        document.getElementById('txtHint').innerHTML =
          "<div class='alert alert-danger' role='alert'>Failed to save. Please try again.</div>"
      }
    })
}
