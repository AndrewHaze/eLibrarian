<?php
// регистрируем функцию завершения, чтобы обрабатывать грубые ошибки, 
//например вызов несуществующего метода у объекта
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && ($error['type'] == E_ERROR || $error['type'] == E_PARSE || $error['type'] == E_COMPILE_ERROR)) {
        $res=array(
            'buffer'=>ob_get_contents(),
            'success'=>false,
            'error'=>"PHP Fatal: ".$error['message']." in ".preg_replace('/(.*)\/(.*)/', "$2", $error['file']).":".$error['line']
        );
        ob_clean();
        header('HTTP/1.1 200 Ok');
        header("Access-Control-Allow-Origin: *");
        echo json_encode($res);
        // ... завершаемая корректно ....
    }
});

// для кроссдоменного CORS, при необходимости - закомментировать или заменить звездочку на требуемое
if($_SERVER['REQUEST_METHOD']=='OPTIONS' ){
    ob_clean();
    header("Access-Control-Allow-Origin: *");
    header("Content-type: application/json; charset=utf-8");
    header("Access-Control-Allow-Headers: X-Requested-With, Content-Type");
    header("Access-Control-Request-Method: POST");
    return true;
}

// /*
// протокол обмена
// - вход - команда по сегменту, например http://site.ru/api/goods - api - попали сюда, goods - команда REST
// в php://input должен быть json, в котором обязателен параметр action, например getgoodsinfo
// в результате формируется имя функции класса goods_getgoodsinfo, которая вызывается 
// с параметром входящего json

// функция класса должна вернуть массив с тремя полями - data & success & error
// в поле data возвращается непосредственно результат функции, в нашем случае - реестр чеков
// в поле success возвращается true | false - признак успешного выполнения
// в поле error возвращается описание ошибки в случае неудачного выполнения функции
// */

$api=new ApiCls();

// функция, проверяющая залогиненность юзера
if(is_user_login()){ 
    $api->checkcommand();
}else{
    $res=array('succes'=>false,'error'=>'Пользователь не авторизован','data'=>'');
    ob_clean();
    header("Content-type: application/json; charset=utf-8");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: X-Requested-With, Content-Type");
    echo json_encode($res);
}

return true;

class ApiCls{
    function checkcommand(){ // точка входа для класса api, здесь первичный разбор, 
        //вызов метода и возврат результата
        // главное - определить второй сегмент в url, то есть в случае http://site.ru/api/goods
        $segment=????????; // в $segment должен оказаться 'goods'

        $res=array('success'=>false,'error'=>'Empty action'); // сразу проверка на наличие action в параметрах
        if(!$segment){
            $res['error']='Empty command';
        }else{
            if($_SERVER['REQUEST_METHOD']=='PUT' || $_SERVER['REQUEST_METHOD']=='POST'){
                $reqdata = file_get_contents('php://input'); 
                $b=json_decode($reqdata);
                $b=get_object_vars($b);
                if(isset($b['action'])){
                    $res['error']='no error';
                    $nm=$segment.'_'.$b['action'];
                    $r=$this->$nm($b); // вызов метода по action из пост и дальнейшая обработка результатов
                    // чё-то тут намутил, но работает - и ладно
                    if(!isset($r['success'])){
                        $res['success']=false;
                        $res['error']='No success flag in method '.$nm;
                    }else{
                        if(!isset($r['data'])){
                            $res['success']=false;
                            $res['error']='No result data in method '.$nm;
                        }else{
                            $res['success']=$r['success'];
                            $res['data']=$r['data'];
                            if(!$r['success']){
                                if(isset($r['error'])){
                                    $res['error']=$r['error'];
                                }else{
                                    $res['error']='Success is false, but no error message in method '.$nm;
                                }
                            }
                        }
                    }
                }
            }
        }
        // непосредственная выдача данных
        $this->_printresponse($res);
    }
    function _printresponse($res){
        // проверяем наличие html-вывода (отладка или warnings)
        $res['buffer']=ob_get_contents();
        if($res['buffer']!=''){
            $res['success']=false;
            isset($res['error'])?$res['error']=implode(',',array('module error',$res['error'])):$res['error']='module error';
        }
        // очищаем буфер вывода и формируем свои заголовки
        ob_clean();
        header("Content-type: application/json; charset=utf-8");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-Requested-With, Content-Type");
        // ну и, наконец, выдаем результирующий JSON
        echo json_encode($res);
    }
    function __call($name,$post){
        // эта функция вызывается при попытке вызвать несуществующий метод класса, то есть при вызове '/api/blablabla' получим отлуп
        $info=array('method'=>$name,'post'=>$post,'error'=>'Method not found','success'=>false);
        return $info;
    }
    /*********************************************************/
    // начинаем блок методов api
    function goods_getgoodsinfo($prm){
        // формируем заготовку ответа
        $res=array('data'=>array(),'success'=>true,'error'=>'');
        // при необходимости здесь можно вставить проверку наличия полномочий у пользователя
        // и если что не так, то выключить success, прописать "облом!" в error и не возвращать данные
        $res['data']['articleinfo']= getArticleInfo($prm['article']); // вызов функции, 
        //которая собирает требуемые данные для определенного артикула и возвращает, опять же
        // в формате JSON
        return $res;
    }
}