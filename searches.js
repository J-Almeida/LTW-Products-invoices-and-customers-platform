function submitForm() {

    var form = "?";
    form += $('form').serialize();

    if (form.indexOf("op") < 0) {
        alert("Please specify an operation!");
        return;
    }

    $.ajax($('#searchMenu form').attr('action') + form, {
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

    if (json.length == 0) {
        tables = "<div id=\"noResults\">No results found.</div>";
        $("#results").html(tables);
        return;
    }

    // print table headers
    tables += "<thead><tr>";
    for(field in json[0]) {
        tables += "<th>";
        tables += fieldNames[field];
        tables += "</th>"
    }
    tables += "</tr></thead>";

    var hyperlinks = {
        "invoiceNo" : "<a href='invoice_detailed.html?InvoiceNo=",
        "customerId": "<a href='customer_detailed.html?CustomerID=",
        "productCode": "<a href='product_detailed.html?ProductCode="
    };

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

    // make entire rows clickable
    $("#results tbody tr").click( function() {
        var $link = $(this).find('a');
        window.open($link.attr('href'), $link.attr('target') );
        return false;
    }).hover( function() {
            $(this).toggleClass('hover');
        });

    // paginate the table
    $('table.paginated').each(function() {
        var currentPage = 0;
        var numPerPage = 10;
        var $table = $(this);
        var $pageNumber = $('<span class="page-number"></span>');
        var numRows = $table.find('tbody tr').length;
        var numPages = Math.ceil(numRows / numPerPage);
        if (numPages == 1 ) // cancel the paginator
            return;

        $table.bind('repaginate', function() {
            $table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
            $pageNumber.text((currentPage+1).toString() + " / " + numPages.toString());
        });

        $table.trigger('repaginate');

        var $pager = $('<div class="pager"></div>');

        $('<span class="page-change-button"></span>').text("Previous").bind('click', {
            newPage: -1
        }, function(event) {
            if ( currentPage > 0) {
                currentPage += event.data['newPage'];
                $table.trigger('repaginate');
            }
        }).appendTo($pager).addClass('clickable');

        $pageNumber.appendTo($pager);

        $('<span class="page-change-button"></span>').text("Next").bind('click', {
            newPage: 1
        }, function(event) {
            if ( currentPage < numPages - 1) {
                currentPage += event.data['newPage'];
                $table.trigger('repaginate');
            }
        }).appendTo($pager).addClass('clickable');

        $pager.insertBefore($table);
    });
}

var fieldTypes = {
    'invoiceDate' : 'type="date"',
    'taxPayable' : 'type="number"',
    'netTotal' : 'type="number"',
    'grossTotal' : 'type="number"',
    'customerId' : 'type="number"',
    'customerTaxId' : 'type="number',
    'unitPrice' : 'type="number"',
    'email' : 'type="email"'
};

function getValueBoxes(operation, field) {
    var valueBoxes = "";

    var input = "";
    if (field in fieldTypes && operation != 'contains') {
        input = '<input name="value[]"';
        input += fieldTypes[field]; input += ">";
    } else
        input = '<input name="value[]" type="text">';

    $( "#fieldSelect" ).show();
    if(operation == "range") {
        valueBoxes +=("<label>From: </label>");
        valueBoxes +=(input);

        valueBoxes +=("   <label>To: </label>");
        valueBoxes +=(input);
    }
    else if(operation != "min" && operation != "max" && operation != "listall") {
        valueBoxes +=('<label>Search for: </label>');
        valueBoxes +=(input);
    }

    if(operation == "listall") {
        $( "#fieldSelect" ).hide();
    }

    return valueBoxes;
}

function updateValueBoxes() {
    var op = $( "#op option:selected" ).val();
    var field = $("#field option:selected").val();
    $( "#valueBoxes" ).html( getValueBoxes(op, field) );
}