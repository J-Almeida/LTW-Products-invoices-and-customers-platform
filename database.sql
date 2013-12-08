PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS InvoiceLine;
DROP TABLE IF EXISTS Tax;
DROP TABLE IF EXISTS Permission;
DROP TABLE IF EXISTS Product;
DROP TABLE IF EXISTS Invoice;
DROP TABLE IF EXISTS Customer;
DROP TABLE IF EXISTS User;
DROP TABLE IF EXISTS Country;

CREATE TABLE Customer (
	CustomerID INTEGER PRIMARY KEY AUTOINCREMENT,
	CustomerTaxID INTEGER UNIQUE NOT NULL,
	CompanyName TEXT NOT NULL,
	AddressDetail TEXT NOT NULL,
	City TEXT NOT NULL,
	CountryID INTEGER REFERENCES Country(CountryID) ON DELETE CASCADE,
	PostalCode TEXT NOT NULL,
	Email TEXT
);

CREATE TABLE Country (
	CountryID INTEGER PRIMARY KEY AUTOINCREMENT,
	CountryName TEXT NOT NULL,
	Country TEXT NOT NULL
);

CREATE TABLE Product (
	ProductID INTEGER PRIMARY KEY AUTOINCREMENT,
	ProductCode INTEGER UNIQUE,
	ProductDescription TEXT NOT NULL,
	UnitPrice REAL NOT NULL,
	UnitOfMeasure TEXT NOT NULL
);

CREATE TABLE Invoice (
	InvoiceID INTEGER PRIMARY KEY AUTOINCREMENT,
	InvoiceNo TEXT UNIQUE NOT NULL,
	InvoiceDate DATE NOT NULL,
	CustomerID INTEGER REFERENCES Customer(CustomerID) ON DELETE CASCADE,
	SystemEntryDate TIMESTAMP,
	/* Document Totals */
	TaxPayable REAL DEFAULT 0, /* Sum of taxes of all lines */ 
	NetTotal REAL DEFAULT 0,   /* Sum of price of all lines w/o tax */ 
	GrossTotal REAL DEFAULT 0  /* NetTotal + TaxPayable */
);

CREATE TABLE InvoiceLine (
	InvoiceLineID INTEGER PRIMARY KEY AUTOINCREMENT,
	InvoiceID INTEGER REFERENCES Invoice(InvoiceID) ON DELETE CASCADE,
	LineNumber INTEGER DEFAULT 0,
	ProductID INTEGER REFERENCES Product(ProductID) ON DELETE CASCADE,
	Quantity INTEGER NOT NULL,
	CreditAmount REAL,
	TaxID INTEGER REFERENCES Tax(TaxID) ON DELETE CASCADE
);

CREATE TABLE Tax (
	TaxID INTEGER PRIMARY KEY AUTOINCREMENT,
	TaxType TEXT UNIQUE NOT NULL,
	TaxPercentage REAL NOT NULL,
	TaxDescription TEXT
);

CREATE TABLE Permission (
	permissionId INTEGER PRIMARY KEY AUTOINCREMENT,
	permissionType TEXT UNIQUE NOT NULL,
	permissionRead INTEGER NOT NULL,
	permissionWrite INTEGER NOT NULL,
	promote INTEGER NOT NULL
);

CREATE TABLE User (
	userId INTEGER PRIMARY KEY AUTOINCREMENT,
	username TEXT UNIQUE NOT NULL,
	name TEXT NOT NULL,
	password TEXT,
	Email TEXT UNIQUE NOT NULL,
	permissionId INTEGER REFERENCES Permission(permissionId) ON DELETE CASCADE
);

CREATE TRIGGER setSystemEntryDate
AFTER INSERT ON Invoice
FOR EACH ROW
BEGIN
	UPDATE Invoice SET SystemEntryDate = strftime('%Y-%m-%dT%H:%M:%f')
	WHERE Invoice.InvoiceID = NEW.InvoiceID; /* 2013-10-18T09:36:54.129+01:00 */
