# Database Schemas — ruhaco_ruhavms

All tables used by the registration report v2 page.

---

## visitor_master

Primary table for all registered visitors.

| Field | Type | Null | Key | Default | Extra |
|---|---|---|---|---|---|
| id | int(11) | NO | MUL | NULL | |
| name | varchar(255) | NO | | NULL | |
| gender | varchar(20) | YES | | NULL | |
| designation | varchar(50) | NO | | NULL | |
| company_name | varchar(100) | NO | | NULL | |
| city | varchar(50) | NO | | NULL | |
| pincode | int(50) | NO | | NULL | |
| business_type | varchar(50) | NO | | NULL | |
| event_id | varchar(50) | NO | MUL | 1 | |
| mobile_number | varchar(255) | YES | | NULL | |
| email | varchar(100) | YES | | NULL | |
| field3 | varchar(100) | NO | | NULL | |
| prepared_by | varchar(255) | NO | | NULL | |
| auto_id | int(11) | NO | PRI | NULL | auto_increment |
| date | date | NO | | NULL | |
| time | time | NO | | NULL | |
| flag | varchar(50) | NO | | NULL | |
| id_proof | varchar(255) | NO | | NULL | |
| visitor_photo | varchar(500) | NO | | NULL | |
| certificate_download_flag | varchar(15) | NO | | NULL | |
| certificate_download_date | date | NO | | NULL | |
| certificate_download_time | time | NO | | NULL | |
| ticket_id | int(11) | NO | | NULL | |
| security_code | varchar(255) | NO | | NULL | |
| grp_id | int(11) | NO | | NULL | |
| reg_type | varchar(255) | NO | | NULL | |
| type_id | int(11) | NO | MUL | NULL | |
| country_code | varchar(255) | NO | | NULL | |
| status | varchar(255) | NO | | NULL | |
| otp_no | varchar(255) | NO | | NULL | |
| photo | varchar(255) | NO | | NULL | |
| visitor_id_type | varchar(255) | YES | | NULL | |
| visitor_purpose | text | YES | | NULL | |
| visitor_description | text | YES | | NULL | |
| amount | float | NO | | NULL | |
| paid_photo | varchar(255) | NO | | NULL | |
| scan_pic | varchar(255) | NO | | NULL | |
| sitting_seq | int(11) | NO | | NULL | |
| attachment | varchar(255) | NO | | NULL | |
| attachment2 | varchar(255) | NO | | NULL | |
| vendor_id | int(11) | NO | | NULL | |
| qr_value | varchar(255) | NO | | NULL | |
| reg_no | int(11) | NO | | NULL | |
| management | enum('YES','NO') | NO | | NO | |
| management_seq | int(11) | NO | | NULL | |
| done_deal | int(11) | NO | | NULL | |
| cgi_relative_qr_path | varchar(255) | YES | | NULL | |
| cgi_absolute_qr_path | varchar(255) | YES | | NULL | |
| business_recieved | float | NO | | NULL | |
| business_given | float | NO | | NULL | |
| confirm_pay | varchar(255) | NO | | NULL | |
| confirm_pay_desc | text | NO | | NULL | |
| coupen_code | varchar(255) | NO | | NULL | |
| browser_id | varchar(255) | NO | | NULL | |
| no_of_person | int(11) | NO | | NULL | |
| parent_id | int(11) | NO | MUL | NULL | |
| send_pass | varchar(255) | NO | | NULL | |
| gst_no | varchar(20) | YES | | NULL | |
| reg_type_ticket_ids | varchar(255) | NO | | NULL | |
| reg_token | varchar(20) | NO | MUL | | |
| token_main | enum('YES','NO') | NO | | NO | |

**Existing indexes:**
- `PRIMARY (auto_id)`
- `MUL on id, event_id, type_id, parent_id, reg_token`

**Indexes to add (migration):**
- `idx_event_pay_id (event_id, confirm_pay, auto_id)`
- `idx_event_date_pay_id (event_id, date, confirm_pay, auto_id)`

---

## visitor_extra_info

Custom form field values linked to visitors.

