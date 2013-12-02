$(document).on('click', '.removeRow', function(){
    var numLines = $('.invoiceLine').length;
    if (numLines > 1) {
        $(this).closest('.invoiceLine').remove();
    }
    else
        alert('Invoice needs at least one product.');
    updateTotals();
});

$(document).on('click', '.addRow', function(){
    var newRow = $('.invoiceLines table:last tr:last').clone();
    var lastLineNumber = $('.invoiceLines table:last tr:last').attr('id');
    var newLineNumber = parseInt(lastLineNumber) + 1;
    newRow.find('th >:first-child').each(function(){
        var fieldName = $(this).attr('name');
        if ( fieldName ) {
            fieldName = fieldName.replace(lastLineNumber, newLineNumber.toString());
            $(this).attr('name', fieldName);
        }
    })
    $('.invoiceLines table').append('<tr class="invoiceLine" id=' + newLineNumber + '>' + newRow.html() + '</tr>');
    updateLine($('.invoiceLine:last'));
    updateTotals();
});

function updateLine($element) {
    var line = $element.closest('.invoiceLine');
    var unitPrice = parseFloat(line.find('.productCode option:selected').data('unitprice'));
    var quantity = parseInt(line.find('.quantity').val());
    line.find('.unitPrice').val(unitPrice);
    line.find('.creditAmount').val(unitPrice * quantity);
    updateTotals();
}

function updateTotals() {
    var netTotal = 0.0;
    var payableTax = 0.0;
    $('#invoiceLines').find('.invoiceLine').each(function(){
        netTotal += parseFloat($(this).find('.creditAmount').val());
        payableTax += parseFloat($(this).find('.creditAmount').val()) * 0.01 * parseFloat($(this).find('.taxId option:selected').data('taxpercentage'));
    });
    var grossTotal = netTotal + payableTax;

    $('#taxPay').text(payableTax.toFixed(2));
    $('#netTotal').text(netTotal.toFixed(2));
    $('#grossTotal').text(grossTotal.toFixed(2));
}