END;

CREATE TRIGGER updateSystemEntryDate
AFTER UPDATE ON Invoice
FOR EACH ROW
BEGIN
	UPDATE Invoice SET SystemEntryDate = strftime('%Y-%m-%dT%H:%M:%f')
	WHERE Invoice.InvoiceID = OLD.InvoiceID;
END;

CREATE TRIGGER updateInvoiceTotals
AFTER INSERT ON InvoiceLine
FOR EACH ROW
BEGIN
	UPDATE InvoiceLine SET LineNumber = 
	1 + (SELECT max(LineNumber) FROM InvoiceLine WHERE invoiceLine.InvoiceID = NEW.InvoiceID)
	WHERE invoiceLine.InvoiceLineID = NEW.InvoiceLineID;

	UPDATE InvoiceLine SET CreditAmount = NEW.Quantity * (SELECT UnitPrice FROM Product WHERE ProductID = NEW.ProductID)
	WHERE invoiceLine.InvoiceLineID = NEW.InvoiceLineID;

	UPDATE Invoice SET TaxPayable = 
	TaxPayable + (SELECT TaxPercentage FROM Tax WHERE TaxID = NEW.TaxID) * 0.01
	* (SELECT CreditAmount FROM InvoiceLine WHERE InvoiceLine.InvoiceLineID = NEW.InvoiceLineID) 
	WHERE Invoice.InvoiceID = NEW.InvoiceID; 

	UPDATE Invoice SET NetTotal = 
	NetTotal + (SELECT CreditAmount FROM InvoiceLine WHERE InvoiceLine.InvoiceLineID = NEW.InvoiceLineID)
	WHERE Invoice.InvoiceID = NEW.InvoiceID;

	UPDATE Invoice SET GrossTotal =
	(SELECT TaxPayable FROM Invoice WHERE Invoice.InvoiceID = NEW.InvoiceID) 
	+ (SELECT NetTotal FROM Invoice WHERE Invoice.InvoiceID = NEW.InvoiceID)
	WHERE Invoice.InvoiceID = NEW.InvoiceID;
END;

INSERT INTO Permission(permissionType, permissionRead, permissionWrite, promote) VALUES ( "admin", 1, 1, 1);
INSERT INTO Permission(permissionType, permissionRead, permissionWrite, promote) VALUES ("editor", 1, 1, 0);
INSERT INTO Permission(permissionType, permissionRead, permissionWrite, promote) VALUES ("reader", 1, 0, 0);

INSERT INTO User(username, name, password, Email, permissionId) VALUES ("MHawk", "Michael Hawk", "supercalifragilisticexpialidocious", "mhawk@hotmail.com", 1);
INSERT INTO User(username, name, password, Email, permissionId) VALUES ("Jakim", "Joaquim Esteves", "1234abcd", "jakim@ltw.pt", 1);
INSERT INTO User(username, name, password, Email, permissionId) VALUES ("AnaMaria12", "Ana Maria Santos", "abcd1234", "ana@ltw.pt", 2);
INSERT INTO User(username, name, password, Email, permissionId) VALUES ("Sususu", "Susana Isabel Barros", "12345678abcdefgh", "flores@ltw.pt", 2);
INSERT INTO User(username, name, password, Email, permissionId) VALUES ("LunaSol", "Luis Miguel", "sol974", "solluna@gmail.com", 3);
INSERT INTO User(username, name, password, Email, permissionId) VALUES ("HenriqueLuis", "Henrique Luis Pimenta", "1990motocicleta", "henri1990@gmail.com", 3);
INSERT INTO User(username, name, password, Email, permissionId) VALUES ("Mikki", "Maria Ines Sousa", "sousaesousa9090", "mari.ines@gmail.com", 3);

INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (001,"Borracha Quenaoapaga",2.50,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (048,"Caneta Reynolds - Azul",1.50,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (049,"Caneta Reynolds - Preta",1.50,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (050,"Caneta Reynolds - Vermelha",1.50,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (051,"Caneta Reynolds - Verde",1.50,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (052,"Caneta Reynolds - Azul Claro",1.50,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (053,"Caneta Reynolds - Roxa",1.50,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (054,"Caneta Reynolds - Amarela",1.52,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (099,"Borracha Seriva - PVC-Free",0.7,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (100,"Borracha Seriva - PVC Included",0.5,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (110,"Caderno Capa Preta",9.0,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (115,"Caderno Capa Branca",15.0,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (116,"Caderno Capa Pokemon",5.0,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (117,"Caneta BIC",1.0,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (101,"Lapis Staedtler HB 2",0.9,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
      VALUES (125, "Carimbos MIEIC", 90.0123, "Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
      VALUES (126, "Camisola FEUP", 450.055, "Un");
INSERT INTO Product(ProductCode,ProductDescription,UnitPrice,UnitOfMeasure)
			VALUES (335,"Camisola FEUP Autografada",300.50,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (330,"Panuelos Renova - 6 Un",1.50,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (339,"Nada",0.10,"Kg");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (340,"Tinteiro Pelican (cor preta) - A melhor tinta para canetas, nao seca",11999.99,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (341,"Tinteiro Pelican (cor azul) - A melhor tinta para canetas, nao seca",99.99,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (342,"Tinteiro Pelican (cor vermelha) - A melhor tinta para canetas, nao seca",11.99,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (343,"Tinteiro Pelican (cor amarela) - A melhor tinta para canetas, nao seca",11.99,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (344,"Tinteiro Pelican (cor roxa) - A melhor tinta para canetas, nao seca",11.99,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (345,"Tinteiro Pelican (cor dourada) - A melhor tinta para canetas, nao seca",11.99,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (346,"Tinteiro Pelican (cor verde) - A melhor tinta para canetas, nao seca",111.99,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (347,"Tinteiro Pelican (cor castanha) - A melhor tinta para canetas, nao seca",11.99,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (348,"Tinteiro Pelican (cor prateada) - A melhor tinta para canetas, nao seca",0.99,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (349,"Tinteiro Pelican (cor laranja) - A melhor tinta para canetas, nao seca",211.99,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (350,"Tinteiro Pelican (cor-de-rosa) - A melhor tinta para canetas, nao seca",11.99,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (351,"Tinteiro Pelican (cor cinzenta) - A melhor tinta para canetas, nao seca",11.99,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (352,"Caneta FEUP - Autografada",98607879.99,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (353,"Caneta FEUP - Sem Autografos",999999.99,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (354,"Saquinho de Compras FEUP - Autografado",98607.99,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (355,"Folhas A4 (250 Un) - Marca AEFEUP, lapis gratis",30.56,"Emb");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (356,"Folhas A5 (250 Un) - Marca AEFEUP, lapis gratis",20.56,"Emb");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (357,"Queijos FEUP",999999.99,"Kg");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (358,"Calculadora Texas TI-84",300.99,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (359,"Calculadora Texas TI-84 Plus",301.00,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (360,"Régua Staples - 15 cm",0.50,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (361,"Régua Staples - 20 cm",0.60,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (362,"Régua Staples - 30 cm",0.70,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (363,"Esquadro Staples - Inquebravel, 45graus, 25cm",1.00,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (364,"Esquadro Staples - Inquebravel, 60graus, 25cm",1.01,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (365,"Transferidor Staples",1.00,"Un");
INSERT INTO Product(ProductCode, ProductDescription, UnitPrice, UnitOfMeasure)
			VALUES (366,"Esquadro Plastico Cristal Liderpapel - 60graus, 25cm",1.05,"Un");

INSERT INTO Country(Country, CountryName)
			VALUES ("PT", "Portugal");
INSERT INTO Country(Country, CountryName)
			VALUES ("GB", "Inglaterra");
INSERT INTO Country(Country, CountryName)
			VALUES ("ES", "Espanha");

INSERT INTO Customer(CustomerTaxID, CompanyName, AddressDetail, City, CountryID, PostalCode, Email)
            VALUES (1234567, "Feup", "Rua Doutor Roberto Frias", "Porto", 1, "4200-465", "feup@fe.up.pt");

INSERT INTO Customer(CustomerTaxID, CompanyName, AddressDetail, City, CountryID, PostalCode, Email)
            VALUES (7654321, "Flup", "Via Panorâmica", "Porto", 1, "4150-564", "flup@fl.up.pt");

INSERT INTO Customer(CustomerTaxID, CompanyName, AddressDetail, City, CountryID, PostalCode, Email)
            VALUES (9874567, "Hounslow", "Potatos Street", "Londres", 2, "ML1 2DA", "houwnslow@hotmail.com");

INSERT INTO Customer(CustomerTaxID, CompanyName, AddressDetail, City, CountryID, PostalCode, Email)
            VALUES (4457812, "Kingston", "30 Leicester Square", "Londres", 2, "WC2H 7LA", "kingstone@gmail.com");

INSERT INTO Customer(CustomerTaxID, CompanyName, AddressDetail, City, CountryID, PostalCode, Email)
            VALUES (9875669, "La Tienda", "Sr. D. Alvaro Blanco Ruiz", "Madrid", 3, "28300", "tienda@lojita.com");

INSERT INTO Customer(CustomerTaxID, CompanyName, AddressDetail, City, CountryID, PostalCode, Email)
            VALUES (4565453, "Green Park", "Mary's Street", "Londres", 2, "AB3C 3LA", "greensales@gmail.com");

INSERT INTO Customer(CustomerTaxID, CompanyName, AddressDetail, City, CountryID, PostalCode, Email)
            VALUES (3453259, "Ouro e Prata", "Avenida da Liberdade", "Lisboa", 1, "1268-121", "prata@ouro.pt");

INSERT INTO Customer(CustomerTaxID, CompanyName, AddressDetail, City, CountryID, PostalCode, Email)
            VALUES (9574891, "Bolachas Inc.", "Rua Das Flores", "Porto", 1, "4510-145", "cookies@lojaporto.pt");

INSERT INTO Customer(CustomerTaxID, CompanyName, AddressDetail, City, CountryID, PostalCode, Email)
            VALUES (00056010, "Sir Francis Burdett & Son", "St. James's Place", "Londres", 2, "SW1 E17", "francis@sir.com");

INSERT INTO Customer(CustomerTaxID, CompanyName, AddressDetail, City, CountryID, PostalCode, Email)
            VALUES (9593205891, "Jaquet-Droz's Automata", "Draughtsman Street", "Londres", 2, "SW19 3RQ", "epicdolls@gmail.com");




INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/1", "2013-09-27", 1);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/3", "2013-10-11", 3);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/14", "2013-09-30", 2);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/11", "2013-11-01", 5);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/2", "2013-09-29", 4);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/15", "2013-11-02", 1);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/16", "2013-11-05", 3);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/17", "2013-11-07", 4);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/18", "2013-11-09", 1);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/19", "2013-11-09", 5);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/20", "2013-11-10", 3);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/21", "2013-11-11", 10);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/22", "2013-11-11", 9);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/23", "2013-11-11", 8);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/24", "2013-11-15", 5);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/25", "2013-11-15", 10);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/26", "2013-11-16", 10);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/27", "2013-11-17", 7);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/28", "2013-11-17", 6);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/29", "2013-11-17", 8);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/30", "2013-11-17", 5);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/31", "2013-11-17", 3);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/32", "2013-11-18", 3);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/33", "2013-11-19", 1);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/34", "2013-11-19", 1);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/35", "2013-11-20", 5);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/36", "2013-11-21", 3);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/37", "2013-11-23", 5);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/38", "2013-11-25", 1);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/39", "2013-11-25", 2);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/40", "2013-11-25", 3);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/41", "2013-11-25", 4);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/42", "2013-11-27", 1);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/43", "2013-11-29", 1);

INSERT INTO Invoice (InvoiceNo, InvoiceDate, CustomerID)
            VALUES ("FT SEQ/44", "2013-11-30", 5);


INSERT INTO Tax(TaxType, TaxPercentage, TaxDescription) VALUES ("IVA 1", 23.00, "Taxa Normal");
INSERT INTO Tax(TaxType, TaxPercentage, TaxDescription) VALUES ("IVA 2", 13.00, "Taxa Intermédia");
INSERT INTO Tax(TaxType, TaxPercentage, TaxDescription) VALUES ("IVA 3",  6.00, "Taxa Reduzida");


INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 1, 3, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 2, 3, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 3, 3, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 4, 3, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 5, 3, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 6, 3, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 7, 3, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 8, 3, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 9, 3, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 10, 3, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 11, 3, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 12, 3, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 13, 3, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 14, 3, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 15, 3, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 16, 3, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 17, 3, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 18, 3, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 19, 3, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 20, 3, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 21, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 22, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 23, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 24, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 25, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 26, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 27, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 28, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 29, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 30, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 31, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 32, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 33, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 34, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 35, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 36, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 37, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 38, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 39, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 40, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 41, 2, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 42, 2, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 43, 2, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 44, 2, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 45, 2, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (1, 46, 1, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (2, 43, 2, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (2, 44, 2, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (2, 45, 2, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (2, 46, 1, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (3, 10, 5, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (3, 17, 2, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (4, 18, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (4, 15, 10, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (4, 10, 2, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (5, 1, 2, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (5, 9, 9, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (5, 11, 2, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (5, 2, 3, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (6, 1, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (6, 2, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (6, 3, 3, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (6, 4, 5, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (6, 5, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (6, 10, 2, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (6, 11, 3, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (6, 12, 7, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (7, 5, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (7, 6, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (7, 7, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (7, 8, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (7, 11, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (7, 9, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (7, 14, 10, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (7, 15, 1, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (8, 1, 3, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (8, 7, 3, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (8, 2, 15, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (9, 1, 1, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (10, 1, 23, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (10, 2, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (10, 3, 10, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (10, 4, 10, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (10, 5, 10, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (10, 10, 45, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (10, 11, 40, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (10, 12, 50, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (11, 13, 5, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (11, 14, 3, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (11, 17, 15, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (12, 18, 1, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (13, 18, 50, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (14, 2, 5, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (14, 10, 3, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (14, 11, 1, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (15, 10, 10, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (16, 16, 7, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (17, 16, 37, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (17, 15, 37, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (17, 14, 37, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (18, 6, 50, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (18, 7, 20, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (18, 8, 20, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (19, 1, 2, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (19, 2, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (19, 3, 2, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (20, 4, 20, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (20, 5, 10, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (20, 6, 20, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (21, 7, 20, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (21, 8, 10, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (21, 9, 20, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (22, 7, 10, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (22, 8, 20, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (22, 9, 10, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (23, 10, 10, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (23, 1, 2, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (24, 2, 2, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (24, 1, 2, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (25, 10, 11, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (25, 11, 11, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (26, 3, 10, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (26, 4, 10, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (26, 5, 10, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (26, 6, 20, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (26, 7, 20, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (26, 8, 20, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (27, 7, 99, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (27, 8, 99, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (28, 5, 20, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (28, 6, 20, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (28, 7, 20, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (29, 7, 99, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (29, 8, 99, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (30, 2, 5, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (31, 1, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (31, 2, 20, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (31, 3, 20, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (31, 4, 20, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (31, 5, 20, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (32, 1, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (32, 2, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (32, 3, 5, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (32, 4, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (32, 5, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (32, 6, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (32, 7, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (32, 8, 5, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (32, 9, 1, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (32, 10, 1, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (33, 3, 9, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (34, 1, 99, 1);

INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (35, 14, 50, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (35, 14, 5, 1);
INSERT INTO InvoiceLine(InvoiceID, ProductID, Quantity, TaxID)
            VALUES (35, 5, 14, 1);