| Field | Type | Null | Key | Default | Extra |
|---|---|---|---|---|---|
| id | int(11) | NO | PRI | NULL | auto_increment |
| user_id | int(11) | NO | | NULL | |
| visitor_id | int(11) | NO | MUL | NULL | |
| pin_code | varchar(255) | NO | | NULL | |
| address | varchar(255) | NO | | NULL | |
| state | varchar(255) | NO | | NULL | |
| city | varchar(255) | NO | | NULL | |
| comp_nm | varchar(255) | NO | | NULL | |
| field1 | varchar(255) | NO | | NULL | |
| field2 | varchar(255) | NO | | NULL | |
| field3 | varchar(255) | NO | | NULL | |
| field4 | varchar(255) | NO | | NULL | |
| field5 | varchar(255) | NO | | NULL | |
| field6 | varchar(255) | NO | | NULL | |
| field7 | varchar(255) | NO | | NULL | |
| field8 | varchar(255) | NO | | NULL | |
| name | varchar(255) | NO | | NULL | |
| mob_no | varchar(255) | NO | | NULL | |
| email | varchar(255) | NO | | NULL | |
| field9 | varchar(255) | NO | | NULL | |
| field10 | varchar(255) | NO | | NULL | |
| field11 | varchar(255) | NO | | NULL | |
| field12 | text | NO | | NULL | |
| field13 | text | NO | | NULL | |
| field14 | text | NO | | NULL | |
| field15 | text | NO | | NULL | |
| gst_no | varchar(255) | NO | | NULL | |

---

## pg_event_master

Event definitions.

| Field | Type | Null | Key | Default | Extra |
|---|---|---|---|---|---|
| event_id | int(10) | NO | PRI | NULL | auto_increment |
| event_head_id | int(11) | NO | | NULL | |
| e_type | varchar(50) | NO | | NULL | |
| event_title | text | NO | | NULL | |
| start_dt | date | NO | | NULL | |
| start_tm | time | NO | | NULL | |
| end_dt | date | NO | | NULL | |
| end_tm | time | NO | | NULL | |
| desc_below_date | varchar(500) | NO | | NULL | |
| short_nm | varchar(10) | NO | | NULL | |
| description | text | NO | | NULL | |
| private | varchar(20) | NO | | NULL | |
| banner | varchar(100) | NO | | NULL | |
| event_top_logo | varchar(255) | YES | | NULL | |
| mail_header_image | enum('logo','banner') | NO | | logo | |
| comp_id | varchar(20) | NO | | NULL | |
| off_payment | varchar(10) | NO | | NULL | |
| confirmation | varchar(5) | NO | | NULL | |
| pass | varchar(5) | NO | | NULL | |
| registration | varchar(50) | NO | | NULL | |
| seo_url | varchar(255) | NO | | NULL | |
| mob_banner | varchar(255) | NO | | NULL | |
| payment_enviroment | enum('prod','test') | YES | | prod | |
| wa_provider | varchar(32) | NO | | meta_vision | |
| pay_gate | varchar(50) | YES | | NULL | |

*(Trimmed to report-relevant columns. Full table has ~80 columns.)*

---

## ex_reg_type

Registration type/category definitions per event.

| Field | Type | Null | Key | Default | Extra |
|---|---|---|---|---|---|
| id | int(11) | NO | PRI | NULL | |
| event_id | int(11) | NO | MUL | NULL | |
| type | varchar(255) | NO | | NULL | |
| description | varchar(255) | NO | | NULL | |
| email_body | text | NO | | NULL | |
| t_and_c | text | NO | | NULL | |
| sms_body | text | NO | | NULL | |
| amount | float | NO | | NULL | |
| seo_nm | varchar(255) | NO | | NULL | |
| amt_desc | varchar(255) | NO | | NULL | |
| amount2 | float | NO | | NULL | |
| amt_desc2 | varchar(255) | NO | | NULL | |
| photo_required | varchar(255) | NO | | NULL | |
| face_scan_required | varchar(255) | NO | | NULL | |
| pay_mode | varchar(255) | NO | | NULL | |
| multi_ticket_enable | enum('YES','NO') | NO | | NO | |
| multi_ticket_max | int(11) | NO | | 0 | |
| group_pass_mode | enum('INDIVIDUAL','PARENT') | NO | | INDIVIDUAL | |
| banner | varchar(255) | NO | | | |
| attachment_required | varchar(255) | NO | | NULL | |
| management | enum('YES','NO') | NO | | NO | |
| qr_back_img | varchar(255) | NO | | NULL | |
| feedback_required | varchar(255) | NO | | NULL | |
| qr_type | varchar(255) | NO | | NULL | |
| subject | varchar(255) | NO | | NULL | |
| token_enabled | enum('YES','NO') | NO | | NO | |

