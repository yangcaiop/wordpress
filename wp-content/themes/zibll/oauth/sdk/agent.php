<?php
/*
 * @Author: Qinver
 * @Url: dkewl.com
 * @Date: 2021-07-04 01:07:47
 * @LastEditTime: 2021-07-12 23:03:49
 */

namespace agent;

class agentException extends \Exception
{
}

class OAuth2
{
    protected $config;
    protected $type;
    public $state;

    function __construct($config, $type = '')
    {
        $this->type     = $type;
        $this->config   = $config;
    }

    function getUrl($api_url, $data)
    {

        $sign = $this->sign($data);

        $request_data = $data;
        $request_data['sign'] = $sign;
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $url = $api_url . '?' . http_build_query($request_data);

        return $url;
    }

    function getCallbackUrl($data)
    {

        $data['agent_back_url'] = $this->config['agent_back_url'];
        $api_url = rtrim($this->config['url'], '/') . '/oauth/' . $this->type . '/callback';

        return $this->getUrl($api_url, $data);
    }

    function getAuthUrl()
    {
        $state = md5(time() . mt_rand(11, 99));
        $this->state = $state;
        $api_url = rtrim($this->config['url'], '/') . '/oauth/' . $this->type;

        $parameter['state'] = $this->state;
        $parameter['agent_back_url'] = $this->config['agent_back_url'];
        if (!empty($_REQUEST["bind"])) {
            $parameter['bind'] = $_REQUEST["bind"];
        }

        return $this->getUrl($api_url, $parameter);
    }

    function sign($parameter)
    {
        if (isset($parameter['sign'])) unset($parameter['sign']);
        $parameter = implode('', $parameter) . $this->config['key'];
        return md5($parameter);
    }

    function verifySign($data = '')
    {
        if (!$data) $data = $_GET;
        $sign = $this->sign($data);

        return (!empty($data['sign']) && $data['sign'] == $sign);
    }

    function getBackUrl($url, $data)
    {
        if(!empty($_REQUEST['oauth_rurl'])) $data['oauth_rurl'] = $_REQUEST['oauth_rurl'];

        return $this->getUrl($url, $data);
    }
}
