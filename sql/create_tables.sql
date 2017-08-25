-- Lisää CREATE TABLE lauseet tähän tiedostoon

CREATE TABLE Käyttäjä(
	id SERIAL PRIMARY KEY,
	nimi varchar(30) NOT NULL,
	puh varchar(15),
	sähköposti varchar(50),
	liittymispvm DATE DEFAULT CURRENT_DATE,
	salasana varchar(40) NOT NULL
);

CREATE TABLE Tuote(
	id SERIAL PRIMARY KEY,
	myyjä_id INTEGER REFERENCES Käyttäjä(id),
	kuvaus varchar(30) NOT NULL,
	hinta DECIMAL(20,2) NOT NULL,
	lisätietoja varchar(300),
	lisäyspäivä DATE DEFAULT CURRENT_DATE
);

CREATE TABLE Tarjous(
	id SERIAL PRIMARY KEY,
	tuote_id INTEGER REFERENCES Tuote(id),
	ostaja_id INTEGER REFERENCES Käyttäjä(id),
	hintatarjous DECIMAL(20,2) NOT NULL,
	lisätietoja varchar(300),
	päivämäärä DATE DEFAULT CURRENT_DATE
);


