FROM node:20-bullseye

RUN apt-get update && apt-get install -y \
    libgtk2.0-0 \
    libgtk-3-0 \ 
    libgbm-dev \ 
    libnotify-dev \
    libgconf-2-4 \ 
    libnss3 \ 
    libxss1 \
    libasound2\
    libxtst6\
    xauth\
    xvfb

RUN wget https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb && \
    apt install -y ./google-chrome-stable_current_amd64.deb

ARG UID
ARG GID

RUN echo "uid: $UID, gid: $GID"

RUN if [ ${GID} -eq 1000 ]; then groupmod --new-name e2e node; else \
    if [ $(getent group ${GID} | cut -d: -f1) ]; then \
    printf "%s\n" ${GID} | xargs -I{} awk -F: '$3 == {}' /etc/group | cut -d: -f1 | xargs -n1 groupdel; \
    fi &&\
    groupadd -g ${GID} e2e; \
    fi && \
    if [ ${UID} -ne 1000 ]; then useradd -l -u ${UID} -g e2e e2e; else \
    usermod -l e2e node; fi; \
    mkdir -p /home/e2e/tests

WORKDIR /home/e2e/tests

COPY . .

RUN chown -R e2e:e2e /home/e2e

USER e2e

RUN npm install && npx cypress install

ENTRYPOINT [ "tail",  "-f",  "/dev/null" ]