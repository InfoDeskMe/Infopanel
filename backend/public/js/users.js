let tmpNr = ''
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
  apiFetch('api/show_users')
    .then((response) => response.json())
    .then((data) => {
      let dataLength = data.length
      for (let i = 0; i < dataLength; i++) {
        let tmpId = makeId(5)
        let role = ''
        if (data[i].role.includes('ROLE_EDITOR')) {
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
    })
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
  apiFetch('api/change_password', { method: 'POST', body: data }).then((response) => {
      if (response.ok) {
          closeButton.dispatchEvent(new MouseEvent('click', {shiftKey: true}))
      }
  });
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

  apiFetch('api/delete_user', { method: 'POST', body: data }).then((response) => {
      if (response.ok) {
        divBlock.innerHTML = ''
        showUsers()
      }
  })
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

  apiFetch('api/create_user', { method: 'POST', body: data }).then((response) => {
      if (response.ok) {
        location.reload();
      }
  })
}
