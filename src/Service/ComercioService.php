<?php

namespace App\Service;

use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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

        foreach ($horas as $horario) {
            $horaApertura = DateTime::createFromFormat("H:i", $horario['horaApertura']->format("H:i"));
            $horaCierre = DateTime::createFromFormat("H:i", $horario['horaCierre']->format("H:i"));
            $nombreDia = strtolower($horario['nombreDia']);

            if ($ahora >= $horaApertura && $ahora <= $horaCierre && $nombreDiaActual == $nombreDia) {
                $comercioAbierto = true;
                break;
            }
        }

        if ($comercioAbierto) {
            return 'Abierto';
        } else {
            return 'Cerrado';
        }
    }

    public function json($data): JsonResponse
    {
        $normalizers = [new ObjectNormalizer()];
        $encoders = [new JsonEncoder()];

        $serializer = new Serializer($normalizers, $encoders);
        $json = $serializer->serialize($data, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
            'circular_reference_limit' => 0,
        ]);

        return new JsonResponse($json, JsonResponse::HTTP_OK, [], true);
    }
}