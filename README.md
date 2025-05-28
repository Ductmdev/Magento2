## Installation

### Using docker

```bash
mkdir -p ~/Sites/bapec
cd $_

curl -s https://raw.githubusercontent.com/markshust/docker-magento/master/lib/template | bash

# If you're using linux
cp compose.dev-linux.yaml compose.dev.yaml

git clone --branch develop git@ssh.gitlab-new.bap.jp:BAPSoftware/division8/internal/training/magentodemo.git src

bin/start --no-dev
bin/copytocontainer --all
bin/composer install
bin/setup bap-ec.demo
bin/restart
```