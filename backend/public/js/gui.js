function saveNews() {
  if (validateData()) {
  let xmlhttp = new XMLHttpRequest()
  let headline = document.getElementById('headline').value
  let content = document.getElementById('content').value
  let url = document.getElementById('url').value
  let image = document.getElementById('image').value

  //create a unique ID for the news
  function makeid(length) {
    var result = ''
    var characters =
      '123456789'
    var charactersLength = characters.length
    for (var i = 0; i < length; i++) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength))
    }
    return result
  }

  xmlhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById('txtHint').innerHTML =
        "<div class='alert alert-success' role='alert'>Successfully saved</div>"
      setTimeout(function () {
        hideAlert()
      }, 2000)
    }
  }
  xmlhttp.open(
    'POST',
    'api/add_news?headline=' +
      headline +
      '&content=' +
      content +
      '&url=' +
      url +
      '&image=' +
      image +
      '&uniqueId=' +
      makeid(5),
  )
  xmlhttp.send()
  } else {
    alert('All input fields are required!')
  }
}

function hideAlert() {
  document.getElementById('txtHint').remove()
  document.getElementById('headline').value = ''
  document.getElementById('content').value = ''
  document.getElementById('url').value = ''
  document.getElementById('image').value = ''
  location.reload()
}

function validateData() {
  let headline = document.getElementById('headline').value
  let content = document.getElementById('content').value
  let url = document.getElementById('url').value
  let image = document.getElementById('image').value

  if (headline === "" || content === "" || url === "" || image === "") {
    return false;
  }
  return true;
}