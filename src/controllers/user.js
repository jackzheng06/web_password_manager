import {User} from '@models';
import Boom   from 'boom';
import pbkdf2 from '@utilities/pbkdf2';
import uuidv4 from 'uuid/v4';

export class UserController {
    /**
     * Create a new user
     */
    static async create(data) {
        // First validate the user
        if (!data.username.match(/^[a-z0-9]+$/)) {
            throw Boom.badRequest("Username must be alphanumeric, and must only contain lowercase letters.");
        }
        // Validate the password
        if (data.password.length < 8) {
            throw Boom.badRequest("Password must be at least 8 characters long.");
        }
        let user = await User.findOne({where: {username: data.username}});
        if (user) {
            throw Boom.conflict("Username already exists.");
        }
        // Create a new user
        const salt = uuidv4();
        user = await User.create({
            username: data.username,
            password: await pbkdf2(data.password, salt),
            salt: salt,
        });

        return user;
    }
}
