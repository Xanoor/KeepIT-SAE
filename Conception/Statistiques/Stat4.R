#Cette statistique nous donne le nombre de PC jugée "faible" (par rapport à la RAM) 
#associés à des écrans considérés trop grands
alerte_ecran_pc <- function(fichier_pc, fichier_ecrans, seuil_max_ram, seuil_mini_ecran) {
  
  pc = read.csv(fichier_pc, sep = ",")
  ecrans = read.csv(fichier_ecrans, sep = ",")
  
  donnees_fusionnees = merge(pc, ecrans, by.x = "NAME", by.y = "ATTACHED_TO")
  
  incoherences = donnees_fusionnees[
    donnees_fusionnees$RAM_MB < seuil_max_ram & 
      donnees_fusionnees$SIZE_INCH >= seuil_mini_ecran, 
  ]

  nbre_incoherences = nrow(incoherences)
  
  # On crée un fichier csv conservant la liste des UC concernés (et leur site)
  postes_concernes = incoherences[, c("NAME", "RAM_MB", "SIZE_INCH", "LOCATION")]
  write.csv(postes_concernes, file = "list_monitor_device.csv", row.names = F)
  
  cat("Nombre de grands écrans associés à un PC pas assez puissant :", nbre_incoherences, "\n")

  return(nbre_incoherences)
}

alerte_ecran_pc("inventory_devices.csv", "inventory_monitors2.csv", 16384, 27)
