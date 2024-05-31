# Pulling ubuntu image with a specific tag from the docker hub.
FROM ubuntu:22.04

# Updating the packages and installing cron and vim editor if you later want to edit your script from inside your container.
RUN apt-get update && apt-get install cron -y && apt-get install vim -y && apt-get install python3 -y && apt-get install python3-pip -y

# Install mariadb connector from CS repo
RUN apt install -y wget curl
RUN wget https://r.mariadb.com/downloads/mariadb_repo_setup
RUN echo "26e5bf36846003c4fe455713777a4e4a613da0df3b7f74b6dad1cb901f324a84  mariadb_repo_setup"  | sha256sum -c -
RUN chmod +x mariadb_repo_setup
RUN ./mariadb_repo_setup --mariadb-server-version="mariadb-10.6"
RUN apt-get install -y libmariadb3 libmariadb-dev

# Fix locales issues
RUN apt-get install --reinstall -y locales
RUN sed -i 's/# en_US.UTF-8 UTF-8/en_US.UTF-8 UTF-8/' /etc/locale.gen
RUN locale-gen en_US.UTF-8
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US
ENV LC_ALL en_US.UTF-8


RUN pip3 install python-slugify requests configparser mariadb

RUN mkdir /files

COPY ./UseEventFetch.py /files
COPY ./pyapiconf.ini /files
COPY ./cronfile /etc/cron.d/container_cronfile

RUN touch /var/log/cron.log
RUN crontab /etc/cron.d/container_cronfile

# Giving executable permission to script file.
# RUN chmod +x /script.sh

# Running commands for the startup of a container.
WORKDIR /files
CMD ["/bin/bash", "-c", "chmod 644 /etc/cron.d/container_cronfile && cron && tail -f /var/log/cron.log"]
