<?php
namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;


class Demo extends Command
{
    protected function configure()
    {

        //设置参数
        $this->addArgument('email', Argument::REQUIRED); //必传参数
        $this->addArgument('mobile', Argument::OPTIONAL);//可选参数


        //选项定义
        $this->addOption('message', 'm', Option::VALUE_REQUIRED, 'test'); //选项值必填
        $this->addOption('status', 's', Option::VALUE_OPTIONAL, 'test'); //选项值选填

        $this->setName('demo')->setDescription('Here is the remark ');
    }

    /**
     * 可以这样执行命令 php think test hello 13333333333 -m"this value should set" -s"this value can be null"
     * @param Input $input
     * @param Output $output
     * <a href='/@return'>@小鱼儿</a> void;
     */
    protected function execute(Input $input, Output $output)
    {
        //获取参数值
        $args = $input->getArguments();
        $output->writeln('The args value is:');
        print_r($args);

        //获取选项值
        $options = $input->getOptions();
        $output->writeln('The options value is:');
        print_r($options);

        $output->writeln('Now execute command...');

        $output->writeln("End..");
    }
}