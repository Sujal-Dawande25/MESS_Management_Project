drop table hostel;
drop table staff;
drop table student;
drop table other;
drop table one;
drop table two;
drop  sequence one_subscription_id_seq;
drop table payment_one;
drop table payment_two;


create table hostel(hostel_id int primary key,h_name  varchar(200) unique,h_address varchar(220),h_mobile_number varchar(200),h_email varchar(200),h_password varchar(200),block_no integer,room_no integer);

create table staff(staff_id int primary key,s_name   varchar(200),s_address varchar(200),s_mobile_number varchar(200),s_email varchar(200),s_password varchar(200));



create table student(student_id int primary key,st_name   varchar(200) unique,st_address varchar(500),st_mobile_number varchar(500),st_email varchar(500),st_password varchar(200));


create table other(other_id int primary key,o_name  varchar(200) unique,o_address varchar(200),o_mobile_number varchar(200),o_email varchar(200),o_password varchar(200));


CREATE SEQUENCE one_subscription_id_seq START 500;

CREATE TABLE one (
    o_subscription_id INT PRIMARY KEY DEFAULT nextval('one_subscription_id_seq'),
    o_username VARCHAR(200) UNIQUE,
o_usertype VARCHAR(200),start_date date,end_date date ,attendance varchar(200),payment_status varchar(200),o_total_no_of_absentees varchar(200)
);


create table two(t_subscription_id SERIAL  primary key,t_username varchar(200) unique,t_usertype varchar(200),start_date date,end_date date,attendance varchar(200),payment_status varchar(200),t_total_no_of_absentees varchar(200));

create table payment_one(o_subscription_id int,o_transaction_id bigint unique,o_name varchar(200),o_date_of_payment date,o_time_of_payment time,o_amount int,verify_payment_status varchar(200));


create table payment_two(t_subscription_id int,t_transaction_id bigint unique,t_name varchar(200),t_date_of_payment date,t_time_of_payment time,t_amount int,verify_payment_status varchar(200));

select * from hostel;
select *from staff;
select * from student;
select * from other;
select * from one;
select * from two;
select * from payment_one;
select * from payment_two;