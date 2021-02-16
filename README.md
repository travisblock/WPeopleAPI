# WPeopleAPI

A wordpress plugin to create contact in google contact using PeopleAPI 


## Example Request

```
Request                               | Parameter
--------------------------------------+---------------------------------------------
POST /wp-json/wpeopleapi/v1/contact   | header:
                                      |  - HTTP_AUTHORIZATION => Your Bearer token. 
Desc: Create Contact                  |     setting in plugin settings page. 
                                      |     default: wpeopleapi (required)
                                      | body:
                                      |  - name  : string (required)
                                      |  - phone : number (required)
                                      |  - email : string valid email (required)
                                      |  - photo : string base64 encoded (optional)
--------------------------------------+---------------------------------------------
GET /wp-json/wpeopleapi/v1/contact    | not required
                                      |
Desc: List all contact (admin only)   |
--------------------------------------+---------------------------------------------
```


### Example create contact
```
curl --location --request POST 'https://yourdomain.com/wp-json/wpeopleapi/v1/contact' \
--header 'Authorization: Bearer wpeopleapi' \
--form 'name="Stark"' \
--form 'phone="07662123213"' \
--form 'email="stark@gmail.com"'
```