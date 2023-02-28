DROP DATABASE IF EXISTS AsistenciaTW;
CREATE DATABASE AsistenciaTW;
USE AsistenciaTW;

CREATE TABLE tipoUsuario (
idTipoUsuario int not null,
nombretipoUsuario varchar (30) not null,
Primary key (idTipoUsuario)
);

CREATE TABLE usuarios (
cedula int not null,
nombre varchar (50) not null,
apellido1 varchar (50) not null,
apellido2 varchar (50) not null,
pass varchar (30) not null,
telefono int not null,
correo varchar (30) not null,
idTipoUsuario int not null,
mailNotif int not null,
wsNotif int not null,
PRIMARY KEY (cedula),
FOREIGN KEY (idTipoUsuario) REFERENCES tipoUsuario (idTipoUsuario)
);

CREATE TABLE marcas (
id int UNSIGNED NOT NULL AUTO_INCREMENT,
horaIngreso time(6),
horaSalida time(6),
horaSalidaAlmuerzo time (6),
horaEntradaAlmuerzo time (6),
cedula int not null,
fecha date,
ubicacion varchar (50),
PRIMARY KEY (id),
FOREIGN KEY (cedula) REFERENCES usuario (cedula)
);

CREATE TABLE tipoMarca (
idTipoMarca int,
nombreTipoMarca varchar (50),
PRIMARY KEY (id),
FOREIGN KEY (idTipoMarca) REFERENCES marcas (idTipoMarca)
);

CREATE TABLE notificaciones (
cedula int not null,
telefono int not null,
correo varchar (30) not null,
PRIMARY KEY (cedula),
FOREIGN KEY (cedula) REFERENCES usuario (cedula)
);