import pbkdf2 from 'pbkdf2';
import util from 'util';

const pbkdf2Async = util.promisify(pbkdf2.pbkdf2);

export default async (password, salt) => (await pbkdf2Async(password, salt, 1, 32, 'sha512')).toString('hex');
