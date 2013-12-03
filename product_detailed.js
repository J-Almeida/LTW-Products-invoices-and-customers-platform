function hideRestrictedElements() {
    $.ajax("./api/getPermissions.php", {
        async: false,
        data: "",
        success: function(data)
        {
            if(data == "none") {
                $("#edit").hide();
                return;
            }
            
            var permissions = JSON.parse(data);
            
            if(permissions.write != 1) {
                $("#edit").hide();
            }
        },
        error: function(a, b, c)
        {
            console.log(a + ", " + b + ", " + c);
        }
    })
}

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

function drawProductStructure(productData) {
    var json = JSON.parse(productData);

    $("#productCode").html(json.productCode);
    $("#productDescription").html(json.productDescription);
    $("#unitPrice").html("€" + json.unitPrice);
    $("#unitOfMeasure").html(json.unitOfMeasure);
}

function displayProduct(productCode) {

    $.ajax("./api/getProduct.php?ProductCode=" + productCode, {
        async: false,
        type: "GET",
        data: "",
        success: function(data)
        {
            drawProductStructure(data);
        },
        error: function(a, b, c)
        {
            console.log(a + ", " + b + ", " + c);
        }
    })

    hideRestrictedElements();

    $("#loadingProduct").fadeOut(400, function() {
        $("#product").fadeIn('slow', function() {});
    });
}

function setProductCode() {
    var productCode = getParameter(document.location.search).ProductCode;
    $("#productCodeInput").val(productCode);
}