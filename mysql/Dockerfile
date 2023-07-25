## mysql image is extended for testing purposes (in order to dump and load the database during e2e tests)
FROM mysql:8.0.30-debian

LABEL maintainer="Jérémy Riverain <jeremy@geekco.fr>"

RUN apt update && apt install -y curl nodejs npm

COPY ./ /home

WORKDIR /home

RUN npm install

EXPOSE 3306 33060 80