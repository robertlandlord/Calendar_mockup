function logout(){
fetch("logout.php", {
            method: 'POST',
            headers: { 'content-type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(function(data){
            if(data.success){
                showLogin();
                setup();
            }
            else{
                console.log(`Failed to logout ${data.message}`);
            }
        });
    
        
}


function showLogin(){
    
    $(document).ready(function(){
        $("#login-form").find("input[type=text], input[type=password]").val("");
        $("#creation-form").find("input[type=text], input[type=password]").val("");
        $("#username").show();
        $("#password").show();
        $("#login_btn").show();
        $("#newuser").show();
        $("#newpass").show();
        $("#create_btn").show();
        $("#logout_btn").hide();
        $("#logged-user").hide();
        });
}
document.getElementById("logout_btn").addEventListener("click", logout);