import Sequelize   from 'sequelize';
import {sequelize} from "@sequelize";

export const User = sequelize.define('user', {
    username: Sequelize.STRING,
    password: Sequelize.TEXT,
    salt: Sequelize.TEXT,
});

User.sync();