import requests

base_url = "https://api.talentiq.co/v3/person?"
api_key = "api_key=YOUR_API_KEY"
payload = {"name": "sean thorne", "profile": "www.linkedin.com/in/seanthorne", "required": "emails.address"}
request_url = "%s%s" % (base_url, api_key)

response = requests.get(request_url, params=payload)
print response.json()

