# REST API v4
 

### Overview 

This document describes the People Data Labs v4 REST API. Canonical datasets used to supplement the person dataset can be found in the [data directory](https://github.com/peopledatalabs/docs/tree/master/data). Example profiles and API responses can be found in the [examples directory](https://github.com/peopledatalabs/docs/tree/master/examples), and their corresponding schemas can be found in the [schemas directory](https://github.com/peopledatalabs/docs/tree/master/schemas). 

If you have any problems or requests, please contact your account manager, or email us at <a href="mailto:support@peopledatalabs.com">support@peopledatalabs.com</a> 

1. [Overview](#overview) 
1. [Introduction](#introduction) 
1. [Endpoint](#endpoint) 
1. [Versioning](#versioning) 
1. [Authentication](#authentication) 
1. [Rate Limiting](#rate-limiting) 
1. [Requests](#requests) 
1. [Parameters](#parameters) 
1. [Required Parameter](#required-parameter) 
1. [Likelihood](#likelihood) 
1. [Response](#response) 
1. [Errors](#errors) 


### Introduction  

The API is designed to enrich information on a single person. 

The API is organized around [REST](https://en.wikipedia.org/wiki/Representational_state_transfer) and uses HTTP response codes to indicate API errors. All API responses, including errors, are returned in [JSON](http://www.json.org). 


### Endpoint  

The API resides at `api.peopledatalabs.com`. All API requests must be made over [HTTPS](https://en.wikipedia.org/wiki/HTTPS). Calls made over plain HTTP will fail. API requests without authentication will also fail.

```curl 
https://api.peopledatalabs.com
```

The `v4` API version has a single resource, `person`, at `/v4/person` 

```curl 
curl -X GET \
  'https://api.peopledatalabs.com/v4/person'
```

All requests should be made via `GET`. 


### Versioning 

API versions are specified in the url. Older versions of the API will continue to be supported. 
 
When we make backwards-incompatible changes to the API or dataset, we'll create a new version. The most up to date version of the API is `v4`. The next version of the API will be `v5`. We perform batch data builds on a monthly basis. We don't perform any streaming updates to the dataset between data builds. Each version of the API will use the most recent, up-to-date data build.  

As can be seen in the [Example Person Data Model](https://github.com/peopledatalabs/docs/blob/master/examples/person.json), all data fields are stored as a list of objects. Data builds are ran approximately every 2 months. After a data build is ran, barring any significant update that breaks existing functionality in the API, the newly built data will be returned in existing versions of the API. Therefore, the dataset is not completely static; the number of fields/data points a specific profile has may increase/decrease through time.    

In a single API version, the number of canonical values for `phone_numbers.type`, `locations.type`, `emails.type`, `profiles.network`, `industries.name`, and `locations` may increase, but the original values will not change. For example, in `v4`, a github profile in the `profiles` field's `network` field will always have the value `github`, yet if a social network which is not currently in the dataset, e.g. `yelp.com` is added, `yelp` would be added as a potential to the [list of canonical social network types](https://github.com/peopledatalabs/docs/blob/master/data/profiles_network.txt). The most up-to-date list of canonical values for these fields can always be found in [github.com/peopledatalabs/docs/tree/master/data](https://github.com/peopledatalabs/docs/tree/master/data)    

On the other hand, the canonical `education.school` data, and the canonical `experience.company` data may change slightly within the span of a single API version. 



### Authentication 

There are two ways to authenticate requests to `v4`.  

**In URL** 

```curl  
curl -X GET \
  'https://api.peopledatalabs.com/v4/person?api_key=xxxx'
``` 

**In Header** 

```curl  
curl -X GET \
  'https://api.peopledatalabs.com/v4/person' \ 
  -H 'X-Api-Key: xxxx'
``` 

Requests made with an invalid API Key will return a `401` error. If you need any assistance here, would like to request a new API key, or delete your existing key, please contact your account manager or reach out to us at <a href="mailto:support@peopledatalabs.com">support@peopledatalabs.com</a>


### Rate Limiting

Rate limits are defined on a per-minute basis. We use a fixed-window rate limiting strategy, so if your API key's rate limit is `5000` requests per minute, those `5000` api calls can be made at any interval within the 60 second window. 


```curl 
curl -i -X GET \
  'https://api.peopledatalabs.com/v4/person' \
  -H 'X-Api-Key: xxxx'
  
HTTP/2 404 
date: Tue, 16 Jan 2018 17:38:18 GMT
content-type: application/json
content-length: 4731
server: nginx/1.11.13
x-totallimit-limit: 10000000
x-totallimit-remaining: 9723340
x-ratelimit-limit: 10000
x-ratelimit-remaining: 9999
x-ratelimit-reset: 1516124358
retry-after: 59
```  


| Header Name | Description | 
|-----------|---------| 
| `x-totallimit-limit` | The maximum number of API requests which return a `200` you're able to make. |
| `x-totallimit-remaining` | The number of API requests which return a `200` you have remaining |
| `x-ratelimit-limit` | The maximum number of requests you're permitted to make per minute. | 
| `x-ratelimit-remaining` | The number of requests remaining in the current rate limit window. |
| `x-ratelimit-reset` |     The time at which the current rate limit window resets in UTC epoch seconds. |
| `retry-after` | The number of seconds left until the current rate limit window resets. |

If your account has a limit on the number of `200` API calls you're able to make, once `x-totallimit-remaining` reaches 0, all succeeding api requests will return `403` errors, and once `x-ratelimit-remaining` reaches 0, all succeeding requests made will return `429` errors, until the current rate limit window resets. If you'd like to increase your account's `x-totallimit-limit` or `x-ratelimit-limit`, please contact your account manager or reach out to us at <a href="mailto:support@peopledatalabs.com">support@peopledatalabs.com</a>

 
### Requests 

When a matching person is returned, the HTTP Response code will be `200`, and when no matching person is found or returned, the HTTP Response code will be a `404`. 

When an API request is executed, the queried data points are preprocessed and built into a query, which is then executed against our production dataset. If the query yields 1 or more matching persons from the dataset, the person returned in the API response is the one who is most likely to be the same person as the person requested. This degree of confidence is represented by the `likelihood` field in the API response. The `likelihood` field is an integer between `0` and `10` that represents how confident we are the person returned is the same as the person requested. The minimum likelihood score a response must possess in order to return a `200` can be controlled in the api request using the `min_likelihood` param, [described in the parameters section below](#parameters). 

Query parameters should be separated by an ampersand `&`. 

```curl
GET /v4/person?api_key=xxxx&min_likelihood=6&required=emails AND experience&profile=http://linkedin.com/in/seanthorne 
```

```curl 
GET /v4/person?api_key=xxxx&company=People Data Labs&company=Hallspot&email=sean.thorne@talentiq.co 
```

### Parameters  

Data points on the queried person are added as key/value pairs to the query string of a `v4` request.  

A number of key/values can also be added to the query string of a `v4` request to control the data points a response must contain to be returned as a `200`, and also to control certain parts of the API response schema.  

All query parameters listed below are optional. 

**Data Parameters** 

The following parameters can be used to specify information on the requested person. Adding more data points to a request increases the probability of a `200` response, and further, will increase the accuracy of the response's likelihood score.

| Parameter Name | Description | Example |
|-----------|---------|---------| 
| `name` | The person's full name, at least first and last. | `Jennifer C. Jackson` |
| `first_name` | The person's first name | `Jennifer` |
| `last_name` | The person's last name | `Jackson` |
| `middle_name` | The person's middle  | `Cassandra` |
| `location` | The location in which a person lives. Can be anything from a street address to a country name | `Medford, OR USA` |
| `locality` | A locality in which the person lives | `Boise` |
| `region` | A state or region in which the person lives | `Idaho` |
| `country` | A country in which the person lives | `United States` |
| `company` | A name, website, or social url of a company where the person has worked | `Amazon Web Services` |
| `school` | A name, website, or social url of a university or college the person has attended | `university of iowa` |
| `phone` | A phone number the person has used | `+1 541-672 2910` |
| `email` | An email the person has used | `renee.c.paulsen1959@yahoo.com` |  
| `email_hash` | A sha256 email hash | `e206e6cd7fa5f9499fd6d2d943dcf7d9c1469bad351061483f5ce7181663b8d4 ` |  
| `profile` | A social profile the person has used. [List of available social profiles](https://github.com/peopledatalabs/docs/blob/master/data/profiles_network.txt) | `https://linkedin.com/in/seanthorne` |  

The minumum combination of data points a request must contain in order to have a possibility of returning a `200` response are:  

```curl 
profile OR email OR phone OR ( 
    (
        (first_name AND last_name) OR name) AND 
        (locality OR region OR company OR school OR location)
    )
```


**Examples** 


```curl 
curl -X GET -G \
  'https://api.peopledatalabs.com/v4/person' \
  -H 'X-Api-Key: xxxx' \ 
  --data-urlencode 'email=johnlsmith1983@gmail.com' \
  --data-urlencode 'email=john.smith@stanford.edu' \ 
  --data-urlencode 'name=John Smith'

``` 

```curl 
curl -X GET -G \
  'https://api.peopledatalabs.com/v4/person' \
  -H 'X-Api-Key: xxxx' \ 
  --data-urlencode 'company=Google' \
  --data-urlencode 'first_name=John' \ 
  --data-urlencode 'last_name=Smith' \
  --data-urlencode 'school=Arizona State University' 

```

```curl 
curl -X GET -G \
  'https://api.peopledatalabs.com/v4/person' \
  -H 'X-Api-Key: xxxx' \ 
  --data-urlencode 'name=Jeff L. Paulse' \ 
  --data-urlencode 'location=SF Bay Area' \
  --data-urlencode 'phone=1 503-2353497' 

```


**Response Filtering Parameters** 

These parameters can be used to describe the characteristics an API response must posess to return a `200`. 

| Parameter Name | Description | Default |
|-----------|---------|---------| 
| `min_likelihood` | The minimum `likelihood` score a response must have to return a `200` | `0` |
| `required` | Parameter specifying the fields and data points a response must have to return a `200` | |

For more information on the `min_likelihood` param, see the [likelihood](#likelihood) section, and for more information on the `required param`, see the [required param](#required-parameter) section. 


**Response Formatting Parameters** 

The following parameter can be used to control/specify certain things about the formatting of the person data in the API response. 

| Parameter Name | Description | Default |
|-----------|---------|---------| 
| `titlecase` | All text in the data of API responses is returned as lowercase by default. Setting `titlecase` to `true` will titlecase the person data in `200` responses | `false` |  

 
 
### Required Parameter  


The required parameter ensures that you only get charged for responses which have the data fields you're interested in. The value is formatted as a boolean statements

Response must contain an email

```curl 
required=emails
```
 
Response must contain a linkedin url 

```curl 
required=profiles.network:linkedin 
``` 

Response must contain experience and emails

```curl 
required=experience AND emails  
``` 

### Likelihood 

The likelihood score gives you the ability to control match precision vs. recall. Increasing the `min_likelihood` score will result in lower match rates, yet less false positive `200` responses. For use cases which rely on a high degree of data accuracy, only records with a likelihood of approximately `8` or above should be used. By default, match recall is kept very high, so a response which returns a likelihood score of `2` will roughly have just a 10 to 30 percent chance of being the same person as the one requested. Adding more data points to your requests will increase the probability of a `200` response returning a higher likelihood score. 

Requests made with only a few data points, e.g. a name and location, will rarely return a `200` response with a likelihood score > 5, and requests made with just an email will rarely return a `200` response with a likelihood score > 7. 


### Response


**200 Response Fields**  

| Field Name | Type | Description |
|-----------|---------|---------| 
| `data` | object | The person response object |  
| `status` | integer | HTTP Status Code |  
| `likelihood` | integer | Likelihood Score |  
 
 
**Example 200 Response**  

```json 
{
  "status": 200,
  "data": {
    "id": "d293d894-5aa1-46e6-bf96-c1a8d741781c",
    "skills": [
      {
        "name": "social media"
      },
      {
        "name": "strategic partnerships"
      },
      {
        "name": "public speaking"
      },
      {
        "name": "sales"
      },
      {
        "name": "photoshop"
      },
      {
        "name": "networking"
      },
      {
        "name": "mobile marketing"
      },
      {
        "name": "start ups"
      },
      {
        "name": "business development"
      },
      {
        "name": "fundraising"
      },
      {
        "name": "strategic planning"
      },
      {
        "name": "seo"
      },
      {
        "name": "strategy"
      },
      {
        "name": "idea generation"
      },
      {
        "name": "enterprise technology sales"
      },
      {
        "name": "entrepreneurship"
      },
      {
        "name": "microsoft excel"
      },
      {
        "name": "social networking"
      },
      {
        "name": "creative strategy"
      },
      {
        "name": "training"
      },
      {
        "name": "time management"
      },
      {
        "name": "product management"
      },
      {
        "name": "social media marketing"
      },
      {
        "name": "css"
      },
      {
        "name": "saas"
      },
      {
        "name": "management"
      },
      {
        "name": "business strategy"
      },
      {
        "name": "project management"
      },
      {
        "name": "public relations"
      },
      {
        "name": "marketing communications"
      },
      {
        "name": "marketing strategy"
      },
      {
        "name": "mobile devices"
      },
      {
        "name": "company culture"
      },
      {
        "name": "strategic vision"
      },
      {
        "name": "html5"
      },
      {
        "name": "hiring"
      }
    ],
    "industries": [
      {
        "name": "computer software"
      }
    ],
    "interests": [
      {
        "name": "location based services"
      },
      {
        "name": "mobile"
      },
      {
        "name": "social media"
      },
      {
        "name": "colleges"
      },
      {
        "name": "university students"
      },
      {
        "name": "consumer internet"
      },
      {
        "name": "college campuses"
      }
    ],
    "profiles": [
      {
        "network": "linkedin",
        "ids": [
          "145991517"
        ],
        "url": "http://www.linkedin.com/in/seanthorne",
        "clean": "linkedin.com/in/seanthorne",
        "aliases": [],
        "username": "seanthorne"
      },
      {
        "network": "klout",
        "ids": [],
        "url": "http://www.klout.com/seanthorne5",
        "clean": "klout.com/seanthorne5",
        "aliases": [],
        "username": "seanthorne5"
      },
      {
        "network": "twitter",
        "ids": [],
        "url": "http://www.twitter.com/seanthorne5",
        "clean": "twitter.com/seanthorne5",
        "aliases": [],
        "username": "seanthorne5"
      },
      {
        "network": "aboutme",
        "ids": [],
        "url": "http://www.about.me/sean_thorne",
        "clean": "about.me/sean_thorne",
        "aliases": [],
        "username": "sean_thorne"
      },
      {
        "network": "medium",
        "ids": [],
        "url": "http://www.medium.com/@seanthorne5",
        "clean": "medium.com/@seanthorne5",
        "aliases": [],
        "username": "@seanthorne5"
      },
      {
        "network": "angellist",
        "ids": [],
        "url": "http://www.angel.co/sean-thorne-1",
        "clean": "angel.co/sean-thorne-1",
        "aliases": [],
        "username": "sean-thorne-1"
      },
      {
        "network": "gravatar",
        "ids": [],
        "url": "http://www.gravatar.com/seanthorne5",
        "clean": "gravatar.com/seanthorne5",
        "aliases": [],
        "username": "seanthorne5"
      },
      {
        "network": "angellist",
        "ids": [],
        "url": "http://www.angel.co/475041",
        "clean": "angel.co/475041",
        "aliases": [],
        "username": "475041"
      }
    ],
    "emails": [
      {
        "address": "sean.thorne@talentiq.co",
        "local": "sean.thorne",
        "domain": "talentiq.co",
        "type": "professional",
        "sha256": "92e0b06495f24c1cb453b6d2a4d3374c3052fa9a87faf14ad8841ca7e92a586f"
      },
      {
        "address": "sean@talentiq.co",
        "local": "sean",
        "domain": "talentiq.co",
        "type": "professional",
        "sha256": "312680c9c9dacac4307197777433e39f255ce7175c6085073aedc95c1e750bd6"
      },
      {
        "address": "sean@hallspot.com",
        "local": "sean",
        "domain": "hallspot.com",
        "type": "professional",
        "sha256": "f61d233a1e3bf232f63c322a6d52ed692f69ec3a9585140302cd32a50027d704"
      },
      {
        "address": "sthorne@uoregon.edu",
        "local": "sthorne",
        "domain": "uoregon.edu",
        "type": null,
        "sha256": "e206e6cd7fa5f9499fd6d2d943dcf7d9c1469bad351061483f5ce7181663b8d4"
      },
      {
        "address": "sean@datemyschool.com",
        "local": "sean",
        "domain": "datemyschool.com",
        "type": "professional",
        "sha256": "ced7fdad56974dfbb8d9ccde17b5437e776ee3ca19c283b2ff940965dfb4f570"
      }
    ],
    "phone_numbers": [
      {
        "area_code": "925",
        "country_code": "1",
        "E164": "+19252783011",
        "national_number": "9252783011",
        "number": "+19252783011"
      },
      {
        "area_code": "415",
        "country_code": "1",
        "E164": "+14155688415",
        "national_number": "4155688415",
        "number": "+14155688415"
      }
    ],
    "gender": "male",
    "birth_date_fuzzy": "1990",
    "birth_date": "1990-12-03",
    "locations": [
      {
        "name": "eugene, oregon, united states",
        "locality": "eugene",
        "region": "oregon",
        "subregion": "lane county",
        "country": "united states",
        "continent": "north america",
        "type": "locality",
        "geo": "44.05,-123.08",
        "postal_code": "97401",
        "street_address": "1400 mill st",
        "address_line_2": null,
        "most_recent": false,
        "is_primary": false
      },
      {
        "name": "san francisco, california, united states",
        "locality": "san francisco",
        "region": "california",
        "subregion": "city and county of san francisco",
        "country": "united states",
        "continent": "north america",
        "type": "locality",
        "geo": "37.77,-122.41",
        "postal_code": "94105",
        "street_address": "399 fremont st",
        "address_line_2": null,
        "most_recent": true,
        "is_primary": true
      }
    ],
    "names": [
      {
        "first_name": "sean",
        "last_name": "thorne",
        "middle_name": "fong",
        "name": "sean fong thorne",
        "clean": "sean thorne",
        "is_primary": true
      }
    ],
    "experience": [
      {
        "company": {
          "name": "talentiq technologies",
          "industry": "computer software",
          "profiles": [
            "linkedin.com/company/6404208",
            "linkedin.com/company/talentiq"
          ],
          "website": "talentiq.co"
        },
        "start_date": "2015-03",
        "end_date": null,
        "title": {
          "levels": [
            "owner"
          ],
          "name": "co-founder",
          "functions": [
            "founder"
          ]
        },
        "locations": [
          {
            "continent": "north america",
            "country": "united states",
            "region": "california",
            "locality": "san francisco",
            "geo": "37.77,-122.41",
            "type": "locality",
            "postal_code": null,
            "subregion": null,
            "address_line_2": null,
            "street_address": null,
            "name": "san francisco, california, united states"
          }
        ],
        "type": null,
        "most_recent": false,
        "is_primary": false
      },
      {
        "company": {
          "name": "hallspot",
          "industry": "computer software",
          "profiles": [
            "linkedin.com/company/hallspot",
            "linkedin.com/company/3019184"
          ],
          "website": "hallspot.com"
        },
        "start_date": "2012-08",
        "end_date": "2015-02",
        "title": {
          "levels": [
            "owner"
          ],
          "name": "co-founder",
          "functions": [
            "founder"
          ]
        },
        "locations": [
          {
            "continent": "north america",
            "country": "united states",
            "region": "california",
            "locality": "san francisco",
            "geo": "37.77,-122.41",
            "type": "locality",
            "postal_code": null,
            "subregion": null,
            "address_line_2": null,
            "street_address": null,
            "name": "san francisco, california, united states"
          }
        ],
        "type": null,
        "most_recent": false,
        "is_primary": false
      },
      {
        "company": {
          "name": "datemyschool",
          "industry": "internet",
          "profiles": [
            "linkedin.com/company/datemyschool",
            "linkedin.com/company/1977175"
          ],
          "website": "datemyschool.com"
        },
        "start_date": "2012-01",
        "end_date": "2012-08",
        "title": {
          "levels": [
            "mgr"
          ],
          "name": "marketing manager",
          "functions": [
            "marketing manager"
          ]
        },
        "locations": [],
        "type": null,
        "most_recent": false,
        "is_primary": false
      }
    ],
    "education": [
      {
        "school": {
          "name": "university of oregon",
          "type": "post-secondary institution",
          "location": "eugene, oregon, united states",
          "profiles": [
            "linkedin.com/edu/university-of-oregon-19207",
            "facebook.com/universityoforegon"
          ],
          "website": "uoregon.edu"
        },
        "degrees": [],
        "start_date": "2010",
        "end_date": "2014",
        "majors": [
          "entrepreneurship"
        ],
        "minors": [],
        "gpa": null,
        "most_recent": true,
        "is_primary": true
      }
    ]
  },
  "likelihood": 10
}
```
 



 
### Errors 

The API uses conventional HTTP response codes to indicate the success or failure of an API request. A `200` means there was a matching person returned, a `404` means that a matching person was not found or returned, any `4xx` besides `404` indicates an issue with the request, and `5xx` errors indicate an internal issue with the API. 


**4xx Response Fields**  

| Field Name | Type | Description |
|-----------|---------|---------| 
| `error` | object | Object containing the error type message |  
| `status` | integer | HTTP Status Code |  


**Example 404 Response** 

```json 
{
    "status": 404,
    "error": {
        "type": "not_found",
        "message": "No records were found matching your request"
    }
} 
```

**Example 429 Response** 

```json 
{
    "status": 429,
    "error": {
        "type": "rate_limit_error",
        "message": "An error occurred due to requests hitting the API too quick"
    }
} 
```

**Error codes** 

| Status | Error Name | Description |
|--------|-----------|---------|
| `400` | `invalid_request_error` | Request contained either missing or invalid parameters | 
| `401` | `authentication_error` | Request contained a missing or invalid key |  
| `403` | `quota_exceeded ` | Your account has reached its quota for successful API calls |  
| `404` | `not_found ` | No records were found matching your request |  
| `405` | `invalid_request_error ` | Request method is not allowed on the requested resource |  
| `429` | `rate_limit_error ` | An error occurred due to requests hitting the API too quick |  
| `5xx` | `api_error ` | The server encountered an unexpected condition which prevented it from fulfilling the request |  




