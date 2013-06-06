<?php
/**
 * Created by JetBrains PhpStorm.
 * User: wangjuqing
 * Date: 13-6-6
 * Time: 上午9:57
 * To change this template use File | Settings | File Templates.
 */

class Base {

    //api方式列表
    public $api_list = array('post_code','phone_own');

    //请求地址
    public $action_list = array(
        'post_code'=>'http://www.ip138.com/post/search.asp',
        'phone_own'=>'http://api.showji.com/Locating/www.showji.com.aspx'
    );

    //请求方式
    public $action_type_list = array(
        'post_code'=>'file_get_contents',
        'phone_own'=>'file_get_contents'
    );

    //参数列表
    public $param_list = array(
        'post_code'=>array(
            'area'=>'n2c',
            'action'=>'area2zone'
        ),
        'phone_own'=>array(
            'm'=>'n2c',
            'output'=>'json',
            'callback'=>''
        )
    );

    //header构建
    public $header_list = array(
        'post_code'=>array(),
        'phone_own'=>array()
    );

    // 获取的结果转码
    public $iconv_mark = array(
        'post_code'=>array('GBK','UTF8'),
        'phone_own'=>array('UTF-8','UTF-8')

    );
    //提取正则
    public $preg_flag = array(
        'post_code'=>'/tdc2\>(.*)\<\/td\>/Ui',
        'phone_own'=>'json'
    );
    //输入类型
    public $value_char_type = array(
        'post_code'=>'GBK',
        'phone_own'=>'UTF-8'
    );

    private $action_name = "";  //服务名称
    private $action_url = "";   //请求URL
    private $action_type = "";  //请求类型
    private $param = array();   //参数列表
    private $header = array();  //header列表
    private $preg = "";         //解析结果用的正则

    //main
    public function __construct($name,$value){

        if(!in_array($name,$this->api_list)){
            die(404);
        }

        $this->action_name = $name;
        $this->action_url = $this->action_list[$name];
        $this->action_type = $this->action_type_list[$name];
        $this->param = $this->param_list[$name];
        $this->header = $this->header_list[$name];
        $this->preg = $this->preg_flag[$name];
        $value = iconv('UTF-8',$this->value_char_type[$name],$value);

        $handle = iconv($this->iconv_mark[$name][0],$this->iconv_mark[$name][1],$this->handle($value)); //获取信息源

        $final_result = $this->catchResult($handle);    //正则拿走
        echo $final_result;
    }

    //截获结果
    public function catchResult($handle){
        switch($this->action_name){
            case 'post_code':
                $handle = str_replace('&nbsp;'," ",$handle);
                preg_match_all($this->preg,$handle,$result);
                $out = @$result[1][1];
                if($out===NULL){
                    $out = '地名有误,请确认重新输入';
                }
                break;
            case 'phone_own':
                $out = json_decode(stripcslashes($handle),true);
                $out = $out['Mobile'].' 号码属于 '.$out['City'].'市 运营商:'.$out['Corp'].' 邮政编码：'.$out['PostCode'];
        }
        return $out ;
    }


    //从远程获取源
    public function handle($value){
        //参数替换
        foreach($this->param as $key=>$val){
            if($val==='n2c'){
                $this->param[$key] = $value;
                break;
            }else{
                continue ;
            }
        }
        //根据type去做相应操作
        switch($this->action_type){
            case 'file_get_contents':
                $response = '?'.http_build_query($this->param);
                $content_url = $this->action_url.$response;
                $out = file_get_contents($content_url);
            break ;
        }
        return $out;
    }

}