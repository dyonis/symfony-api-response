# ApiResponse
## Usage in controllers:
### Success response
```php
return (new ApiResponse())
  ->setData(['product' => $product])
  ->setMessage('Successfully saved');
```

### Redirect response
```php
return (new ApiResponse())
  ->apiRedirect('https://some.url', 302);
```

### Error response
```php
$response = new ApiResponse();
$response->addErrors(['Error 1', 'Error 2'])
$response->addError('Error 3');
$response->addError('Error 4', 'Field');

return $response
```
