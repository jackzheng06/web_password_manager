import {gql} from 'apollo-server-express';

export const typeDefs = gql`
    type User {
        id: Int
        username: String
    }
    
    input CreateUserInput {
        username: String!
        password: String!
    }
    
    extend type Mutation {
        createUser(data: CreateUserInput!): User
    }
`;
