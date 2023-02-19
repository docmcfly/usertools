#
# Table structure for table 'fe_users'
#
CREATE TABLE fe_users (

	allow_display_email smallint(5) unsigned DEFAULT '0' NOT NULL,
	allow_display_image_internal smallint(5) unsigned DEFAULT '0' NOT NULL,
	allow_display_image_public smallint(5) unsigned DEFAULT '0' NOT NULL,
	new_email varchar(255) DEFAULT '' NOT NULL,
	new_email_token varchar(255) DEFAULT '' NOT NULL,
	password_token varchar(255) DEFAULT '' NOT NULL,
	allow_display_phone smallint(5) unsigned DEFAULT '0' NOT NULL,
	currently_off_duty smallint(5) unsigned DEFAULT '0' NOT NULL,
    currently_off_duty_until DATE DEFAULT '0000-00-00',

);



