function saveTicker() {
  let content = document.getElementById('addTickerNews').value

  let data = new FormData()
  data.append('content', content)

  apiFetch('api/add_tickernews', { method: 'POST', body: data }).then((response) => {
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
}

function loadTickerNews() {
  apiFetch('api/read_ticker')
    .then((response) => response.json())
    .then((data) => {
      let newsList = data.news || []
      if (newsList.length === 0) {
        document.getElementById('contentTicker').innerHTML = 'No news found'
      } else {
        document.getElementById('contentTicker').innerHTML = ''
        for (let i = 0; i < newsList.length; i++) {
          let entry = newsList[i]

          document.getElementById('contentTicker').innerHTML +=
            '<div id="' +
            entry.id +
            '" class="card mb-3" >\
            <a href=\'#\' onclick="removeTickerNews(\'' + entry.id + '\')" >\
            <button type="button" class="btn-close btn-place" aria-label="Close" ></button></a>\
            <a href=\'#/\' onclick="editTickerNews(\'' +
            entry.id + '\')" >\
            <button type="button" class="btn btn-sm rounded-0" aria-label="Edit" ><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/></svg></button></a>\
                <div class="card-body">\
                    <p>' +
                    entry.content +
            '</p> \
                </div>\
            </div>'
        }
      }
    })
    .catch(() => {})
}

function editTickerNews(id) {
    let blockToChange = document.getElementById(id)
    let blockToChangeId = document.getElementById(id).id
    let contentToChange = document.getElementById(id).children[2].childNodes[1]
    let editButton = document.getElementById(id).children[1]

    let newInputContent = document.createElement('textarea')
    newInputContent.classList.add('form-control')
    newInputContent.value = contentToChange.textContent
    contentToChange.parentNode.replaceChild(newInputContent, contentToChange)

    let saveButton = document.createElement('BUTTON')
    saveButton.innerHTML = 'SAVE'
    saveButton.classList.add('btn', 'btn-primary', 'btn-lg')
    saveButton.setAttribute('name', blockToChangeId)
    saveButton.onclick = function () {
      sendUpdates(
        blockToChangeId,
        newInputContent.value
      )
    }
    blockToChange.appendChild(saveButton)
    editButton.style.display = 'none'
}

function hideAlert() {
    document.getElementById('txtHint').innerHTML = ''
    document.getElementById('addTickerNews').value = ' '
    loadTickerNews()
  }

function removeTickerNews(el) {
    let element = document.getElementById(el).id;
    let data = new FormData()
    data.append('id', element)
    apiFetch('api/remove_tickernews', { method: 'POST', body: data }).then(() => {
        document.getElementById(element).remove()
        loadTickerNews()
    })
}

function sendUpdates(form, contentNew) {
  let data = new FormData()
  data.append('uniqueId', form)
  data.append('content', contentNew)

  apiFetch('api/edit_tickernews', { method: 'POST', body: data }).then((response) => {
    if (!response.ok) {
      return
    }
    // remove save button
    var saveButtonBlock = document.getElementById(form)
    var saveButton = saveButtonBlock.children[3]
    saveButton.remove()

    //change all inputs back
    let contentToChange = document.getElementById(form).children[2].childNodes[1]
    let newInputContent = document.createElement('p')
    newInputContent.innerHTML = contentToChange.value
    contentToChange.parentNode.replaceChild(newInputContent, contentToChange)

    //activate edit button
    let editButton = document.getElementById(form).children[1]
    editButton.style.display = ''
  })
}
