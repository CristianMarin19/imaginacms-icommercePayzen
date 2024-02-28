# imaginacms-icommercepayzen (PaymentMethod)

## Install
```bash
composer require imagina/icommercepayzen-module=v8.x-dev
```

## Enable the module
```bash
php artisan module:enable Icommercepayzen
```

## Seeder

```bash
php artisan module:seed Icommercepayzen
```

## Configurations
	- site id 
    - signature key

INFO: https://payzen.io/lat/form-payment/quick-start-guide/establecer-dialogo-con-la-plataforma-de-pago.html

## Admin
Account: https://secure.payzen.lat/vads-merchant/

## URL Notifications
https://payzen.io/lat/form-payment/quick-start-guide/configurar-notificaciones.html

Confirmation URL:
https://mydomain/api/icommercepayzen/v1/confirmation

## Tests before Production
https://payzen.io/lat/form-payment/quick-start-guide/proceder-a-la-fase-de-prueba.html
