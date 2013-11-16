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

    if (json.length == 0) {
        tables = "<div>Got no results</div>";
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

    // paginate the table
    $('table.paginated').each(function() {
        var currentPage = 0;
        var numPerPage = 10;
        var $table = $(this);
        var $pageNumber = $('<span class="page-number"></span>');
        $table.bind('repaginate', function() {
            $table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
            $pageNumber.text(currentPage+1);
        });
        $table.trigger('repaginate');
        var numRows = $table.find('tbody tr').length;
        var numPages = Math.ceil(numRows / numPerPage);

        if (numPages == 1 ) // cancel the paginator
            return;

        var $pager = $('<div class="pager"></div>');
        $pageNumber.appendTo($pager);

        $('<span class="page-change-button"></span>').text("Previous").bind('click', {
            newPage: -1
        }, function(event) {
            if ( currentPage > 0) {
                currentPage += event.data['newPage'];
                $table.trigger('repaginate');
            }
        }).appendTo($pager).addClass('clickable');

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