<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 * ### System requirements
	 * 
	 * Kohana 3.3
	 * PHP 5.2.4 or greater
	 * driver - phpredis (https://github.com/nicolasff/phpredis)
	 * ---------------------------------------------------------
	 * @package    Kohana/Cache
	 * @category   Module
	 * @version    1.0
	 * @author     Mikhno Roman (admin@infarmer.ru)
	 * @copyright  (c) 2012 Mikhno Roman
	 * @license    халява
	 */
	class Kohana_Cache_Redis extends Cache {

		/**
		 * Redis resource
		 *
		 * @var Redis
		 */
		protected $_redis;

		/**
		 * The default configuration for the redis server
		 *
		 * @var array
		 */
		protected $_default_config = array(
			'host'  => 'localhost',
			'port'  => 6379,
			'db_num' => 0,
			'igbinary_serialize' => false,
		);

		/**
		 * Constructs the redis Kohana_Cache object
		 *
		 * @param   array     configuration
		 * @throws  Kohana_Cache_Exception
		 */
		protected function __construct(array $config)
		{
			if (!extension_loaded('redis'))
			{
				throw new Cache_Exception('PHP redis extension is not available.');
			}

			parent::__construct($config);

			$host=(isset($config['host'])) ? $config['host'] : $this->_default_config['host'];
			$port=(isset($config['port'])) ? $config['port'] : $this->_default_config['port'];
			$this->_redis = new Redis();
			$this->_redis->connect($host, $port, 1);

			if(@$config['igbinary_serialize']===true){
				$this->_redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_IGBINARY);
			}

			$db_num=(isset($config['db_num'])) ? $config['db_num'] : $this->_default_config['db_num'];
			$this->_redis->select($db_num);
		}


		public function get($id, $default = NULL)
		{
			// Get the value from Redis
			$value = $this->_redis->get($id);

			if ($value==false) {
				$value = $default;
			}

			// Return the value
			return $value;
		}

		public function set($id, $data, $lifetime = false)
		{
			if($lifetime){
				return $this->_redis->setex($id, $lifetime, $data);
			} else {
				return $this->_redis->set($id, $data);
			}
		}

		public function delete($id)
		{
			return $this->_redis->del($id); 
		}

		public function delete_all()
		{
			$this->_redis->flushDB();
		}

		public function __call($name, $arguments)
		{
			try {
				$rez=call_user_func_array(array($this->_redis, $name), $arguments);
			} catch (ErrorException $e){
				throw new Kohana_Cache_Exception($e->getMessage());
			}
			
			return $rez;
		}

	}
