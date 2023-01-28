DROP DATABASE IF EXISTS Colaboradores;
CREATE DATABASE Colaboradores;
USE Colaboradores;

CREATE TABLE Perfiles(
Cedula varchar (50)not null,
Nombre varchar (100) not null,
Contraseña varchar (20) not null,
Primary key (Cedula)
);

CREATE TABLE Fechaperfil(
ID int UNSIGNED NOT NULL AUTO_INCREMENT,
HoraIngreso time(6),
HoraSalida time(6),
HoraSalidaAlmuerzo time (6),
HoraEntradaAlmuerzo time (6),
Cedula varchar (50),
Fecha date,
Ubicacion varchar (50),
PRIMARY KEY (ID),
FOREIGN KEY (Cedula) REFERENCES Perfiles (Cedula)
); 
