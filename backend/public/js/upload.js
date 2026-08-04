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
  let allowed_mime_types = ['image/jpeg', 'image/png', 'video/mp4']
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

  apiFetch('/doUpload', { method: 'POST', body: data }).then(async (response) => {
    if (response.ok) {
      let uploadedName = (await response.json()).filename
      fileName = uploadedName
      let uploadArea = document.getElementById('uploadArea')
      uploaderButton.value = 'uploads/images/' + uploadedName
      let selectButton = document.getElementById('myfile')
      selectButton.disabled = true
      uploadArea.innerHTML =
        '<button id="b" type="button" class="btn-close" aria-label="Close" onclick="deleteFile()"></button>'
    } else {
      uploaderButton.value = 'file could not be loaded and will be not available...'
    }
  })
}

async function deleteFile() {
  var fileToDelete = fileName
  apiFetch('/doDelete', { method: 'POST', body: JSON.stringify({ filename: fileToDelete }) }).then((response) => {
    if (response.ok) {
      let uploaderButton = document.getElementById('image')
      let ButtonToDelete = document.getElementById('b')
      let selectFileButton = document.getElementById('myfile')
      selectFileButton.value = []
      uploaderButton.value = ''
      selectFileButton.disabled = false
      ButtonToDelete.remove()
    }
  })
}

async function deleteImage(id) {
  var fileToDelete = id
  let areaToDelete = document.getElementById(id)
  let fileBeDeleted = areaToDelete.children[0].children[0].value
  apiFetch('/doDelete', { method: 'POST', body: JSON.stringify({ filename: fileBeDeleted }) }).then((response) => {
    if (response.ok) {
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
  })
}

async function showAvailableImages() {
  let filesArea = document.getElementById('selectFileArea');
  apiFetch('/showfiles')
    .then((response) => response.json())
    .then((parseData) => {
      let data = JSON.parse(parseData);
      let countFiles = Object.keys(data).length;

      if (countFiles !== 0) {
        filesArea.innerHTML = '';

        for (let i = 0; i < countFiles; i++) {
          var m = makeId(5);
          function checkMimeType(num) {
            if (data[num].slice(-3) !== 'mp4') {
              return '<img alt="' + data[i] + '" class="prerenderImage" src=\"uploads/images/' + data[i] +  '\">'
            } else {
              return '<p class="prerenderImage">' + data[i] + '</p>'
            }
          };

          filesArea.innerHTML +=
            '<div class="imageBlock" id="' + m +'"> \
              <div class="form-check"> \
                 <input class="form-check-input" type="radio" name="image" id="flexRadioDefault2" value="' + data[i] + '" onClick="document.getElementById(\'closeModal\').disabled = false"> \
               </div>'
               + checkMimeType(i); + '\
                \
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
    })
}

function putSelectedImage() {
  let imageField = document.getElementById('image');
  let closeButton = document.getElementById('selectImagesModal')
  let selectedOption = document.getElementsByClassName('form-check-input');

  for(i = 0; i < selectedOption.length; i++) {
    if(selectedOption[i].type="radio" && selectedOption[i].checked == true) {

        imageField.value = "http://localhost/uploads/images/" + selectedOption[i].value;
        closeButton.dispatchEvent(new MouseEvent('click', {shiftKey: true}))
    }
  }
}
