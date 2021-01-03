<?php
    echo "<script id='php-to-js-variables'>
// PHP Variables -> JS Variables

var mapCenterLat = ".$_SESSION['mapCenterLat'].";
var mapCenterLng = ".$_SESSION['mapCenterLng'].";

var tourActuel = ".$_SESSION['tourActuel'].";
var strategie = '".$_SESSION['strategie']."';

var nbDetectives = ".$_SESSION['nbDetectives'].";
var nbJoueursTotal = ".$_SESSION['nbJoueursTotal'].";
var nbRoutesPlayer = ".$_SESSION['nbRoutesPlayer'].";

var idQNextMX = [];
idQNextMX[0] = ".$_SESSION['idQNextMX'][0].";
idQNextMX[1] = '".$_SESSION['idQNextMX'][1]."';

var idQNextPlayer = [];
idQNextPlayer[0] = ".$_SESSION['idQNextPlayer'][0].";
idQNextPlayer[1] = '".$_SESSION['idQNextPlayer'][1]."';

var idQNextDet = [];

var idQPosActuel = [];

var idQDeDepart = [];

var tabRoutesPlayer = [];

if (strategie == 'econome' && tourActuel == 1) {
    var nbTicketsTaxi = [];
    var nbTicketsBus = [];
    var nbTicketsMetro = [];
}
";

	for ($i=0; $i<$_SESSION['nbJoueursTotal']; ++$i) {
        echo "
idQPosActuel[".$i."] = ".$_SESSION['idQPosActuel'][$i].";
idQDeDepart[".$i."] = ".$_SESSION['idQDeDepart'][$i].";
";
    }

    for ($i=0; $i<$_SESSION['nbRoutesPlayer']; ++$i) {
        echo "
tabRoutesPlayer[".$i."] = {
	idQArrivee: ".$_SESSION['tabRoutesPlayer'][$i][0].",
	transport : '".$_SESSION['tabRoutesPlayer'][$i][1]."'
};
";
    }

    for ($i=0; $i<$_SESSION['nbDetectives']; ++$i) {
        echo "
idQNextDet[".$i."] = {
    quartier: ".$_SESSION['idQNextDet'][$i][0].",
    transport : '".$_SESSION['idQNextDet'][$i][1]."'
};
";
    }

if ($_SESSION['strategie'] == 'econome' && $_SESSION['tourActuel'] == 1) {
    for ($i=0; $i<$_SESSION['nbJoueursTotal']-1; ++$i) {
        echo "
nbTicketsTaxi[".$i."] = ".$_SESSION['nbTicketsTaxi'][$i].";
nbTicketsBus[".$i."] = ".$_SESSION['nbTicketsBus'][$i].";
nbTicketsMetro[".$i."] = ".$_SESSION['nbTicketsMetro'][$i].";
";
    }
}

if($_SESSION['strategie'] == 'pistage' && $_SESSION['tourActuel'] > 2) {
    echo "
var pathGamer = [];
";
    for($i=0;$i<count($_SESSION['futurePosPlayer']);$i++){
        echo "
pathGamer[".$i."] = ".$_SESSION['futurePosPlayer'][$i].";
";
    }
}

    echo "
</script>";

?>