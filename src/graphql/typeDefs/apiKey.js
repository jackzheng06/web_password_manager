import {gql} from 'apollo-server-express';

export const typeDefs = gql`
    type ApiKey {
        body: String
    }

    input CreateApiKeyInput {
        username: String!
        password: String!
    }

    extend type Mutation {
        createApiKey(data: CreateApiKeyInput!): ApiKey
    }
`;
