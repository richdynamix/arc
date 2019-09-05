FROM digitonic1/php7.3-nginx:latest

# Install node npm
RUN curl -sL https://deb.nodesource.com/setup_11.x > /tmp/install-node.sh \
 && bash /tmp/install-node.sh \
 && apt-get update -qq -y \
 && DEBIAN_FRONTEND=noninteractive apt-get -qq -y --no-install-recommends install \
    nodejs \
    rsyslog \
    sudo \
    php-sqlite3 \
 \
 # Configure Node dependencies \
 && npm config set --global loglevel warn \
 && npm install --global marked \
 && npm install --global node-gyp \
 && npm install --global yarn \
 \
 # Install node-sass's linux bindings \
 && npm rebuild node-sass \
 \
 # Clean the image \
 && apt-get remove -qq -y php7.2-dev pkg-config libmagickwand-dev build-essential \
 && apt-get auto-remove -qq -y \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*


COPY ./tools/docker/etc/ /etc/
COPY ./tools/docker/usr/ /usr/

# Add the application
COPY . /app
WORKDIR /app

RUN container build
