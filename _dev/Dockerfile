# Build package
FROM node:20-alpine as package

WORKDIR /app

ADD package.json package.json

RUN npm install

## Build vuejs
FROM package as builder

WORKDIR /app
ADD . .
RUN npm run build-storybook


### Build clean docker image with only needed files
FROM nginx:stable-alpine as production

COPY --from=builder /app/storybook-static /usr/share/nginx/html
ADD ./.docker/default.conf /etc/nginx/conf.d/default.conf
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
