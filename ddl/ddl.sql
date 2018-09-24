create table device
(
  id int(10) unsigned auto_increment primary key,
  name varchar(255) not null,
  status tinyint not null,
  address varchar(46) not null,
  port smallint(6) not null,
  consumed_total float not null default 0,
  session_id int(10) unsigned null,
  failed_attempts tinyint(3) unsigned not null default 0
)
;

create table session
(
  id int(10) unsigned auto_increment primary key,
  start datetime not null,
  end datetime null,
  device_id int(10) unsigned not null,
  constraint session_device_fk foreign key (device_id) references device (id) on delete cascade
)
;

create index session_device_fk
  on session (device_id)
;

alter table `device` add constraint device_session_fk foreign key (`session_id`) references `session`(`id`) on delete set null;

create table measurement
(
  id int(10) unsigned auto_increment primary key,
  time datetime not null,
  value float not null,
  device_id int(10) unsigned not null,
  session_id int(10) unsigned not null,
  constraint measurement_device_fk foreign key (device_id) references device (id) on delete cascade,
  constraint measurement_session_fk foreign key (session_id) references session (id)
)
;

create index measurement_device_fk
  on measurement (device_id)
;

create index measurement_session_fk
  on measurement (session_id)
;

create table user
(
  id int(10) unsigned auto_increment primary key,
  username varchar(255) not null,
  password char(60) not null
)
;