update loans set product_id = 48, product_name = 'FC5',           loan_principal = 750000, flow_fee = 17000, due_amount = 767000, current_os_amount = 767000 where id = 10225

update loan_txns set amount = 750000 where id = 26692 and loan_doc_id = 'CCA-618302-91105';
update loan_txns set amount = 767000 where id = 26693 and loan_doc_id = 'CCA-618302-91105';
#Adjust disbursal & repayment txn

update loans set product_id = 49, product_name = 'FC6',           loan_principal = 1000000, flow_fee = 22000, due_amount = 1022000, current_os_amount = 1022000 where id = 9812
update loan_txns set amount = 1000000 where id = 19218 and loan_doc_id = 'UCC01945'; 
#Adjust disbursal

update loans set product_id = 66, product_name = 'FCPROBATION 6', loan_principal = 1000000, flow_fee = 22000, due_amount = 1022000, current_os_amount = 1022000 where id = 9819
update loan_txns set amount = 1000000 where id = 19225 and loan_doc_id = 'UCC01952';
#Adjust disbursal

update loans set product_id = 66, product_name = 'FCPROBATION 6', loan_principal = 1000000, flow_fee = 22000, due_amount = 1022000, current_os_amount = 1022000 where id = 9845
update loan_txns set amount = 1000000 where id = 25952 and loan_doc_id = 'CCA-547997-98520';
#Adjust disbursal

update loan_txns set txn_id= 'W8wruIwu' where id = 27410 and loan_doc_id = 'CCA-790166-23507';

#15-Oct to disable the customers
update borrowers set status = 'disabled' where data_prvdr_cust_id in (6419969,4652968,34705935,56487622,2761
4369,56354303,84058711,14345680,18975206,52179958,15069616,97677895,77871189,65228360,96203256,77757165,96222909,01
146057,12711103,68078598,9513259,81528719,73922267,08792849,96493909,21831388,19837714,35194577,27457676,76043025,4
9425235,67880965,66421334,71658149,95406953,66790435,35217614,94231194,04031394,37908251,14664703,12938837,82447268
,18311738,23574656,33127560,84240893,45199192,58494076);    

# Justine Relationship Manager
update borrowers set flow_rel_mgr_id = 1006 where cust_id in ('UEZM-794042','UEZM-764227','UEZM-613837','UEZM-191118','UEZM-429483','UEZM-984653','UEZM-532847','UEZM-163298','UEZM-757546','UEZM-189433','UEZM-364651','UEZM-866190','UEZM-598427','UEZM-570466','UEZM-996224','UEZM-861743','UEZM-320692','UEZM-603886','UEZM-611490','UEZM-553035','UEZM-606240','UEZM-100405','UEZM-514358','UEZM-903973','UEZM-305072','UEZM-195009','UEZM-736043','UEZM-463669','UEZM-868172','UEZM-742583','UEZM-234297','UEZM-884681','UEZM-504844','UEZM-938241','UEZM-799902','UEZM-987479','UEZM-959839','UEZM-831449','UEZM-571484','UEZM-853494','UEZM-650113','UEZM-719135','UEZM-884869','UEZM-662682','UEZM-147234','UEZM-774460','UEZM-923548','UEZM-609859','UEZM-570971','UEZM-161628','UEZM-454613','UEZM-237450','UEZM-568453','UEZM-629824','UEZM-986050','UEZM-244091','UEZM-611112','UEZM-872501','UEZM-147085','UEZM-621630','UEZM-351273','UEZM-947349','UEZM-425067','UEZM-965266','UEZM-666257','UEZM-810178','UEZM-315937','UEZM-740881','UEZM-548112','UEZM-948075','UEZM-269457','UEZM-924303','UEZM-106615','UEZM-403826','UEZM-453270','UEZM-446347','UEZM-503539','UEZM-426084','UEZM-803916','UEZM-295121','UEZM-669449','UEZM-676248','UEZM-316969','UEZM-191786','UEZM-449275','UEZM-863502','UEZM-546073','UEZM-392457','UEZM-119803','UEZM-783137','UEZM-152134','UEZM-345738','UEZM-529408','UEZM-583498','UEZM-967008','UEZM-490242','UEZM-115702','UEZM-847684','UEZM-988372','UEZM-635431','UEZM-633312','UEZM-219542','UEZM-569194','UEZM-499857','UEZM-158953','UEZM-199080','UEZM-594823','UEZM-559741','UEZM-292102','UEZM-826321','UEZM-461185','UEZM-818083','UEZM-270205','UEZM-669484','UEZM-810595-DLTD','UEZM-154632','UEZM-242935-DLTD','UEZM-389171','UEZM-945510','UEZM-660841','UEZM-772875','UEZM-408791','UEZM-297897','UEZM-241936','UEZM-788402','UEZM-398893','UEZM-927911','UEZM-505992','UEZM-436040','UEZM-861579','UEZM-734219','UEZM-527512','UEZM-596917','UEZM-477326','UEZM-836702')

