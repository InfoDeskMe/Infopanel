var fileName = ''

const selectElement = document.querySelector('#myfile')
selectElement.addEventListener(
  'change',
  (event) => {
    changeImage()
  },
  { once: false },
)

const selectFiles = document.querySelector('#selectImagesModal')
selectFiles.addEventListener(
  'show.bs.modal',
  (event) => {
    showAvailableImages()
    let shootButton = document.getElementById('closeModal')
    shootButton.disabled = true
  },
  { once: false },
)

function changeImage() {
  let file = document.querySelector('#myfile').files[0]
  let allowed_mime_types = ['image/jpeg', 'image/png']
  let allowed_size_mb = 2

  if (allowed_mime_types.indexOf(file.type) == -1) {
    alert('Error : Incorrect file type')
  } else if (file.size > allowed_size_mb * 1024 * 1024) {
    alert('Error : Exceeded size')
  } else {
    document.getElementById('form').submit()
  }
}

async function uploadFile() {
  let data = new FormData()
  data.append('file', document.querySelector('#myfile').files[0])
  let uploaderButton = document.getElementById('image')
  let xhttp = new XMLHttpRequest()
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      var newfile = data.get('file')
      fileName = newfile
      let uploadArea = document.getElementById('uploadArea')
      uploaderButton.value = 'uploads/images/' + newfile.name
      let selectButton = document.getElementById('myfile')
      selectButton.disabled = true
      uploadArea.innerHTML =
        '<button id="b" type="button" class="btn-close" aria-label="Close" onclick="deleteFile()"></button>'
    } else {
      uploaderButton.value = 'file could not be loaded and will be not available...'
    }
  } 
  xhttp.open('POST', '/doUpload')
  xhttp.send(data)
}

async function deleteFile() {
  var fileToDelete = fileName.name
  console.log(fileToDelete)
  let xhttp = new XMLHttpRequest()
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      let uploaderButton = document.getElementById('image')
      let ButtonToDelete = document.getElementById('b')
      let selectFileButton = document.getElementById('myfile')
      selectFileButton.value = []
      uploaderButton.value = ''
      selectFileButton.disabled = false
      ButtonToDelete.remove()
    }
  }
  xhttp.open('POST', '/doDelete')
  xhttp.send(fileToDelete)
}

async function deleteImage(id) {
  var fileToDelete = id
  let areaToDelete = document.getElementById(id)
  let fileBeDeleted = areaToDelete.children[0].children[0].value
  let xhttp = new XMLHttpRequest()
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      let uploaderButton = document.getElementById('image')
      let ButtonToDelete = document.getElementById('b')
      let selectFileButton = document.getElementById('myfile')
      let closeButton = document.getElementById('selectImagesModal')
      let selectedOption = document.getElementsByClassName('form-check-input')
    
     
      selectFileButton.value = []
      uploaderButton.value = ''
      selectFileButton.disabled = false
     // ButtonToDelete.remove()
      showAvailableImages()
      if (selectedOption.length == 0) {
        closeButton.dispatchEvent(new MouseEvent('click', {shiftKey: true}))
      } else {
        areaToDelete.remove()
      }
        
    }
  }
  xhttp.open('POST', '/doDelete')
  xhttp.send(fileBeDeleted)
}

async function showAvailableImages() {
  let filesArea = document.getElementById('selectFileArea')
  let xhttp = new XMLHttpRequest()
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      let parseData = JSON.parse(this.responseText)
      let data = JSON.parse(parseData)
      let countFiles = Object.keys(data).length
      if (countFiles !== 0) {
        filesArea.innerHTML = ''
        for (let i = 0; i < countFiles; i++) {
          var m = randomId(5)
          filesArea.innerHTML +=
            '<div class="imageBlock" id="' + m +'"> \
              <div class="form-check"> \
                 <input class="form-check-input" type="radio" name="image" id="flexRadioDefault2" value="' + data[i] + '" onClick="document.getElementById(\'closeModal\').disabled = false"> \
               </div> \
               <img class="prerenderImage" src=\"uploads/images/' + data[i] +  '\"> \
               <a href=\'#\' onclick="deleteImage(' + m + ')" > \
                 <button type=\"button\" class=\"btn-close btn-place\" aria-label=\"Close\" ></button></a>\
            </div>'
        }
      } else {
        let closeModal = document.getElementById('closeModal')
        let ButtonToDelete = document.getElementById('b')
        closeModal.disabled = true
        filesArea.innerHTML = ''
        filesArea.innerHTML += "<p>No files available</p>"
        if (ButtonToDelete) {
          ButtonToDelete.remove()
        }
       
      }
    }
  }
  xhttp.open('GET', '/showfiles')
  xhttp.send()
}

function putSelectedImage() {
  let imageField = document.getElementById('image');
  let closeButton = document.getElementById('selectImagesModal')
  let selectedOption = document.getElementsByClassName('form-check-input');

  for(i = 0; i < selectedOption.length; i++) {
    if(selectedOption[i].type="radio" && selectedOption[i].checked == true) {
        
        imageField.value = "uploads/images/" + selectedOption[i].value;
        closeButton.dispatchEvent(new MouseEvent('click', {shiftKey: true}))   
    }
  }
}

function randomId(length) {
  var result = ''
  var characters =
    '123456789'
  var charactersLength = characters.length
  for (var i = 0; i < length; i++) {
    result += characters.charAt(Math.floor(Math.random() * charactersLength))
  }
  return result
}