---

## ex_typewise_form

Custom form field definitions per registration type.

| Field | Type | Null | Key | Default | Extra |
|---|---|---|---|---|---|
| id | int(11) | NO | PRI | NULL | auto_increment |
| type_id | varchar(20) | NO | | NULL | |
| name | varchar(150) | NO | | NULL | |
| field_nm | varchar(20) | NO | | NULL | |
| options | text | NO | | NULL | |
| default_value | varchar(100) | NO | | | |
| show_if_field | varchar(20) | NO | | | |
| show_if_value | varchar(100) | NO | | | |
| type | varchar(20) | NO | | NULL | |
| flag | varchar(5) | NO | | NULL | |
| validation | varchar(20) | NO | | NULL | |
| seq | int(10) | NO | | NULL | |
| unique_field | enum('YES','NO') | NO | | NO | |
| help_text | text | YES | | NULL | |

---

## ex_reg_type_tickets

Ticket/activity definitions per registration type.

| Field | Type | Null | Key | Default | Extra |
|---|---|---|---|---|---|
| id | int(11) | NO | PRI | NULL | auto_increment |
| type_id | int(11) | NO | | NULL | |
| event_id | int(11) | NO | | NULL | |
| ticket_name | varchar(255) | NO | | NULL | |
| time_label | varchar(100) | NO | | | |
| team_label | varchar(100) | NO | | | |
| amount | float | NO | | NULL | |
| description | text | NO | | NULL | |
| seq | int(11) | NO | | 0 | |
| max_slots | int(11) | NO | | 0 | |
| active_from | date | YES | | NULL | |
| active_till | date | YES | | NULL | |
| is_visible | enum('YES','NO') | NO | | YES | |
| max_per_booking | int(11) | NO | | 0 | |

---

## online_mem_approve

Payment transaction records.

| Field | Type | Null | Key | Default | Extra |
|---|---|---|---|---|---|
| id | int(11) | NO | PRI | NULL | auto_increment |
| reciept_no | varchar(255) | NO | | NULL | |
| cust_id | int(11) | NO | MUL | NULL | |
| txn_id | varchar(255) | NO | | NULL | |
| amount | float | NO | | NULL | |
| approve_date | date | NO | | NULL | |
| txn_time | time | NO | | NULL | |
| trans_status | varchar(255) | NO | | NULL | |
| ebuzz_id | varchar(255) | NO | | NULL | |
| payment_mode | varchar(255) | NO | | NULL | |
| prep_by | int(11) | NO | | NULL | |
| bank_name | varchar(255) | NO | | NULL | |
| cheque_no | varchar(255) | NO | | NULL | |
| remark | text | NO | | NULL | |
| flag | varchar(255) | NO | | NULL | |
| event_id | int(11) | NO | | NULL | |

---

## ja_comp_master

Company master — used for `web_add` (base URL for assets).

| Field | Type | Null | Key | Default | Extra |
|---|---|---|---|---|---|
| comp_id | int(11) | NO | PRI | NULL | auto_increment |
| comp_name | varchar(255) | NO | | NULL | |
| web_add | varchar(100) | NO | | NULL | |
| comp_logo | varchar(100) | NO | | NULL | |
| logo_path | varchar(100) | NO | | NULL | |
| off_email | varchar(50) | NO | | NULL | |
| email_for_send_mail | text | NO | | NULL | |
| pass_for_send_mail | text | NO | | NULL | |
| mail_config | enum('Regular','gmail') | NO | | NULL | |
| vms_user_login_id | int(11) | NO | | NULL | |

*(Trimmed to report-relevant columns. Full table has ~170 columns.)*
