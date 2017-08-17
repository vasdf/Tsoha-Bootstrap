<?php

  class Tuote extends BaseModel{

  	public $id, $myyjä_id, $myyjä_nimi, $kuvaus, $hinta, $lisätietoja, $lisäyspäivä;

  	public function __construct($attributes){
  		parent::__construct($attributes);
      $this->validators = array('validate_kuvaus', 'validate_hinta', 'validate_lisätiedot');
  	}

  	public static function kaikki(){
  		$query = DB::connection()->prepare('SELECT t.id, t.myyjä_id, k.nimi, t.kuvaus, t.hinta, t.lisätietoja, t.lisäyspäivä FROM Tuote t INNER JOIN Käyttäjä k ON t.myyjä_id = k.id');
  		$query->execute();
  		$rivit = $query->fetchAll();
  		$tuotteet = array();

  		foreach($rivit as $rivi){

  			$tuotteet[] = new Tuote(array(
  				'id' => $rivi['id'],
  				'myyjä_id' => $rivi['myyjä_id'],
          'myyjä_nimi' => $rivi['nimi'],
  				'kuvaus' => $rivi['kuvaus'],
  				'hinta' => $rivi['hinta'],
  				'lisätietoja' => $rivi['lisätietoja'],
  				'lisäyspäivä' => $rivi['lisäyspäivä']
  				));
  		}

  		return $tuotteet;
  	}

  	public static function etsi($id){
  		$query = DB::connection()->prepare('SELECT * FROM Tuote WHERE id = :id LIMIT 1');
  		$query->execute(array('id' => $id));
  		$rivi = $query->fetch();

  		if ($rivi){
  			$tuote = new Tuote(array(
  				'id' => $rivi['id'],
  				'myyjä_id' => $rivi['myyjä_id'],
  				'kuvaus' => $rivi['kuvaus'],
  				'hinta' => $rivi['hinta'],
  				'lisätietoja' => $rivi['lisätietoja'],
  				'lisäyspäivä' => $rivi['lisäyspäivä']
  				));

  			return $tuote;
  		}

      return null;
  	}

    public static function käyttäjän_tuotteet($id){
      $query = DB::connection()->prepare('SELECT * FROM Tuote WHERE myyjä_id = :myyja_id');
      $query->execute(array('myyja_id' => $id));
      $rivit = $query->fetchAll();
      $tuotteet = array();

      foreach($rivit as $rivi){

        $tuotteet[] = new Tuote(array(
          'id' => $rivi['id'],
          'myyjä_id' => $rivi['myyjä_id'],
          'kuvaus' => $rivi['kuvaus'],
          'hinta' => $rivi['hinta'],
          'lisätietoja' => $rivi['lisätietoja'],
          'lisäyspäivä' => $rivi['lisäyspäivä']
          ));
      }

      return $tuotteet;
    }

    public static function etsi_tuotteen_myyjä($id){
      $query = DB::connection()->prepare('SELECT myyjä_id FROM Tuote WHERE id = :id LIMIT 1');
      $query->execute(array('id' => $id));
      $rivi = $query->fetch();

      $myyjä_id = $rivi['myyjä_id'];

      return $myyjä_id;

    }

    public function save(){
      $query = DB::connection()->prepare('INSERT INTO Tuote (myyjä_id, kuvaus, hinta, lisätietoja, lisäyspäivä) VALUES (:myyjaid, :kuvaus, :hinta, :lisatietoja, CURRENT_DATE) RETURNING id');

      $hinta = str_replace(',','.', $this->hinta);

      $this->hinta = $hinta;

      $query->execute(array('myyjaid' => $this->myyjä_id, 'kuvaus' => $this->kuvaus, 'hinta' => floatval($this->hinta), 'lisatietoja' => $this->lisätietoja));

      $rivi = $query->fetch();

      $this->id = $rivi['id'];
    }

    public function päivitä(){
      $query = DB::connection()->prepare('UPDATE Tuote SET kuvaus = :kuvaus, hinta = :hinta, lisätietoja = :lisatietoja, lisäyspäivä = CURRENT_DATE WHERE id = :id');

      $query->execute(array('kuvaus' => $this->kuvaus, 'hinta' => $this->hinta, 'lisatietoja' => $this->lisätietoja, 'id' => $this->id));

    }

    public function poista(){
      $query = DB::connection()->prepare('DELETE FROM Tuote WHERE id = :id');
      $query->execute(array('id' => $this->id));
    }

    public function validate_kuvaus(){
      $errors = array();

      $kuvaus = str_replace(' ', '', $this->kuvaus);

      if($kuvaus == '' || $kuvaus == null || strlen($kuvaus) < 3){
        $errors[] = 'Kuvaus pitää olla vähintään 3 merkkiä!';
      }

      if(strlen($this->kuvaus) > 30){
        $errors[] = 'Kuvaus ei saa olla yli 30 merkkiä!';
      } 

      return $errors;
    }

    public function validate_hinta(){
      $errors = array();
      if($this->hinta == '' || $this->hinta == null){
        $errors[] = 'Hinta ei voi olla tyhjä!';
      }

      if(strlen($this->hinta) > 10){
        $errors[] = 'Hinta ei voi olla yli 10 numeroa!';
      }

      $hinta = str_replace(',','.', $this->hinta);

      $this->hinta = $hinta;

      if(is_numeric($this->hinta) == false){
        $errors[] = 'Hinta täytyy olla numero tai desimaaliluku!';
      }

      return $errors;
    }

    public function validate_lisätiedot(){
      $errors = array();
      if(strlen($this->lisätietoja) > 300){
        $errors[] = 'Lisätiedot eivät voi olla yli 300 merkkiä!';
      }

      return $errors;
    }
  }