import dotenv         from 'dotenv';
import express        from 'express';
import typeDefs       from '@graphql/typeDefs';
import resolvers      from '@graphql/resolvers';
import {ApolloServer} from "apollo-server-express";

dotenv.load();

const app = express();

const server = new ApolloServer({typeDefs, resolvers});

server.applyMiddleware({app});

app.listen(process.env.PORT || 3000, () => console.log("Service started..."));