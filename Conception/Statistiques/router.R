library(plumber)
library(jsonlite)

# On charge vos fichiers existants pour que les fonctions soient en mémoire
source("Stat1.R")
source("Stat3.R")
source("Stat4.R")

#* @apiTitle API de Statistiques Inventaire
#* @apiDescription Cette API expose les calculs statistiques R pour PHP.

# --- Endpoint pour Stat1 ---
#* @get /stat1
#* @param ville
#* @param ram
#* @param disk
function(ville, ram, disk) {
  pourcentage_pc_ok("inventory_devices.csv", ville, as.numeric(ram), as.numeric(disk))
}

#* @get /stat2
#* @param debut Date au format YYYY-MM-DD
#* @param mois Nombre de mois à projeter
function(debut, mois) {
  donnees <- read.csv("inventory_devices.csv", sep = ",") #
  donnees$WARRANTY_END <- as.Date(donnees$WARRANTY_END) #
  
  debut_obj <- as.Date(debut)
  fin_obj <- seq(debut_obj, length = 2, by = paste(mois, "months"))[2]
  
  # Filtrage des dates
  selection <- donnees[donnees$WARRANTY_END >= debut_obj & donnees$WARRANTY_END <= fin_obj, ]
  selection$Mois <- format(selection$WARRANTY_END, "%Y-%m")
  
  # On crée un comptage croisé (Mois x Constructeur)
  res <- as.data.frame(table(selection$Mois, selection$MANUFACTURER))
  colnames(res) <- c("Mois", "Constructeur", "Nombre")
  
  return(res)
}

# --- Endpoint pour Stat3 ---
#* Renvoie les paliers de connexion
#* @get /stat3
#* @param paliers Nombre de paliers souhaités
function(paliers) {
  # Appel de moyennes_connections (issue de Stat3.R)
  res_df <- moyennes_connections("connections.csv", as.numeric(paliers))
  
  # Un dataframe est automatiquement converti en tableau JSON par Plumber
  return(res_df)
}

#* @get /stat4
#* @param ram
#* @param ecran
function(ram, ecran) {
  resultat = alerte_ecran_pc("inventory_devices.csv", "inventory_monitors2.csv", as.numeric(ram), as.numeric(ecran))
  return(resultat)
}