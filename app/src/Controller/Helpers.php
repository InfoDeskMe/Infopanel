<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

class Helpers extends AbstractController {
    /**
     * @Route("/api/read_config", name="readConfig")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */ 
    public function readConfig() {
        $response = new Response();
        if (file_exists('data.json')) {
            $json = file_get_contents('data.json');
            $response->headers->set('Content-Type', 'application/json');
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->setContent(json_encode($json,JSON_PRETTY_PRINT ));
            return $response;
        } else {
            throw $this->createNotFoundException('No data');
        }
    }

    /**
     * @Route("/api/add_news", name="addNews")
     */
    public function addNews() {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); // blocks an illigal access
        $data_headline = $_REQUEST["headline"];
        $data_content = $_REQUEST["content"];
        $data_url = $_REQUEST["url"];
        $data_image = $_REQUEST["image"];
        $data_uniqueId = $_REQUEST["uniqueId"];

        if (file_exists('data.json') && filesize('data.json') !== 0) {
            $contents = file_get_contents('data.json');
            $json_arr = json_decode($contents, true);
            $json_length = count($json_arr); 
            $json_arr[ "news" . $json_length] = array(
                   "headline" => $data_headline,
                   "content" => $data_content,
                   "url" => $data_url,
                   "image" => $data_image,
                   "id" => $data_uniqueId
                );

            $json = json_encode($json_arr, JSON_PRETTY_PRINT);
            $bytes = file_put_contents("data.json", $json);
        } else {
            $json = json_encode(array(
                "news0" => array(
                   "headline" => $data_headline,
                   "content" => $data_content,
                   "url" => $data_url,
                   "image" => $data_image,
                   "id" => '0'
                )),JSON_PRETTY_PRINT);
    
            $bytes = file_put_contents("data.json", $json);
        }
        $response = new Response();
        echo $response;
        return $response;
    }

    /**
     * @Route("/api/edit_news", name="editNews")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function editNews() {  
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $data_headline = $_REQUEST["headline"];
        $data_content = $_REQUEST["content"];
        $data_url = $_REQUEST["url"];
        $data_image = $_REQUEST["image"];
        $data_uniqueId = $_REQUEST["uniqueId"];

        $contents = file_get_contents('data.json');
        $json_arr = json_decode($contents, true);
        $json_length = count($json_arr);
        $m = array($json_arr);
        $s = 0;
        foreach($json_arr as $key) {
            $s++;
            $m = array_keys($json_arr);
            if ($key['id'] == $data_uniqueId) {  
               $k = $m[$s - 1]; // returns the right news 
               $json_arr[$k] = array(
                "headline" => $data_headline,
                "content" => $data_content,
                "url" => $data_url,
                "image" => $data_image,
                "id" => $data_uniqueId 
             );
                $json = json_encode($json_arr, JSON_PRETTY_PRINT);
                $bytes = file_put_contents("data.json", $json);
            } 
        }
        $response = new Response();
        return $response;
    }

    /**
     * @Route("/api/remove_news", name="removeNews")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function removeNews() {  
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $data_toBeDeleted = $_REQUEST["id"];
        $contents = file_get_contents('data.json');
        $json_arr = json_decode($contents, true);
        $json_length = count($json_arr);
        if ($json_length == 0) {
            unlink('data.json');
        } else {
            $m = array($json_arr);
            $s = 0;
            foreach($json_arr as $key) {
                $s++;
                $m = array_keys($json_arr);
                if ($key['id'] == $data_toBeDeleted) {  
                    $k = $m[$s - 1]; // returns the right news 
                    unset($json_arr[$k]);
                    $json = json_encode($json_arr, JSON_PRETTY_PRINT);
                    $bytes = file_put_contents("data.json", $json);
                } 
            }
        $response = new Response();
        return $response;
        }
    }

    /**
     * @Route("/api/read_settings", name="readSettings")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function readSettings() {  
        $response = new Response();
        if (file_exists('config.json')) {
            $json = file_get_contents('config.json');
            $response->headers->set('Content-Type', 'application/json');
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->setContent(json_encode($json, JSON_PRETTY_PRINT ));
            return $response;
        } else {
            throw $this->createNotFoundException('No settings');
        }
    }
    
    /**
     * @Route("/api/save_settings", name="saveSettings")
    */
    public function saveSettings() {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); // blocks an illigal access
        $setting_basicReload = (int)$_REQUEST["basicReload"];
        $setting_newsNumber = (int)$_REQUEST["newsNumber"];
        $setting_tickerSpeed = (int)$_REQUEST["tickerSpeed"];
        $setting_layoutOrientation = (int)$_REQUEST["layoutOrientation"];
        $setting_tickerOrientation = $_REQUEST["tickerOrientation"];
        $setting_tickerOffset = $_REQUEST["tickerOffset"];
        $setting_tickerMode = $_REQUEST["tickerMode"];
        $setting_tickerHeight = (int)$_REQUEST["tickerHeight"];
        $setting_showTicker = (int)$_REQUEST["showTicker"];
        $setting_slideDelay = (int)$_REQUEST["slideDelay"];

        if (file_exists('config.json') && filesize('config.json') !== 0) {
            $contents = file_get_contents('config.json');
            $json_arr = json_decode($contents, true);
            $json_arr = array(
                   "generalReload" => $setting_basicReload,
                   "newsNumber" => $setting_newsNumber,
                   "layout_type" => $setting_layoutOrientation,
                   "tickerSpeed" => $setting_tickerSpeed,
                   "tickerOrientation" => $setting_tickerOrientation,
                   "tickerOffset" => $setting_tickerOffset,
                   "tickerMode" => $setting_tickerMode,
                   "tickerHeight" => $setting_tickerHeight,
                   "showTicker" => $setting_showTicker,
                   "slideDelay" => $setting_slideDelay
                );
            $json = json_encode($json_arr, JSON_PRETTY_PRINT , JSON_NUMERIC_CHECK);
            $bytes = file_put_contents("config.json", $json);
        } else {
            $json = json_encode(array(
                    "generalReload" => 60000,
                    "newsNumber" => 4,
                    "layout_type" => 0,
                    "tickerSpeed" => 10,
                    "tickerOrientation" => 'toLeft',
                    "tickerOffset" => 'run-in',
                    "tickerMode" => 'chain',
                    "tickerHeight" => '80',
                    "showTicker" => 1,
                    "slideDelay" => 8000
                ), JSON_PRETTY_PRINT);
    
            $bytes = file_put_contents("config.json", $json);
        }
         
        $response = new Response();
        echo $response;
        return $response;
    }


    /**
     * @Route("/api/read_ticker", name="readTickerData")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
    */ 
    public function readTickerData() {
        $response = new Response();
        if (file_exists('data_ticker.json')) {
            $json = file_get_contents('data_ticker.json');
            $response->headers->set('Content-Type', 'application/json');
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->setContent(json_encode($json, JSON_PRETTY_PRINT ));
            return $response;
        } else {
            throw $this->createNotFoundException('No ticker data');
        }
    }


    /**
     * @Route("/api/add_tickernews", name="addTickerNews")
     */
    public function addTickerNews() {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); 
        $data_content = $_REQUEST["content"];
        $data_uniqueId = $_REQUEST["uniqueId"];

        if (file_exists('data_ticker.json') && filesize('data_ticker.json') !== 0) {
            $contents = file_get_contents('data_ticker.json');
            $json_arr = json_decode($contents, true);
            $json_length = count($json_arr); 
            $json_arr[ "news" . $json_length] = array(
                   "content" => $data_content,
                   "id" => $data_uniqueId
                );

            $json = json_encode($json_arr, JSON_PRETTY_PRINT);
            $bytes = file_put_contents("data_ticker.json", $json);
        } else {
            $json = json_encode(array(
                "news0" => array(
                   "content" => $data_content,
                   "id" => '0'
                )),JSON_PRETTY_PRINT);
    
            $bytes = file_put_contents("data_ticker.json", $json);
        }
        $response = new Response();
        echo $response;
        return $response;
    }

    /**
     * @Route("/api/remove_tickernews", name="removeTickerNews")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function removeTickerNews() {  
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $data_toBeDeleted = $_REQUEST["id"];
        $contents = file_get_contents('data_ticker.json');
        $json_arr = json_decode($contents, true);
        $json_length = count($json_arr);
        if ($json_length == 0) {
            unlink('data_ticker.json');
        } else {
            $m = array($json_arr);
            $s = 0;
            foreach($json_arr as $key) {
                $s++;
                $m = array_keys($json_arr);
                if ($key['id'] == $data_toBeDeleted) {  
                    $k = $m[$s - 1]; // returns the right news 
                    unset($json_arr[$k]);
                    $json = json_encode($json_arr, JSON_PRETTY_PRINT);
                    $bytes = file_put_contents("data_ticker.json", $json);
                } 
            }
        $response = new Response();
        return $response;
        }
    }  

    /**
     * @Route("/api/edit_tickernews", name="editTickerNews")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function editTickerNews() {  
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $data_content = $_REQUEST["content"];
        $data_uniqueId = $_REQUEST["uniqueId"];

        $contents = file_get_contents('data_ticker.json');
        $json_arr = json_decode($contents, true);
        $json_length = count($json_arr);
        $m = array($json_arr);
        $s = 0;
        foreach($json_arr as $key) {
            $s++;
            $m = array_keys($json_arr);
            if ($key['id'] == $data_uniqueId) {  
               $k = $m[$s - 1]; // returns the right news 
               $json_arr[$k] = array(
                "content" => $data_content,
                "id" => $data_uniqueId 
             );
                $json = json_encode($json_arr, JSON_PRETTY_PRINT);
                $bytes = file_put_contents("data_ticker.json", $json);
            } 
        }
        $response = new Response();
        return $response;
    }
}


