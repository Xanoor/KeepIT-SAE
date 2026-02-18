proba_binomial <- function(fichier, ville, taille_salle, minimum_ok, minimum_ram, minimum_disk) {
  
  donnees <- read.csv2(fichier, sep = ",")
  
  donnees_ville <- donnees[donnees$LOCATION == ville, ]
  
  total <- nrow(donnees_ville)
  reussite <- sum(donnees_ville$RAM_MB >= minimum_ram & donnees_ville$DISK_GB >= minimum_disk)
  moy_ok <- reussite / total
  
  resultat_probabilite <- round(pbinom(minimum_ok - 1, 
                                 size = taille_salle, 
                                 prob = moy_ok, 
                                 lower.tail = FALSE) * 100, 2)
  
  taux_ok = round(moy_ok * 100, 2)
  ligne1 <- paste("Analyse faites sur la totalité du site de :", ville)
  ligne2 <- paste("Taux de PC OK sur le site :", taux_ok, "%")
  ligne3 <- paste("Probabilité d'avoir au moins", minimum_ok, "PC prêts sur", taille_salle, ":")
  ligne4 <- paste(resultat_probabilite, "%")
  
  mon_rapport <- c(ligne1, ligne2, ligne3, ligne4)
  
  print(mon_rapport)
  return(resultat_probabilite)
}

proba_binomial("inventory_devices.csv", "Rambouillet", 20, 15, 8192, 256)

