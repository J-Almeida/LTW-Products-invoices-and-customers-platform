function checkUsernameInput() {
    var usernameInput = document.getElementById("username");
    var value = usernameInput.value;

    var submitButton = document.getElementById("commit");
    $(submitButton).prop('disabled', true);
    $.ajax("./api/checkUserExists.php?Data=" + value, {
        async: false,
        type: "GET",
        data: "",
        success: function(data)
        {
            var warning = document.getElementById("userExists"); 
            if(data) {
                $(warning).show();
                usernameInput.setCustomValidity('Username is taken');
            }
            else {
                $(warning).hide();
                usernameInput.setCustomValidity('');
            }
        },
        error: function(a, b, c)
        {
            console.log(a + ", " + b + ", " + c);
        }
    })
    $(submitButton).prop('disabled', false);
}

function checkEmailInput() {
    var emailInput = document.getElementById("email");
    var value = emailInput.value;

    var submitButton = document.getElementById("commit");
    $(submitButton).prop('disabled', true);
    $.ajax("./api/checkUserExists.php?Data=" + value, {
        async: false,
        type: "GET",
        data: "",
        success: function(data)
        {
            var warning = document.getElementById("emailExists"); 
            if(data) {
                $(warning).show();
                emailInput.setCustomValidity('Username is taken');
            }
            else {
                $(warning).hide();
                emailInput.setCustomValidity('');
            }
        },
        error: function(a, b, c)
        {
            console.log(a + ", " + b + ", " + c);
        }
    })
     $(submitButton).prop('disabled', false);
}

$(document).ready(function() {
    var usernameInput = document.getElementById("username");
    $(usernameInput).change(checkUsernameInput);

    var emailInput = document.getElementById("email");
    $(emailInput).change(checkEmailInput);
});