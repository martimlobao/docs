# Example requests to our v3 person enrichment API

### Python

```python
import requests

base_url = "https://api.talentiq.co/v3/person?"
api_key = "api_key=YOUR_API_KEY"
payload = {"name": "sean thorne", "profile": "www.linkedin.com/in/seanthorne", "required": "emails.address"}
request_url = "%s%s" % (base_url, api_key)

response = requests.get(request_url, params=payload)
print response.json()

```

### Node
```javascript
const request = require('request')
const url = "https://api.talentiq.co/v3/person"
const paramObj = {
  name: "sean thorne",
  profile: "www.linkedin.com/in/seanthorne",
  api_key: "YOUR-API-KEY",
  required: "emails.address"
}

request({url: url, qs: paramObj}, function(error, response, body) {
  if (!error && response.statusCode == 200) {
    console.log(body)
  } else {
    console.log(`${response.statusCode}: ${response.statusMessage}`)
  }
})

```

### PHP
```php
<?php
    $ch = curl_init();
    $base_url = "https://api.talentiq.co/v3/person?";
    $data = array(
        'name' => 'sean thorne',
        'profile' => 'www.linkedin.com/in/seanthorne',
        'api_key' => 'YOUR_API_KEY',
        'required' => 'emails.address'
    );
    $request_url = $base_url . http_build_query($data);
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $request_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $data = curl_exec($ch);
    print_r($data);
    curl_close($ch);
?>
```
