# Tokoana - Point of Sales App

![1](./screenshots//1.png)

## About Tokoana

### Tokoana is a point of sales application, used to help cashiers or shop owners to manage the products being sold.

## Features

-   CRUD Product category, list & unit
-   Create transactions sales, stock in & stock out
-   Print transaction detail
-   History transaction
-   Multi role user admin & cashier

## Screenshots

![2](./screenshots/2.png)
![3](./screenshots/3.png)
![4](./screenshots/4.png)
![5](./screenshots/5.png)

## Library / Framework used

-   [Vue JS](vuejs.org)
-   [Inertia JS](https://inertiajs.com/)
-   [Laravel](laravel.com)
-   [pdfMake](pdfmake.github.io)
-   [ApexCharts](https://apexcharts.com/)
-   [SASS](sass-lang.com)

## Installation

1. Duplicate .env-example as .env

```
  cp .env-example .env
```

2. Generate key

```
  php artisan key:generate
```

3. Create and configure database in .env

```
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=point_of_sales_app
  DB_USERNAME=root
  DB_PASSWORD=root
```

4. Run migrate and seed

```
  php artisan migrate:fresh --seed
```

5. Run project

-   Development mode
    ```
      npm run dev
      php artisan serve
    ```
