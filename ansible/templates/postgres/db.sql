-- for uuids
CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- updated_at refresh function (to be called inside a trigger)
CREATE OR REPLACE FUNCTION refresh_updated_at()
RETURNS TRIGGER AS $$
BEGIN
  NEW.updated_at = NOW();
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- migrations
CREATE TABLE migrations (
    pk INT GENERATED ALWAYS AS IDENTITY,
    name TEXT NOT NULL,

    PRIMARY KEY (pk)
);

-- jobs
CREATE TABLE jobs (
    pk INT GENERATED ALWAYS AS IDENTITY,
    class TEXT NOT NULL,

    is_running BOOLEAN NOT NULL,
    is_exclusive BOOLEAN NOT NULL,
    context TEXT NULL DEFAULT NULL,
    report TEXT NULL DEFAULT NULL,
    progress FLOAT NOT NULL DEFAULT 0,

    scheduled_for TIMESTAMPTZ NULL DEFAULT NULL,
    schedule_from TIMESTAMPTZ NULL DEFAULT NULL,
    schedule_frequency TEXT NULL DEFAULT NULL,

    last_run_at TIMESTAMPTZ NULL DEFAULT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    PRIMARY KEY (pk)
);

-- attempts
CREATE TABLE attempts (
    pk INT GENERATED ALWAYS AS IDENTITY,

    ip TEXT NOT NULL,
    uri TEXT NOT NULL,
    at TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    PRIMARY KEY (pk)
);

-- s3 cache
CREATE TABLE storage_s3_medias (
    pk INT GENERATED ALWAYS AS IDENTITY,
    filename TEXT NOT NULL,
    url TEXT NOT NULL,
    expires_at TIMESTAMPTZ NOT NULL,

    PRIMARY KEY(pk)
);

-- countries
CREATE TABLE countries (
    pk UUID DEFAULT gen_random_uuid(),

    code TEXT NOT NULL,
    name TEXT NOT NULL,
    is_inside_eu BOOLEAN NOT NULL DEFAULT FALSE,

    PRIMARY KEY (pk)
);

