# Génère un graphique permettant de prévoir pour chaque mois
# le nombre de fin de garanties à partir d'une date

library(ggplot2)

graphique_garantie <- function(fichier, date_debut, nb_mois) {

  donnees <- read.csv2(fichier, sep = ",")

  donnees$WARRANTY_END <- as.Date(donnees$WARRANTY_END)

  date_debut_obj <- as.Date(date_debut)
  date_fin_obj <- seq(date_debut_obj, length = 2, by = paste(nb_mois, "months"))[2]

  filtre_dates <- donnees$WARRANTY_END >= date_debut_obj & donnees$WARRANTY_END <= date_fin_obj
  donnees_selection <- donnees[filtre_dates, ]
  
  donnees_selection$Mois_Annee <- format(donnees_selection$WARRANTY_END, "%Y-%m")
  
  graphique <- ggplot(donnees_selection, aes(x = Mois_Annee, fill = MANUFACTURER)) +
    geom_bar() +
    scale_fill_manual(values = c("Dell" = "#9C51B6", "HP" = "#005FFF", "Lenovo" = "#E00000")) +
    labs(
      title = paste("Fins de garantie sur", nb_mois, "mois"),
      subtitle = paste("À partir de :", date_debut),
      x = "Mois d'échéance",
      y = "Nombre d'actifs",
      fill = "Constructeur"
    ) +
    theme_minimal() +
    # Incline les dates sur l'échelle x (sinon c'est illisible)
    theme(axis.text.x = element_text(angle = 45, hjust = 1))

  print(graphique)
}

graphique_garantie("inventory_devices.csv", "2026-01-01", 18)