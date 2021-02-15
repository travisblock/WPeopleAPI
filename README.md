# WPeopleAPI

A wordpress plugin to create contact in google contact using PeopleAPI 


## Example Request

```
Request                               | Parameter
--------------------------------------+---------------------------------------------
POST /wp-json/wpeopleapi/v1/contact   | - name  : string (required)
                                      | - phone : number (required)
Desc: Create Contact                  | - email : string valid email (required)
                                      | - photo : string base64 encoded (optional)
--------------------------------------+---------------------------------------------
GET /wp-json/wpeopleapi/v1/contact    | not required
                                      |
Desc: List all contact (admin only)   |
--------------------------------------+---------------------------------------------
```