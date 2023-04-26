## RECENCY API

>Fields
> - start_date (required when *end_date* is set). It filters using the date collected.
> - end_date *(greater than or equal to **start_date**)*
> - date_dispatched_start (required when *date_dispatched_end* is set)
> - date_dispatched_end  *(greater than or equal to **date_dispatched_start**)*
> - recency_number *Either a single recency_number or a comma separated list of recency_numbers with no spaces.*
> - facility_code *The five digit MFL code* Also comma separated.

> This link is paginated i.e. only 50 results at a time will be returned. The return data has a field called next_page_url and other links to help you get all the results. There is also other useful data such as the total results found.