INSERT INTO countries (code, name) VALUES ('AF', 'Afghanistan');
INSERT INTO countries (code, name) VALUES ('AX', 'Åland Islands');
INSERT INTO countries (code, name) VALUES ('AL', 'Albania');
INSERT INTO countries (code, name) VALUES ('DZ', 'Algeria');
INSERT INTO countries (code, name) VALUES ('AS', 'American Samoa');
INSERT INTO countries (code, name) VALUES ('AD', 'Andorra');
INSERT INTO countries (code, name) VALUES ('AO', 'Angola');
INSERT INTO countries (code, name) VALUES ('AI', 'Anguilla');
INSERT INTO countries (code, name) VALUES ('AQ', 'Antarctica');
INSERT INTO countries (code, name) VALUES ('AG', 'Antigua & Barbuda');
INSERT INTO countries (code, name) VALUES ('AR', 'Argentina');
INSERT INTO countries (code, name) VALUES ('AM', 'Armenia');
INSERT INTO countries (code, name) VALUES ('AW', 'Aruba');
INSERT INTO countries (code, name) VALUES ('AU', 'Australia');
INSERT INTO countries (code, name, is_inside_eu) VALUES ('AT', 'Austria', TRUE);
INSERT INTO countries (code, name) VALUES ('AZ', 'Azerbaijan');
INSERT INTO countries (code, name) VALUES ('BS', 'Bahamas');
INSERT INTO countries (code, name) VALUES ('BH', 'Bahrain');
INSERT INTO countries (code, name) VALUES ('BD', 'Bangladesh');
INSERT INTO countries (code, name) VALUES ('BB', 'Barbados');
INSERT INTO countries (code, name) VALUES ('BY', 'Belarus');
INSERT INTO countries (code, name, is_inside_eu) VALUES ('BE', 'Belgium', TRUE);
INSERT INTO countries (code, name) VALUES ('BZ', 'Belize');
INSERT INTO countries (code, name) VALUES ('BJ', 'Benin');
INSERT INTO countries (code, name) VALUES ('BM', 'Bermuda');
INSERT INTO countries (code, name) VALUES ('BT', 'Bhutan');
INSERT INTO countries (code, name) VALUES ('BO', 'Bolivia');
INSERT INTO countries (code, name) VALUES ('BA', 'Bosnia & Herzegovina');
INSERT INTO countries (code, name) VALUES ('BW', 'Botswana');
INSERT INTO countries (code, name) VALUES ('BV', 'Bouvet Island');
INSERT INTO countries (code, name) VALUES ('BR', 'Brazil');
INSERT INTO countries (code, name) VALUES ('IO', 'British Indian Ocean Territory');
INSERT INTO countries (code, name) VALUES ('VG', 'British Virgin Islands');
INSERT INTO countries (code, name) VALUES ('BN', 'Brunei');
INSERT INTO countries (code, name, is_inside_eu) VALUES ('BG', 'Bulgaria', TRUE);
INSERT INTO countries (code, name) VALUES ('BF', 'Burkina Faso');
INSERT INTO countries (code, name) VALUES ('BI', 'Burundi');
INSERT INTO countries (code, name) VALUES ('KH', 'Cambodia');
INSERT INTO countries (code, name) VALUES ('CM', 'Cameroon');
INSERT INTO countries (code, name) VALUES ('CA', 'Canada');
INSERT INTO countries (code, name) VALUES ('CV', 'Cape Verde');
INSERT INTO countries (code, name) VALUES ('BQ', 'Caribbean Netherlands');
INSERT INTO countries (code, name) VALUES ('KY', 'Cayman Islands');
INSERT INTO countries (code, name) VALUES ('CF', 'Central African Republic');
INSERT INTO countries (code, name) VALUES ('TD', 'Chad');
INSERT INTO countries (code, name) VALUES ('CL', 'Chile');
INSERT INTO countries (code, name) VALUES ('CN', 'China');
INSERT INTO countries (code, name) VALUES ('CX', 'Christmas Island');
INSERT INTO countries (code, name) VALUES ('CC', 'Cocos (Keeling) Islands');
INSERT INTO countries (code, name) VALUES ('CO', 'Colombia');
INSERT INTO countries (code, name) VALUES ('KM', 'Comoros');
INSERT INTO countries (code, name) VALUES ('CG', 'Congo - Brazzaville');
INSERT INTO countries (code, name) VALUES ('CD', 'Congo - Kinshasa');
INSERT INTO countries (code, name) VALUES ('CK', 'Cook Islands');
INSERT INTO countries (code, name) VALUES ('CR', 'Costa Rica');
INSERT INTO countries (code, name) VALUES ('CI', 'Côte d’Ivoire');
INSERT INTO countries (code, name, is_inside_eu) VALUES ('HR', 'Croatia', TRUE);
INSERT INTO countries (code, name) VALUES ('CU', 'Cuba');
INSERT INTO countries (code, name) VALUES ('CW', 'Curaçao');
INSERT INTO countries (code, name, is_inside_eu) VALUES ('CY', 'Cyprus', TRUE);
INSERT INTO countries (code, name, is_inside_eu) VALUES ('CZ', 'Czechia', TRUE);
INSERT INTO countries (code, name, is_inside_eu) VALUES ('DK', 'Denmark', TRUE);
INSERT INTO countries (code, name) VALUES ('DJ', 'Djibouti');
INSERT INTO countries (code, name) VALUES ('DM', 'Dominica');
INSERT INTO countries (code, name) VALUES ('DO', 'Dominican Republic');
INSERT INTO countries (code, name) VALUES ('EC', 'Ecuador');
INSERT INTO countries (code, name) VALUES ('EG', 'Egypt');
INSERT INTO countries (code, name) VALUES ('SV', 'El Salvador');
INSERT INTO countries (code, name) VALUES ('GQ', 'Equatorial Guinea');
INSERT INTO countries (code, name) VALUES ('ER', 'Eritrea');
INSERT INTO countries (code, name, is_inside_eu) VALUES ('EE', 'Estonia', TRUE);
INSERT INTO countries (code, name) VALUES ('SZ', 'Eswatini');
INSERT INTO countries (code, name) VALUES ('ET', 'Ethiopia');
INSERT INTO countries (code, name) VALUES ('FK', 'Falkland Islands');
INSERT INTO countries (code, name) VALUES ('FO', 'Faroe Islands');
INSERT INTO countries (code, name) VALUES ('FJ', 'Fiji');
INSERT INTO countries (code, name, is_inside_eu) VALUES ('FI', 'Finland', TRUE);
INSERT INTO countries (code, name, is_inside_eu) VALUES ('FR', 'France', TRUE);
INSERT INTO countries (code, name) VALUES ('GF', 'French Guiana');
INSERT INTO countries (code, name) VALUES ('PF', 'French Polynesia');
INSERT INTO countries (code, name) VALUES ('TF', 'French Southern Territories');
INSERT INTO countries (code, name) VALUES ('GA', 'Gabon');
INSERT INTO countries (code, name) VALUES ('GM', 'Gambia');
INSERT INTO countries (code, name) VALUES ('GE', 'Georgia');
INSERT INTO countries (code, name, is_inside_eu) VALUES ('DE', 'Germany', TRUE);
INSERT INTO countries (code, name) VALUES ('GH', 'Ghana');
INSERT INTO countries (code, name) VALUES ('GI', 'Gibraltar');
INSERT INTO countries (code, name, is_inside_eu) VALUES ('GR', 'Greece', TRUE);
INSERT INTO countries (code, name) VALUES ('GL', 'Greenland');
INSERT INTO countries (code, name) VALUES ('GD', 'Grenada');
INSERT INTO countries (code, name) VALUES ('GP', 'Guadeloupe');
INSERT INTO countries (code, name) VALUES ('GU', 'Guam');
INSERT INTO countries (code, name) VALUES ('GT', 'Guatemala');
INSERT INTO countries (code, name) VALUES ('GG', 'Guernsey');
INSERT INTO countries (code, name) VALUES ('GN', 'Guinea');
INSERT INTO countries (code, name) VALUES ('GW', 'Guinea-Bissau');
INSERT INTO countries (code, name) VALUES ('GY', 'Guyana');
INSERT INTO countries (code, name) VALUES ('HT', 'Haiti');
INSERT INTO countries (code, name) VALUES ('HM', 'Heard & McDonald Islands');
INSERT INTO countries (code, name) VALUES ('HN', 'Honduras');
INSERT INTO countries (code, name) VALUES ('HK', 'Hong Kong SAR China');
INSERT INTO countries (code, name, is_inside_eu) VALUES ('HU', 'Hungary', TRUE);
INSERT INTO countries (code, name) VALUES ('IS', 'Iceland');
INSERT INTO countries (code, name) VALUES ('IN', 'India');
INSERT INTO countries (code, name) VALUES ('ID', 'Indonesia');
INSERT INTO countries (code, name) VALUES ('IR', 'Iran');
INSERT INTO countries (code, name) VALUES ('IQ', 'Iraq');
INSERT INTO countries (code, name, is_inside_eu) VALUES ('IE', 'Ireland', TRUE);
INSERT INTO countries (code, name) VALUES ('IM', 'Isle of Man');
INSERT INTO countries (code, name) VALUES ('IL', 'Israel');
INSERT INTO countries (code, name, is_inside_eu) VALUES ('IT', 'Italy', TRUE);
INSERT INTO countries (code, name) VALUES ('JM', 'Jamaica');
INSERT INTO countries (code, name) VALUES ('JP', 'Japan');
INSERT INTO countries (code, name) VALUES ('JE', 'Jersey');
INSERT INTO countries (code, name) VALUES ('JO', 'Jordan');
INSERT INTO countries (code, name) VALUES ('KZ', 'Kazakhstan');
INSERT INTO countries (code, name) VALUES ('KE', 'Kenya');
INSERT INTO countries (code, name) VALUES ('KI', 'Kiribati');
INSERT INTO countries (code, name) VALUES ('KW', 'Kuwait');
INSERT INTO countries (code, name) VALUES ('KG', 'Kyrgyzstan');
INSERT INTO countries (code, name) VALUES ('LA', 'Laos');
INSERT INTO countries (code, name, is_inside_eu) VALUES ('LV', 'Latvia', TRUE);
INSERT INTO countries (code, name) VALUES ('LB', 'Lebanon');
INSERT INTO countries (code, name) VALUES ('LS', 'Lesotho');
INSERT INTO countries (code, name) VALUES ('LR', 'Liberia');
INSERT INTO countries (code, name) VALUES ('LY', 'Libya');
INSERT INTO countries (code, name) VALUES ('LI', 'Liechtenstein');
INSERT INTO countries (code, name, is_inside_eu) VALUES ('LT', 'Lithuania', TRUE);
INSERT INTO countries (code, name, is_inside_eu) VALUES ('LU', 'Luxembourg', TRUE);
INSERT INTO countries (code, name) VALUES ('MO', 'Macao SAR China');
INSERT INTO countries (code, name) VALUES ('MG', 'Madagascar');
INSERT INTO countries (code, name) VALUES ('MW', 'Malawi');
INSERT INTO countries (code, name) VALUES ('MY', 'Malaysia');
INSERT INTO countries (code, name) VALUES ('MV', 'Maldives');
INSERT INTO countries (code, name) VALUES ('ML', 'Mali');
INSERT INTO countries (code, name, is_inside_eu) VALUES ('MT', 'Malta', TRUE);
INSERT INTO countries (code, name) VALUES ('MH', 'Marshall Islands');
INSERT INTO countries (code, name) VALUES ('MQ', 'Martinique');
INSERT INTO countries (code, name) VALUES ('MR', 'Mauritania');
INSERT INTO countries (code, name) VALUES ('MU', 'Mauritius');
INSERT INTO countries (code, name) VALUES ('YT', 'Mayotte');
INSERT INTO countries (code, name) VALUES ('MX', 'Mexico');
INSERT INTO countries (code, name) VALUES ('FM', 'Micronesia');
INSERT INTO countries (code, name) VALUES ('MD', 'Moldova');
INSERT INTO countries (code, name) VALUES ('MC', 'Monaco');
INSERT INTO countries (code, name) VALUES ('MN', 'Mongolia');
INSERT INTO countries (code, name) VALUES ('ME', 'Montenegro');
INSERT INTO countries (code, name) VALUES ('MS', 'Montserrat');
INSERT INTO countries (code, name) VALUES ('MA', 'Morocco');
INSERT INTO countries (code, name) VALUES ('MZ', 'Mozambique');
INSERT INTO countries (code, name) VALUES ('MM', 'Myanmar (Burma)');
INSERT INTO countries (code, name) VALUES ('NA', 'Namibia');
INSERT INTO countries (code, name) VALUES ('NR', 'Nauru');
INSERT INTO countries (code, name) VALUES ('NP', 'Nepal');
INSERT INTO countries (code, name, is_inside_eu) VALUES ('NL', 'Netherlands', TRUE);
INSERT INTO countries (code, name) VALUES ('NC', 'New Caledonia');
INSERT INTO countries (code, name) VALUES ('NZ', 'New Zealand');
INSERT INTO countries (code, name) VALUES ('NI', 'Nicaragua');
INSERT INTO countries (code, name) VALUES ('NE', 'Niger');
INSERT INTO countries (code, name) VALUES ('NG', 'Nigeria');
INSERT INTO countries (code, name) VALUES ('NU', 'Niue');
INSERT INTO countries (code, name) VALUES ('NF', 'Norfolk Island');
INSERT INTO countries (code, name) VALUES ('KP', 'North Korea');
INSERT INTO countries (code, name) VALUES ('MK', 'North Macedonia');
INSERT INTO countries (code, name) VALUES ('MP', 'Northern Mariana Islands');
INSERT INTO countries (code, name) VALUES ('NO', 'Norway');
INSERT INTO countries (code, name) VALUES ('OM', 'Oman');
INSERT INTO countries (code, name) VALUES ('PK', 'Pakistan');
INSERT INTO countries (code, name) VALUES ('PW', 'Palau');
INSERT INTO countries (code, name) VALUES ('PS', 'Palestinian Territories');
INSERT INTO countries (code, name) VALUES ('PA', 'Panama');
INSERT INTO countries (code, name) VALUES ('PG', 'Papua New Guinea');
INSERT INTO countries (code, name) VALUES ('PY', 'Paraguay');
INSERT INTO countries (code, name) VALUES ('PE', 'Peru');
INSERT INTO countries (code, name) VALUES ('PH', 'Philippines');
INSERT INTO countries (code, name) VALUES ('PN', 'Pitcairn Islands');
INSERT INTO countries (code, name, is_inside_eu) VALUES ('PL', 'Poland', TRUE);
INSERT INTO countries (code, name, is_inside_eu) VALUES ('PT', 'Portugal', TRUE);
INSERT INTO countries (code, name) VALUES ('PR', 'Puerto Rico');
INSERT INTO countries (code, name) VALUES ('QA', 'Qatar');
INSERT INTO countries (code, name) VALUES ('RE', 'Réunion');
INSERT INTO countries (code, name, is_inside_eu) VALUES ('RO', 'Romania', TRUE);
INSERT INTO countries (code, name) VALUES ('RU', 'Russia');
INSERT INTO countries (code, name) VALUES ('RW', 'Rwanda');
INSERT INTO countries (code, name) VALUES ('WS', 'Samoa');
INSERT INTO countries (code, name) VALUES ('SM', 'San Marino');
INSERT INTO countries (code, name) VALUES ('ST', 'São Tomé & Príncipe');
INSERT INTO countries (code, name) VALUES ('SA', 'Saudi Arabia');
INSERT INTO countries (code, name) VALUES ('SN', 'Senegal');
INSERT INTO countries (code, name) VALUES ('RS', 'Serbia');
INSERT INTO countries (code, name) VALUES ('SC', 'Seychelles');
INSERT INTO countries (code, name) VALUES ('SL', 'Sierra Leone');
INSERT INTO countries (code, name) VALUES ('SG', 'Singapore');
INSERT INTO countries (code, name) VALUES ('SX', 'Sint Maarten');
INSERT INTO countries (code, name, is_inside_eu) VALUES ('SK', 'Slovakia', TRUE);
INSERT INTO countries (code, name, is_inside_eu) VALUES ('SI', 'Slovenia', TRUE);
INSERT INTO countries (code, name) VALUES ('SB', 'Solomon Islands');
INSERT INTO countries (code, name) VALUES ('SO', 'Somalia');
INSERT INTO countries (code, name) VALUES ('ZA', 'South Africa');
INSERT INTO countries (code, name) VALUES ('GS', 'South Georgia & South Sandwich Islands');
INSERT INTO countries (code, name) VALUES ('KR', 'South Korea');
INSERT INTO countries (code, name) VALUES ('SS', 'South Sudan');
INSERT INTO countries (code, name, is_inside_eu) VALUES ('ES', 'Spain', TRUE);
INSERT INTO countries (code, name) VALUES ('LK', 'Sri Lanka');
INSERT INTO countries (code, name) VALUES ('BL', 'St. Barthélemy');
INSERT INTO countries (code, name) VALUES ('SH', 'St. Helena');
INSERT INTO countries (code, name) VALUES ('KN', 'St. Kitts & Nevis');
INSERT INTO countries (code, name) VALUES ('LC', 'St. Lucia');
INSERT INTO countries (code, name) VALUES ('MF', 'St. Martin');
INSERT INTO countries (code, name) VALUES ('PM', 'St. Pierre & Miquelon');
INSERT INTO countries (code, name) VALUES ('VC', 'St. Vincent & Grenadines');
INSERT INTO countries (code, name) VALUES ('SD', 'Sudan');
INSERT INTO countries (code, name) VALUES ('SR', 'Suriname');
INSERT INTO countries (code, name) VALUES ('SJ', 'Svalbard & Jan Mayen');
INSERT INTO countries (code, name, is_inside_eu) VALUES ('SE', 'Sweden', TRUE);
INSERT INTO countries (code, name) VALUES ('CH', 'Switzerland');
INSERT INTO countries (code, name) VALUES ('SY', 'Syria');
INSERT INTO countries (code, name) VALUES ('TW', 'Taiwan');
INSERT INTO countries (code, name) VALUES ('TJ', 'Tajikistan');
INSERT INTO countries (code, name) VALUES ('TZ', 'Tanzania');
INSERT INTO countries (code, name) VALUES ('TH', 'Thailand');
INSERT INTO countries (code, name) VALUES ('TL', 'Timor-Leste');
INSERT INTO countries (code, name) VALUES ('TG', 'Togo');
INSERT INTO countries (code, name) VALUES ('TK', 'Tokelau');
INSERT INTO countries (code, name) VALUES ('TO', 'Tonga');
INSERT INTO countries (code, name) VALUES ('TT', 'Trinidad & Tobago');
INSERT INTO countries (code, name) VALUES ('TN', 'Tunisia');
INSERT INTO countries (code, name) VALUES ('TR', 'Turkey');
INSERT INTO countries (code, name) VALUES ('TM', 'Turkmenistan');
INSERT INTO countries (code, name) VALUES ('TC', 'Turks & Caicos Islands');
INSERT INTO countries (code, name) VALUES ('TV', 'Tuvalu');
INSERT INTO countries (code, name) VALUES ('UM', 'U.S. Outlying Islands');
INSERT INTO countries (code, name) VALUES ('VI', 'U.S. Virgin Islands');
INSERT INTO countries (code, name) VALUES ('UG', 'Uganda');
INSERT INTO countries (code, name) VALUES ('UA', 'Ukraine');
INSERT INTO countries (code, name) VALUES ('AE', 'United Arab Emirates');
INSERT INTO countries (code, name) VALUES ('GB', 'United Kingdom');
INSERT INTO countries (code, name) VALUES ('US', 'United States');
INSERT INTO countries (code, name) VALUES ('UY', 'Uruguay');
INSERT INTO countries (code, name) VALUES ('UZ', 'Uzbekistan');
INSERT INTO countries (code, name) VALUES ('VU', 'Vanuatu');
INSERT INTO countries (code, name) VALUES ('VA', 'Vatican City');
INSERT INTO countries (code, name) VALUES ('VE', 'Venezuela');
INSERT INTO countries (code, name) VALUES ('VN', 'Vietnam');
INSERT INTO countries (code, name) VALUES ('WF', 'Wallis & Futuna');
INSERT INTO countries (code, name) VALUES ('EH', 'Western Sahara');
INSERT INTO countries (code, name) VALUES ('YE', 'Yemen');
INSERT INTO countries (code, name) VALUES ('ZM', 'Zambia');
INSERT INTO countries (code, name) VALUES ('ZW', 'Zimbabwe');
