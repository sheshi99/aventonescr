


// Scripts de la Base de Datos

CREATE TABLE rides (
    id_ride INT AUTO_INCREMENT PRIMARY KEY,
    id_chofer INT NOT NULL,
    id_vehiculo INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    salida VARCHAR(150) NOT NULL,
    llegada VARCHAR(150) NOT NULL,
    dia ENUM('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo') NOT NULL,
    hora TIME NOT NULL,
    costo DECIMAL(10,2) NOT NULL,
    espacios INT NOT NULL,
    FOREIGN KEY (id_chofer) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_vehiculo) REFERENCES vehiculos(id_vehiculo)
);

CREATE TABLE reservas (
    id_reserva SERIAL PRIMARY KEY,
    id_ride INT NOT NULL,
    id_pasajero INT NOT NULL,
    fecha_reserva TIMESTAMP DEFAULT NOW(),
    estado VARCHAR(20) NOT NULL DEFAULT 'Pendiente', -- Pendiente, Aceptada, Rechazada, Cancelada
    FOREIGN KEY (id_ride) REFERENCES rides(id_ride),
    FOREIGN KEY (id_pasajero) REFERENCES usuarios(id_usuario)
);
