import Sequelize from "sequelize";
const SQLITE_PATH = 'data/main.db';

export const sequelize = new Sequelize('database', 'username', 'password', {
    dialect: 'sqlite',
    storage: SQLITE_PATH,
});
