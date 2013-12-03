<?php


if ($_FILES['file']['error'] == UPLOAD_ERR_OK               //checks for errors
    && is_uploaded_file($_FILES['file']['tmp_name'])) { //checks that file is uploaded
    $contents = file_get_contents($_FILES['file']['tmp_name']);
}

// functions taken from php documentation
// http://us1.php.net/manual/en/domdocument.schemavalidate.php
function libxml_display_error($error)
{
    $return = "<br/>\n";
    switch ($error->level) {
        case LIBXML_ERR_WARNING:
            $return .= "<b>Warning $error->code</b>: ";
            break;
        case LIBXML_ERR_ERROR:
            $return .= "<b>Error $error->code</b>: ";
            break;
        case LIBXML_ERR_FATAL:
            $return .= "<b>Fatal Error $error->code</b>: ";
            break;
    }
    $return .= trim($error->message);
    if ($error->file) {
        $return .=    " in <b>$error->file</b>";
    }
    $return .= " on line <b>$error->line</b>\n";

    return $return;
}

function libxml_display_errors() {
    $errors = libxml_get_errors();
    foreach ($errors as $error) {
        print libxml_display_error($error);
    }
    libxml_clear_errors();
}

// Enable user error handling
libxml_use_internal_errors(true);

$xml= new DOMDocument();
$xml->loadXML($contents, LIBXML_NOBLANKS); // Or load if filename required

if (!$xml->schemaValidate('./saft.xsd')){
    print '<b>DOMDocument::schemaValidate() Generated Errors!</b>';
    libxml_display_errors();
} else {
    echo '<img src="http://cdn.memegenerator.net/instances/500x/43506104.jpg">';
}


?>