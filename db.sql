CREATE TABLE IF NOT EXISTS locationpoints (
  id int(11) NOT NULL AUTO_INCREMENT,
  time datetime NOT NULL,
  latitude decimal(10, 8) NOT NULL,
  longitude decimal(11, 8) NOT NULL,
  accuracy decimal(8, 3) NOT NULL,
  import_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  distance_to_point_before int(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY time (time)
)
