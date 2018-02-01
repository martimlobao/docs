# REST API v3 

1. [Quick Start](#quick-start) 
1. [Requests](#requests) 
1. [Example Requests](#example-requests) 
1. [Response](#response) 


# Quick Start


***You first need an API key for authentication. Please visit [People Data Labs](https://www.peopledatalabs.com/request-api-key) or email [Support](mailto:support@peopledatalabs.com) to get a trial API key***


**Look up by name and email and require an email in response**

```bash
https://api.talentiq.co/v3/person?api_key=YOUR-API-KEY&name=sean thorne&email=sean@talentiq.co&required=emails.address
```

**Look up by name, social profile url, and location and require an email and 1 or more social profile URL's in response**

```bash
https://api.talentiq.co/v3/person?api_key=YOUR-API-KEY&name=sean thorne&profile=https://www.linkedin.com/in/seanthorne&location=san francisco&required=emails.address AND profiles.url
```
 
 

# Requests 

Example: 

```bash
https://api.talentiq.co/v3/person?api_key=YOUR-API-KEY&name=larry page&email=larry.page@gmail.com
```

Endpoint (https required):

```bash
https://api.talentiq.co/v3/person
```

Method: `GET`


### URL Params:
  
**Required**  

| Parameter Name | Description | Example |
|-----------|---------|---------|  
| `api_key` | Your API Key | `12345` |


**Optional** 

| Parameter Name | Description | Example |
|-----------|---------|---------| 
| `name` | The person's full name, at least first and last. | `Jennifer C. Jackson` |
| `location` | The location in which a person lives. Can be anything from a street address to a country name | `Medford, OR USA` |
| `company` | A name, website, or social url of a company where the person has worked | `Amazon Web Services` |
| `school` | A name, website, or social url of a university or college the person has attended | `university of iowa` |
| `phone` | A phone number the person has used | `+1 541-672 2910` |
| `email` | An email the person has used | `renee.c.paulsen1959@yahoo.com` |  
| `profile` | A social profile the person has used. | `https://linkedin.com/in/seanthorne` |  
| `titlecase` | description: set to `true` for capitalization | true |  
| `min_likelihood ` | minimum score accepted, exclusive | 6 |  
| `required ` | a string describing the data a response must have to be returned as a 200 | emails AND locations |  




 
 

### API Key
Pass the API key using a the `api_key` param:  

```
https://api.talentiq.co/v3/person?api_key=YOUR-API-KEY
```

### Key Identifier Params
**The more parameters included in the request, the better the probability of finding a match:**   

```bash
profile=https://www.linkedin.com/in/tlytle&email=larry.page@gmail.com
``` 
Is better than

```bash
profile=https://www.linkedin.com/in/tlytle
``` 

**There is no limit on the number of parameters that can be added to a request:**   

```bash
profile=https://www.linkedin.com/in/tlytle \
    &email=larry.page@gmail.com \
    &email=alternate.email@gmail.com \
    &company=Google \
        &name=larry page
``` 

**Data in a request should represent one unique person:**   

This is good: 

```bash
email=larry.page@gmail.com&name=larry page
``` 

This is bad: 

```bash
email=larry.page@gmail.com&name=Albert Einstein
``` 

**Requests must contain at least one of name, email, profile; Request made with name must contain one or more extra parameters:**   

The following are good: 

```bash
email=larry.page@gmail.com
profile=https://www.linkedin.com/in/tlytle
name=larry page&company=google
name=larry page&location=Mountain View, California
``` 

The following are bad: 

```bash
name=larry page
company=google
company=google&location=Mountain View, California
``` 
### min_likelihood

Set the `min_likelihood` param to the minimum score you will accept minus `1`, as it is an exclusive value. Meaning, if you want to accept profiles with a `7` or higher, set your requests to `min_likelihood=6`. If the `likelihood` score for that profile is below or equal to `min_likelihood` then you will receive a `404`. 


### titlecase

By default all data is returned in lowercase. If you prefer fields such as `names` and `titles` to be returned with some capitalization then set `titlecase=true`.


### Required Param

The required paramater ensures that you only get charged for responses which have the data fields you're interested in. The value is formatted as a boolean statements

Examples: 
    
1. `<field>:<value>` e.g. `required=profiles.network:linkedin` - Response must contain a linkedin url
2. `<field>` e.g. `required=profiles.url` - Response must contain 1 or more profiles.url fields
3. `<field><comparator><value>` e.g. `skills.name>=10` - Response must contain 10 or more skills


+ The required filter supports all fields within the person data structure
+ Statements can be combined via boolean syntax 

```
required=emails.address AND (experience.title.name>2 OR education.school.name:stanford university)
```

+ For ease of use you can convert `emails.address` to  `emails`. This is true for all fields in the person object. 


### Full URL Example

```bash
https://api.talentiq.co/v3/person?api_key=YOUR-API-KEY&name=larry page&email=larry.page@gmail.com&required=profiles.network:linkedin
``` 




# Example Requests 

### Python 

```python 
import requests

base_url = "https://api.talentiq.co/v3/person"
api_key = "YOUR_API_KEY"
payload = {"email": "sean@talentiq.co", "name": "sean thorne", "api_key": api_key}

response = requests.get(base_url, params=payload)
print response.url
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
 
 
 
 
# Response 

Each query will return a single person.

If the call is successful the following fields will be returned. 


### Response Fields
Response Fields | Definition
--- | ---
**likelihood** _integer_ | The calculated likelihood score
**errors** _string_ | Error message
**message** _string_ | Status message ex. Success
**status** _integer_ | status code
**data** _object_ | person object

### Likelihood:

When a successful response (`200`) is received, it means we have been able to match the request parameters with a profile in our dataset. A `200` indicates with great confidence that the response object you are receiving contains the correct person profile with one notable exception:
the request does not include an email or social profile url 

For example:

_If a request is made with only `name` and `location`_

You will receive a `200` with a likelihood of `10`, however, the profile will contain only the `name` and `location` objects, and no additional person information. This is a side effect of our matching logic. In order to avoid receiving incomplete profiles, you should use the `required` param to specify the data fields that you require in a response, see more on the `required` param below.

_If a request is made with `name` and `email`_

If a request is made with `name` and `email` and a match is found, the response is considered highly likely to be the correct person and the `likelihood` score will be `7`. This is due to uniqueness of an `email` as a key identifier. Of course it is possible that an email has been used by multiple people, which is why the `likelihood` score is `7` rather than `10`. Any additional identifiers added to this request that match correctly, such as `location` or `company`, will add 1 point respectively to the `likelihood` score. 


### Response Codes:

Response Codes |     Status
--- | ---
Success | 200 (match)
Bad Request | 400
Invalid API Key | 403
Data Not Found | 404 (no match)
Over API Request Limit | 429
Internal Server Error | 5XX


### Person Object


Person Object Fields | Definition
--- | ---
**education**  | An array of schools attended, majors, and degrees
**emails** | An array of associated emails
**industries** | An array of associated industries
**experience** | An array of current company, past companies, titles, and locations
**interests** | An array of listed interests
**locations** | An array of known locations
**names** | An array of known names
**phone_numbers** | An array of known phone numbers
**photos** | An array of photo urls
**profiles** | An array of social profile urls ex. twitter, linkedin, facebook
**skills** | An array of listed skills
**websites** | An array of known websites

### Full person object schema:

```json
{
  "$schema": "http://json-schema.org/draft-04/schema#",
  "type": "object",
  "properties": {
    "education": {
      "items": {
        "properties": {
          "is_current": [
            "boolean",
            "null"
          ],
          "degree": {
            "type": [
              "string",
              "null"
            ]
          },
          "end_date": {
            "type": [
              "string",
              "null"
            ]
          },
          "locations": {
            "type": "array",
            "items": {
              "type": "object",
              "properties": {
                "continent": {
                  "type": [
                    "string",
                    "null"
                  ]
                },
                "country": {
                  "type": [
                    "string",
                    "null"
                  ]
                },
                "geo": {
                  "type": [
                    "string",
                    "null"
                  ]
                },
                "type": {
                  "type": [
                    "string",
                    "null"
                  ]
                },
                "is_primary": {
                  "type": [
                    "null",
                    "boolean"
                  ]
                },
                "locality": {
                  "type": [
                    "string",
                    "null"
                  ]
                },
                "name": {
                  "type": [
                    "string",
                    "null"
                  ]
                },
                "po_box": {
                  "type": [
                    "string",
                    "null"
                  ]
                },
                "postal_code": {
                  "type": [
                    "string",
                    "null"
                  ]
                },
                "region": {
                  "type": [
                    "string",
                    "null"
                  ]
                },
                "street_address": {
                  "type": [
                    "string",
                    "null"
                  ]
                }
              }
            }
          },
          "majors": {
            "type": "array",
            "items": {
              "type": "string"
            }
          },
          "degrees": {
            "type": "array",
            "items": {
              "type": "string"
            }
          },
          "minors": {
            "type": "array",
            "items": {
              "type": "string"
            }
          },
          "school": {
            "type": "object",
            "properties": {
              "location": {
                "type": [
                  "string",
                  "null"
                ]
              },
              "name": {
                "type": [
                  "string",
                  "null"
                ]
              },
              "website": {
                "type": [
                  "string",
                  "null"
                ]
              },
              "profiles": {
                "type": "array",
                "items": {
                  "type": "string"
                }
              }
            }
          },
          "start_date": {
            "type": [
              "string",
              "null"
            ]
          }
        }
      },
      "type": "array"
    },
    "emails": {
      "type": "array",
      "items": {
        "type": "object",
        "properties": {
          "address": {
            "type": "string"
          },
          "is_primary": {
            "type": [
              "null",
              "boolean"
            ]
          },
          "type": {
            "type": [
              "string",
              "null"
            ]
          },
          "domain": {
            "type": [
              "string",
              "null"
            ]
          },
          "local": {
            "type": [
              "string",
              "null"
            ]
          }
        }
      }
    },
    "industries": {
      "type": "array",
      "items": {
        "type": "object",
        "properties": {
          "is_primary": {
            "type": [
              "null",
              "boolean"
            ]
          },
          "name": {
            "type": "string"
          }
        }
      }
    },
    "experience": {
      "items": {
        "properties": {
          "is_current": [
            "boolean",
            "null"
          ],
          "company": {
            "type": "object",
            "properties": {
              "industry": {
                "type": [
                  "string",
                  "null"
                ]
              },
              "location": {
                "type": [
                  "string",
                  "null"
                ]
              },
              "name": {
                "type": [
                  "string",
                  "null"
                ]
              },
              "size": {
                "type": [
                  "string",
                  "null"
                ]
              },
              "website": {
                "type": [
                  "string",
                  "null"
                ]
              },
              "profiles": {
                "type": "array",
                "items": {
                  "type": "string"
                }
              }
            }
          },
          "end_date": {
            "type": [
              "string",
              "null"
            ]
          },
          "locations": {
            "type": "array",
            "items": {
              "type": "object",
              "properties": {
                "continent": {
                  "type": [
                    "string",
                    "null"
                  ]
                },
                "country": {
                  "type": [
                    "string",
                    "null"
                  ]
                },
                "geo": {
                  "type": [
                    "string",
                    "null"
                  ]
                },
                "type": {
                  "type": [
                    "string",
                    "null"
                  ]
                },
                "is_primary": {
                  "type": [
                    "null",
                    "boolean"
                  ]
                },
                "locality": {
                  "type": [
                    "string",
                    "null"
                  ]
                },
                "name": {
                  "type": [
                    "string",
                    "null"
                  ]
                },
                "po_box": {
                  "type": [
                    "string",
                    "null"
                  ]
                },
                "postal_code": {
                  "type": [
                    "string",
                    "null"
                  ]
                },
                "region": {
                  "type": [
                    "string",
                    "null"
                  ]
                },
                "street_address": {
                  "type": [
                    "string",
                    "null"
                  ]
                }
              }
            }
          },
          "start_date": {
            "type": [
              "string",
              "null"
            ]
          },
          "title": {
            "type": "object",
            "properties": {
              "functions": {
                "type": "array",
                "items": {
                  "type": [
                    "string",
                    "null"
                  ]
                }
              },
              "levels": {
                "type": "array",
                "items": {
                  "type": [
                    "string",
                    "null"
                  ]
                }
              },
              "name": {
                "type": [
                  "string",
                  "null"
                ]
              }
            }
          }
        }
      },
      "type": "array"
    },
    "interests": {
      "items": {
        "type": "object",
        "properties": {
          "name": {
            "type": "string"
          }
        }
      },
      "type": "array"
    },
    "locations": {
      "type": "array",
      "items": {
        "type": "object",
        "properties": {
          "continent": {
            "type": [
              "string",
              "null"
            ]
          },
          "country": {
            "type": [
              "string",
              "null"
            ]
          },
          "geo": {
            "type": [
              "string",
              "null"
            ]
          },
          "type": {
            "type": [
              "string",
              "null"
            ]
          },
          "is_primary": {
            "type": [
              "null",
              "boolean"
            ]
          },
          "locality": {
            "type": [
              "string",
              "null"
            ]
          },
          "name": {
            "type": [
              "string",
              "null"
            ]
          },
          "po_box": {
            "type": [
              "string",
              "null"
            ]
          },
          "postal_code": {
            "type": [
              "string",
              "null"
            ]
          },
          "region": {
            "type": [
              "string",
              "null"
            ]
          },
          "street_address": {
            "type": [
              "string",
              "null"
            ]
          }
        }
      }
    },
    "names": {
      "items": {
        "type": "object",
        "properties": {
          "clean": {
            "type": [
              "string",
              "null"
            ]
          },
          "first_name": {
            "type": [
              "string",
              "null"
            ]
          },
          "is_primary": {
            "type": [
              "boolean",
              "null"
            ]
          },
          "last_name": {
            "type": [
              "string",
              "null"
            ]
          },
          "middle_name": {
            "type": [
              "string",
              "null"
            ]
          },
          "name": {
            "type": [
              "string",
              "null"
            ]
          },
          "pedigree": {
            "type": [
              "string",
              "null"
            ]
          },
          "suffix": {
            "type": [
              "string",
              "null"
            ]
          },
          "title": {
            "type": [
              "string",
              "null"
            ]
          }
        }
      },
      "type": "array"
    },
    "phone_numbers": {
      "type": "array",
      "items": {
        "type": "object",
        "properties": {
          "E164": {
            "type": [
              "string",
              "null"
            ]
          },
          "area_code": {
            "type": [
              "string",
              "null"
            ]
          },
          "country_code": {
            "type": [
              "string",
              "null"
            ]
          },
          "extension": {
            "type": [
              "string",
              "null"
            ]
          },
          "is_primary": {
            "type": [
              "null",
              "boolean"
            ]
          },
          "national_number": {
            "type": [
              "string",
              "null"
            ]
          },
          "number": {
            "type": "string"
          },
          "type": {
            "type": [
              "string",
              "null"
            ]
          }
        }
      }
    },
    "photos": {
      "items": {
        "type": "object",
        "properties": {
          "source": {
            "type": [
              "string",
              "null"
            ]
          },
          "url": {
            "type": "string"
          }
        }
      },
      "type": "array"
    },
    "profiles": {
      "type": "array",
      "items": {
        "type": "object",
        "properties": {
          "aliases": {
            "type": "array",
            "items": {
              "type": "string"
            }
          },
          "ids": {
            "type": "array",
            "items": {
              "type": "string"
            }
          },
          "clean": {
            "type": [
              "string",
              "null"
            ]
          },
          "is_active": {
            "type": [
              "null",
              "boolean"
            ]
          },
          "network": {
            "type": [
              "string",
              "null"
            ]
          },
          "url": {
            "type": [
              "string",
              "null"
            ]
          },
          "username": {
            "type": [
              "string",
              "null"
            ]
          }
        }
      }
    },
    "skills": {
      "items": {
        "type": "object",
        "properties": {
          "name": {
            "type": "string"
          }
        }
      },
      "type": "array"
    },
    "websites": {
      "items": {
        "type": "object",
        "properties": {
          "is_primary": {
            "type": [
              "boolean",
              "null"
            ]
          },
          "type": {
            "type": [
              "string",
              "null"
            ]
          },
          "url": {
            "type": "string"
          },
          "domain": {
            "type": "string"
          }
        }
      },
      "type": "array"
    }
  }
}
```


