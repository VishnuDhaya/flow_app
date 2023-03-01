<?php

use Illuminate\Database\Seeder;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

    DB::statement("INSERT INTO `countries` VALUES (1,'Afghanistan','AFG','Afghan afghani ','AFN','000','34°28\'N','69°11\'E,','disabled'),(2,'Aland Islands','ALA','Euro ','EUR','000','',',','disabled'),(3,'Albania','ALB','Albanian lek ','ALL','000','41°18\'N','19°49\'E,','disabled'),(4,'Algeria','DZA','Algerian dinar ','DZD','000','36°42\'N','03°08\'E,','disabled'),(5,'American Samoa','ASM','','','000','14°16\'S','170°43\'W,','disabled'),(6,'Andorra','AND','Euro ','EUR','000','42°31\'N','01°32\'E,','disabled'),(7,'Angola','AGO','Angolan kwanza ','AOA','000','08°50\'S','13°15\'E,','disabled'),(8,'Anguilla','AIA','East Caribbean dollar ','XCD','000','',',','disabled'),(9,'Antarctica','ATA','','','000','',',','disabled'),(10,'Antigua and Barbuda','ATG','East Caribbean dollar ','XCD','000','17°20\'N','61°48\'W,','disabled'),(11,'Argentina','ARG','Argentine peso ','ARS','000','36°30\'S','60°00\'W,','disabled'),(12,'Armenia','ARM','Armenian dram ','AMD','000','40°10\'N','44°31\'E,','disabled'),(13,'Aruba','ABW','Aruban florin ','AWG','000','12°32\'N','70°02\'W,','disabled'),(14,'Australia','AUS','Australian dollar ','AUD','000','35°15\'S','149°08\'E,','disabled'),(15,'Austria','AUT','Euro ','EUR','000','48°12\'N','16°22\'E,','disabled'),(16,'Azerbaijan','AZE','Azerbaijani manat ','AZN','000','40°29\'N','49°56\'E,','disabled'),(17,'Bahamas','BHS','Bahamian dollar ','BSD','000','25°05\'N','77°20\'W,','disabled'),(18,'Bahrain','BHR','Bahraini dinar ','BHD','000','26°10\'N','50°30\'E,','disabled'),(19,'Bangladesh','BGD','Bangladeshi taka ','BDT','000','23°43\'N','90°26\'E,','disabled'),(20,'Barbados','BRB','Barbadian dollar ','BBD','000','13°05\'N','59°30\'W,','disabled'),(21,'Belarus','BLR','Belarusian ruble ','BYR','000','53°52\'N','27°30\'E,','disabled'),(22,'Belgium','BEL','Euro ','EUR','000','50°51\'N','04°21\'E,','disabled'),(23,'Belize','BLZ','Belize dollar ','BZD','000','17°18\'N','88°30\'W,','disabled'),(24,'Benin','BEN','West African CFA franc ','XOF','000','06°23\'N','02°42\'E,','disabled'),(25,'Bermuda','BMU','Bermudian dollar ','BMD','000','',',','disabled'),(26,'Bhutan','BTN','Bhutanese ngultrum ','BTN','000','27°31\'N','89°45\'E,','disabled'),(27,'Bolivia','BOL','Bolivian boliviano ','BOB','000','16°20\'S','68°10\'W,','disabled'),(28,'Bonaire, Sint Eustatius and Saba','BES','United States dollar ','USD','000','','','disabled'),(29,'Bosnia and Herzegovina','BIH','Bosnia and Herzegovina convertible mark ','BAM','000','43°52\'N','18°26\'E,','disabled'),(30,'Botswana','BWA','Botswana pula ','BWP','000','24°45\'S','25°57\'E,','disabled'),(31,'Bouvet Island','BVT','','','000','',',','disabled'),(32,'Brazil','BRA','Brazilian real ','BRL','000','15°47\'S','47°55\'W,','disabled'),(33,'British Indian Ocean Territory','IOT','United States dollar ','USD','000','18°27\'N','64°37\'W,','disabled'),(34,'Brunei','BRN','Brunei dollar ','BND','000','04°52\'N','115°00\'E,','disabled'),(35,'Bulgaria','BGR','Bulgarian lev ','BGN','000','42°45\'N','23°20\'E,','disabled'),(36,'Burkina Faso','BFA','West African CFA franc ','XOF','000','12°15\'N','01°30\'W,','disabled'),(37,'Burundi','BDI','Burundian franc ','BIF','000','03°16\'S','29°18\'E,','disabled'),(38,'Cambodia','KHM','Cambodian riel ','KHR','000','11°33\'N','104°55\'E,','disabled'),(39,'Cameroon','CMR','Central African CFA franc ','XAF','000','03°50\'N','11°35\'E,','disabled'),(40,'Canada','CAN','Canadian dollar ','CAD','000','45°27\'N','75°42\'W,','disabled'),(41,'Cape Verde','CPV','Cape Verdean escudo ','CVE','000','15°02\'N','23°34\'W,','disabled'),(42,'Cayman Islands','CYM','Cayman Islands dollar ','KYD','000','19°20\'N','81°24\'W,','disabled'),(43,'Central African Republic','CAF','Central African CFA franc ','XAF','000','04°23\'N','18°35\'E,','disabled'),(44,'Chad','TCD','Central African CFA franc ','XAF','000','12°10\'N','14°59\'E,','disabled'),(45,'Chile','CHL','Chilean peso ','CLP','000','33°24\'S','70°40\'W,','disabled'),(46,'China','CHN','Chinese yuan ','CNY','000','39°55\'N','116°20\'E,','disabled'),(47,'Christmas Island','CXR','Australian dollar ','AUD','000','',',','disabled'),(48,'Cocos (Keeling) Islands','CCK','Australian dollar ','AUD','000','',',','disabled'),(49,'Colombia','COL','Colombian peso ','COP','000','04°34\'N','74°00\'W,','disabled'),(50,'Comoros','COM','Comorian franc ','KMF','000','11°40\'S','43°16\'E,','disabled'),(51,'Congo, Republic of the','COG','Central African CFA franc ','XAF','000','04°09\'S','15°12\'E','disabled'),(52,'Congo, the Democratic Republic of the','COD','Congolese franc ','CDF','000','','','disabled'),(53,'Cook Islands','COK','New Zealand dollar ','NZD','000','',',','disabled'),(54,'Costa Rica','CRI','Costa Rican colón ','CRC','000','09°55\'N','84°02\'W,','disabled'),(55,'Cote d\'Ivoire','CIV','West African CFA franc ','XOF','000','06°49\'N','05°17\'W,','disabled'),(56,'Croatia','HRV','Croatian kuna ','HRK','000','45°50\'N','15°58\'E,','disabled'),(57,'Cuba','CUB','Cuban convertible peso ','CUC','000','23°08\'N','82°22\'W,','disabled'),(58,'Curacao','CUW','Netherlands Antillean guilder ','ANG','000','',',','disabled'),(59,'Cyprus','CYP','Euro ','EUR','000','35°10\'N','33°25\'E,','disabled'),(60,'Czech Republic','CZE','Czech koruna ','CZK','000','50°05\'N','14°22\'E,','disabled'),(61,'Denmark','DNK','Danish krone ','DKK','000','55°41\'N','12°34\'E,','disabled'),(62,'Djibouti','DJI','Djiboutian franc ','DJF','000','11°08\'N','42°20\'E,','disabled'),(63,'Dominica','DMA','East Caribbean dollar ','XCD','000','15°20\'N','61°24\'W,','disabled'),(64,'Dominican Republic','DOM','Dominican peso ','DOP','000','18°30\'N','69°59\'W,','disabled'),(65,'East Timor','TLS','United States dollar ','USD','000','08°29\'S','125°34\'E,','disabled'),(66,'Ecuador','ECU','United States dollar ','USD','000','00°15\'S','78°35\'W,','disabled'),(67,'Egypt','EGY','Egyptian pound ','EGP','000','30°01\'N','31°14\'E,','disabled'),(68,'El Salvador','SLV','Salvadoran colón ','SVC','000','13°40\'N','89°10\'W,','disabled'),(69,'Equatorial Guinea','GNQ','Central African CFA franc ','XAF','000','03°45\'N','08°50\'E,','disabled'),(70,'Eritrea','ERI','Eritrean nakfa ','ERN','000','15°19\'N','38°55\'E,','disabled'),(71,'Estonia','EST','Euro ','EUR','000','59°22\'N','24°48\'E,','disabled'),(72,'Ethiopia','ETH','Ethiopian birr ','ETB','000','09°02\'N','38°42\'E,','disabled'),(73,'Falkland Islands (Malvinas)','FLK','Falkland Islands pound ','FKP','000','51°40\'S','59°51\'W,','disabled'),(74,'Faroe Islands','FRO','Danish krone ','DKK','000','62°05\'N','06°56\'W,','disabled'),(75,'Fiji','FJI','Fijian dollar ','FJD','000','18°06\'S','178°30\'E,','disabled'),(76,'Finland','FIN','Euro ','EUR','000','60°15\'N','25°03\'E,','disabled'),(77,'France','FRA','Euro ','EUR','000','48°50\'N','02°20\'E,','disabled'),(78,'French Guiana','GUF','','','000','05°05\'N','52°18\'W,','disabled'),(79,'French Polynesia','PYF','CFP franc ','XPF','000','17°32\'S','149°34\'W,','disabled'),(80,'French Southern Territories','ATF','Euro ','EUR','000','',',','disabled'),(81,'Gabon','GAB','Central African CFA franc ','XAF','000','00°25\'N','09°26\'E,','disabled'),(82,'Gambia','GMB','Gambian dalasi ','GMD','000','13°28\'N','16°40\'W,','disabled'),(83,'Georgia','GEO','Georgian lari ','GEL','000','41°43\'N','44°50\'E,','disabled'),(84,'Germany','DEU','Euro ','EUR','000','52°30\'N','13°25\'E,','disabled'),(85,'Ghana','GHA','Ghanaian cedi ','GHS','000','05°35\'N','00°06\'W,','disabled'),(86,'Gibraltar','GIB','Gibraltar pound ','GIP','000','',',','disabled'),(87,'Greece','GRC','Euro ','EUR','000','37°58\'N','23°46\'E,','disabled'),(88,'Greenland','GRL','Danish krone ','DKK','000','64°10\'N','51°35\'W,','disabled'),(89,'Grenada','GRD','East Caribbean dollar ','XCD','000','',',','disabled'),(90,'Guadeloupe','GLP','','','000','16°00\'N','61°44\'W,','disabled'),(91,'Guam','GUM','United States dollar ','USD','000','',',','disabled'),(92,'Guatemala','GTM','Guatemalan quetzal ','GTQ','000','14°40\'N','90°22\'W,','disabled'),(93,'Guernsey','GGY','British pound ','GBP','000','49°26\'N','02°33\'W,','disabled'),(94,'Guinea','GIN','Guinean franc ','GNF','000','09°29\'N','13°49\'W,','disabled'),(95,'Guinea-Bissau','GNB','West African CFA franc ','XOF','000','11°45\'N','15°45\'W,','disabled'),(96,'Guyana','GUY','Guyanese dollar ','GYD','000','06°50\'N','58°12\'W,','disabled'),(97,'Haiti','HTI','Haitian gourde ','HTG','000','18°40\'N','72°20\'W,','disabled'),(98,'Heard Island and McDonald Islands','HMD','','','000','53°00\'S','74°00\'E,','disabled'),(99,'Honduras','HND','Honduran lempira ','HNL','000','14°05\'N','87°14\'W,','disabled'),(100,'Hong Kong','HKG','Hong Kong dollar ','HKD','000','',',','disabled'),(101,'Hungary','HUN','Hungarian forint ','HUF','000','47°29\'N','19°05\'E,','disabled'),(102,'Iceland','ISL','Icelandic króna ','ISK','000','64°10\'N','21°57\'W,','disabled'),(103,'India','IND','Indian rupee ','INR','IST','28°37\'N','77°13\'E,','enabled'),(104,'Indonesia','IDN','Indonesian rupiah ','IDR','000','06°09\'S','106°49\'E,','disabled'),(105,'Iran','IRN','Iranian rial ','IRR','000','35°44\'N','51°30\'E,','disabled'),(106,'Iraq','IRQ','Iraqi dinar ','IQD','000','33°20\'N','44°30\'E,','disabled'),(107,'Ireland','IRL','Euro ','EUR','000','53°21\'N','06°15\'W,','disabled'),(108,'Isle of Man','IMN','British pound ','GBP','000','',',','disabled'),(109,'Israel','ISR','Israeli new shekel ','ILS','000','31°47\'N','35°12\'E,','disabled'),(110,'Italy','ITA','Euro ','EUR','000','41°54\'N','12°29\'E,','disabled'),(111,'Jamaica','JAM','Jamaican dollar ','JMD','000','18°00\'N','76°50\'W,','disabled'),(112,'Japan','JPN','Japanese yen ','JPY','000','',',','disabled'),(113,'Jersey','JEY','British pound ','GBP','000','',',','disabled'),(114,'Jordan','JOR','Jordanian dinar ','JOD','000','31°57\'N','35°52\'E,','disabled'),(115,'Kazakhstan','KAZ','Kazakhstani tenge ','KZT','000','51°10\'N','71°30\'E,','disabled'),(116,'Kenya','KEN','Kenyan shilling ','KES','000','01°17\'S','36°48\'E,','disabled'),(117,'Kiribati','KIR','Australian dollar ','AUD','000','01°30\'N','173°00\'E,','disabled'),(118,'Kuwait','KWT','Kuwaiti dinar ','KWD','000','29°30\'N','48°00\'E,','disabled'),(119,'Kyrgyzstan','KGZ','Kyrgyzstani som ','KGS','000','42°54\'N','74°46\'E,','disabled'),(120,'Laos','LAO','Lao kip ','LAK','000','17°58\'N','102°36\'E,','disabled'),(121,'Latvia','LVA','Latvian lats ','LVL','000','56°53\'N','24°08\'E,','disabled'),(122,'Lebanon','LBN','Lebanese pound ','LBP','000','33°53\'N','35°31\'E,','disabled'),(123,'Lesotho','LSO','Lesotho loti ','LSL','000','29°18\'S','27°30\'E,','disabled'),(124,'Liberia','LBR','Liberian dollar ','LRD','000','06°18\'N','10°47\'W,','disabled'),(125,'Libya','LBY','Libyan dinar ','LYD','000','32°49\'N','13°07\'E,','disabled'),(126,'Liechtenstein','LIE','Swiss franc ','CHF','000','47°08\'N','09°31\'E,','disabled'),(127,'Lithuania','LTU','Lithuanian litas ','LTL','000','54°38\'N','25°19\'E,','disabled'),(128,'Luxembourg','LUX','Euro ','EUR','000','49°37\'N','06°09\'E,','disabled'),(129,'Macau','MAC','Macanese pataca ','MOP','000','22°12\'N','113°33\'E,','disabled'),(130,'Macedonia','MKD','Macedonian denar ','MKD','000','',',','disabled'),(131,'Madagascar','MDG','Malagasy ariary ','MGA','000','18°55\'S','47°31\'E,','disabled'),(132,'Malawi','MWI','Malawian kwacha ','MWK','000','14°00\'S','33°48\'E,','disabled'),(133,'Malaysia','MYS','Malaysian ringgit ','MYR','000','03°09\'N','101°41\'E,','disabled'),(134,'Maldives','MDV','Maldivian rufiyaa ','MVR','000','04°00\'N','73°28\'E,','disabled'),(135,'Mali','MLI','West African CFA franc ','XOF','000','12°34\'N','07°55\'W,','disabled'),(136,'Malta','MLT','Euro ','EUR','000','35°54\'N','14°31\'E,','disabled'),(137,'Marshall Islands','MHL','United States dollar ','USD','000','',',','disabled'),(138,'Martinique','MTQ','','','000','14°36\'N','61°02\'W,','disabled'),(139,'Mauritania','MRT','Mauritanian ouguiya ','MRO','000','20°10\'S','57°30\'E,','disabled'),(140,'Mauritius','MUS','Mauritian rupee ','MUR','000','',',','disabled'),(141,'Mayotte','MYT','Euro ','EUR','000','12°48\'S','45°14\'E,','disabled'),(142,'Mexico','MEX','Mexican peso ','MXN','000','19°20\'N','99°10\'W,','disabled'),(143,'Micronesia, Federated States of','FSM','Micronesian dollar ','None','000','06°55\'N','158°09\'E','disabled'),(144,'Moldova, Republic of','MDA','Moldovan leu ','MDL','000','47°02\'N','28°50\'E','disabled'),(145,'Monaco','MCO','Euro ','EUR','000','',',','disabled'),(146,'Mongolia','MNG','Mongolian togrog','MNT','000','',',','disabled'),(147,'Montenegro','MNE','Euro ','EUR','000','',',','disabled'),(148,'Montserrat','MSR','East Caribbean dollar ','XCD','000','',',','disabled'),(149,'Morocco','MAR','Moroccan dirham ','MAD','000','',',','disabled'),(150,'Mozambique','MOZ','Mozambican metical ','MZN','000','25°58\'S','32°32\'E,','disabled'),(151,'Myanmar (Burma)','MMR','Myanma kyat ','MMK','000','16°45\'N','96°20\'E,','disabled'),(152,'Namibia','NAM','Namibian dollar ','NAD','000','22°35\'S','17°04\'E,','disabled'),(153,'Nauru','NRU','Australian dollar ','AUD','000','',',','disabled'),(154,'Nepal','NPL','Nepalese rupee ','NPR','000','27°45\'N','85°20\'E,','disabled'),(155,'Netherlands','NLD','Euro ','EUR','000','52°23\'N','04°54\'E,','disabled'),(156,'New Caledonia','NCL','CFP franc ','XPF','000','22°17\'S','166°30\'E,','disabled'),(157,'New Zealand','NZL','New Zealand dollar ','NZD','000','41°19\'S','174°46\'E,','disabled'),(158,'Nicaragua','NIC','Nicaraguan córdoba ','NIO','000','12°06\'N','86°20\'W,','disabled'),(159,'Niger','NER','West African CFA franc ','XOF','000','13°27\'N','02°06\'E,','disabled'),(160,'Nigeria','NGA','Nigerian naira ','NGN','000','09°05\'N','07°32\'E,','disabled'),(161,'Niue','NIU','New Zealand dollar ','NZD','000','',',','disabled'),(162,'Norfolk Island','NFK','Australian dollar ','AUD','000','45°20\'S','168°43\'E,','disabled'),(163,'North Korea','PRK','North Korean won ','KPW','000','',',','disabled'),(164,'Northern Mariana Islands','MNP','United States dollar ','USD','000','15°12\'N','145°45\'E,','disabled'),(165,'Norway','NOR','Norwegian krone ','NOK','000','59°55\'N','10°45\'E,','disabled'),(166,'Oman','OMN','Omani rial ','OMR','000','23°37\'N','58°36\'E,','disabled'),(167,'Pakistan','PAK','Pakistani rupee ','PKR','000','33°40\'N','73°10\'E,','disabled'),(168,'Palau','PLW','Palauan dollar ','None','000','07°20\'N','134°28\'E,','disabled'),(169,'Palestinian Territory, Occupied','PSE','','','000','','','disabled'),(170,'Panama','PAN','Panamanian balboa ','PAB','000','09°00\'N','79°25\'W,','disabled'),(171,'Papua New Guinea','PNG','Papua New Guinean kina ','PGK','000','09°24\'S','147°08\'E,','disabled'),(172,'Paraguay','PRY','Paraguayan guaraní ','PYG','000','25°10\'S','57°30\'W,','disabled'),(173,'Peru','PER','Peruvian nuevo sol ','PEN','000','12°00\'S','77°00\'W,','disabled'),(174,'Philippines','PHL','Philippine peso ','PHP','000','14°40\'N','121°03\'E,','disabled'),(175,'Pitcairn','PCN','New Zealand dollar ','NZD','000','',',','disabled'),(176,'Poland','POL','Polish złoty ','PLN','000','52°13\'N','21°00\'E,','disabled'),(177,'Portugal','PRT','Euro ','EUR','000','38°42\'N','09°10\'W,','disabled'),(178,'Puerto Rico','PRI','United States dollar ','USD','000','18°28\'N','66°07\'W,','disabled'),(179,'Qatar','QAT','Qatari riyal ','QAR','000','25°15\'N','51°35\'E,','disabled'),(180,'Reunion','REU','','','000','',',','disabled'),(181,'Romania','ROU','Romanian leu ','RON','000','44°27\'N','26°10\'E,','disabled'),(182,'Russia','RUS','Russian ruble ','RUB','000','55°45\'N','37°35\'E,','disabled'),(183,'Rwanda','RWA','Rwandan franc ','RWF','CAT','01°59\'S','30°04\'E,','enabled'),(184,'Saint Barthelemy','BLM','Euro ','EUR','000','',',','disabled'),(185,'Saint Helena','SHN','Saint Helena pound ','SHP','000','',',','disabled'),(186,'Saint Kitts and Nevis','KNA','East Caribbean dollar ','XCD','000','',',','disabled'),(187,'Saint Lucia','LCA','East Caribbean dollar ','XCD','000','14°02\'N','60°58\'W,','disabled'),(188,'Saint Martin (French part)','MAF','Euro ','EUR','000','',',','disabled'),(189,'Saint Pierre and Miquelon','SPM','Euro ','EUR','000','46°46\'N','56°12\'W,','disabled'),(190,'Saint Vincent and the Grenadines','VCT','East Caribbean dollar ','XCD','000','13°10\'N','61°10\'W,','disabled'),(191,'Samoa','WSM','Samoan tala ','WST','000','13°50\'S','171°50\'W,','disabled'),(192,'San Marino','SMR','Euro ','EUR','000','43°55\'N','12°30\'E,','disabled'),(193,'Sao Tome and Principe','STP','Sao Tomeeand Principe dobra','STD','000','00°10\'N','06°39\'E,','disabled'),(194,'Saudi Arabia','SAU','Saudi riyal ','SAR','000','24°41\'N','46°42\'E,','disabled'),(195,'Senegal','SEN','West African CFA franc ','XOF','000','14°34\'N','17°29\'W,','disabled'),(196,'Serbia','SRB','Serbian dinar ','RSD','000','',',','disabled'),(197,'Seychelles','SYC','Seychellois rupee ','SCR','000','',',','disabled'),(198,'Sierra Leone','SLE','Sierra Leonean leone ','SLL','000','08°30\'N','13°17\'W,','disabled'),(199,'Singapore','SGP','Brunei dollar ','BND','000','',',','disabled'),(200,'Sint Maarten','SXM','Netherlands Antillean guilder ','ANG','000','',',','disabled'),(201,'Slovakia','SVK','Euro ','EUR','000','48°10\'N','17°07\'E,','disabled'),(202,'Slovenia','SVN','Euro ','EUR','000','46°04\'N','14°33\'E,','disabled'),(203,'Solomon Islands','SLB','Solomon Islands dollar ','SBD','000','09°27\'S','159°57\'E,','disabled'),(204,'Somalia','SOM','Somali shilling ','SOS','000','02°02\'N','45°25\'E,','disabled'),(205,'South Africa','ZAF','South African rand ','ZAR','000','25°44\'S','28°12\'E,','disabled'),(206,'South Georgia and the South Sandwich Islands','SGS','British pound ','GBP','000','',',','disabled'),(207,'South Korea','KOR','South Korean won ','KRW','000','',',','disabled'),(208,'Spain','ESP','Euro ','EUR','000','40°25\'N','03°45\'W,','disabled'),(209,'Sri Lanka','LKA','Sri Lankan rupee ','LKR','000','',',','disabled'),(210,'Sudan','SDN','Sudanese pound ','SDG','000','15°31\'N','32°35\'E,','disabled'),(211,'Suriname','SUR','Surinamese dollar ','SRD','000','05°50\'N','55°10\'W,','disabled'),(212,'Svalbard and Jan Mayen','SJM','','','000','',',','disabled'),(213,'Swaziland','SWZ','Swazi lilangeni ','SZL','000','26°18\'S','31°06\'E,','disabled'),(214,'Sweden','SWE','Swedish krona ','SEK','000','59°20\'N','18°03\'E,','disabled'),(215,'Switzerland','CHE','Swiss franc ','CHF','000','46°57\'N','07°28\'E,','disabled'),(216,'Syrian Arab Republic','SYR','Syrian pound ','SYP','000','33°30\'N','36°18\'E,','disabled'),(217,'Taiwan','TWN','New Taiwan dollar ','TWD','000','',',','disabled'),(218,'Tajikistan','TJK','Tajikistani somoni ','TJS','000','38°33\'N','68°48\'E,','disabled'),(219,'Tanzania','TZA','Tanzanian shilling ','TZS','000','',',','disabled'),(220,'Thailand','THA','Thai baht ','THB','000','13°45\'N','100°35\'E,','disabled'),(221,'Togo','TGO','West African CFA franc ','XOF','000','06°09\'N','01°20\'E,','disabled'),(222,'Tokelau','TKL','New Zealand dollar ','NZD','000','',',','disabled'),(223,'Tonga','TON','Tongan paanga','TOP','000','21°10\'S','174°00\'W,','disabled'),(224,'Trinidad and Tobago','TTO','Trinidad and Tobago dollar ','TTD','000','',',','disabled'),(225,'Tunisia','TUN','Tunisian dinar ','TND','000','36°50\'N','10°11\'E,','disabled'),(226,'Turkey','TUR','Turkish lira ','TRY','000','39°57\'N','32°54\'E,','disabled'),(227,'Turkmenistan','TKM','Turkmenistani manat ','TMT','000','38°00\'N','57°50\'E,','disabled'),(228,'Turks and Caicos Islands','TCA','United States dollar ','USD','000','',',','disabled'),(229,'Tuvalu','TUV','Australian dollar ','AUD','000','08°31\'S','179°13\'E,','disabled'),(230,'Uganda','UGA','Ugandan shilling ','UGX','EAT','00°20\'N','32°30\'E,','enabled'),(231,'Ukraine','UKR','Ukrainian hryvnia ','UAH','000','50°30\'N','30°28\'E,','disabled'),(232,'United Arab Emirates','ARE','United Arab Emirates dirham ','AED','000','24°28\'N','54°22\'E,','disabled'),(233,'United Kingdom','GBR','British pound ','GBP','BST','51°36\'N','00°05\'W,','enabled'),(234,'United States','USA','United States dollar ','USD','000','39°91\'N','77°02\'W,','disabled'),(235,'United States Minor Outlying Islands','UMI','United States dollar ','USD','000','',',','disabled'),(236,'Uruguay','URY','Uruguayan peso ','UYU','000','34°50\'S','56°11\'W,','disabled'),(237,'Uzbekistan','UZB','Uzbekistani som ','UZS','000','41°20\'N','69°10\'E,','disabled'),(238,'Vanuatu','VUT','Vanuatu vatu ','VUV','000','17°45\'S','168°18\'E,','disabled'),(239,'Vatican City','VAT','Euro ','EUR','000','',',','disabled'),(240,'Venezuela','VEN','Venezuelan bolivar ','VEF','000','10°30\'N','66°55\'W,','disabled'),(241,'Vietnam','VNM','Vietnamese dong ','VND','000','21°05\'N','105°55\'E,','disabled'),(242,'Virgin Islands, British','VGB','British Virgin Islands dollar ','None','000','','','disabled'),(243,'Virgin Islands, U.S.','VIR','United States dollar ','USD','000','','','disabled'),(244,'Wallis and Futuna','WLF','CFP franc ','XPF','000','',',','disabled'),(245,'Western Sahara','ESH','Moroccan dirham ','MAD','000','',',','disabled'),(246,'Yemen','YEM','Yemeni rial ','YER','000','',',','disabled'),(247,'Zambia','ZMB','Zambian kwacha ','ZMK','000','15°28\'S','28°16\'E,','disabled'),(248,'Zimbabwe','ZWE','Botswana pula ','BWP','000','17°43\'S','31°02\'E,','disabled'),(256,'','','','','000','','','disabled')"
);
    }
}