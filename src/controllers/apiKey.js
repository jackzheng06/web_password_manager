import {ApiKey, User} from "@models";
import pbkdf2         from '@utilities/pbkdf2';
import Boom           from 'boom';
import uuidv4         from 'uuid/v4';

export class ApiKeyController {
    static async create(data) {
        // First find the user with username provided
        let user = await User.findOne({where: {username: data.username}});
        if (!user) {
            throw Boom.notFound("Username not found.");
        }
        let hashedPassword = await pbkdf2(data.password, user.salt);
        if (hashedPassword !== user.password) {
            throw Boom.unauthorized("Password invalid.");
        }
        return await ApiKey.create({
            body: uuidv4()
        });
    }
}