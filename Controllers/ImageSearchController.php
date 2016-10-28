<?php

class ImageSearchController
{

    public function __construct()
    {
        
    }

    public function GETAction($api_request)
    {
        include 'HTTP/Request2.php';
        
        $request = new Http_Request2('https://api.cognitive.microsoft.com/bing/v5.0/images/search');
        
        $url = $request->getUrl();

        $headers = array(
        // Request headers
         'Ocp-Apim-Subscription-Key' => '2f4d6a9ab0654bed8a2a5325c75448c2',
         );

        $request->setHeader($headers);

        $offset = 0;
        
       

        if(!isset($api_request->url_elements[2])) 
        {
            
            header('HTTP/1.0 400 Bad Request', true, 400);
            $final_response = "No Query Passed";
            return $final_response;
        }

        if(count($api_request->url_elements)>2)
        {
            header('HTTP/1.0 400 Bad Request', true, 400);
            $final_response = "Undefined Error";
            return $final_response;
        }
        

        if(count($api_request->parameters)>2)
        {
              header('HTTP/1.0 400 Cannot accept 2 offsets', true, 400);
              $final_response = "Cant accept more 2 parameters after search query"; 
              return $final_response;
        }
        
        $offset = $api_request->parameters[2];
        $parameters = array(
        // Request parameters
         'q' => $api_request->url_elements[2],
         'count' => '10',
         'offset' => $offset,
         'mkt' => 'en-us',
         'safeSearch' => 'Moderate',
         );

        $url->setQueryVariables($parameters);

        $request->setMethod(HTTP_Request2::METHOD_GET);

        // Request body
        $request->setBody("{body}");

        try
        {
            $response = $request->send();
            
        }
        catch (HttpException $ex)
        {
            echo $ex;
        }
        
        $this->writeEvents($api_request->url_elements[2]);
        
        $api_response = $response->getBody();
        
        $api_response = json_decode($api_response, true);
            
        $final_response = '';
        
        foreach($api_response['value'] as $value)
        {
            $array = array("url"=>$value['contentUrl'],"name"=>$value['name']);
           
            $final_response .= json_encode($array);                  
        }    
       
        return $final_response;
    
    }    

    public function writeEvents($search_query)
    {
                    
         $json_data = json_encode(array("query"=>$search_query,"time"=>time()));

         file_put_contents($_SERVER['DOCUMENT_ROOT'].'/imagesearchapi/latestsearch.json', $json_data.",", FILE_APPEND);

    }

}
