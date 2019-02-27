import {UserController} from "@controllers";

export const resolvers = {
    Mutation: {
        createUser: (_, {data}) => UserController.create(data),
    }
};
