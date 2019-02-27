import {ApiKeyController}   from "@controllers";

export const resolvers = {
    Mutation: {
        createApiKey: (_, {data}) => ApiKeyController.create(data)
    }
};
