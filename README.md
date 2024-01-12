
## Installation

Clone the repository

```bash
  git clone git@github.com:kolabapp/kolab.git
```
Make sure to have composer and yarn install locally.  
Install the required packages via Composer and Yarn

```bash
  composer install
```
```bash
  yarn install
```

Get the .env from one of our developers

Create a database *kolab* in your local environment and run this command

```bash
  php artisan migrate:fresh --seed
```

To generate the laravel key

```bash
  php artisan key:generate
```