# Mugisha Relationship manager
update borrowers set flow_rel_mgr_id = 1680 where cust_id in ('UEZM-379250','UEZM-792581','UEZM-274641','UEZM-319385','UEZM-411278','UEZM-417649','UEZM-833766','UEZM-640498','UEZM-380822','UEZM-475513','UEZM-184781','UEZM-583403','UEZM-524095','UEZM-314731','UEZM-807436','UEZM-387744','UEZM-680593','UEZM-469098','UEZM-738734','UEZM-216673','UEZM-102285','UEZM-267430','UEZM-518370','UEZM-331120','UEZM-188463','UEZM-936496','UEZM-582240','UEZM-488083','UEZM-833656','UEZM-174401','UEZM-836186','UEZM-925041','UEZM-304570','UEZM-225142','UEZM-980222','UEZM-362083','UEZM-689257','UEZM-705738','UEZM-265227','UEZM-191328','UEZM-913710','UEZM-123661','UEZM-805617','UEZM-445727','UEZM-984156','UEZM-645078','UEZM-122818','UEZM-193862','UEZM-185570','UEZM-466888','UEZM-234419','UEZM-373510','UEZM-830719','UEZM-238050','UEZM-239789','UEZM-500311','UEZM-115458','UEZM-856219','UEZM-892678','UEZM-424364','UEZM-152467','UEZM-590106','UEZM-535889','UEZM-501638','UEZM-372403','UEZM-666502','UEZM-936145','UEZM-502664','UEZM-386632','UEZM-235891','UEZM-993633','UEZM-280139','UEZM-844905','UEZM-286207','UEZM-852689','UEZM-309742','UEZM-703497','UEZM-372227','UEZM-976947','UEZM-990953','UEZM-375336','UEZM-374689','UEZM-306462','UEZM-432597','UEZM-821251','UEZM-233609','UEZM-659658','UEZM-370800','UEZM-783940','UEZM-920522','UEZM-687723','UEZM-785851','UEZM-993568','UEZM-617663','UEZM-594388','UEZM-860119','UEZM-323009','UEZM-357173','UEZM-593974','UEZM-608475','UEZM-364573','UEZM-877994','UEZM-223038','UEZM-860154','UEZM-204215','UEZM-591310','UEZM-387599','UEZM-853830','UEZM-734020','UEZM-243806','UEZM-121601','UEZM-561644','UEZM-935678','UEZM-169406','UEZM-338523','UEZM-167694','UEZM-298824','UEZM-916009','UEZM-777051','UEZM-745402','UEZM-997164','UEZM-860648','UEZM-828149','UEZM-798445','UEZM-332154','UEZM-340550');
update app_users set person_id = 1680 where id = 21;
update persons set status = 'disabled' where id = 1489;

# Duplicate / multiple Accounts for customers
select cust_id , acc_number, GROUP_CONCAT(distinct acc_prvdr_code) , count(1)  
from accounts where cust_id is not null
group by cust_id, acc_number 
having count(1) > 1 order by count(1);

update  accounts set status = 'disabled' where cust_id is not null and acc_prvdr_code  = 'UDFC';

delete from accounts where cust_id is not null and acc_prvdr_code  = 'UDFC' and status = 'disabled';

#28-Oct
01-Nov
Daily Activity Report Issue
 select * from (select date(disbursal_date) as disbursal_date,count(id) as disbursed, sum(loan_principal) as disbursed_amount, sum(paid_amount) as repayment_received from loans where data_prvdr_code = 'UEZM' and date(disbursal_date) >= '2020-10-31' and date(disbursal_date) <= '2020-10-31' group by date(disbursal_date)) disb left outer join (select date(paid_date) as repayment_date,count(id) as repaid, sum(paid_amount) as repayment_amount,  sum(paid_amount -  loan_principal) as fee from loans where data_prvdr_code = 'UEZM' and date(paid_date) >= '2020-10-31' and date(paid_date) <= '2020-10-31' group by date(paid_date)) pay on disb.disbursal_date = pay.repayment_date;
