let tmpNr = ''
let xmlhttp = new XMLHttpRequest()
var newPassword = document.getElementById('newPassword')
var newPasswordRepeat = document.getElementById('newPasswordRepeat')
var buttonSubmit = document.getElementById('btnChange')
var buttonSubmitUser = document.getElementById('btnCreate')
var closeButton = document.querySelector('[data-bs-dismiss="modal"]')
let inputPasswordField = document.querySelector('#newPassword')
let inputPasswordRepeater = document.querySelector('#newPasswordRepeat')

inputPasswordField.addEventListener('input', evt => {
    checkIfEmpty();
})

inputPasswordRepeater.addEventListener('input', evt => {
    checkIfEmpty();
})

let inputUserCreate = document.querySelector('#password')
inputUserCreate.addEventListener('input', evt => {
    checkIfEmptyUser();
})

function checkIfEmpty() {
    if (newPassword.value.length > 0 && newPasswordRepeat.value.length > 0) {
        buttonSubmit.disabled = false;
     } else {
        buttonSubmit.disabled = true;
     }
}

function checkIfEmptyUser() {
    if (useremail.value.length > 0 && password.value.length > 0) {
        buttonSubmitUser.disabled = false;
     } else {
        buttonSubmitUser.disabled = true;
     }
}

function showUsers() {
  xmlhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      let parseData = JSON.parse(this.responseText)
      let data = JSON.parse(parseData)
      let dataLength = data.length
      for (let i = 0; i < dataLength; i++) {
        let tmpId = makeid(5)
        let role = ''
        if (JSON.parse(data[i].role).role == 'ROLE_EDITOR') {
          role = 'Editor'
        } else {
          role = 'Administrator'
        }

        if (dataLength == 1) {
            document.getElementById('users').innerHTML +=
            '<div class="customUserBorder" id=' +
            tmpId +
            ' ><div class=""><h6>' +
            data[i].email +
            '</h6><span>' +
            role + 
            '</span></div><div class="customUser"><button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#changePasswordModal" onClick="createHiddenElement(' +
            tmpId +
            ')">Change password</button></div></div>' 
        } else {
            document.getElementById('users').innerHTML +=
            '<div class="customUserBorder" id=' +
            tmpId +
            ' ><div class=""><h6>' +
             data[i].email +
              '</h6><span>' +
             role + 
             '</span></div><div class="customUser"><button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#changePasswordModal" onClick="createHiddenElement(' +
             tmpId +
            ')">Change password</button><button class="btn btn-outline-danger" onClick="deleteUser(' + tmpId + ')">Delete user</button></div></div>'
        }
      }
    }
  }
  xmlhttp.open('GET', 'api/show_users',true)
  xmlhttp.send()
}

async function changePassword() {
  event.preventDefault();
  let userBlock = document.getElementById(tmpNr)
  let user = userBlock.getElementsByTagName('h6')[0].textContent
  let infoMess = document.getElementById('hidden')
  let data = JSON.stringify({
    users: user,
    newPass: newPasswordRepeat.value,
  });

 if (newPassword.value == newPasswordRepeat.value) {
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
      if (xhr.readyState !== 4) return;
      if (xhr.status >= 200 && xhr.status < 300) {
          closeButton.dispatchEvent(new MouseEvent('click', {shiftKey: true}))  
      } 
  };
  xhr.open('POST', 'api/change_password');
  xhr.send(data);
 } else { 
    newPasswordRepeat.classList.add("error");
    newPassword.classList.add("error");
    infoMess.style.display = "block"; 
    setTimeout(function(){ 
        newPasswordRepeat.classList.remove("error");
        newPassword.classList.remove("error");
        infoMess.style.display = "none"; 
    }, 3000);
 }
}

function makeid(length) {
  var result = ''
  var characters = '123456789'
  var charactersLength = characters.length
  for (var i = 0; i < length; i++) {
    result += characters.charAt(Math.floor(Math.random() * charactersLength))
  }
  return result
}

async function createHiddenElement(id) {
  tmpNr = id
}

function deleteUser(id) {
  event.preventDefault();
  let userBlock = document.getElementById(id)
  let divBlock = document.getElementById('users')
  let user = userBlock.getElementsByTagName('h6')[0].textContent
  let data = JSON.stringify({
    userToDelete: user,
  })

  var xhrCall = new XMLHttpRequest()
  xhrCall.onreadystatechange = function () {
      if (xhrCall.readyState !== 4) return;
      if (xhrCall.status >= 200 && xhrCall.status < 300) {
        divBlock.innerHTML = ''
        showUsers()
      } 
  }
  xhrCall.open('POST', 'api/delete_user')
  xhrCall.send(data) 
}

async function createUser() {
   let username = document.getElementById('useremail').value
   let password = document.getElementById('password').value
   let role = document.getElementById('role').value 
   let data = JSON.stringify({
    user: username,
    password: password,
    role: role,
  })

  var xhrCallCreateUser = new XMLHttpRequest()
  xhrCallCreateUser.onreadystatechange = function () {
      if (xhrCallCreateUser.readyState !== 4) return;
      if (xhrCallCreateUser.status >= 200 && xhrCallCreateUser.status < 300) {
        location.reload();
        return false;
      } 
  }
  xhrCallCreateUser.open('POST', 'api/create_user')
  xhrCallCreateUser.send(data) 
}