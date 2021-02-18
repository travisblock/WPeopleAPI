# WPeopleAPI

A wordpress plugin to create contact in google contact using PeopleAPI 


## Create contact

URL: `POST /wp-json/wpeopleapi/v1/contact`

| Parameter  | Desc |
|------------|------|
| header     | `Authorization: Bearer [TOKEN]` |
| body              |
| `name`      | String (required) |
| `phone`     | Number (required) |
| `email`     | String (required) |
| `photo`     | String base64 encoded (optional) |
| `group`     | String: name of existing/new label (optional) |
| `address`   | array : `address[city]` & `address[country]` (optional) |
| `birthday`  | String international date format `yyy-mm-dd` (optional) |
| `events`    | array: `events[type]` & `events[date]` (optional) |
| `urls`      | array: `urls[]` which should contain pipe `\|` which means `type\|value` |
| `custom`    | array: `custom[]` which should contain pipe `\|` which means `key\|value` |


```
curl --location --request POST 'https://yourdomain.com/wp-json/wpeopleapi/v1/contact' \
--header 'Authorization: Bearer wpeopleapi' \
--form 'name="Stark"' \
--form 'phone="08956433721"' \
--form 'email="stark@gamil.com"' \
--form 'group="WPFORM"' \
--form 'address[city]="Medan"' \
--form 'address[country]="Indonesia"' \
--form 'birthday="1997-02-23"' \
--form 'events[type]="Input submitted"' \
--form 'events[date]="2021-02-18"' \
--form 'urls[]="Referer|https://stackoverflow.com/questions/3206020/push-item-to-associative-array-in-php/3206038"' \
--form 'urls[]="Form Submitted|https://masrukhanonline.com"' \
--form 'custom[]="Device|ifhone"' \
--form 'custom[]="maps|6756345,77677654"'
```