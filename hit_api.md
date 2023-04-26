## HIT API


All links are **POST** requests. All post requests are validated using a header called **apikey**. The postman collection has already added them.
*Open the link in **POSTMAN** and then set the request to **POST** *
- Get data by request type
>Fields
> - start_date (required when *end_date* is set). It filters using the date collected.
> - end_date *(greater than or equal to **start_date**)*
> - date_dispatched_start (required when *date_dispatched_end* is set)
> - date_dispatched_end  *(greater than or equal to **date_dispatched_start**)*
> - patient_id *Either a single patient_id or a comma separated list of patient ids with no spaces. Patient id is hei number for eid and ccc number for vl*
> - facility_code *The five digit MFL code* Also comma separated.

> This link is paginated i.e. only 50 results at a time will be returned. The return data has a field called next_page_url and other links to help you get all the results. There is also other useful data such as the total results found.

