<?php

declare(strict_types=1);

namespace App\src;

class Helper
{



    public function filterData($param):array
    {
        $ch = require __DIR__. "\init_curl.php";
        curl_setopt($ch, CURLOPT_URL,"https://api.github.com/orgs/{$param}/repos");
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);

        if (!empty($data['message'])){
            return [];
        }
        $newData = [];
        $i = 0;

        foreach ($data as $value){

            $newData[$i]['name'] = strtolower($value['name']);
            $newData[$i]['url'] = $value['svn_url'];
            $newData[$i]['fork'] = $value['fork'];

            //Count and add contributors to data array
            $ch = require __DIR__. "\init_curl.php";
            curl_setopt($ch, CURLOPT_URL,$value['contributors_url']);
            $response = curl_exec($ch);
            curl_close($ch);
            $contr = json_decode($response, true);

            if (is_array($contr)){
                $newData[$i]['contributors'] = count($contr);
            }else{
                $newData[$i]['contributors'] = 0;
            }

            //Check if its fork, and add to array data original url
            if ($value['fork']){
                $ch = require __DIR__. "\init_curl.php";
                curl_setopt($ch, CURLOPT_URL,$value['url']);
                $response = curl_exec($ch);
                curl_close($ch);
                $fork = json_decode($response, true);
                $original = $fork['parent']['html_url'];

                $newData[$i]['original'] = $original;
            }
            $i++;
        }
        return $newData;
    }


    public function sortData(array $data, string $sort):array
    {
        if ($sort === 'nameAsc'){
            usort($data, function ($item1, $item2) {
                return $item1['name'] <=> $item2['name'];
            });
        }else if ($sort === 'nameDesc'){
            usort($data, function ($item1, $item2) {
                return $item2['name'] <=> $item1['name'];
            });
        }else if ($sort === 'contrAsc'){
            usort($data, function ($item1, $item2) {
                return $item1['contributors'] <=> $item2['contributors'];
            });
        }else {
            usort($data, function ($item1, $item2) {
                return $item2['contributors'] <=> $item1['contributors'];
            });
        }
        return $data;
    }

    public function validateData(array $params):bool
    {
        if ($params['github'] === ""){
            return false;
        }
        if (!($params['sort'] === 'nameAsc' || $params['sort'] === 'nameDesc' || $params['sort'] === 'contrAsc' || $params['sort'] === 'contrDesc')){
            return false;
        }

        return true;
    }
}
