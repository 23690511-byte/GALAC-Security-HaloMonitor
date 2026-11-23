// HaloMonitor - MongoDB Initialization & Schema Validation Script
// Colecciones equivalentes a la base de datos relacional para HaloMonitor / GALAC Security

const dbName = 'halomonitor_db';
db = db.getSiblingDB(dbName);

// 1. Colección de Roles
db.createCollection('roles', {
  validator: {
    $jsonSchema: {
      bsonType: 'object',
      required: ['nombre', 'descripcion'],
      properties: {
        nombre: { bsonType: 'string', description: 'Nombre único del rol (Administrador, Analista, Auditor)' },
        descripcion: { bsonType: 'string' },
        creado_en: { bsonType: 'date' }
      }
    }
  }
});
db.roles.createIndex({ nombre: 1 }, { unique: true });

db.roles.insertMany([
  { nombre: 'Administrador', descripcion: 'Acceso Total (Full Access)', creado_en: new Date() },
  { nombre: 'Analista', descripcion: 'Acceso Operativo (Lectura/Escritura)', creado_en: new Date() },
  { nombre: 'Auditor', descripcion: 'Acceso Solo Lectura (Read-Only)', creado_en: new Date() }
]);

// 2. Colección de Usuarios
db.createCollection('usuarios', {
  validator: {
    $jsonSchema: {
      bsonType: 'object',
      required: ['nombre', 'email', 'password_hash', 'rol', 'activo'],
      properties: {
        nombre: { bsonType: 'string' },
        email: { bsonType: 'string', pattern: '^.+@.+$' },
        password_hash: { bsonType: 'string' },
        rol: { bsonType: 'string', enum: ['Administrador', 'Analista', 'Auditor'] },
        activo: { bsonType: 'bool' },
        departamento: { bsonType: 'string' },
        creado_en: { bsonType: 'date' }
      }
    }
  }
});
db.usuarios.createIndex({ email: 1 }, { unique: true });

// 3. Colección de Incidentes con Documentos Embebidos (Historial & Datos Congelados)
db.createCollection('incidentes', {
  validator: {
    $jsonSchema: {
      bsonType: 'object',
      required: ['folio', 'titulo', 'gravedad', 'estado', 'usuario_reporta_email'],
      properties: {
        folio: { bsonType: 'string' },
        titulo: { bsonType: 'string' },
        descripcion: { bsonType: 'string' },
        gravedad: { enum: ['Low', 'Medium', 'High', 'Critical'] },
        estado: { enum: ['Open', 'InProgress', 'Resolved', 'Closed'] },
        costo_estimado: { bsonType: 'number' },
        precio_contratado: { bsonType: 'number' },
        datos_congelados: { bsonType: 'object' },
        historial: {
          bsonType: 'array',
          items: {
            bsonType: 'object',
            required: ['estado_nuevo', 'comentario', 'fecha_registro'],
            properties: {
              estado_anterior: { bsonType: ['string', 'null'] },
              estado_nuevo: { bsonType: 'string' },
              usuario: { bsonType: 'string' },
              comentario: { bsonType: 'string' },
              fecha_registro: { bsonType: 'date' }
            }
          }
        }
      }
    }
  }
});
db.incidentes.createIndex({ folio: 1 }, { unique: true });
db.incidentes.createIndex({ estado: 1 });
db.incidentes.createIndex({ gravedad: 1 });

print("HaloMonitor MongoDB Schema and Collections Initialized Successfully.");
