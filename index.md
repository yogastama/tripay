## What is Yogastama/Tripay?
This package is un-official for tripay, already compatible with Composer, for more details please visit [My Documentation](https://yogastama.github.io/tripay/) or you can learn more from [Tripay Official Documentation](https://tripay.co.id/developer).

## Installation

```
composer require yogastama/tripay
```
## Configuration
 1. Use Provider in `config/app.php`
```
'providers'  =>  [ 
	...,
	Yogastama\Tripay\YbTripayServiceProvider::class,
]
```
 2. For use the package, you can use this class
```
use Yogastama\Tripay\Library\Tripay;
```
3. Prepare in .env file
```
API_KEY_TRIPAY={YOUR_API_KEY_TRIPAY}
API_KEY_PRIVATE_TRIPAY={YOUR_API_KEY_PRIVATE_TRIPAY}
TRIPAY_MERCHANT_CODE={YOUR_TRIPAY_MERCHANT_CODE}
```
4. APP_ENV in .env
```
APP_ENV=local
```
or
```
APP_ENV=production
```
If you use a production environment, our package will automatically use tripay's production environment, otherwise it will use tripay's testing environment

## Available Methods
1. generateSignature() | Create a signature for transaction | Return : String
2. getInstruksiPembayaran() | Take payment instructions from each channel | Return : String
3. requestTransaksi() | Request new transactions | Object
4. getDetailTransaksi() | Show a transaction | Object
5. getChannelPembayaran() | Take a list of payments that enter the open payment | Object
6. getTransaksi() | Get your transactions by page | Object


## generateSignature()
```
$tripay =  new  Tripay();
//* your invoice code, example here
$merchantRef =  'INV/' . time();
$amount = 100000;

$signature = $tripay->generateSignature([
	'merchant_ref'  => $merchantRef, //* required
	'amount'  => $amount //* required
]);

//* example result
//* 9f167eba844d1fcb369404e2bda53702e2f78f7aa12e91da6715414e65b8c86a
```
## getInstruksiPembayaran()
```
$tripay =  new  Tripay();
$instruksiPembarayan = $tripay->getInstruksiPembayaran([
	'code' => 'BRIVA', //* required
	'pay_code' => '',
	'amount' => '',
	'allow_html' => '' //* [1 = allow, 0 = not allowed] default = 1
])
```
result
```
[
   {
      "title":"Internet Banking",
      "steps":[
         "Login ke internet banking Bank BRI Anda",
         "Pilih menu <b>Pembayaran</b> lalu klik menu <b>BRIVA</b>",
         "Pilih rekening sumber dan masukkan Kode Bayar (<b>{{pay_code}}</b>) lalu klik <b>Kirim</b>",
         "Detail transaksi akan ditampilkan, pastikan data sudah sesuai",
         "Masukkan kata sandi ibanking lalu klik <b>Request</b> untuk mengirim m-PIN ke nomor HP Anda",
         "Periksa HP Anda dan masukkan m-PIN yang diterima lalu klik <b>Kirim</b>",
         "Transaksi sukses, simpan bukti transaksi Anda"
      ]
   },
   {
      "title":"Aplikasi BRImo",
      "steps":[
         "Login ke aplikasi BRImo Anda",
         "Pilih menu <b>BRIVA</b>",
         "Pilih sumber dana dan masukkan Nomor Pembayaran (<b>{{pay_code}}</b>) lalu klik <b>Lanjut</b>",
         "Klik <b>Lanjut</b>",
         "Detail transaksi akan ditampilkan, pastikan data sudah sesuai",
         "Klik <b>Konfirmasi</b>",
         "Klik <b>Lanjut</b>",
         "Masukkan kata sandi ibanking Anda",
         "Klik <b>Lanjut</b>",
         "Transaksi sukses, simpan bukti transaksi Anda"
      ]
   }
]
```

## requestTransaksi()
```
$requestTransaksi = $tripay->requestTransaksi([
	'method'  => 'BRIVA',
	'merchant_ref'  => 'YOUR_MERCHANT_REF',
	'amount'  => 50000,
	'customer_name'  =>  auth()->user()->nama,
	'customer_email'  =>  auth()->user()->email,
	'customer_phone'  =>  auth()->user()->no_telp,
	// 'callback_url' ?? '',
	'return_url'  =>  '',
	// 'expired_time' ?? '',
	'signature'  => $signature,
	'order_items'  =>  [
		[
			'sku'  =>  'PRODUK1,
			'name'  => 'Item paket 1',
			'price'  => 50000,
			'quantity'  =>  1,
			'product_url'  =>  route('product.show',  ['formasi'  =>  $product->id]),
			'image_url'  =>  route('product.show',  ['formasi'  =>  $product->id]),
		]
	]
]);
```

Result
```
array (
  'success' => true,
  'message' => '',
  'data' => 
  array (
    'reference' => 'T0001000000000000006',
    'merchant_ref' => 'INV345675',
    'payment_selection_type' => 'static',
    'payment_method' => 'BRIVA',
    'payment_name' => 'BRI Virtual Account',
    'customer_name' => 'Nama Pelanggan',
    'customer_email' => 'emailpelanggan@domain.com',
    'customer_phone' => '081234567890',
    'callback_url' => 'https://domainanda.com/callback',
    'return_url' => 'https://domainanda.com/redirect',
    'amount' => 50000,
    'fee_merchant' => 1500,
    'fee_customer' => 0,
    'total_fee' => 1500,
    'amount_received' => 48500,
    'pay_code' => '57585748548596587',
    'pay_url' => NULL,
    'checkout_url' => 'https://tripay.co.id/checkout/T0001000000000000006',
    'status' => 'UNPAID',
    'expired_time' => 1582855837,
    'order_items' => 
    array (
      0 => 
      array (
        'sku' => 'PRODUK1',
        'name' => 'Nama Produk 1',
        'price' => 500000,
        'quantity' => 1,
        'subtotal' => 500000,
        'product_url' => 'https://tokokamu.com/product/nama-produk-1',
        'image_url' => 'https://tokokamu.com/product/nama-produk-1.jpg',
      ),
    ),
    'instructions' => 
    array (
      0 => 
      array (
        'title' => 'Internet Banking',
        'steps' => 
        array (
          0 => 'Login ke internet banking Bank BRI Anda',
          1 => 'Pilih menu <b>Pembayaran</b> lalu klik menu <b>BRIVA</b>',
          2 => 'Pilih rekening sumber dan masukkan Kode Bayar (<b>57585748548596587</b>) lalu klik <b>Kirim</b>',
          3 => 'Detail transaksi akan ditampilkan, pastikan data sudah sesuai',
          4 => 'Masukkan kata sandi ibanking lalu klik <b>Request</b> untuk mengirim m-PIN ke nomor HP Anda',
          5 => 'Periksa HP Anda dan masukkan m-PIN yang diterima lalu klik <b>Kirim</b>',
          6 => 'Transaksi sukses, simpan bukti transaksi Anda',
        ),
      ),
    ),
    'qr_string' => NULL,
    'qr_url' => NULL,
  ),
)
```

## getDetailTransaksi()
```
$tripay =  new  Tripay;

$response = $tripay->getDetailTransaksi([
	'reference'  => $reference
]);
```
Result
```
array (
  'success' => true,
  'message' => '',
  'data' => 
  array (
    'reference' => 'T0001000000000000006',
    'merchant_ref' => 'INV345675',
    'payment_selection_type' => 'static',
    'payment_method' => 'BRIVA',
    'payment_name' => 'BRI Virtual Account',
    'customer_name' => 'Nama Pelanggan',
    'customer_email' => 'emailpelanggan@domain.com',
    'customer_phone' => '081234567890',
    'callback_url' => 'https://domainanda.com/callback',
    'return_url' => 'https://domainanda.com/redirect',
    'amount' => 1000000,
    'fee_merchant' => 1500,
    'fee_customer' => 0,
    'total_fee' => 1500,
    'amount_received' => 998500,
    'pay_code' => '57585748548596587',
    'pay_url' => NULL,
    'checkout_url' => 'https://tripay.co.id/checkout/T0001000000000000006',
    'status' => 'PAID',
    'paid_at' => '1582856000',
    'expired_time' => 1582855837,
    'order_items' => 
    array (
      0 => 
      array (
        'sku' => 'PRODUK1',
        'name' => 'Nama Produk 1',
        'price' => 500000,
        'quantity' => 1,
        'subtotal' => 500000,
        'product_url' => 'https://tokokamu.com/product/nama-produk-1',
        'image_url' => 'https://tokokamu.com/product/nama-produk-1.jpg',
      ),
      1 => 
      array (
        'sku' => 'PRODUK2',
        'name' => 'Nama Produk 2',
        'price' => 500000,
        'quantity' => 1,
        'subtotal' => 500000,
        'product_url' => 'https://tokokamu.com/product/nama-produk-2',
        'image_url' => 'https://tokokamu.com/product/nama-produk-2.jpg',
      ),
    ),
    'instructions' => 
    array (
      0 => 
      array (
        'title' => 'Internet Banking',
        'steps' => 
        array (
          0 => 'Login ke internet banking Bank BRI Anda',
          1 => 'Pilih menu <b>Pembayaran</b> lalu klik menu <b>BRIVA</b>',
          2 => 'Pilih rekening sumber dan masukkan Kode Bayar (<b>57585748548596587</b>) lalu klik <b>Kirim</b>',
          3 => 'Detail transaksi akan ditampilkan, pastikan data sudah sesuai',
          4 => 'Masukkan kata sandi ibanking lalu klik <b>Request</b> untuk mengirim m-PIN ke nomor HP Anda',
          5 => 'Periksa HP Anda dan masukkan m-PIN yang diterima lalu klik <b>Kirim</b>',
          6 => 'Transaksi sukses, simpan bukti transaksi Anda',
        ),
      ),
      1 => 
      array (
        'title' => 'Aplikasi BRImo',
        'steps' => 
        array (
          0 => 'Login ke aplikasi BRImo Anda',
          1 => 'Pilih menu <b>BRIVA</b>',
          2 => 'Pilih sumber dana dan masukkan Nomor Pembayaran (<b>57585748548596587</b>) lalu klik <b>Lanjut</b>',
          3 => 'Klik <b>Lanjut</b>',
          4 => 'Detail transaksi akan ditampilkan, pastikan data sudah sesuai',
          5 => 'Klik <b>Konfirmasi</b>',
          6 => 'Klik <b>Lanjut</b>',
          7 => 'Masukkan kata sandi ibanking Anda',
          8 => 'Klik <b>Lanjut</b>',
          9 => 'Transaksi sukses, simpan bukti transaksi Anda',
        ),
      ),
    ),
  ),
)
```
## getChannelPembayaran()
```
$tripay =  new Tripay;
$tripay->getChannelPembayaran()
```
Result
```
array (
  0 => 
  array (
    'group' => 'Virtual Account',
    'code' => 'BRIVA',
    'name' => 'BRI Virtual Account',
    'type' => 'direct',
    'fee_merchant' => 
    array (
      'flat' => 1500,
      'percent' => '0.00',
    ),
    'fee_customer' => 
    array (
      'flat' => 0,
      'percent' => '0.00',
    ),
    'total_fee' => 
    array (
      'flat' => 1500,
      'percent' => '0.00',
    ),
    'icon_url' => 'https://tripay.co.id/xxxxxxxxx.png',
    'active' => true,
  ),
)
```

## getTransaksi()
```
$tripay =  new Tripay;
$transactions = $tripay->getTransaksi($request->get('page',  1))
```
Result
```
array (
  'success' => true,
  'message' => 'Success',
  'data' => 
  array (
    0 => 
    array (
      'reference' => 'T015100000358440000',
      'merchant_ref' => 'INV123',
      'payment_selection_type' => 'static',
      'payment_method' => 'MYBVA',
      'payment_name' => 'Maybank Virtual Account',
      'customer_name' => 'Nama Customer',
      'customer_email' => 'emailcustomer@gmail.com',
      'customer_phone' => NULL,
      'callback_url' => NULL,
      'return_url' => NULL,
      'amount' => 153750,
      'fee_merchant' => 3750,
      'fee_customer' => 0,
      'total_fee' => 3750,
      'amount_received' => 150000,
      'pay_code' => 45649878666155,
      'pay_url' => NULL,
      'checkout_url' => 'https://tripay.co.id/checkout/T015100000358440000',
      'order_items' => 
      array (
        0 => 
        array (
          'sku' => NULL,
          'name' => 'T-Shirt',
          'price' => 150000,
          'quantity' => 1,
          'subtotal' => 150000,
        ),
      ),
      'status' => 'UNPAID',
      'note' => NULL,
      'created_at' => 1592381058,
      'expired_at' => 1592388303,
      'paid_at' => NULL,
    ),
  ),
  'pagination' => 
  array (
    'sort' => 'desc',
    'offset' => 
    array (
      'from' => 1,
      'to' => 1,
    ),
    'current_page' => 1,
    'previous_page' => NULL,
    'next_page' => NULL,
    'last_page' => 1,
    'per_page' => 25,
    'total_records' => 1,
  ),
)
```
