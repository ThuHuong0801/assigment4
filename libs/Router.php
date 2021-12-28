<?php
namespace libs;
/**
 * Router
 * @param $method
 * @param $pattern
 * @param array $dest
 * eg: $this->register('get', '/users/detail/{id}, ['UserController', 'detail'])
 * eg: $this->register('get', '/users/{name}/{id}, 'delete')
*/
class Router
{
    private static string $url = '';
    public static $routeTable = [];
    public static $currentRoute = null;

    //class contructor detect URL automally 
    public function __construct()
    {
        static::$url = $_SERVER['REQUEST_URI'];

    }

    /**
     * add a route to routeTable[]
     * @param string $method
     * @param string $pattern
     * @param array $dest have controller, action, params, ...
     * @param array $middleware
     * @return void
     *
     */
    public static function register($method,$pattern,$dest = [])
    {
        /*
        $this->routeTable = [
            'get' => [
                'pattern1' => ['controller'=>'xxx', 'action'=>'yy', 'params'=>'zz']
            ]
        ]
        */
        $method = strtolower($method);
        if(!is_array($dest))
        {
            $dest = [$dest];
        }
        if ($pattern == '') 
        {
            $pattern = '/home';
        }
        //trim( $string, $char): remove whitespace
        $pattern = trim($pattern, '/');

        static::$routeTable[$method][$pattern] = [
            'controller' => $dest[0],
            'action' => $dest[1] ?? 'index',
        ];
    }
    //match current url to route table and set current route
    protected static function matching()
    {
        $url = parse_url(static::$url)['path'];
        $path = trim($url, '/');
        if ($path == '') 
        {
            $path = 'home';
        }
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        $patternScore = [];
        foreach (static::$routeTable[$method] as $pattern => $controller) 
        {
            
            if ($pattern == $path) 
            {
                static::$currentRoute = static::$routeTable[$method][$pattern];
                break;
            }
            $patternScore[] = static::patternScore($path, $pattern);
        };
        usort($patternScore, function($a, $b)
        {
            if ($a['score'] == $b['score']) 
            {
                return count($a['params']) < count($b['params']);
            }
            return $a['score'] < $b['score'];
        });

        
        if ($patternScore != null && static::$currentRoute == null) 
        {
            if ($patternScore[0]['score'] == 0 && !isset(static::$currentRoute['score'])) 
            {
                //not fouund
                http_response_code(404);
                exit();
            }
            static::$currentRoute = static::$routeTable[$method][$patternScore[0]['pattern']];
            static::$currentRoute['params'] = $patternScore[0]['params'];
        }
    }
    /**
     * pattern score
     * @param string $path
     * @param string $patternStr
     * @return array
     */
    private static function patternScore(string $path, string $patternStr)
    {
        $path = explode('/', $path);
        $pattern = explode('/', $patternStr);
        // var_dump($path);
        //var_dump($patternStr);
        if (count($path) != count($pattern)) 
        {
            return ['score' => 0, 'params' => [], 'pattern' => $patternStr];
        }

        $score = 0;
        $param = [];
        foreach ($pattern as $i => $section) 
        {
            if($path[$i] === $section)
            {
                $score += 1;
            } else 
            {
                $p = self::convertParams($section);
                if($p)
                {
                    $param[$p] = $path[$i];
                }
            }
        }
        return ['score' => $score, 'params'=> $param, 'pattern'=> $patternStr];
    }

    private static function convertParams($section)
    {
        $start = substr($section, 0, 1);
        $end = substr($section, -1, 1);

        if ($start == '{' && $end == '}') 
        {
            return str_replace(['{','}'], '', $section);
        }
        return '';
    }
    /**
     * get route 
     * @return array
     *
    **/
    public static function getRoute()
    {
        static::matching();
        return static::$currentRoute;
    } 
}
