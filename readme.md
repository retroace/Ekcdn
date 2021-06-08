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



### Uploading File 

To upload file from the config use
```
    Storage::disk('ekcdn')->putFileAs("/user/avatar", Request::file('my_image'), "my_image.png"); // return appropriate file name
    Storage::disk('ekcdn')->putFile("/user/avatar", Request::file('my_image')); // returns random image name with path
```
### Getting File Url 
To get file url use any of the following

```
    Storage::disk('ekcdn')->url("/user/avatar/my_image.png");
    Storage::disk('ekcdn')->path("/user/avatar/my_image.png");
```

## Exception Handling
When uploading any assets the exception thrown by the library extends **RuntimeException** of php. The exceptions thrown by system are:
```
Retroace\Storage\Exceptions\CorsException
Retroace\Storage\Exceptions\DomainNotAllowedException
Retroace\Storage\Exceptions\ExceededDiskUsageException
Retroace\Storage\Exceptions\FileNotAllowedException
Retroace\Storage\Exceptions\FileNotFoundException
Retroace\Storage\Exceptions\FilePostLimitException
Retroace\Storage\Exceptions\InvalidApiKeyException
Retroace\Storage\Exceptions\UnverifiedUserException
\Exception
```
