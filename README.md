# WPeopleAPI

A wordpress plugin to create contact in google contact using PeopleAPI 


## Example Request

```
Request                               | Parameter
--------------------------------------+---------------------------------------------
POST /wp-json/wpeopleapi/v1/contact   | header:
                                      |  - x-WPeopleAPI-key => your own key. 
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