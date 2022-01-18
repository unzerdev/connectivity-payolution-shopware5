# PolPaymentPayolution

## Introduction

Implements the Payolution payment workflow into shopware.

## Key Facts

Customer: [best it](http://bestit-online.de/)

Product Owner / Project Manager: [Johannes Terhürne](mailto:johannes.terhuerne@stit-online.de)

Tech Lead: [Martin Knoop](mailto:martin.knoop@bestit-online.de)

Wiki: [Confluence](https://bestit.atlassian.net/wiki/spaces/PAYOL)

GIT Flow: [GitFlow Workflow](https://www.atlassian.com/git/tutorials/comparing-workflows)

System: [Shopware 5.2+](https://en.shopware.com)

Credentials: Confluence / LastPass

## Development Setup

In order to run a shopware instance locally to develop or test features, you have to run the following steps:

#### Starting Vagrant
Before you start you have to install composer in the project root on your host system.
```bash
$ composer install
```

After that you can startup the vagrant with:
```bash
$ vagrant up
```
This could take some time! Perfect time for a coffee :)

#### Upload project files

Open `Tools > Deployment > Browse Remote Host`.

Create a new remote host called `Vagrant` and open it.

##### Tab: Connection

| Configuration Field | Value              |
| --------------------|--------------------|
| SFTP Host           | 192.168.33.10      |
| Port                | 22                 |
| Root path           | /home/vagrant/www/ |
| User name           | vagrant            |
| Auth type           | Password           |
| Password            | vagrant            |
| Save password       | Tick checkbox      |

##### Tab: Mappings

| Local Path        | Deployment Path | Web Path |
| ------------------|-----------------|----------|
| %PATH_TO_PROJECT% | /               | /        |


##### Tab: Excluded Paths

| Add Local Paths                               |
| ----------------------------------------------|
| %PATH_TO_PROJECT%/.vagrant/                   |
| %PATH_TO_PROJECT%/.idea/                      |
| %PATH_TO_PROJECT%/vendor/                     |

Upload all project files via `Tools > Deployment > Upload to Vagrant` or let the rsync handle it.

#### Connecting to vagrant
```bash
$ vagrant ssh
```
To connect to vagrant.

```bash
$ cd /home/vagrant/www
```
Change directory to project path.

#### Install composer
```bash
$ composer install
```

#### Hosts entry
If you already haven't done this add following `/etc/hosts` entry.

```text
192.168.33.10   local.dev.bestit-online.de
```


#### Build fully functional shopware instance

```bash
$ ./vendor/bin/phing build
```
This installs a shopware instance by configured version with listed features:
- Install Cron and SwagDemoDataDE Plugin
- Install some language packages necessary for language shops
- Install PolPaymentPayolution Plugin
- Create different language and subshops in order to configure PolPaymentPayolution to handle them
- Activate and assign payment method to standard shipping
- Clear shopware cache

You should now be able to browse the shop under `https://local.dev.bestit-online.de/`.

#### FileWatcher Service
As of a bug with the shopware smarty function "link" to resolve symlinks not correctly there is a service "iwatch"
installed which moves created, changed, deleted files under `/home/vagrant/www/src/PolPaymentPayolution/*`
automatically to the corresponding path within the shopware instance.
If your changes doesn't get synced check if this service is running.

### Debugging with PHPStorm

#### XDebug Configuration

Add the Shopware codebase to your include path under `Languages & Frameworks > PHP`:

   - Include path: %PATH_TO_SHOPWARE%

Go to the Toolbar->Run->Edit Configurations...
Add a new "PHP Remote Debug" and apply the following configuration:

   - Name: local.dev.bestit-online.de
   - Check "Filter debug connection by IDE key"
   - Server: *see instructions below*
   - IDE key(session id): PHPSTORM
   
You will need to add an appropriate Server which you can do by clicking the button with the three dots
which is placed next to the Server select box.

Now enter the following information:

   - Name: vagrant  
   - Host: local.dev.bestit-online.de
   - Port: 80
   - Debugger: Xdebug
   - Use path mappings: true
   - Paths
       - Path #1
           - File/Dir: %PATH_TO_PROJECT%/src/PolPaymentPayolution
           - Absolute Path: home/vagrant/www/html/engine/Shopware/Plugins/Community/Frontend/PolPaymentPayolution
       - Path #2
           - File/Dir: %PATH_TO_SHOPWARE%
           - Absolute Path: /home/vagrant/www/html

Once you have done so, create a copy if your just now created server configuration by clicking the stacked paper icon in the upper left corner.

Rename the copied configuration to "vagrant ssh" and edit the Port to 443.
   
You can now save the new server configurations, select in the previously created "PHP Remote Debug" on of your server configurations (which one doesn't matter) and save it.

You can either go to "Run->Debug" to start debugging or set the option "Run->Start Listening for PHP Debug Connections" and the use some XDebug helper like the [Chrome Extension](https://chrome.google.com/webstore/detail/xdebug-helper/eadndfjplgieldjbigjakmdgkmoaaaoc).


### Technical requirements
#### For the development process

Please ensure that the following tools are installed on your host system (minimum version specified)

- Virtualbox 5.2+
- Vagrant 2.0+ (as development machine)
- Docker 18.03+ (currently only for automated frontend testing with behat)

#### For the plugin
- Shopware 5.2+
- PHP 5.6+
- MySQL 5.6+

## (Software) Architecture 

The architecture is currently in a major change, so this section would be described later.

## Testing
### PHPUnit
Simply run in the vagrant
```bash
$ /home/vagrant/www/vendor/bin/phpunit
```

### Behat (with vagrant & docker)
To execute the behat tests you need to setup a working instance in the container:

Create the docker network for the container stack, only required once: 
```bash
$ docker network create -d bridge payolution_local
```

Start the container stack with a running bash
```bash
$ docker run --name payolution_mysql --network payolution_local -e MYSQL_DATABASE='shopware' -e MYSQL_ROOT_PASSWORD='root' -d mysql:5.7
$ docker run -it -v $(pwd):/app -e PIPELINES_BUILD_CONTAINER_XDEBUG_STATUS=1 -e HTML_WORKING_DIR=/www -e PIPELINES_BUILD_CONTAINER_ENVIRONMENT=php7.1 -w /app --network payolution_local bestitdocker/pipelines-build-container
```

Build the working instance in the stack:
```bash
$ ./vendor/bin/phing -Denvironment=pipelines -Dmysql.host=payolution_mysql build -debug
$ symfony server:start -d --port=80 --passthru=/www/shopware.php --document-root=/www
```

After this you can run behat:
```bash
$ ./vendor/bin/behat
```

## Build & Deployment
There is currently no automated deployment on any instance.
The build is done via Bitbucket Pipelines.
It sets up a complete Shopware instance and runs PHPUnit.
Behat is currently disabled because there are some major problems with Shopware which let them fail.

It generates a zip-artifact on every successful build on the master-sw5 branch and stores the artifact in the [Download-Section](https://bitbucket.org/best-it/polpaymentpayolution/downloads/).
The naming is "PolPaymentPayolution_mastervX.X.X.zip" The version at the end is determined from the plugin.json file.
**Be aware that the first change after a release must update the version so that older artifacts don't get polluted.**
