


// Scripts de la Base de Datos
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    cedula VARCHAR(20) UNIQUE NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    correo VARCHAR(150) UNIQUE NOT NULL,
    telefono VARCHAR(20),
    fotografia VARCHAR(255),
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM('Administrador', 'Chofer', 'Pasajero') NOT NULL,
    estado ENUM('Pendiente', 'Activo', 'Inactivo') DEFAULT 'Pendiente',
    token_activacion VARCHAR(255),  -- para el link de activación
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE vehiculos (
    id_vehiculo INT AUTO_INCREMENT PRIMARY KEY,
    id_chofer INT NOT NULL,
    numero_placa VARCHAR(20) NOT NULL,
    color VARCHAR(50) NOT NULL,
    marca VARCHAR(50) NOT NULL,
    modelo VARCHAR(50) NOT NULL,
    anno INT NOT NULL,
    capacidad_asientos INT NOT NULL,
    fotografia VARCHAR(255),
    CONSTRAINT fk_chofer FOREIGN KEY (id_chofer) REFERENCES usuarios(id_usuario)
);


CREATE TABLE rides (
    id_ride INT AUTO_INCREMENT PRIMARY KEY,
    id_chofer INT NOT NULL,
    id_vehiculo INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    salida VARCHAR(100) NOT NULL,
    llegada VARCHAR(100) NOT NULL,
    dia DATE NOT NULL,         -- campo tipo DATE
    hora TIME NOT NULL,
    costo NUMERIC(10,2) NOT NULL,
    espacios INT NOT NULL,
    CONSTRAINT fk_chofer_rides FOREIGN KEY (id_chofer) REFERENCES usuarios(id_usuario),
    CONSTRAINT fk_vehiculo_rides FOREIGN KEY (id_vehiculo) REFERENCES vehiculos(id_vehiculo)
);

CREATE TABLE reservas (
    id_reserva INT AUTO_INCREMENT PRIMARY KEY,
    id_ride INT NOT NULL,
    id_pasajero INT NOT NULL,
    fecha_reserva TIMESTAMP DEFAULT NOW(),
    estado VARCHAR(20) NOT NULL DEFAULT 'Pendiente', -- Pendiente, Aceptada, Rechazada, Cancelada
    FOREIGN KEY (id_ride) REFERENCES rides(id_ride),
    FOREIGN KEY (id_pasajero) REFERENCES usuarios(id_usuario)
);
