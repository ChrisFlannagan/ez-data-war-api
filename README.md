# ez-data-war-api
This is the custom plugin to extend the war-api

## Data Models
There are 2 main data models
### Items
_Authorization Required to Read/Write/Delete_  
Items are the main data and associated to `groups`  
__Parameters:__  
* __groups__ - `required` - `string` the name of the group you want the item associated with
* __value__ - `integer` an integer value for the item
* __misc_one__ - `integer` another integer value for the item
* __misc_two__ - `integer` another integer value for the item
* __misc_three__ - `string` a text value for the item
* __misc_four__ - `string` another text value for the item
  
### Groups
_Authorization Required to Read/Write/Delete_
Groups are the container associate for items. The return value for any group `with sideLoad=true` will include all associated items
__Parameters:__  
* __name__ - `required` - `string` the name of the group
* __description__ - `string` a description of that group
  
## Associations
Many items can be associated to 1 group, however the name must match exactly for the `sideLoad` to load in all the associated items