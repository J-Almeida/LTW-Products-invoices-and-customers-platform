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

    $("#productCode").html(json.ProductCode);
    $("#productDescription").html(json.ProductDescription);
    $("#unitPrice").html("€" + json.UnitPrice);
    $("#unitOfMeasure").html(json.UnitOfMeasure);
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

    $("#loadingProduct").fadeOut(400, function() {
        $("#product").fadeIn('slow', function() {});
    });
}

function setProductCode() {
    var productCode = getParameter(document.location.search).ProductCode;
    $("#productCodeInput").val(productCode);
}

function setDeleteParameters() {
    var productCode = getParameter(document.location.search).ProductCode;
    $("#tableDel").val('Product');
    $("#fieldDel").val('productCode');
    $("#valueDel").val(productCode);
}