USE restaurant_db;

CREATE TABLE IF NOT EXISTS tentative_connexion (
  id_tentative    INT AUTO_INCREMENT PRIMARY KEY,
  ip              VARCHAR(45)  NOT NULL,
  identifiant     VARCHAR(150) NOT NULL,
  espace          ENUM('client','admin') NOT NULL,
  date_tentative  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_tentatives (espace, ip, date_tentative)
);
