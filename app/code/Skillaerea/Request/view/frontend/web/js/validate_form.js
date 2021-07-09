function validateForm() {
    isNameValid = checkIfValidName();
    isEmailValid = checkIfValidEmail();
    isMessageValid = checkIfValidMessage();
    isAgeValid = checkIfValidAge();
    isAllValid = isNameValid
                 && isEmailValid
                 && isMessageValid
                 && isAgeValid;

    return isAllValid;
}

function checkIfValidName() {
    var letters = /^[A-Za-z]+$/;
    var inputedName = document.forms["contact_form_data"]["name"].value;
    if (!inputedName.match(letters)) {
        document.getElementById("name_error").innerHTML = "First name must contain letters only";
        return false;
    } else {
        document.getElementById("name_error").innerHTML = "<br>";
        return true;
    }
}

function checkIfValidEmail() {
    var inputedEmail = document.forms["contact_form_data"]["email"].value;
    var isEmailValid;
    if (inputedEmail.length > 5) {
        var isEmailSymbolExists = inputedEmail.includes("@");
        var isDotSymbolExists = inputedEmail.includes(".");
        if (isEmailSymbolExists && isDotSymbolExists) {
            var indexOfEmailSymbol = inputedEmail.indexOf("@");
            var indexOfDotSymbol = inputedEmail.indexOf(".");
            var lastIndexOfEmailSymbol = inputedEmail.lastIndexOf("@");
            var lastIndexOfDotSymbol = inputedEmail.lastIndexOf(".");
            if ((indexOfEmailSymbol == lastIndexOfEmailSymbol)
                && (indexOfDotSymbol == lastIndexOfDotSymbol)
                && (indexOfEmailSymbol > 0)
                && (indexOfDotSymbol < inputedEmail.length - 1)
                && (indexOfEmailSymbol + 1 < indexOfDotSymbol)
            ) {
                isEmailValid = true;
            } else {
                isEmailValid = false;
            }
        } else {
            isEmailValid = false;
        }
    } else {
        isEmailValid = false;
    }

    if (isEmailValid) {
        document.getElementById("email_error").innerHTML = "<br>";
    } else {
        document.getElementById("email_error").innerHTML = "Email is invalid";
    }

    return isEmailValid;
}

function checkIfValidMessage() {
    var inputedMessage = document.forms["contact_form_data"]["message"].value;
    if (inputedMessage != "" && inputedMessage != null) {
        document.getElementById("message_error").innerHTML = "<br>";
        return true;
    } else {
        document.getElementById("message_error").innerHTML = "Please enter a message";
        return false;
    }
}

function checkIfValidAge() {
    var inputedAge = document.forms["contact_form_data"]["age"].value;
    if (inputedAge != "") {
        document.getElementById("age_error").innerHTML = "<br>";
        return true;
    } else {
        document.getElementById("age_error").innerHTML = "Please select your age";
        return false;
    }
}
