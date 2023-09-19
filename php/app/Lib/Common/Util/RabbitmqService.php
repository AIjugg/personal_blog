<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-09-19
 * Time: 21:18
 */

namespace App\Lib\Common\Util;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitmqService
{

    private static function getConnect()
    {
        $config = [
            'host' => env('RABBITMQ_HOST', '127.0.0.1'),
            'port' => env('RABBITMQ_PORT', 5672),
            'user' => env('RABBITMQ_USER', 'guest'),
            'password' => env('RABBITMQ_PASSWORD', 'guest'),
            'vhost' => env('RABBITMQ_VHOST', '/'),
        ];
        return new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['password'], $config['vhost']);
    }


    /**
     * 数据插入到mq队列中（生产者）
     * @param string $queue 队列名称
     * @param array $messageBody 消息体
     * @param string $exchange 交换机名称
     * @param string $routing_key 设置路由
     * @throws \Exception
     */
    public static function push($queue, $exchange, $routing_key, $messageBody)
    {
        //获取连接
        $connection = self::getConnect();

        //构建通道（mq的数据存储与获取是通过通道进行数据传输的）
        $channel = $connection->channel();

        //监听数据,成功
        $channel->set_ack_handler(function (AMQPMessage $message) {
            dump("数据写入成功");
        });

        //监听数据,失败
        $channel->set_nack_handler(function (AMQPMessage $message) {
            dump("数据写入失败");
        });

        //声明一个队列
        //$channel->queue_declare($queue, false, true, false, false);

        //指定交换机，若是路由的名称不匹配不会把数据放入队列中
        $channel->exchange_declare($exchange, 'direct', false, true, false);

        //队列和交换器绑定/绑定队列和类型
        //$channel->queue_bind($queue, $exchange, $routing_key);

        $config = [
            'content_type' => 'application/json',
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
        ];

        $messageStr = json_encode($messageBody, JSON_UNESCAPED_UNICODE);
        //实例化消息推送类
        $message = new AMQPMessage($messageStr, $config);

        //消息推送到路由名称为$exchange的队列当中
        $channel->basic_publish($message, $exchange, $routing_key);

        //监听写入
        $channel->wait_for_pending_acks();

        //关闭消息推送资源
        $channel->close();

        //关闭mq资源
        $connection->close();
    }


    /**
     * @param $queue
     * @param $callback
     * @return bool
     * @throws \Exception
     */
    public static function pop($queue, $callback)
    {
        $connection = self::getConnect();

        //构建消息通道
        $channel = $connection->channel();

        //从队列中取出消息，并且消费
        $message = $channel->basic_get($queue);

        if (!$message) return false;

        //消息主题返回给回调函数
        $res = $callback($message->body);

        if ($res) {
            print_r('ack验证' . PHP_EOL);
            //ack验证，如果消费失败了，从新获取一次数据再次消费
            $channel->basic_ack($message->getDeliveryTag());
        }

        print_r('ack消费完成' . PHP_EOL);

        $channel->close();
        $connection->close();

        return true;
    }
}
