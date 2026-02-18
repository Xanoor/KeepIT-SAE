# Renvoie un dataframe indiquant le nombre de connexion dont le temps
# d'activité (en minutes) correspond à un palier

moyennes_connections <- function(fichier, nombre_paliers) {
  
  donnees = read.csv(fichier, sep = ",")
  
  stats_moyennes = aggregate(duration_seconds ~ login, 
                              data = donnees, 
                              FUN = mean)
  stats_moyennes$moyenne_min = stats_moyennes$duration_seconds / 60
  
  val_min = min(stats_moyennes$moyenne_min)
  val_max = max(stats_moyennes$moyenne_min)
  
  pas = (val_max - val_min) / nombre_paliers
  
  paliers = c()
  valeurs = c()
  
  for (i in 1:nombre_paliers) {
    borne_basse = val_min + (i - 1) * pas
    borne_haute = val_min + i * pas
    effectif <- sum(stats_moyennes$moyenne_min >= borne_basse & stats_moyennes$moyenne_min <= borne_haute)
    paliers[i] = round(borne_basse, 0)
    valeurs[i] = effectif
  }
  
  affichage = data.frame(
    Palier = paliers,
    Effectif = valeurs
  )
  
  return(affichage)
}

moyennes_connections("connections.csv", 10)
