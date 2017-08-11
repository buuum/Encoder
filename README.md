# Encoder
A simple library to encode and decode data in PHP. Crypt/Decrypt

[![Latest Stable Version](https://poser.pugx.org/buuum/encoder/v/stable)](https://packagist.org/packages/buuum/encoder)
[![license](https://img.shields.io/github/license/mashape/apistatus.svg?maxAge=2592000)](#license)
# Install

### System Requirements

You need PHP >= 5.5.0 to use Buuum\Encoder but the latest stable version of PHP is recommended.

### Composer

Buuum is available on Packagist and can be installed using Composer:

```
composer require buuum/encoder
```

### Manually

You may use your own autoloader as long as it follows PSR-0 or PSR-4 standards. Just put src directory contents in your vendor directory.


## How to use

###  initialize Secret key

```
$encoder = new Encoder($my_secret_key);
```

### Set Method (default 'AES-256-CBC')
openssl_get_cipher_methods valid method
```
$encoder->setMethod($method)
```

### Encode Data
```
$data = [
    'key' => 'value',
    'key2 => 'value 2'
];
$code = $encoder->encode($data);

// encode and return always the same result
$code = $encoder->encode($data, [], false);

```

###  Decode Data

```
$encoder->decode($code);
```

###  Expires Token
If you want that your secret hash expire 
```
$seconds = 10;
$code = $encoder->encode($data, ['expires' => $seconds]);
```

### Delay Token
If you want add delay hash to open
```
$seconds = 10;
$code = $encoder->encode($data, ['delay' => $seconds]);
```

### Exceptions
```php
$seconds = 10;
$code = $encoder->encode($data, ['delay' => $seconds]);

try{
    $data = $encoder->decode($code);
}catch(DelayException $e){
    $e->getDate(); // date active
}catch(ExpiresException $e){
    echo $e->getDate(); // date expiration
}catch (\Exception $e){
    echo $e->getMessage());
}
```


## LICENSE

The MIT License (MIT)

Copyright (c) 2017

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
