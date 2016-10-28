<?php

class LatestController
{

    public function GETAction($request)
    {
        
         


         $response = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/imagesearchapi/latestsearch.json');
         return $response;
    
    }


}
