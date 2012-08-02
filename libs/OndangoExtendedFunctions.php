<?

class OndangoExtendedFunctions 
{
    
    // route : uri => function
    
    static $extended_functions = array( "products/best_sellers_ids" => "best_sellers_ids", 
                                       "hello/ondango" => "test"
                                       );
    
    
    public static function is_extended_function ($url)
    {
        if (in_array($url,array_keys(self::$extended_functions)))
        {
            return true;
        } 
        
        return false;
        
    }
    
    // delegate request
    
    public static function request ($method, $url,$params)
    {
        $function=self::$extended_functions[$url];
        return self::$function($method, $url,$params);
    }
    
    // custom (static) functions here
    
    public function test ($method, $url, $params = array ())
    {
        return array("Hi there =)");
    }
    
    public static function best_sellers_ids ($method, $url, $params)
	{
        
        $request = new OndangoRequest ($method, "/sales/all", $params);
        $data =  json_decode ($request->execute (),1);		
        
        if($data['is_error']==1)
        {
            die ("Fatal error: server time out");
        }
        
        $best_sellers = array();
        
        
        foreach ($data['data'] as $k => $v )
        {
            foreach ($v as $k2 => $v2)
			{				
				if($v2['status_payment'] =="paid")
				{
                    $p_id = $v2['Sales']['Sale'][0]['product_id'];
                    
                    if(array_key_exists ($p_id, $best_sellers))
					{					
                        
                        $best_sellers[$p_id]["quantity"] += 1; 
                        
                        
                    } else {					
                        
                        $best_sellers[$p_id] = array( "quantity" => 1,
                                                     "product_id" => $p_id
                                                     );
                    }
                    
                }
            }    
        }
        
        
        arsort ($best_sellers);
        
        $best_sellers=array_slice ($best_sellers,0, $params["limit"],true);
        
        
        if (isset ($params['fields']))
        {
            $categories=array();
            $params['product_id']=implode (",",array_keys($best_sellers));
            $request = new OndangoRequest ($method, "/products", $params);
            $data =  json_decode ($request->execute (),1);
            
            if ($data['is_error']==1)
            {
                die ("Fatal error: server time out");
            }
            
            foreach ($data['data'] as $k=>$v){
                
                $best_sellers[$v["Product"]["product_id"]]=array_merge ($best_sellers[$v["Product"]["product_id"]], array_intersect_key($v["Product"],array_flip($params['fields'])));
                $categories[]=$v["Product"]["category_id"];
                
            }
            
        }
        
        if ((isset ($params['fetch_category_name'])) AND ( $params['fetch_category_name']=='true' ))
        {
            
            $params['category_id']=implode (",",array_unique($categories));
            $request = new OndangoRequest ($method, "/categories", $params);
            $data =  json_decode ($request->execute (),1);
            
            if ($data['is_error']==1)
            {
                die ("Fatal error: server time out");
            }
            /*
             etc etc etc.... 
             */
            
        }   
        
        return $best_sellers;
        
        
	}
    
}
?>