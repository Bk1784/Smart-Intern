import 'dotenv/config';
import mysql from 'mysql2/promise';

async function testConnection() {
    try {
        console.log('Connecting to:', process.env.DATABASE_URL?.replace(/:[^:@]*@/, ':***@'));
        const connection = await mysql.createConnection(process.env.DATABASE_URL!);
        console.log('Connected successfully!');

        const [rows] = await connection.query('SHOW TABLES');
        console.log('Tables:', rows);

        await connection.end();
    } catch (err) {
        console.error('Connection failed:', err);
    }
}

testConnection();