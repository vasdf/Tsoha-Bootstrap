<?php
  
  class KäyttäjäController extends BaseController{

  	public static function kirjaudu(){
  		View::make('käyttäjä/kirjaudu.html');
  	}

  	public static function käsittele_kirjautuminen(){
  		$tiedot = $_POST;

  		$käyttäjä = Käyttäjä::tunnistaudu($tiedot['käyttäjätunnus'], $tiedot['salasana']);

  		if(!$käyttäjä){
  			View::make('käyttäjä/kirjaudu.html', array('error' => 'Väärä käyttäjätunnus tai salasana!', 'käyttäjätunnus' => $tiedot['käyttäjätunnus']));
  		} else {
  			$_SESSION['käyttäjä'] = $käyttäjä->id;

  			Redirect::to('/', array('message' => 'Tervetuloa takaisin ' . $käyttäjä->nimi . '!'));
  		}
  	}

    public static function kirjaudu_ulos(){
      $_SESSION['käyttäjä'] = null;
      Redirect::to('/kirjaudu', array('message' => 'Olet kirjautunut ulos!'));
    }

    public static function näytä($id){
      $käyttäjä = Käyttäjä::etsi($id);
      $käyttäjän_tuotteet = TuoteController::käyttäjän_tuotteet($id);

      View::make('käyttäjä/profiili.html', array('käyttäjä' => $käyttäjä, 'tuotteet' => $käyttäjän_tuotteet));
    }

    public static function rekisteröidy(){
      View::make('käyttäjä/rekisteröidy.html');
    }

    public static function tallenna(){
      $tiedot = $_POST;

      $käyttäjä = new Käyttäjä(array(
        'nimi' => $tiedot['nimi'],
        'puh' => $tiedot['puhelinnumero'],
        'sähköposti' => $tiedot['sähköposti'],
        'salasana' => $tiedot['salasana'],
        'salasana2' => $tiedot['salasana2']
        ));

      $errors = $käyttäjä->errors();

      if(count($errors) == 0){
        
        $käyttäjä->tallenna();

        Redirect::to('/kirjaudu', array('message' => 'Olet nyt rekisteröitynyt sivulle'));
      } else {
        $attributes = array(
          'nimi' => $tiedot['nimi'],
          'puhelinnumero' => $tiedot['puhelinnumero'],
          'sähköposti' => $tiedot['sähköposti']
        );

        View::make('käyttäjä/rekisteröidy.html', array('errors' => $errors, 'attributes' => $attributes));
      }


    }
  }