<?php
/**
 * Created by PhpStorm.
 * User: milic
 * Date: 28/10/2015
 * Time: 16:49
 */

namespace tresrisolution\Classes;


class Horas
{
    public function getHoraInt()
    {
        $dh = new Data();

        $hora = $dh->getHora();

        if( $dh->getMinuto() < 10 )
            $hora += "0" + $dh->getMinuto();
        else
            $hora += $dh->getMinuto();

        if( $dh->getSegundo() < 10 )
            $hora += "0" + $dh->getSegundo();
        else
            $hora += $dh->getSegundo();

        return $hora;
    }

}