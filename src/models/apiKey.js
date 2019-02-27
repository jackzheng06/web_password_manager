import Sequelize   from 'sequelize';
import {sequelize} from "@sequelize";
import {User}      from "./user";

export const ApiKey = sequelize.define('apiKey', {
    body: Sequelize.DataTypes.TEXT,
});

ApiKey.belongsTo(User);

ApiKey.sync();
