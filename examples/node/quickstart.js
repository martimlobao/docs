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

