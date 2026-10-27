
<?php

function formatJoueurs($min, $max){

    // on vérifie si c'est une partie en solo 
    if($min == 1 && $max == 1){
        return "Solo";
    }

    // si le minimum et le maximum sont égaux
    elseif($min == $max){
        return $min." joueurs";
    }

    // dans les autres cas
    else{
        return $min." à ".$max." joueurs";
    }
}


function categorieDuree($duree){

    // durée est inférieure à 30 minutes
    if($duree < 30){
        return "Partie rapide";
    }

    // durée est comprise entre 30 et 90 minutes inclus
    elseif($duree <= 90){
        return "Partie moyenne";
    }

    // au-delà de 90 minutes
    else{
        return "Partie longue";
    }
}

?>
