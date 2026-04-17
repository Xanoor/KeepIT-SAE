#Renvoie le pourcentage de PC correspondant aux exigences passés en paramètres
#en fonction du site

pourcentage_pc_ok <- function(fichier, ville, minimum_ram, minimum_disk) {
  
  donnees <- read.csv2(fichier, sep = ",")
  
  donnees_ville <- donnees[donnees$LOCATION == ville, ]
  
  total <- nrow(donnees_ville)
  reussite <- sum(donnees_ville$RAM_MB >= minimum_ram & donnees_ville$DISK_GB >= minimum_disk)
  
  taux_ok <- round((reussite / total) * 100, 2)
  taux_ko <- 100 - taux_ok
  
  # On retourne une liste que Plumber transformera en JSON
  return(list(
    labels = c("Conformes", "Non-conformes"),
    valeurs = c(taux_ok, taux_ko)
  ))
}

pourcentage_pc_ok("inventory_devices.csv", "Rambouillet", 16384, 512)

