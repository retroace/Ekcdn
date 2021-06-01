## EKCDN

This package is a filesystem wrapper for laravel. This make it easy to perform actions like copy, move, upload.

### Introduction
This package is a wrapper around the flysystem adapter. This package adds the new flysystem driver ```ekcdn-storage```
any of the driver using this should have three configs around this apiKey, domainUrl and url. 

ApiKey         - Api Key of your project
url            - Domain or your subdomain name
domainUrl      - Domain of ekcdn https://ekcdn.ekbana.info


### Usage

Put these in the config/fileystems.php file and make sure to add the config url 
```
'ekcdn' => [
    'driver' => 'ekcdn-storage',
    'url' => env('EKCDN_ASSET_PREFIX_URL'),
    'apiKey' => env('EKCDN_API_KEY'),
    'domainUrl' =>  env('EKCDN_STORAGE_URL', "https://ekcdn.ekbana.info")
]
```

Replace your subdomain name with subdomain in the url. And put your key in ```EKCDN_API_KEY```.


And put your credential in .env file. Ideally these would look something like below

```
EKCDN_STORAGE_URL=http://ekcdn.ekbana.info
EKCDN_ASSET_PREFIX_URL=http://subdomain.devcdn.ekbana.com
EKCDN_API_KEY=RANDOM_API_KEY_GENERATED_FROM_PROJECT
```



### Uploading File 

To upload file from the config use
```
    Storage::disk('ekcdn')->putFileAs("/user/avatar", Request::file('my_image'), "my_image.png");
```
