<?php
  /**
   * Luokka hallinnoi hyväksyttyjä tarjouksia eli kauppoja
   */
  class KaupatController extends BaseController{

  	public static function tallenna($tarjous_id){
      $kaupat = new Kaupat(array(
      	'tarjous_id' => $tarjous_id
      	));

      //Tarkistut ettei samaa tarjousta hyväksy kahteen kertaan
      if(self::onko_tarjous_hyväksytty($tarjous_id)){
        Redirect::to('/profiili/' . $_SESSION['käyttäjä'], array('error' => 'Tarjous, jota yritit hyväksyä, on jo hyväksytty!'));
      }


      if(TarjousController::onko_tarjous_voimassa($tarjous_id)){
        
        //Seuraavat rivit tarkistavat ovatko käyttäjän näytöllä näkyvät tiedot tarjouksesta samat kuin tietokannassa olevat tiedot tarjouksesta (Voi olla eri jos tarjousta on muokattu ja käyttäjä ei ole päivittänyt sivuaan)
        $tarjous_tiedot = $_POST;
        $tarjous_tietokannassa = TarjousController::hae_tarjous($tarjous_id);

        if($tarjous_tiedot['hintatarjous'] != $tarjous_tietokannassa->hintatarjous || $tarjous_tiedot['lisätietoja'] != $tarjous_tietokannassa->lisätietoja){
        Redirect::to('/profiili/' . $_SESSION['käyttäjä'], array('error' => 'Tarjousta, jota olit hyväksymässä, oli muokattu!'));
        }

        $kaupat->tallenna();

        TarjousController::aseta_voimassa_false($tarjous_id);

        $tuote_id = TarjousController::hae_tuotteen_id($tarjous_id);
      TuoteController::aseta_myynnissä_false($tuote_id);


        Redirect::to('/profiili/' . $_SESSION['käyttäjä'], array('message' => 'Tarjous hyväksytty!'));
      } else {
        Redirect::to('/profiili/' . $_SESSION['käyttäjä'], array('error' => 'Tarjous ei enää voimassa!'));
      }
  	}

  	public static function käyttäjän_ostamat_tuotteet($käyttäjä_id){
  	  return Kaupat::käyttäjän_ostamat_tuotteet($käyttäjä_id);
  	}

  	public static function käyttäjän_myydyt_tuotteet($käyttäjä_id){
  	  return Kaupat::käyttäjän_myydyt_tuotteet($käyttäjä_id);
  	}

    public static function onko_tarjous_hyväksytty($tarjous_id){
      return Kaupat::onko_tarjous_hyväksytty($tarjous_id);
    }

    public static function onko_tuotteesta_tehty_kaupat($tuote_id){
      return Kaupat::onko_tuotteesta_tehty_kaupat($tuote_id);
    }
  }