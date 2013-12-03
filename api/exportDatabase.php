<?php

header ("Content-Type:text/xml");

$doc = new DOMDocument('1.0','UTF-8');


/****************************************************
AUDIT ELEMENT
****************************************************/
$AuditElement = $doc->createElement("AuditFile");

$AuditAttr = $doc->createAttribute("xmlns");
$AuditAttr->value = "urn:OECD:StandardAuditFile-Tax:PT_1.03_01";
$AuditAttr2 = $doc->createAttribute("xmlns:xsi");
$AuditAttr2->value = "http://www.w3.org/2001/XMLSchema-instance";
$AuditAttr3 = $doc->createAttribute("xmlns:spi");
$AuditAttr3->value = "http://Empresa.pt/invoice1";
$AuditAttr4 = $doc->createAttribute("xmlns:saf");
$AuditAttr4->value = "urn:OECD:StandardAuditFile-Tax:PT_1.03_01";
$AuditAttr5 = $doc->createAttribute("xsi:schemaLocation");
$AuditAttr5->value = "urn:OECD:StandardAuditFile-Tax:PT_1.03_01 http://serprest.pt/tmp/SAFTPT-1.03_01.xsd";

$AuditElement->appendChild($AuditAttr);
$AuditElement->appendChild($AuditAttr2);
$AuditElement->appendChild($AuditAttr3);
$AuditElement->appendChild($AuditAttr4);
$AuditElement->appendChild($AuditAttr5);

$root = $doc->appendChild($AuditElement);


/****************************************************
HEADER
****************************************************/

$headerElement = $doc->createElement("Header");
$AuditFileVersionElement = $doc->createElement("AuditFileVersion");
$CompanyIdElement = $doc->createElement("CompanyID");
$TaxRegistrationNumberElement = $doc->createElement("TaxRegistrationNumber");
$TaxAccountingBasisElement = $doc->createElement("TaxAccountingBasis");
$CompanyNameElement = $doc->createElement("CompanyName");
$CompanyAddressElement = $doc->createElement("CompanyAddress");
$AddressDetailElement = $doc->createElement("AddressDetail");
$CityElement = $doc->createElement("City");
$PostalCodeElement = $doc->createElement("PostalCode");
$CountryElement = $doc->createElement("Country");
$FiscalYearElement = $doc->createElement("FiscalYear");
$StartDateElement = $doc->createElement("StartDate");
$EndDateElement = $doc->createElement("EndDate");
$CurrencyCodeElement = $doc->createElement("CurrencyCode");
$DateCreatedElement = $doc->createElement("DateCreated");
$TaxEntityElement = $doc->createElement("TaxEntity");
$ProductCompanyTaxIDElement = $doc->createElement("ProductCompanyTaxIDElement");
$SoftwareCertificateNumberElement = $doc->createElement("SoftwareCertificateNumber");
$ProductIDElement = $doc->createElement("ProductID");
$ProductVersionElement = $doc->createElement("ProductVersion");

$AuditFileVersionNode = $doc->createTextNode("1.03_01");
$CompanyIDNode = $doc->createTextNode("Leiria 55555");
$TaxRegistrationNumberNode = $doc->createTextNode("506219300");
$TaxAccountingBasisNode = $doc->createTextNode("F");
$CompanyNameNode = $doc->createTextNode("Empresa, Lda");
$AddressDetailNode = $doc->createTextNode("Rua nome da rua, nº 100");
$CityNode = $doc->createTextNode("Leiria");
$PostalCodeNode = $doc->createTextNode("4400-125");
$CountryNode = $doc->createTextNode("PT");
$FiscalYearNode = $doc->createTextNode("2013");
$StartDateNode = $doc->createTextNode("2013-10-01");
$EndDateNode = $doc->createTextNode("2013-10-31");
$CurrencyCodeNode = $doc->createTextNode("EUR");
$DateCreatedNode = $doc->createTextNode("2013-11-15");
$TaxEntityNode = $doc->createTextNode("Global");
$ProductCompanyTaxIDNode = $doc->createTextNode("506209365");
$SoftwareCertificateNumberNode = $doc->createTextNode("0");
$ProductIDNode = $doc->createTextNode("Empresa/invWeb");
$ProductVersionNode = $doc->createTextNode("0.9");

$AuditFileVersionElement->appendChild($AuditFileVersionNode);
$CompanyIdElement->appendChild($CompanyIDNode);
$TaxRegistrationNumberElement->appendChild($TaxRegistrationNumberNode);
$TaxAccountingBasisElement->appendChild($TaxAccountingBasisNode);
$CompanyNameElement->appendChild($CompanyNameNode);
$AddressDetailElement->appendChild($AddressDetailNode);
$CityElement->appendChild($CityNode);
$PostalCodeElement->appendChild($PostalCodeNode);
$CountryElement->appendChild($CountryNode);
$FiscalYearElement->appendChild($FiscalYearNode);
$StartDateElement->appendChild($StartDateNode);
$EndDateElement->appendChild($EndDateNode);
$CurrencyCodeElement->appendChild($CurrencyCodeNode);
$DateCreatedElement->appendChild($DateCreatedNode);
$TaxEntityElement->appendChild($TaxEntityNode);
$ProductCompanyTaxIDElement->appendChild($ProductCompanyTaxIDNode);
$SoftwareCertificateNumberElement->appendChild($SoftwareCertificateNumberNode);
$ProductIDElement->appendChild($ProductIDNode);
$ProductVersionElement->appendChild($ProductVersionNode);

$CompanyAddressElement->appendChild($AddressDetailElement);
$CompanyAddressElement->appendChild($CityElement);
$CompanyAddressElement->appendChild($PostalCodeElement);
$CompanyAddressElement->appendChild($CountryElement);
$headerElement->appendChild($AuditFileVersionElement);
$headerElement->appendChild($CompanyIdElement);
$headerElement->appendChild($TaxRegistrationNumberElement);
$headerElement->appendChild($TaxAccountingBasisElement);
$headerElement->appendChild($CompanyNameElement);
$headerElement->appendChild($CompanyAddressElement);
$headerElement->appendChild($FiscalYearElement);
$headerElement->appendChild($StartDateElement);
$headerElement->appendChild($EndDateElement);
$headerElement->appendChild($CurrencyCodeElement);
$headerElement->appendChild($DateCreatedElement);
$headerElement->appendChild($TaxEntityElement);
$headerElement->appendChild($ProductCompanyTaxIDElement);
$headerElement->appendChild($SoftwareCertificateNumberElement);
$headerElement->appendChild($ProductIDElement);
$headerElement->appendChild($ProductVersionElement);

$root->appendChild($headerElement);


/****************************************************
MASTERFILES
****************************************************/
$masterElement = $doc->createElement("MasterFiles");

$customerElement = $doc->createElement("Customer");

$root->appendChild($masterElement);

echo $doc->saveXML();
?>
