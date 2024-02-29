## mysql image is extended for testing purposes (in order to dump and load the database during e2e tests)
FROM mysql:8.0.36-debian

LABEL maintainer="Jérémy Riverain <riverainjeremy@gmail.com>"

SHELL ["/bin/bash", "--login", "-c"]

RUN apt update && apt install -y curl

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && apt-get install -y nodejs

COPY ./ /home

WORKDIR /home

RUN npm install

EXPOSE 3306 33060 80