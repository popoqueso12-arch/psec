const mysql = require('mysql2/promise');

const dbConfig = {
  host: 'mysql-86c4508-javiercarva913-1fe5.a.aivencloud.com',
  port: 26767,
  user: 'avnadmin',
  password: 'AVNS_ntoX9d2Nu632L7lQ-Ca',
  database: 'defaultdb',
  ssl: { rejectUnauthorized: false }
};

async function verificarColumnas() {
  let conexion;
  try {
    conexion = await mysql.createConnection(dbConfig);
    console.log('🔄 Verificando estructura de m3it3m...\n');

    // Obtener información de la tabla
    const [columnas] = await conexion.query("DESCRIBE m3it3m");

    console.log('📋 COLUMNAS EXISTENTES EN m3it3m:');
    console.log('════════════════════════════════════');

    const columnasExistentes = {};
    columnas.forEach(col => {
      columnasExistentes[col.Field] = true;
      console.log(`✓ ${col.Field} (${col.Type})`);
    });

    console.log('\n📊 VERIFICACIÓN DE COLUMNAS NECESARIAS:');
    console.log('════════════════════════════════════');

    const columnasNecesarias = [
      'nombre',
      'apellido',
      'tipo_doc',
      'cedula',
      'celular',
      'direccion',
      'empresa',
      'referencia',
      'tipo_cliente',
      'valor_factura'
    ];

    const columnasQueFaltan = [];
    const columnasQueExisten = [];

    columnasNecesarias.forEach(col => {
      if (columnasExistentes[col]) {
        console.log(`✅ ${col} - YA EXISTE`);
        columnasQueExisten.push(col);
      } else {
        console.log(`❌ ${col} - FALTA AGREGAR`);
        columnasQueFaltan.push(col);
      }
    });

    console.log('\n📌 RESUMEN:');
    console.log('════════════════════════════════════');
    console.log(`Columnas que ya existen: ${columnasQueExisten.length}`);
    console.log(`Columnas que faltan: ${columnasQueFaltan.length}`);

    if (columnasQueFaltan.length > 0) {
      console.log('\n⚠️ COLUMNAS A AGREGAR:');
      columnasQueFaltan.forEach(col => {
        console.log(`   - ${col}`);
      });

      console.log('\n🔧 Ejecutar: node tablasnuevas.js');
    } else {
      console.log('\n✅ ¡Todas las columnas ya existen! No hay nada que agregar.');
    }

  } catch (err) {
    console.error('❌ Error:', err.message);
  } finally {
    if(conexion) await conexion.end();
  }
}

verificarColumnas();
