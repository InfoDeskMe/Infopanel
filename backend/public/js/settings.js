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
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
 
      if (this.readyState == 4 && this.status == 200) {
        dataConfig = JSON.parse(this.responseText)
        let data = JSON.parse(dataConfig)
        basicReload.value = data.generalReload
        newsNumber.value = data.newsNumber 
        layoutOrientation.value = data.layout_type
        tickerSpeed.value = data.tickerSpeed
        tickerOrientation.value = data.tickerOrientation
        tickerHeight.value = data.tickerHeight
        showTicker.value = data.showTicker
        slideDelay.value = data.slideDelay
      }
    };
    xhttp.open("GET", "api/read_settings");
    xhttp.send();
  }


function saveSettings() {
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
 
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById('txtHint').innerHTML =
        "<div class='alert alert-success' role='alert'>Successfully saved</div>"
      setTimeout(function () {
        document.getElementById('txtHint').style.display = 'none';
      }, 2000)
      }
    };
    xhttp.open("POST", 
    "api/save_settings?basicReload=" +
    basicReload.value +
    '&newsNumber=' + 
    newsNumber.value +
    '&layoutOrientation='+
    layoutOrientation.value + 
    '&tickerSpeed=' + 
    tickerSpeed.value +
    '&tickerOrientation=' +
    tickerOrientation.value +  
    '&tickerHeight=' +
    tickerHeight.value + 
    '&showTicker=' + 
    showTicker.value +
    '&slideDelay=' + 
    slideDelay.value
    );
    xhttp.send();
}