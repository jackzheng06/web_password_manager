import {resolvers as base}   from './base';
import {resolvers as user}   from './user';
import {resolvers as apiKey} from './apiKey';

const resolvers = [
    base,
    user,
    apiKey,
];

export default resolvers;
