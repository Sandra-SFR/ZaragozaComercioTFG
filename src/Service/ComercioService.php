<?php

namespace App\Service;



use DateTime;

class ComercioService
{
    public function verificarEstadoComercio($horas): string
    {
        date_default_timezone_set('Europe/Madrid');

        $horaActual = new DateTime();
        $ahora = DateTime::createFromFormat("H:i",$horaActual->format("H:i"));
        $diaActual = date("l");
        $diasSemana = [
            'monday' => 'lunes',
            'tuesday' => 'martes',
            'wednesday' => 'miercoles',
            'thursday' => 'jueves',
            'friday' => 'viernes',
            'saturday' => 'sabado',
            'sunday' => 'domingo'];

        $nombreDiaActual = $diasSemana[strtolower($diaActual)];

        $comercioAbierto = false;

//        dd($horas);

        foreach ($horas as $horario) {
            $horaApertura = DateTime::createFromFormat("H:i", $horario['horaApertura']->format("H:i"));
            $horaCierre = DateTime::createFromFormat("H:i", $horario['horaCierre']->format("H:i"));
            $nombreDia = strtolower($horario['nombreDia']);

//            dd("Ahora: $ahora, Apertura: $horaApertura, Cierre: $horaCierre, Día Actual: $nombreDiaActual, Día Horario: $nombreDia");
//            dd("Ahora: $ahora, Apertura: " . $horaApertura->format("H:i") . ", Cierre: " . $horaCierre->format("H:i") . ", Día Actual: $nombreDiaActual, Día Horario: $nombreDia");

//            dd("Dentro del bucle: Día Horario: $nombreDia");

//            dd("Iteración: Día Actual: $nombreDiaActual, Día Horario: $nombreDia");

            if ($ahora >= $horaApertura && $ahora <= $horaCierre && $nombreDiaActual == $nombreDia) {
                $comercioAbierto = true;
                break;
            }
        }
//        dd("Después del bucle: Día Actual: $nombreDiaActual");

        if ($comercioAbierto) {
            return 'Abierto';
        } else {
            return 'Cerrado';
        }
    }
}