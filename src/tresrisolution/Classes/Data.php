<?php
/**
 * Created by PhpStorm.
 * User: milic
 * Date: 28/10/2015
 * Time: 16:46
 */

namespace tresrisolution\Classes;

use \DateTime;

class Data extends DateTime
{
    private $hora;
    private $minuto;
    private $segundo;


    /**
     * @return mixed
     */

    public function getHora()
    {
        $this->hora = $this->format('H');
        return $this->hora;
    }

    /**
     * @return mixed
     */
    public function getMinuto()
    {
        $this->minuto = $this->format('i');
        return $this->minuto;
    }

    /**
     * @return mixed
     */
    public function getSegundo()
    {
        $this->segundo = $this->format('s');
        return $this->segundo;
    }

}