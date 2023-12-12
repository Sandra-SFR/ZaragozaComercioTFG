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

//        dd($horas);

        foreach ($horas as $horario) {
            $horaApertura = DateTime::createFromFormat("H:i", $horario['horaApertura']->format("H:i"));
            $horaCierre = DateTime::createFromFormat("H:i", $horario['horaCierre']->format("H:i"));
            $nombreDia = strtolower($horario['nombreDia']);

//            dd("Ahora: $ahora, Apertura: $horaApertura, Cierre: $horaCierre, D�a Actual: $nombreDiaActual, D�a Horario: $nombreDia");
//            dd("Ahora: $ahora, Apertura: " . $horaApertura->format("H:i") . ", Cierre: " . $horaCierre->format("H:i") . ", D�a Actual: $nombreDiaActual, D�a Horario: $nombreDia");

//            dd("Dentro del bucle: D�a Horario: $nombreDia");

//            dd("Iteraci�n: D�a Actual: $nombreDiaActual, D�a Horario: $nombreDia");

            if ($ahora >= $horaApertura && $ahora <= $horaCierre && $nombreDiaActual == $nombreDia) {
                $comercioAbierto = true;
                break;
            }
        }
//        dd("Despu�s del bucle: D�a Actual: $nombreDiaActual");

        if ($comercioAbierto) {
            return 'Abierto';
        } else {
            return 'Cerrado';
        }
    }

//    public function json($data){
//        $normalizer = new \Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer();
//        $normalizer->setCircularReferenceLimit(1);
//        $normalizer->setCircularReferenceHandler(function ($object) {
//            return $object->getId();
//        });
//        $normalizers = array($normalizer);
//        $encoders = array("json" => new \Symfony\Component\Serializer\Encoder\JsonEncoder());
//
//        $serializer = new \Symfony\Component\Serializer\Serializer($normalizers, $encoders);
//        $json = $serializer->serialize($data, 'json');
//
//        $response = new \Symfony\Component\HttpFoundation\Response();
//        $response->setContent($json);
//        $response->headers->set("Content-Type", "application/json");
//
//        return $response;
//    }

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