import {gql} from 'apollo-server-express';

export const typeDefs = gql`    
    type Query {
        base: Boolean
    }
    
    type Mutation {
        base: Boolean
    }
`;