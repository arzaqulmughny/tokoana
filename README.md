# Tokoana - Point of Sales App

## About Tokoana

### Tokoana adalah sebuah aplikasi point of sales, digunakan untuk membantu kasir atau pemilik toko untuk me-manage produk-produk yang dijual.

## Features

-   CRUD Product category, list & unit
-   Create transactions sales, stock in & stock out
-   Print sale receipt, detail history & daftar riwayat transaksi berdasarkan tanggal
-   History transaction
-   Multi role user admin & cashier

## Library / Framework used

-   [Vue JS](vuejs.org)
-   [Inertia JS](https://inertiajs.com/)
-   [Laravel](laravel.com)
-   [pdfMake](pdfmake.github.io)
-   [ApexCharts](https://apexcharts.com/)
-   [SASS](sass.com)

## Install Instructions

### Requirements

-   [Composer]()
-   [NPM]()

1. Clone this repository

    ```sh
    git clone https://github.com/zaarza/point-of-sales-app
    ```

2. Download required packages

    ```sh
    cd point-of-sales-app/

    composer install
    npm install
    ```

3. Copy configuration example, then change the APP and DB configuration

    ```sh
    cp .env.example .env
    nano .env
    ```

4. Run application

    in development mode

    ```sh
    npm run dev
    php artisan serve
    ```

    or,
