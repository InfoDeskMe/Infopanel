function getNews() {
  var xmlhttp = new XMLHttpRequest()
  try {
    xmlhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        let parseData = JSON.parse(this.responseText)
        let data = JSON.parse(parseData)
        let countNews = Object.keys(data).length

        for (let i = 0; i < countNews; i++) {
          let data = JSON.parse(parseData)
          let newsHeadlines = []
          let newsContents = []
          let newsUrls = []
          let newsImages = []
          let newsId = []

          Object.entries(data).forEach((entry) => {
            const [key, value] = entry
            newsHeadlines.push(`${value.headline}`)
            newsContents.push(`${value.content}`)
            newsUrls.push(`${value.url}`)
            newsImages.push(`${value.image}`)
            newsId.push(`${value.id}`)
          })

          document.getElementById('newsOverview').innerHTML +=
            '<div id="' +
            newsId[i] +
            '"class="card mb-3" >\
            <a href=\'#/\' onclick="removeNews(' +
            newsId[i] +
            ')" >\
            <button type="button" class="btn-close btn-place" aria-label="Close" ></button></a>\
            <a href=\'#/\' onclick="editNews(' +
            newsId[i] +
            ')" >\
            <button type="button" class="btn btn-sm rounded-0" aria-label="Edit" ><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/></svg></button></a>\
                <div class="card-body">\
                    <h4 class="card-text">' +
            newsHeadlines[i] +
            '</h4>\
                    <h6>Content:</h6><p>' +
            newsContents[i] +
            '</p><h6>Link: </h6>\
                    <p>' +
            newsUrls[i] +
            '</p>\
                    <h6>Image URL:</h6>\
                    <p>' +
            newsImages[i] +
            '</p>\
                </div>\
            </div>'
        }
      }
    }
    xmlhttp.open('GET', 'api/read_config')
    xmlhttp.send()
  } catch {
    console.log('found')
  }
}

function removeNews(el) {
  let element = document.getElementById(el).id
  var xhr = new XMLHttpRequest()
  xhr.onreadystatechange = function () {
    if (xhr.readyState == XMLHttpRequest.DONE) {
      document.getElementById(element).remove()
    }
  }
  xhr.open('POST', 'api/remove_news?id=' + element, true)
  xhr.send()
}

function editNews(id) {
  let blockToChange = document.getElementById(id)
  let blockToChangeId = document.getElementById(id).id
  let headerToChange = document.getElementById(id).children[2].childNodes[1]
  let contentToChange = document.getElementById(id).children[2].childNodes[4]
  let linkToChange = document.getElementById(id).children[2].childNodes[7]
  let imageToChange = document.getElementById(id).children[2].childNodes[11]
  let editButton = document.getElementById(id).children[1]

  let newInputHeader = document.createElement('input')
  let newInputContent = document.createElement('textarea')
  let newInputUrl = document.createElement('input')
  let newInputImage = document.createElement('input')

  newInputHeader.classList.add('form-control')
  newInputContent.classList.add('form-control')
  newInputUrl.classList.add('form-control')
  newInputImage.classList.add('form-control')

  newInputHeader.value = headerToChange.textContent
  newInputContent.value = contentToChange.textContent
  newInputUrl.value = linkToChange.textContent
  newInputImage.value = imageToChange.textContent

  headerToChange.parentNode.replaceChild(newInputHeader, headerToChange)
  contentToChange.parentNode.replaceChild(newInputContent, contentToChange)
  linkToChange.parentNode.replaceChild(newInputUrl, linkToChange)
  imageToChange.parentNode.replaceChild(newInputImage, imageToChange)

  let saveButton = document.createElement('BUTTON')
  saveButton.innerHTML = 'SAVE'
  saveButton.classList.add('btn', 'btn-primary', 'btn-lg')
  saveButton.setAttribute('name', blockToChangeId)
  saveButton.onclick = function () {
    sendUpdates(
      blockToChangeId,
      newInputHeader.value,
      newInputContent.value,
      newInputUrl.value,
      newInputImage.value,
    )
  }
  blockToChange.appendChild(saveButton)
  editButton.style.display = 'none'
}

function sendUpdates(form, header, contentNew, link, imageUrl) {
  const xhttp = new XMLHttpRequest()
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      // remove save button
      var saveButtonBlock = document.getElementById(form)
      var saveButton = saveButtonBlock.children[3]
      saveButton.remove()

      //change all inputs back
      let headerToChange = document.getElementById(form).children[2]
        .childNodes[1]
      let contentToChange = document.getElementById(form).children[2]
        .childNodes[4]
      let linkToChange = document.getElementById(form).children[2].childNodes[7]
      let imageToChange = document.getElementById(form).children[2]
        .childNodes[11]

      let newInputHeader = document.createElement('h4')
      let newInputContent = document.createElement('p')
      let newInputUrl = document.createElement('p')
      let newInputImage = document.createElement('p')

      newInputHeader.classList.add('card-text')

      newInputHeader.innerHTML = headerToChange.value
      newInputContent.innerHTML = contentToChange.value
      newInputUrl.innerHTML = linkToChange.value
      newInputImage.innerHTML = imageToChange.value

      headerToChange.parentNode.replaceChild(newInputHeader, headerToChange)
      contentToChange.parentNode.replaceChild(newInputContent, contentToChange)
      linkToChange.parentNode.replaceChild(newInputUrl, linkToChange)
      imageToChange.parentNode.replaceChild(newInputImage, imageToChange)

      //activate edit button
      let editButton = document.getElementById(form).children[1]
      editButton.style.display = ''
    }
  }
  xhttp.open(
    'POST',
    'api/edit_news?uniqueId=' +
      form +
      '&headline=' +
      header +
      '&content=' +
      contentNew +
      '&url=' +
      link +
      '&image=' +
      imageUrl,
  )
  xhttp.send()
}
