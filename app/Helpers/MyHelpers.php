<?php

namespace App\Helpers;

use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use Illuminate\Support\Facades\DB;

setlocale(LC_ALL, "pt_BR.utf8");

/**
 * Class MyHelpers
 * @package App\Helpers
 */
class MyHelpers
{
    /**
     * @param $arrayErros
     * @return array
     */
    static function getErrosFromValidator($arrayErros)
    {
        $err = [];

        foreach (json_decode($arrayErros) as $erros) {
            $err[] = implode('', $erros);
        }

        return ($err) ?? [];
    }

    /**
     * @return mixed|null
     */
    static function getUsrIp()
    {
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            //check for ip from share internet
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                // Check for the Proxy User
                $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else {
                $ip = $_SERVER["SERVER_ADDR"];
            }
        }
        // This will print user's real IP Address
        // does't matter if user using proxy or not.

        return filter_var($ip, FILTER_VALIDATE_IP) ?? null;
    }

    /**
     * @param $data
     * @param $result_separador
     * @return string|null
     */
    static function convertData($data, $result_separador = '-')
    {
        $separador = (strpos($data, '/')) ? '/' : '-';
        list($a, $b, $c) = explode($separador, $data);
        if (strlen($a) == 4) {
            return (checkdate($b, $c, $a)) ? $c . $result_separador . $b . $result_separador . $a : null; # dia-mes-ano
        } else {
            return (checkdate($b, $a, $c)) ? $a . $result_separador . $b . $result_separador . $c : null; # dia-mes-ano
        }
    }

    /**
     * @param $data
     * @return false|string
     */
    static function convertDataTime($data)
    {
        return strftime("%d de %b de %Y, %H:%M", strtotime($data));
    }

    /**
     * @param $lat_inicial
     * @param $long_inicial
     * @param $lat_final
     * @param $long_final
     * @return float
     */
    static function getDistance($lat_inicial, $long_inicial, $lat_final = -21.7407, $long_final = -43.3775)
    {
        $d2r = 0.017453292519943295769236;

        $dlong = ($long_final - $long_inicial) * $d2r;
        $dlat = ($lat_final - $lat_inicial) * $d2r;

        $temp_sin = sin($dlat / 2.0);
        $temp_cos = cos($lat_inicial * $d2r);
        $temp_sin2 = sin($dlong / 2.0);

        $a = ($temp_sin * $temp_sin) + ($temp_cos * $temp_cos) * ($temp_sin2 * $temp_sin2);
        $c = 2.0 * atan2(sqrt($a), sqrt(1.0 - $a));

        return 6368.1 * $c;
    }

    /**
     * @param $data
     * @return false|string
     */
    public static function dataDiaUtil($data)
    {
        $feriados = DB::connection('tenant')
            ->table('feriado')
            ->get()
            ->pluck('data')
            ->toArray();

        $novaData = strtotime($data);

        while (in_array(date('w', $novaData), [0, 6]) || in_array($novaData, $feriados)) {
            $novaData = strtotime('-1 days', $novaData);
        }

        return date('Y-m-d', $novaData);
    }

    /**
     * @param $uf
     * @return string
     */
    public static function estadoFromUf($uf)
    {
        $estadosBrasileiros = array(
            'AC' => 'Acre',
            'AL' => 'Alagoas',
            'AP' => 'Amapá',
            'AM' => 'Amazonas',
            'BA' => 'Bahia',
            'CE' => 'Ceará',
            'DF' => 'Distrito Federal',
            'ES' => 'Espírito Santo',
            'GO' => 'Goiás',
            'MA' => 'Maranhão',
            'MT' => 'Mato Grosso',
            'MS' => 'Mato Grosso do Sul',
            'MG' => 'Minas Gerais',
            'PA' => 'Pará',
            'PB' => 'Paraíba',
            'PR' => 'Paraná',
            'PE' => 'Pernambuco',
            'PI' => 'Piauí',
            'RJ' => 'Rio de Janeiro',
            'RN' => 'Rio Grande do Norte',
            'RS' => 'Rio Grande do Sul',
            'RO' => 'Rondônia',
            'RR' => 'Roraima',
            'SC' => 'Santa Catarina',
            'SP' => 'São Paulo',
            'SE' => 'Sergipe',
            'TO' => 'Tocantins'
        );

        return $estadosBrasileiros[$uf] ?? '';
    }

    public static function isDigitableLine($line)
    {
        if (strlen($line) < 40) {
            return '';
        }

        return $line;
    }

    /**
     * @param $valor
     * @return array|string|string[]
     */
    public static function onlyNumeric($valor)
    {
        $valor = trim($valor);
        $valor = preg_replace("/[^0-9]/", "", $valor);
        $valor = str_replace(".", "", $valor);
        $valor = str_replace(",", "", $valor);
        $valor = str_replace("-", "", $valor);
        $valor = str_replace("/", "", $valor);
        return $valor;
    }

    /**
     * @param $val
     * @param $mask
     * @return string
     */
    public function mask($val, $mask)
    {
        $maskared = '';
        $k = 0;
        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] == '#') {
                if (isset($val[$k])) {
                    $maskared .= $val[$k++];
                }
            } else {
                if (isset($mask[$i])) {
                    $maskared .= $mask[$i];
                }
            }
        }
        return $maskared;
    }


    public static function diferencaCadastro($created_at)
    {
        $datetime1 = date_create($created_at);
        $datetime2 = date_create();

        $diff = date_diff($datetime1, $datetime2);

        $array = (array)$diff;

        if($array['y'] > 0){
            return $array['y'] == 1 ? "Há 1 ano" : "Há " . $array['y'] . " anos";
        }elseif ($array['m'] > 0){
            return $array['m'] == 1 ? "Há 1 mês" : "Há " . $array['m'] . " meses";
        }elseif ($array['days'] > 0){
            return $array['days'] == 1 ? "Há 1 dia" : "Há " . $array['days'] . " dias";
        }elseif ($array['h'] > 0){
            return $array['h'] == 1 ? "Há 1 hora" : "Há " . $array['h'] . " horas";
        }elseif ($array['i'] > 0){
            return $array['i'] == 1 ? "Há 1 minuto" : "Há " . $array['i'] . " minutos";
        }else{
            return "Há instantes";
        }
    }


    /**
     * @param $length
     * @return false|string
     */
    static public function genSku($length = 6)
    {
        return strtoupper(substr(md5(mt_rand() . microtime(true)), 0, min($length, 32)));
    }


}
