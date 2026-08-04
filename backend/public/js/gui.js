function saveNews() {
  if (validateData()) {
    let headline = document.getElementById('headline').value
    let content = document.getElementById('content').value
    let url = document.getElementById('url').value
    let image = document.getElementById('image').value

    let data = new FormData()
    data.append('headline', headline)
    data.append('content', content)
    data.append('url', url)
    data.append('image', image)

    apiFetch('api/add_news', { method: 'POST', body: data })
      .then((response) => {
        if (response.ok) {
          document.getElementById('txtHint').innerHTML =
            "<div class='alert alert-success' role='alert'>Successfully saved</div>"
          setTimeout(function () {
            hideAlert()
          }, 2000)
        } else {
          document.getElementById('txtHint').innerHTML =
            "<div class='alert alert-danger' role='alert'>Failed to save. Please try again.</div>"
        }
      })
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
