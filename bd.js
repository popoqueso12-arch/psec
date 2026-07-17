require('dotenv').config();

const mysql = require('mysql2/promise');

const pool = mysql.createPool({
  host: process.env.DB_HOST || 'mysql-86c4508-ja13-1fe5.a.aivencloud.com',
  port: process.env.DB_PORT || 26767,
  user: process.env.DB_USER || 'avnadmin',
  password: process.env.DB_PASSWORD || 'default_password',
  database: process.env.DB_NAME || 'defaultdb',
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0,
  ssl: 'require', // SSL requerido por Aiven
  enableKeepAlive: true,
  keepAliveInitialDelayMs: 0
});

module.exports = pool;
