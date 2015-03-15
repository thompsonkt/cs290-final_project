function validateLogin(form) {
    var username = document.getElementById("login_username").value;
    var password = document.getElementById("login_password").value;
    var httpRequest;
    var response;
    var funcReturn = false;
    var funcOnReadyStateChange = function () {
        if (httpRequest.readyState === 4) {
            if (httpRequest.status === 200) {
                response = JSON.parse(httpRequest.responseText);
                /*read_response(response);*/
                if (response["parameters"]["Account"] === 'Valid')
                {
                    funcReturn = true;
                } else {
                    document.getElementById("login_error").innerHTML = "Incorrect User ID and/or Password Supplied";
                    funcReturn = false;
                }
            } else {
                alert('There was a problem with the request.');
            }
        }
    };
    if (window.XMLHttpRequest) {
        httpRequest = new XMLHttpRequest();
    } else if (window.ActiveXObject) {
        httpRequest = new window.ActiveXObject("Microsoft.XMLHTTP");
    }
    if (!httpRequest) {
        alert('Could not create httpRequest');
    }
    httpRequest.onreadystatechange = funcOnReadyStateChange;
    httpRequest.open('POST', 'http://web.engr.oregonstate.edu/~thomkevi/cs290-final_project/ajaxlogin.php', false);
    httpRequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    httpRequest.send("user=" + username + "&pass=" + password);

    return funcReturn;
}

function validateNewAccount(form) {
    var fname = document.getElementById("create_fname").value;
    var lname = document.getElementById("create_lname").value;
    var email = document.getElementById("create_email").value;
    var user = document.getElementById("create_username").value;
    var pass = document.getElementById("create_password").value;
    var httpRequest;
    var response;
    var funcReturn = false;
    if (fname.length < 1 || fname.length > 50) {
        alert("First Name Must Be Between 1 and 50 Characters");
        return false;
    }
    if (lname.length < 1 || lname.length > 50) {
        alert("Last Name Must Be Between 1 and 50 Characters");
        return false;
    }
    if (email.length < 3 || email.length > 100) {
        alert("Email Must Be Between 3 and 100 Characters");
        return false;
    }
    if (user.length < 1 || user.length > 100) {
        alert("Username Must Be Between 1 and 100 Characters");
        return false;
    }
    var funcOnReadyStateChange = function () {
        if (httpRequest.readyState === 4) {
            if (httpRequest.status === 200) {
                response = JSON.parse(httpRequest.responseText);
                /*read_response(response);*/
                if (response["parameters"]["Account"] === 'Available')
                {
                    funcReturn = true;
                } else {
                    document.getElementById("create_error").innerHTML = "Username Not Available";
                    funcReturn = false;
                }
            } else {
                alert('There was a problem with the request.');
            }
        }
    };
    if (window.XMLHttpRequest) {
        httpRequest = new XMLHttpRequest();
    } else if (window.ActiveXObject) {
        httpRequest = new window.ActiveXObject("Microsoft.XMLHTTP");
    }
    if (!httpRequest) {
        alert('Could not create httpRequest');
    }
    httpRequest.onreadystatechange = funcOnReadyStateChange;
    httpRequest.open('POST', 'http://web.engr.oregonstate.edu/~thomkevi/cs290-final_project/ajaxUsernameAvailable.php', false);
    httpRequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    httpRequest.send("user=" + user + "&pass=" + pass + "&fname=" + fname + "&lname=" + lname + "&email=" + email);

    return funcReturn;
}