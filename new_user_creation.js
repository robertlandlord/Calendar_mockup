// ajax.js

function createAjax(event) {
    const newuser = document.getElementById("newuser").value; // Get the username from the form
    const newpass = document.getElementById("newpass").value; // Get the password from the form
    console.log(newuser);
    console.log(newpass);
    // Make a URL-encoded string for passing POST data:
    const data = { 'newuser': newuser, 'newpass': newpass };

    fetch("new_user_creation.php", {
            method: 'POST',
            body: JSON.stringify(data),
            headers: { 'content-type': 'application/json',
            'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => console.log(data.success ? alert("User Created! Please Log in") : alert(`You were not created ${data.message}`)))
        .then(document.getElementById('newpass').value = "")
        .then(document.getElementById('newuser').value = "");
}

document.getElementById("create_btn").addEventListener("click", createAjax, false); // Bind the AJAX call to button click