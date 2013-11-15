function submitForm() {

    var form = "?";
    form += $('form').serialize();
    console.log(form);

    $.ajax($('#content form').attr('action') + form, {
        type: "GET",
        data: "",
        success: function(data)
        {
            drawSearchResults(data, fieldNames);
        },
        error: function(a, b, c)
        {
            console.log(a + ", " + b + ", " + c);
        }
    })
}

function drawSearchResults(data, fieldNames) {
    var json = JSON.parse(data);
    var tables = "<table class=\"paginated\">";

    // print table headers
    tables += "<thead><tr>";
    for(field in json[0]) {
        tables += "<th>";
        tables += fieldNames[field];
        tables += "</th>"
    }
    tables += "</tr></thead>";

    var hyperlinks = {
        "invoiceNo" : "<a href='invoice_det.html?InvoiceNo=",
        "customerId": "<a href='customer_det.html?CustomerID=",
        "productCode": "<a href='product_det.html?ProductCode="};

    tables += "<tbody>"
    // print table contents
    for(result in json) {
        tables += "<tr>";
        var object = json[result];
        for(field in object) {
            tables += "<td>";
            if( field in hyperlinks ) {
                tables += hyperlinks[field];
                tables += object[field];
                tables += "'target='_blank'>";
                tables += object[field];
                tables += "</a>";
            }
            else {
                tables += object[field];
            }
            tables += "</td>";
        }
        tables += "</tr>";
    }

    tables += "</tbody></table>";
    $("#results").html(tables);

    // paginate the table
    $('table.paginated').each(function() {
        var currentPage = 0;
        var numPerPage = 5;
        var $table = $(this);
        $table.bind('repaginate', function() {
            $table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
        });
        $table.trigger('repaginate');
        var numRows = $table.find('tbody tr').length;
        var numPages = Math.ceil(numRows / numPerPage);
        var $pager = $('<div class="pager"></div>');
        for (var page = 0; page < numPages; page++) {
            $('<span class="page-number"></span>').text(page + 1).bind('click', {
                newPage: page
            }, function(event) {
                currentPage = event.data['newPage'];
                $table.trigger('repaginate');
                $(this).addClass('active').siblings().removeClass('active');
            }).appendTo($pager).addClass('clickable');
        }
        $pager.insertBefore($table).find('span.page-number:first').addClass('active');
    });
}

function getValueBoxes(operation) {
    var valueBoxes = "";
    $( "#fieldSelect" ).show();
    if(operation == "range") {
        valueBoxes +=("<label>From: </label>");
        valueBoxes +=('<input name="value[]" type="text">');

        valueBoxes +=("   <label>To: </label>");
        valueBoxes +=('<input name="value[]" type="text">');
    }
    else if(operation != "min" && operation != "max" && operation != "listall") {
        valueBoxes +=('<label>Search for: </label>');
        valueBoxes +=('<input name="value[]" type="text">');
    }

    if(operation == "listall") {
        $( "#fieldSelect" ).hide();
    }

    return valueBoxes;
}

function getOperation() {
    var op = $( "#op option:selected" ).val();
    $( "#queryBoxes" ).html( getValueBoxes(op) );
}

function displayResults(data) {
    $("#searchResults").html(data);
}