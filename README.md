<h1 align="center">
    <a href="https://proto.utwente.nl">
        <img alt="Proto logo" src="public/images/logo/banner-regular.png" width="100%">
    </a>
    <br>
    S.A. Proto
</h1>

<p align="center"> 
    <b>The website of S.A. Proto</b>.<br> 
    The study association of BSc. Creative Technology and MSc. Interaction Technology.<br>
    <a href="https://github.com/saproto/saproto/issues">
        <img alt="issues badge" src="https://img.shields.io/github/issues/saproto/saproto?color=%2503b71a">
    </a>
    <a href="https://github.com/saproto/saproto/graphs/contributors">
        <img alt="contributors badge" src="https://img.shields.io/github/contributors/saproto/saproto?color=%2503b71a">
    </a>
    <img alt="open source badge" src="https://badges.frapsoft.com/os/v2/open-source.svg?v=103">
</p>

## Contributors

[Here](https://github.com/saproto/saproto/graphs/contributors) you can find the people that have contributed to the code to this project. But, let's not forget the other members of the [HYTTIOAOAc](https://www.proto.utwente.nl/committee/haveyoutriedturningitoffandonagain)!

## Prerequisites

This README is tailored to install on a Debian system. You may need to change commands to match your operating system.

Before following the installation instructions, you need to have a working installation of `php`, `git`, `npm`, `composer`. Depending on your operating system and plans for your development environment, you need to either set up a webserver and database or install docker using the [instructions below](#running-with-docker). JetBrains [PhpStorm IDE](https://www.jetbrains.com/help/phpstorm/installation-guide.html) is strongly recommended for development on this project, especially with the laravel plugin for proper code completion and reverences.

If you want to run a development environment in Docker **(you most likely do)** you can start at the [Running with Docker](#running-with-docker) section below. If you want to install without Docker you can find the instructions in the section [Running without Docker](#running-without-docker).

## Running with Docker
This repository can be run through Docker by using `docker compose`. The website can be run locally using the instructions below. For more information on installing and using Docker on your system check out their documentation at [docs.docker.com](https://docs.docker.com).

### WSL2 vs. HyperV
On Windows, Docker will now default to [WSL2](https://docs.microsoft.com/en-us/windows/wsl/install). However, if you prefer to not use WSL, you can turn it off going to Settings > General and disable the option "Use the WSL 2 based engine". Be aware that in this case you need to have **Windows Educational or Pro** installed, because Docker uses the Hyper-V engine instead of WSL. Which needs to be activated in the Windows "optional features" settings page.

To use WSL however you do need to install a distribution of your choice, such as Ubuntu, using `wsl --install -d <Distribution Name>`. Now you can continue the setup from within your WSL installation by starting your distribution using the command `wsl` and navigating to a directory inside your distribution. So, not a directory under `/mnt`, which are mounted from the Windows file system.

### Download
First you need to clone the repository somewhere on your system.

```
git clone git@github.com:saproto/saproto.git
```

### Setup
After installing Docker and cloning the repository the following instructions can be run in the terminal in the source folder of the project.

#### Configuration
Copy and rename `.env.docker.example` to `.env`.

```
cp .env.docker.example .env
```

After that, open the new `.env` file and set the `PERSONAL_PROTO_KEY` to your personal Proto key, which can be found/generated on the bottom of [your dashboard](https://www.proto.utwente.nl/user/dashboard) on the ***live*** Proto website.

#### Client-side dependencies
To install the client-side dependencies you'll need to run `npm install` to install all client-side dependencies.

To compile the project assets (JS/CSS) run `npm run dev` to compile once or `npm run watch` to keep checking for changes to scripts or stylesheets.

When adding a new library or client-side dependency through npm don't forget to require the scripts in `application.js` and the stylesheet in `vendor.scss`.

#### Initial application setup
```
docker compose up -d
docker compose exec app /bin/bash
composer install
php artisan key:generate
php artisan migrate --seed
```

If you are on WSL, you might run into some permission issues. While still inside the docker container, you can use `bash fix_permissions.sh` to change the permissions of the troublesome directories.

When you have finished the setup and Docker the following port will be exposed on localhost.

- `8080` = Website
- `8081` = PhpMyAdmin
- `8082` = [Mailhog](https://github.com/mailhog/MailHog)

You can sign in with the same Proto username you use on the ***live*** website and the password given to you during the database seeding. This user will have full admin rights on the ***local*** website.

### Useful commands

#### Start server
```
docker compose up -d
```

#### Stop server
```
docker compose stop
```

#### Access to PHP container
```
docker compose exec app /bin/bash
```

#### Nuke your database *(run in container)*
```
php artisan migrate:fresh --seed
```

### Code completion, style and static analysis
##### IDE-helper
When writing code it is useful to have tools such as code completion and linting in an integrated development environment (IDE). As mentioned before [PHPStorm](https://www.jetbrains.com/phpstorm/) is the recommended IDE for this project. To add additional code completion for Laravel you can run `composer ide-helper` in the docker container to let [Laravel-IDE-Helper](https://github.com/barryvdh/laravel-ide-helper) generate an `_ide_helper.php` file which tells PHPStorm what certain classes and function are, so it can perform proper code completion and show documentation.

##### PHP-CS-Fixer
Run `composer fix` in the docker container to fix stylistic errors in your code using [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer). This will also be done automatically when creating a pull-request or pushing to a branch with an open pull-request.

##### Larastan
There is also the option for static analysis of your code. Run `composer analyse` in the docker container to let [Larastan](https://github.com/nunomaduro/larastan) find any potential bugs in your code. 

## Debugging
### Xdebug
Xdebug has been added to the php runner in docker to aid you while debugging the website.
Xdebug enables breakpoints and step debugging which can easily be controlled from your IDE.
For this to work, you will have to set up your IDE correctly.

#### PhpStorm Configuration
To make the Xdebug server connect to PhpStorm you can follow the steps below.
1. Open the Settings menu by clicking File>Settings in the top right.
2. Under the PHP menu, click on the Servers entry.
3. Add a new server with the following parameters:
   - Name: `saproto-debug` (or any other name you like)
   - Host: `localhost`
   - Port: `8080`
   - Debugger: `Xdebug`
   - Use path mappings: `True`
     - Absolute path on the server: `/var/www`  
       **Note:** *only set this for the `saproto` folder under `Project files`*

    ![xdebug settings](public/images/readme/xdebug.png)
4. Click OK in the bottom right
5. In the dropdown to the left of the run button, choose `Edit Configurations...`
6. Add a new `PHP Remote Debug` configuration with the following parameters:
   - Name: `saproto-debug` (or any other name you like)
   - Server: `saproto-debug` (or the server name you chose in step 3)
   - IDE key(session id): `XDEBUG_ECLIPSE`
7. Click OK in the bottom right
8. Select the `saproto-debug` configuration in the dropdown menu on the left of the run button.
9. Press the green `Debug` button to start debugging.

### Browser configuration
If you just visit the local website normally, no debug session will be started.
A specific parameter has to be sent with the request to enable debugging.
Luckily, there are easy browser extensions to help you with this:
- [Xdebug Helper for Firefox](https://addons.mozilla.org/en-GB/firefox/addon/xdebug-helper-for-firefox/)
- [Xdebug Helper for Chrome](https://chrome.google.com/extensions/detail/eadndfjplgieldjbigjakmdgkmoaaaoc)
- [XDebugToggle for Safari](https://apps.apple.com/app/safari-xdebug-toggle/id1437227804?mt=12)

If you don't want to use a browser extension, you can also choose to add `?XDEBUG_SESSION=XDEBUG_ECLIPSE` after the url.
Do keep in mind that clicking an internal link will not add this parameter, so it has to be added every time you navigate to a new page.
When using a browser extension this will be automatically taken care of.

### Clockwork
[Clockwork](https://underground.works/clockwork) is a php dev tool in your browser. When running the website in debug mode you can access the clockwork debug page at <localhost:8080/clockwork>. The application has various debugging features such as timelines of runtime requests, database queries and client-metrics.


## Running without Docker (Legacy)
This is the old method of running a local environment. This is no longer recommended. Please first try running with Docker first.

### Download
First you need to clone the repository somewhere on your system.

```
git clone git@github.com:saproto/saproto.git
```

### Setup

#### configuration
In the repository you'll find a file called `.env.example`. Make a copy of this file called `.env`:

```
cp .env.example .env
```

This is the environment configuration. In this file you will establish your own database connection, e-mail credentials and whatnot. You will at least need to set the following options for your instance of the website to work.

* `APP_URL` is the URL (including https://, but **without** trailing slash) of your own website instance.
* `PRIMARY_DOMAIN` and `FALLBACK_COOKIE_DOMAIN` is only the [FQDN](https://en.wikipedia.org/wiki/Fully_qualified_domain_name) domain part of `APP_URL`.
* `DEBUG` should be set to `true` so you can see in detail what goes wrong if an error occurs.
* All the `DB_*` settings should be set to reflect your database set-up.
* `DEV_ALLOWED` is a comma separated list of IP addresses (IPv4 or IPv6) that may access your application. As long as `APP_ENV` is `local`, only whitelisted IPs are allowed to connect.
* `PERSONAL_PROTO_KEY` should be set to your personal Proto key, which can be found/generated on the bottom of your dashboard on the *live* Proto site.

You can skip all the other stuff (mailing API, Google API) until you need to work on the specific part of the website that uses them.

#### Initial application setup
Now we can initialize the project.

```
chmod +x update.sh
composer install
npm install
npm run dev
php artisan key:generate
```

After this is done we can install, and later update, our project using our own update utility.

```
./update.sh
```

This update utility will initialize the website for you. It can also be used to update to a newer version of our code.

Next on the list is to initialize the database. The following command rebuilds the database from scratch (**warning**: this will completely empty the database, only use when you are sure!) and fills it with some dummy data:

```
php artisan migrate:refresh --seed
```

The database seeder copies some non-sensitive data from the live website, add your user account and display the randomly generated password in the console and finally adds more dummy users, orders and the likes. If you need more dummy data, feel free to improve the database seeder.

Now you have set up your website correctly. The only thing that remains is pointing your web directory to the `public` directory of the website. This is where the front-facing controllers reside. The rest of the project is then shielded from public access. You could do this using symlinks. An example command on a webserver running DirectAdmin could look like this.
```
ln -s /home/user/domains/example.saproto.nl/saproto/public /home/user/domains/example.saproto.nl/public_html
```

That's it, everything should be up and running!