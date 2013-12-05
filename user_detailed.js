function getParameter(urlQuery) {
    urlQuery = urlQuery.split("+").join(" ");

    var params = {};
    var tokens;
    var regex = /[?&]?([^=]+)=([^&]*)/g;

    while (tokens = regex.exec(urlQuery)) {
        params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
    }

    return params;
}

function drawUserStructure(userData) {
    var json = JSON.parse(userData);

    $("#Username").html(json.username);
    $("#Name").html(json.name);
    $("#emailAddress").html(json.email);
    $("#PermissionType").html(json.permissionType);
}

function displayUser(username) {

    $.ajax("./api/getUser.php?Username=" + username, {
        async: false,
        type: "GET",
        data: "",
        success: function(data)
        {
            drawUserStructure(data);
        },
        error: function(a, b, c)
        {
            console.log(a + ", " + b + ", " + c);
        }
    })

    $("#loadingUser").fadeOut(400, function() {
        $("#user").fadeIn('slow', function() {});
    });
}

function setUserID() {
    var username = getParameter(document.location.search).Username;
    $("#UsernameInput").val(username);
}