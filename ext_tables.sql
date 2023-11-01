#
# Table structure for table 'tx_marketingautomation_persona'
#
CREATE TABLE tx_marketingautomation_persona
(
    title       varchar(255) DEFAULT '' NOT NULL,
    description text
);

CREATE TABLE pages
(
    tx_marketingautomation_persona varchar(100) DEFAULT '' NOT NULL,
);

CREATE TABLE tt_content
(
    tx_marketingautomation_persona varchar(100) DEFAULT '' NOT NULL,
);